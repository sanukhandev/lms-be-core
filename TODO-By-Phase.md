# LMS SaaS — Phase-wise To‑Do Checklist (Setup → Full‑Fledged APIs)
_Last updated: 2025-08-25_

> Use this as a project execution board. Each phase has **goals, tasks, dependencies, deliverables, and exit criteria**.  
> Tick boxes as you complete tasks. Phases can run in **two tracks**: Core (backend/platform) and Feature (domain APIs).

---

## Phase 0 — Repo & Environment Bootstrap
**Goal:** Baseline repo, CI, and local env ready.

- [ ] Initialize mono‑repo structure (`/api`, `/cms`, `/infra`, `/docs`)
- [ ] Create Laravel API skeleton (`api/`) with PHP 8.2 & Composer
- [ ] Add base Docker Compose (Laravel + MySQL + Redis + Mailhog)
- [ ] Set up `.env.example` (Laravel, DB, Redis, Mail, Queue)
- [ ] Configure queues (Redis) & default workers (`horizon` optional)
- [ ] Install L5‑Swagger & basic docs route
- [ ] Add coding standards (PHPCS, PHPStan), pre‑commit hooks
- [ ] GitHub Actions/ADO pipeline (build, test, lint, swagger publish)
- [ ] Seed demo data script (`php artisan demo:seed`)
- [ ] Project README + contribution guide

**Deliverables:** Running `php artisan serve`, CI green, Swagger baseline.  
**Exit Criteria:** Local dev onboarding < 30 mins.

---

## Phase 1 — Tenancy & RBAC Foundations
**Goal:** Hard isolation & role permissions in place.

- [ ] Add `tenant_id` to tenant‑owned tables; migrations & backfill
- [ ] Tenant resolver (domain → tenant) + middleware + caching
- [ ] Global Eloquent scope `BelongsToTenant`
- [ ] Base `TenantPolicy` + Policy mapping per model
- [ ] Roles & permissions tables; seed default role sets
- [ ] Permission registry + Gates (enum/constant keys)
- [ ] Auth: JWT + refresh + impersonation (super admin)
- [ ] Standard API error/response envelope
- [ ] Audit log model + middleware (actor, action, entity, payload)

**Deliverables:** `/auth/*`, `/me`, tenant context header, seeded roles.  
**Exit Criteria:** Cross‑tenant access attempts denied by default; policy tests pass.

---

## Phase 2 — Strapi CMS Wiring (Content Source of Truth)
**Goal:** Strapi manages course taxonomy & content; Laravel mirrors.

- [ ] Stand up Strapi (Docker) with admin user
- [ ] Content types: Category, Course, Module, Chapter (relations)
- [ ] Webhooks (create/update/delete) + signing secret
- [ ] Laravel `StrapiService` (REST auth, retries, backoff)
- [ ] Sync workers to mirror IDs → `courses/modules/chapters`
- [ ] Cache course trees per tenant; bust on webhook
- [ ] Multilingual fields & slugs; basic SEO metadata
- [ ] Content search indexing (title, tags, category) in Laravel
- [ ] Docs: “Strapi ↔ Laravel sync contract”

**Deliverables:** `/content/courses` read APIs (from mirror), sync jobs & webhook endpoint.  
**Exit Criteria:** Strapi edits reflect in API within 60s; idempotent sync proven in tests.

---

## Phase 3 — Storage & Media (Box + YouTube)
**Goal:** Per‑tenant secure files & unlisted videos.

- [ ] `BoxStorageService`: auth, presigned upload/download, folder bootstrap
- [ ] Auto folder schema: `/{tenant}/courses/{slug}/resources|assignments|submissions/{user}`
- [ ] Storage quotas & analytics events
- [ ] `YouTubeService`: channel/playlist binding per tenant
- [ ] Upload flow (unlisted), metadata, thumbnail, retries
- [ ] Map video IDs to chapters/sessions
- [ ] Media access policies (students: enrolled only; instructors: own courses)
- [ ] Docs: Media ACL, URL expiry, watermarking options

**Deliverables:** `/storage/*` and `/media/*` endpoints, video links in chapter payloads.  
**Exit Criteria:** No direct public links; all downloads via signed URLs; policy tests pass.

---

## Phase 4 — Payments & Packages (Stripe)
**Goal:** Monetization & feature gating.

- [ ] Stripe account (Connect or standard) mapping per tenant
- [ ] Models: `packages`, `features`, `tenant_subscriptions`, `usage_quotas`
- [ ] `SubscriptionService`: checkout, proration, trials, upgrades/downgrades
- [ ] Webhooks (invoice.paid/failed, subscription.updated) with idempotency
- [ ] Refunds & dunning flows; grace windows
- [ ] Feature flags/limits middleware (package → permission → quota)
- [ ] Tenant billing endpoints & invoices
- [ ] Revenue share calc → instructor earnings cron

**Deliverables:** `/billing/*`, `/packages/*`, Stripe webhook handlers, reports.  
**Exit Criteria:** Purchase → enrollment & access granted; failed payment → restricted gracefully.

---

## Phase 5 — Core Domain: Instructor Track (✅ baseline exists)
**Goal:** Solidify instructor features and tests.

- [ ] Courses (CRUD own), modules/chapters manage via Strapi hooks
- [ ] Class planner: availability, sessions, attendance, conflict rules
- [ ] Assignments: create, rubric, deadlines
- [ ] Grading & feedback; gradebook export
- [ ] Earnings dashboard (periodized, downloadable CSV)
- [ ] Instructor resources (Box) with versioning
- [ ] Analytics: engagement, completion, revenue (own courses)
- [ ] Contract tests for every endpoint

