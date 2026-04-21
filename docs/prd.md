# PRD — Product Requirements (for AI)

> **Rule for AI:** always update this file and `docs/sdd.md` together when there is a relevant change to product, user journeys, business rules, or scope.  
> **How these docs relate:** this PRD describes **what the product is and why it exists**; the SDD describes **how the system implements it**.

> **Code language:** all source code must be in **English** (identifiers, in-code comments, DocBlocks). End-user text is localized via translation files; product docs for humans may be localized separately — this PRD/SDD pair is maintained in **English** for tooling and AI.

- **Last updated:** 2026-04-21  
- **Purpose:** fast product understanding for AI agents

**Note (ops):** Coolify/env templates are under `docs/deploy/` (see root `README.md` and `.env.example`).

## 1) Problem and goal

- **Problem:** grow and maintain a digital collection with curatorial quality and external participation.
- **Goal:** enable public contributions with basic safety and an admin-manageable validation flow.

## 2) Personas

- **Public visitor:** explores, searches, and browses the catalog.
- **External collaborator:** submits new items and extras via email verification.
- **Curator / admin:** reviews, validates, and organizes content and taxonomy.
- **Operations admin:** maintains authentication, locks, and technical configuration.

## 3) Product capabilities (current scope)

- **Public exploration:**
  - item listing with filters, search, and sorting;
  - detail page with rich context.
- **Public contribution:**
  - submit an item with metadata, images, and relations;
  - submit an extra on an existing item;
  - collaborator verification via email code.
- **Curation / admin:**
  - CRUD for items, categories, tags, extras, components, and relations;
  - content validation/moderation;
  - image and QR management.
- **Multi-locale content:**
  - translations and locale fallback.

## 4) Core functional requirements

- FR-01: the public only sees validated items.
- FR-02: public contribution requires an active verification session.
- FR-03: internal or blocked collaborators cannot use the public contribution path.
- FR-04: admins can review and validate catalog entities.
- FR-05: the system keeps language/content consistent per locale.
- FR-06: public files are served through the application proxy.

## 5) Non-functional requirements

- NFR-01: local/CI operations via Docker and `./run`.
- NFR-02: MySQL-oriented compatibility.
- NFR-03: throttling and anti-automation on sensitive endpoints.
- NFR-04: transactional robustness on contribution flows.

## 6) Boundaries and out of scope (today)

- No traditional end-user public account model.
- No multi-state moderation workflow beyond validated / not validated.
- No formal public API for external integrations.
- No full audit trail of per-entity approvals.

## 7) AI-assisted evolution opportunities

- Auto-suggest tags, categories, components.
- Detect likely duplicate items (text/image).
- Prioritize moderation queue by risk or relevance.
- Semantic search and related-item recommendations.

## 8) How AI should use this file

- Use this PRD to check that code changes align with business intent.
- Cross-reference `docs/sdd.md` for technical impact of each requirement.
- Update **Last updated** and the change log when scope, rules, or journeys change.

## Change log (short)

- **2026-04-21**: Initial PRD; English-only code rule. Deploy docs pointer `docs/deploy/`; note on stricter rate limit for public item contribution POST (abuse mitigation). Companion doc renamed to `docs/sdd.md` (software design document; replaces typo `spp.md`).
- **2026-04-21 (later):** Public contribution may only link **validated** catalog items as components (aligned with “public sees validated content”). Internal note on admin edit locks: `docs/internal/edit-locks.md`.
