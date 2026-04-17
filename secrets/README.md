# `secrets/` — encrypted-at-rest secrets

Files in this directory follow the SOPS+age convention (see `../SECRETS.md`).

- `*.enc.yaml` / `*.enc.json` / `*.enc.env` — **ciphertext**, committed to git.
- `*.plain.yaml` / anything else — **plaintext**, gitignored, never committed.

Decrypt one file:

```
just secrets-decrypt secrets/verpex.enc.yaml
```

Encrypt edits made to a plaintext file back to its `.enc` counterpart:

```
just secrets-encrypt secrets/verpex.plain.yaml
```

The age private key lives at `~/.config/sops/age/keys.txt` (outside the repo).
The public recipient lives in `../.sops.yaml` and is safe to commit.
