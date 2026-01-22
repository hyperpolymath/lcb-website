# SPDX-License-Identifier: PMPL-1.0-or-later
# Varnish Configuration for LCB Website - Production
# Includes full consent-aware HTTP enforcement

vcl 4.1;

import std;
import directors;

backend wordpress_primary {
    .host = "wordpress";
    .port = "8080";
    .connect_timeout = 5s;
    .first_byte_timeout = 60s;
    .between_bytes_timeout = 10s;
    .probe = {
        .url = "/health";
        .interval = 10s;
        .timeout = 3s;
        .window = 5;
        .threshold = 3;
    }
}

# Include consent checking logic
include "/etc/varnish/consent-check.vcl";

sub vcl_init {
    # Setup director for load balancing (single backend for now)
    new wordpress_cluster = directors.round_robin();
    wordpress_cluster.add_backend(wordpress_primary);
}

sub vcl_recv {
    # Set backend
    set req.backend_hint = wordpress_cluster.backend();

    # Health check endpoint
    if (req.url == "/health" || req.url == "/ping") {
        return (synth(200, "OK"));
    }

    # Enforce AIBDP consent (from consent-check.vcl)
    call check_aibdp_consent;

    # Block non-HTTPS in production
    if (req.http.X-Forwarded-Proto != "https") {
        return (synth(301, "https://" + req.http.Host + req.url));
    }

    # WordPress admin always bypasses cache
    if (req.url ~ "^/wp-admin" || req.url ~ "^/wp-login" || req.url ~ "^/wp-cron") {
        return (pass);
    }

    # Bypass cache for POST, PUT, DELETE
    if (req.method != "GET" && req.method != "HEAD") {
        return (pass);
    }

    # Authenticated users bypass cache
    if (req.http.Cookie ~ "wordpress_logged_in" || req.http.Cookie ~ "wordpress_sec") {
        return (pass);
    }

    # Remove tracking and analytics cookies
    set req.http.Cookie = regsuball(req.http.Cookie, "(^|;\s*)(__utm[a-z]+|_ga|_gid|_gat|_fbp|_fbc)=[^;]*", "");
    set req.http.Cookie = regsuball(req.http.Cookie, "^;\s*", "");

    # If no cookies remain, remove the header
    if (req.http.Cookie == "") {
        unset req.http.Cookie;
    }

    # Normalize Accept-Encoding
    if (req.http.Accept-Encoding) {
        if (req.http.Accept-Encoding ~ "gzip") {
            set req.http.Accept-Encoding = "gzip";
        } elsif (req.http.Accept-Encoding ~ "deflate") {
            set req.http.Accept-Encoding = "deflate";
        } else {
            unset req.http.Accept-Encoding;
        }
    }

    return (hash);
}

sub vcl_hash {
    # Include URL and host in cache key
    hash_data(req.url);
    if (req.http.Host) {
        hash_data(req.http.Host);
    } else {
        hash_data(server.ip);
    }

    # Vary on consent level (if present)
    if (req.http.X-AIBDP-Purpose) {
        hash_data(req.http.X-AIBDP-Purpose);
    }

    return (lookup);
}

sub vcl_backend_response {
    # Cache static assets for 7 days
    if (bereq.url ~ "\.(jpg|jpeg|png|gif|webp|avif|svg|ico|css|js|woff|woff2|ttf|eot|otf)$") {
        set beresp.ttl = 7d;
        set beresp.grace = 1h;
        unset beresp.http.Set-Cookie;
    }

    # Cache HTML for 15 minutes with 1 hour grace
    if (beresp.http.Content-Type ~ "text/html") {
        set beresp.ttl = 15m;
        set beresp.grace = 1h;
    }

    # Cache JSON API responses for 1 minute
    if (beresp.http.Content-Type ~ "application/json") {
        set beresp.ttl = 1m;
        set beresp.grace = 5m;
    }

    # Don't cache errors (serve stale if available)
    if (beresp.status >= 500) {
        set beresp.ttl = 0s;
        set beresp.grace = 1h;
        return (deliver);
    }

    # Don't cache 4xx errors
    if (beresp.status >= 400 && beresp.status < 500) {
        set beresp.ttl = 0s;
    }

    return (deliver);
}

sub vcl_deliver {
    # Add cache status headers
    if (obj.hits > 0) {
        set resp.http.X-Cache = "HIT";
        set resp.http.X-Cache-Hits = obj.hits;
    } else {
        set resp.http.X-Cache = "MISS";
    }

    # Add age header
    set resp.http.X-Cache-Age = obj.age;

    # Remove internal headers
    unset resp.http.X-Varnish;
    unset resp.http.Via;
    unset resp.http.X-Powered-By;
    unset resp.http.Server;

    # Production security headers
    set resp.http.X-Content-Type-Options = "nosniff";
    set resp.http.X-Frame-Options = "SAMEORIGIN";
    set resp.http.X-XSS-Protection = "1; mode=block";
    set resp.http.Referrer-Policy = "strict-origin-when-cross-origin";
    set resp.http.Permissions-Policy = "geolocation=(), microphone=(), camera=()";

    # HSTS (Strict-Transport-Security)
    if (req.http.X-Forwarded-Proto == "https") {
        set resp.http.Strict-Transport-Security = "max-age=31536000; includeSubDomains; preload";
    }

    # CSP (Content-Security-Policy) - adjust as needed
    set resp.http.Content-Security-Policy = "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:;";

    # Add AIBDP policy link
    set resp.http.Link = "</.well-known/aibdp.json>; rel=\"consent-policy\"";

    return (deliver);
}

sub vcl_synth {
    # 301 Redirect to HTTPS
    if (resp.status == 301) {
        set resp.http.Location = resp.reason;
        set resp.reason = "Moved Permanently";
        return (deliver);
    }

    # Custom 430 Consent Required response
    if (resp.status == 430) {
        set resp.http.Content-Type = "application/json; charset=utf-8";
        set resp.http.Preference-Required = "consent-aware-http";
        set resp.http.Link = "</.well-known/aibdp.json>; rel=\"consent-policy\"";

        synthetic({"{"error": "Consent Required", "status": 430, "message": "This service requires AIBDP consent declaration", "policy": "/.well-known/aibdp.json", "documentation": "https://consent-aware-http.org/", "enforcement": "strict"}"});

        return (deliver);
    }
}

# Handle backend errors with grace period
sub vcl_backend_error {
    # Serve stale content if available
    if (bereq.is_bgfetch) {
        return (abandon);
    }

    # Otherwise return error
    set beresp.http.Content-Type = "text/html; charset=utf-8";
    synthetic({"<!DOCTYPE html><html><head><title>Service Temporarily Unavailable</title></head><body><h1>503 Service Temporarily Unavailable</h1><p>The server is temporarily unable to service your request. Please try again later.</p></body></html>"});
    return (deliver);
}