**Deliverables:** `/instructor/*` complete with policy & contract tests.  
**Exit Criteria:** All endpoints documented; >80% coverage for instructor module.

---

## Phase 6 — Core Domain: Student Track
**Goal:** Student experience E2E.

- [ ] Dashboard (progress, deadlines, recommendations)
- [ ] Browse/Enroll + enrolled courses list
- [ ] Content access (chapters with YT + Box links; resume position)
- [ ] Assignments: submit (Box), resubmit policy, view grades/feedback
- [ ] Classes: join live/unlisted sessions, attendance record view
- [ ] Certificates: generation, verification endpoint, shareable link
- [ ] Payments: history, invoices, manage subscription
- [ ] Personal analytics; privacy controls

**Deliverables:** `/student/*` endpoints & UI contracts.  
**Exit Criteria:** A student can enroll, learn, submit, get graded, and earn certificates.

---

## Phase 7 — Tenant Admin Track
**Goal:** Operate a tenant: people, content approvals, finance.

- [ ] Dashboard KPIs (enrollment, revenue, sessions, NPS)
- [ ] User & role management; invites; bulk import
- [ ] Course publishing approvals (Strapi → review → publish)
- [ ] Calendar oversight; capacity & conflict resolution
- [ ] Pricing overrides, promos, coupons
- [ ] Orders, refunds, invoices & exports
- [ ] Storage quotas & access logs
- [ ] AI assistant config (KB uploads, tone presets)
- [ ] Reports (enrollment, completion, finance, instructor performance)

**Deliverables:** `/tenant-admin/*` suite.  
**Exit Criteria:** Tenant can fully operate without platform intervention.

---

## Phase 8 — Super Admin Track
**Goal:** Platform‑wide governance & observability.

- [ ] Tenants CRUD, suspension/reactivation
- [ ] Global packages & feature matrices
- [ ] Payment analytics & chargeback handling
- [ ] Integration health (Strapi/Box/YT/Stripe/Gemini) dashboards
- [ ] Feature flags & maintenance mode
- [ ] Backups, data lifecycle, exports
- [ ] Platform analytics & alerts; SLA endpoints

**Deliverables:** `/super-admin/*` suite.  
**Exit Criteria:** NOC‑ready dashboards; runbooks published.

---

## Phase 9 — AI Assistant (Gemini) — Per Tenant
**Goal:** Tenant‑scoped AI support & learning helpers.

- [ ] KB bootstrap (FAQ, policy, catalog snapshot)
- [ ] Admin tools: upload, version, evaluate
- [ ] Chat proxy: tenant context, safety filters, PII redaction
- [ ] Conversation logs (opt‑in); analytics
- [ ] Recommendation hooks for student dashboard
- [ ] Quality evaluation set & scoring

**Deliverables:** `/ai-assistant/*` for tenant admins; `/student/ai/*` for learners.  
**Exit Criteria:** Useful responses with guardrails; isolation proven.

---

## Phase 10 — White‑Label & Theming
**Goal:** Tenant branding & UI contracts for FE teams.

- [ ] Branding APIs: logo, colors, fonts, assets
- [ ] Themes & custom CSS injection (allow‑list)
- [ ] Navigation schema (menu items with permissions)
- [ ] Content endpoints proxy (Strapi) for FE
- [ ] Storage quotas & analytics exposure
- [ ] Preview endpoints for themes/content

**Deliverables:** `/white-label/*` suite.  
**Exit Criteria:** Tenant can style FE without code changes.

---

## Phase 11 — Documentation, QA, and SRE
**Goal:** Make it operable, testable, and learnable.

- [ ] Swagger tagging per role; request/response examples
- [ ] Error catalog & common problem recipes
- [ ] Postman collection auto‑export
- [ ] Load tests (enrollment, file uploads, class creation)
- [ ] Security tests (OWASP); dependency & secret scans
- [ ] Observability: correlation IDs, trace, metrics, alerts
- [ ] Backups, restore drill, incident runbooks
- [ ] Changelog & API versioning policy

**Deliverables:** `/docs/*`, CI gates for tests/quality.  
**Exit Criteria:** Release candidate can be on‑boarded by a new team in <1 day.

---

## Cross‑Cutting Acceptance Criteria (Definition of Done)
- [ ] **Isolation:** All queries scoped by `tenant_id`; policy tests enforce RBAC
- [ ] **Resilience:** External calls retried with circuit‑breaker & idempotency
- [ ] **Security:** Signed URLs for files; webhook replay protection; secrets vaulted
- [ ] **Performance:** P95 latency goals met; pagination & N+1 addressed
- [ ] **DX:** Swagger complete; examples; Postman export; seed data; make targets
- [ ] **SRE:** Dashboards + alerts live; backups verified; runbooks merged

---

## Suggested Milestone Cadence (8–10 Weeks)
- **W1–W2:** Phases 0–1
- **W3:** Phase 2
- **W4:** Phase 3
- **W5:** Phase 4
- **W6:** Phase 5
- **W7:** Phase 6
- **W8:** Phases 7–8
- **W9:** Phase 9–10
- **W10:** Phase 11 + hardening

---

## Task Labels (for Issues)
`core-tenancy`, `rbac`, `strapi-sync`, `storage`, `video`, `payments`, `instructor`, `student`, `tenant-admin`, `super-admin`, `ai`, `white-label`, `docs`, `sre`, `qa`, `observability`, `security`, `performance`

