# SPDX-License-Identifier: PMPL-1.0-or-later
# Consent-Aware HTTP (AIBDP) Check for Varnish
# Implements HTTP 430 enforcement as per consent-aware-http.org

sub check_aibdp_consent {
    # Skip consent check for .well-known paths (allow discovery)
    if (req.url ~ "^/.well-known/") {
        return;
    }

    # Skip consent check for static assets (conditional)
    # Uncomment to allow unconditional static asset access
    # if (req.url ~ "\.(jpg|jpeg|png|gif|webp|svg|css|js|woff|woff2|ttf)$") {
    #     return;
    # }

    # Check for AIBDP consent header
    if (!req.http.X-AIBDP-Consent || req.http.X-AIBDP-Consent != "accepted") {
        # Check if User-Agent indicates a bot/AI
        if (req.http.User-Agent ~ "(?i)(bot|crawler|spider|scraper|ai|gpt|claude|llm|agent)") {
            return (synth(430, "Consent Required"));
        }
    }

    # Validate AIBDP purpose if provided
    if (req.http.X-AIBDP-Purpose) {
        # Acceptable purposes: research, training-excluded, monitoring, security-audit
        if (req.http.X-AIBDP-Purpose !~ "^(research|training-excluded|monitoring|security-audit)$") {
            return (synth(403, "Forbidden - Invalid Purpose"));
        }
    }

    # If consent header exists but purpose is missing, reject
    if (req.http.X-AIBDP-Consent == "accepted" && !req.http.X-AIBDP-Purpose) {
        return (synth(400, "Bad Request - Purpose Required"));
    }

    # Log consent information for audit
    if (req.http.X-AIBDP-Consent == "accepted") {
        std.log("aibdp:consent=accepted purpose=" + req.http.X-AIBDP-Purpose);
    }
}
