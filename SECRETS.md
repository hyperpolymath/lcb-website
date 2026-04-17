# SPDX-License-Identifier: PMPL-1.0-or-later
# SPDX-FileCopyrightText: 2026 Jonathan D.A. Jewell <j.d.a.jewell@open.ac.uk>

= Secrets management for lcb-website

Every operational identifier or credential this repo references is either
(a) encrypted at rest inside `secrets/` via SOPS+age, or
(b) injected at runtime from GitHub Actions secrets / the Cloudflare dashboard
   / the Verpex cPanel, never from the repo.

There is no third path. If you see a plaintext secret in this repo, that
is a bug — please open an issue and do not push.

== Three-layer defence

1. *Encrypted at rest*: `secrets/*.enc.yaml` files are AES-256-GCM-encrypted
   with SOPS. The age recipient (public key) is in `.sops.yaml`. Only a
   holder of the matching age secret key (at `~/.config/sops/age/keys.txt`,
   never in this repo) can decrypt.
2. *Pre-commit hook*: `hooks/pre-commit` runs gitleaks + a plaintext-SOPS
   detector on every `git commit`. A plaintext secret cannot reach `origin`
   even by accident.
3. *Runtime-only for live credentials*: Cloudflare API tokens, Stripe keys,
   SMTP passwords, etc. live in GitHub Actions repo secrets + the provider
   dashboard. They are never even encrypted-in-repo — they stay entirely
   out-of-tree.

== First-time setup (new dev machine)

. Install tools: `age`, `age-keygen`, `sops`, `gitleaks`.
. Obtain the age secret key from a trusted channel (password manager,
  hardware key backup, in-person). Do NOT email, Slack, or commit it.
. Place it at `~/.config/sops/age/keys.txt` (single file, chmod 600).
. Verify: `sops -d secrets/verpex.enc.yaml` should print plaintext.

If `sops -d` fails with `no age identity found`, your key path is wrong or
the key does not match the recipient in `.sops.yaml`.

== Daily workflow

=== Read a secret

[source,bash]
----
sops -d secrets/verpex.enc.yaml
# or
just secrets-decrypt secrets/verpex.enc.yaml
----

=== Edit a secret in place

[source,bash]
----
sops secrets/verpex.enc.yaml
# Opens $EDITOR on plaintext; re-encrypts on save. Never writes plaintext to disk.
----

=== Add a new secret file

[source,bash]
----
# 1. Create plaintext (gitignored as *.plain.*):
cat > secrets/myservice.plain.yaml <<'EOF'
myservice:
  api_token: xxxxx
EOF

# 2. Encrypt it:
sops -e secrets/myservice.plain.yaml > secrets/myservice.enc.yaml

# 3. Verify round-trip:
sops -d secrets/myservice.enc.yaml

# 4. Delete plaintext (or just leave it — gitignored):
rm secrets/myservice.plain.yaml

# 5. Commit only the .enc.yaml:
git add secrets/myservice.enc.yaml
----

== Rotating the age key

Lost laptop, team change, or scheduled rotation:

. Generate new key: `age-keygen -o new-key.txt`
. Update `.sops.yaml` with the new recipient.
. Re-encrypt every file:
+
[source,bash]
----
find secrets -name '*.enc.*' -exec sops updatekeys {} \;
----
. Commit the updated `.enc` files.
. Distribute the new secret key through your trusted channel.
. Old key: shred + remove from all machines.

== What this does NOT protect against

* A compromised dev machine. If an attacker has your `~/.config/sops/age/keys.txt`,
  they can decrypt everything. Use full-disk encryption, 2FA, and if your threat
  model warrants it, keep the key on a hardware token (YubiKey via age-plugin-yubikey).
* A compromised runtime. Live tokens in GitHub Actions / Cloudflare can be read
  by anyone with write access to the workflow file. Use branch protection +
  required reviews + audit logs.
* Someone you gave the secret key to. Need-to-know only; rotate on offboarding.

== Why SOPS+age over alternatives

* vs. `git-crypt`: age is modern (NaCl-based), hardware-key-friendly, and
  file-granular (SOPS encrypts only values, not keys, so diffs are reviewable).
* vs. `ansible-vault`: no secondary passphrase to lose; no Ansible dependency.
* vs. Hashicorp Vault: no server to run, no network dependency, $0 hosting.
* vs. GitHub encrypted secrets alone: those are runtime-only; SOPS lets infra
  code (compose files, k9 manifests, SaltStack pillar) reference encrypted
  values directly.
