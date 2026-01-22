# SPDX-License-Identifier: PMPL-1.0-or-later
# Varnish Configuration for LCB Website - Development
vcl 4.1;

import std;

backend default {
    .host = "wordpress";
    .port = "80";
    .connect_timeout = 5s;
    .first_byte_timeout = 60s;
    .between_bytes_timeout = 10s;
}

# Consent-aware HTTP check (basic version for dev)
sub vcl_recv {
    # Allow health checks
    if (req.url == "/health" || req.url == "/ping") {
        return (synth(200, "OK"));
    }

    # Check for AIBDP consent header (simple version)
    if (req.http.X-AIBDP-Consent && req.http.X-AIBDP-Consent != "accepted") {
        return (synth(430, "Consent Required"));
    }

    # Pass through WordPress admin
    if (req.url ~ "^/wp-admin" || req.url ~ "^/wp-login") {
        return (pass);
    }

    # Don't cache POST requests
    if (req.method == "POST") {
        return (pass);
    }

    # Don't cache authenticated users
    if (req.http.Cookie ~ "wordpress_logged_in") {
        return (pass);
    }

    # Remove tracking cookies
    set req.http.Cookie = regsuball(req.http.Cookie, "(^|;\s*)(__utm[a-z]+|_ga|_gid)=[^;]*", "");
    set req.http.Cookie = regsuball(req.http.Cookie, "^;\s*", "");

    if (req.http.Cookie == "") {
        unset req.http.Cookie;
    }

    return (hash);
}

sub vcl_backend_response {
    # Cache static assets for 1 hour
    if (bereq.url ~ "\.(jpg|jpeg|png|gif|webp|svg|css|js|woff|woff2|ttf)$") {
        set beresp.ttl = 1h;
        unset beresp.http.Set-Cookie;
    }

    # Cache HTML for 5 minutes
    if (beresp.http.Content-Type ~ "text/html") {
        set beresp.ttl = 5m;
    }

    # Don't cache errors
    if (beresp.status >= 500) {
        set beresp.ttl = 0s;
        return (deliver);
    }

    return (deliver);
}

sub vcl_deliver {
    # Add cache status header
    if (obj.hits > 0) {
        set resp.http.X-Cache = "HIT";
        set resp.http.X-Cache-Hits = obj.hits;
    } else {
        set resp.http.X-Cache = "MISS";
    }

    # Remove internal headers
    unset resp.http.X-Varnish;
    unset resp.http.Via;
    unset resp.http.X-Powered-By;

    # Add security headers (basic set for dev)
    set resp.http.X-Content-Type-Options = "nosniff";
    set resp.http.X-Frame-Options = "SAMEORIGIN";
    set resp.http.X-XSS-Protection = "1; mode=block";

    return (deliver);
}

sub vcl_synth {
    # Custom 430 Consent Required response
    if (resp.status == 430) {
        set resp.http.Content-Type = "application/json";
        set resp.http.Preference-Required = "consent-aware-http";
        set resp.http.Link = "</.well-known/aibdp.json>; rel=\"consent-policy\"";

        synthetic({"{"error": "Consent Required", "status": 430, "message": "This service requires AIBDP consent declaration", "policy": "/.well-known/aibdp.json", "documentation": "https://consent-aware-http.org/"}"});

        return (deliver);
    }
}
