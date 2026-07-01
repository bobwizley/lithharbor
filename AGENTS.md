# AGENTS.md

lithharbor — phpBB forum served at `lithharbor.net`, deployed to the CodeLab VPS. Two images build from this repo: `lithharbor` (php-fpm) and `lithharbor-nginx`.

## Agent skills

### Issue tracker

Issues and PRDs are tracked in CodeLab Gitea using the `tea` CLI. See `docs/agents/issue-tracker.md`.

### Triage labels

Uses the default triage label vocabulary. See `docs/agents/triage-labels.md`.

### Domain docs

Single-context repo: read root `CONTEXT.md` and root `docs/adr/` when present. See `docs/agents/domain.md`.

---

## Platform contract

- The `deploy-stack` reference in `deploy.yml` must be a full Gitea URL (`https://git.codelab.tec.br/...@master`), not the short `owner/repo` form.
- `traefik-public` and `mariadb-internal` are external and platform-owned — never declare a new network.
- This project owns its own DNS zone and serves the apex directly; it does NOT use the platform's wildcard-subdomain pattern.
- `.env.example` is the full env schema (every variable the app reads, each with a local-dev default). Keep non-secret config there; never hoist it into `compose.yml`.
- Keep `clear_env = no` in the `Dockerfile` php-fpm config — without it the `env_file` vars don't reach worker processes and `config.php` loses its `DB_*` connection.
- Secrets live only in this stack's Vault collection (`lithharbor`); `DB_PASSWORD` is empty in `.env.example`. The four `bw-*` inputs in `deploy.yml` are all-or-nothing.
- This repo must carry the Gitea topic `codelab-stack` to appear in the derived stack inventory.

Canonical contract (networks, middlewares, deploy action, stack template) lives in [codelab-infra](https://git.codelab.tec.br/codelab/infra) and [deploy-stack](https://git.codelab.tec.br/codelab/deploy-stack). When in doubt, read these.
