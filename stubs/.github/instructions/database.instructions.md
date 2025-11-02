---
applyTo: '**'
---
Temula Database Design Instructions

This document defines the database design principles, naming conventions, relationship contracts, and phased schema required to build Temula. It aligns with the consolidated relationship matrix and the product roadmap (Phases 0–6).

## Core principles

- UUID everywhere: All primary keys are ULIDs/UUIDs provided by `App\Models\Base` (via Traitify InteractsWithUuid). No auto-increment IDs in public APIs.
- Soft deletes by default: Add `deleted_at` to all user-facing tables. Use Laravel’s `SoftDeletes`.
 - Multi-tenancy: Include `organization_id` (UUID, required) on multi-tenant tables. Index `organization_id` + key business columns. Use `team_id` for access-control scoping (default org team vs per-event team), not tenancy.
- Ownership & authz: Enforce access via policies and Spatie Permissions; never rely only on foreign keys.
- Money is integer: Store prices/amounts in minor units (e.g., cents) as `bigInteger` to avoid float errors. Currency code (ISO 4217) as string(3).
- Enums: Use string-backed PHP enums (Traitify contract). Store enum values as strings.
- Metadata: For extensibility, provide `meta` JSON where useful; avoid unbounded schemaless usage for core concepts.
- Auditing: Changes to sensitive tables are audited by `owen-it/laravel-auditing`.
- Media: Use Spatie Media Library for file assets (tickets, certificates); prefer secure URLs (Media Secure).
 - Indexing: Add composite indexes for common filters (organization_id + event_id, status, dates). Add uniqueness where business rules require it.
- No raw queries in app code: Use Eloquent/Query Builder; see architecture tests.

## Standard columns

- id (uuid, pk)
 - team_id (uuid, fk -> teams.id) when access-control or operational scoping is needed (e.g., event team)
- created_at, updated_at (timestampsTz)
- deleted_at (soft deletes)
- meta (json, nullable)

## Organizer ownership model

- Organizer = Organization: In Temula, the organizer concept maps to an Organization. Every event is owned by exactly one organization via `organization_id`. Data isolation is enforced by scoping queries to `organization_id`.
- Teams (default vs event): A Team belongs to an Organization. Each organization can have a default team for general collaboration and may create per-event teams for event-specific roles and operations.
- Event teams: An Event can have zero or many Teams (small events may have none). Model this via the `event_team` pivot (M:M). Do not store a single `team_id` on `events`.
- Creator/Owner user: Optionally include `owner_user_id` on `events` to track the creating user; authorization still derives from organization membership and team roles/policies.

Tables that MUST include organization_id (tenancy scope):
- events, ticket_types, registrations, registration_items, transactions, attendees, ticket_instances, check_ins
- registration_forms, registration_form_fields (via form -> event/organization), attendee_responses (denormalize `organization_id` for indexing)
- categories and tags (when not global); pivots are implicitly scoped by related entities
- event_members, certificate_templates, certificate_issues, kit_items, kit_allocations, kit_handover_logs
- qa_questions, qa_votes, polls, poll_options, poll_votes, announcements, rfid_checkpoints, rfid_devices, rfid_reads
- payment_gateway_configs (catalog `payment_gateways` is global; configs are organization/event scoped)
- email_campaigns, email_messages, email_bounces, team_branding, event_branding
- profiles (if organizer-scoped), points_ledger, badges (organization-specific), attendee_badges, leaderboards, leaderboard_entries

Tables that SHOULD include team_id (access & operational scoping):
- event_members, check_ins (who checked-in), engagement items (Q&A, polls) when team-level moderation applies.

Notes:
- Global catalogs (e.g., `payment_gateways`) omit `organization_id`; configuration/instances (`payment_gateway_configs`) are scoped by organization and optional event.
- Where a table is tightly coupled to an entity that already has `organization_id` (e.g., `attendee_responses` via `attendees`), consider duplicating `organization_id` to optimize indexes and simplify tenant scoping.

## Relationship overview (contract)

Use the following as the canonical contract; implement via the tables listed in the phase sections below.

- 1:M User -> AuditLog (P0)
- M:M Role <-> Permission via role_permissions (P0)
- 1:M Organization -> Team (P0/P2)
- 1:M Organization -> Event (P2)
- M:M Event <-> Team via event_team (P2)
- 1:M Event -> TicketType (P1)
- 1:M TicketType -> RegistrationItem (P1)
- 1:M Registration -> RegistrationItem (P1)
- 1:M Registration -> Transaction (P1)
- 1:M PaymentGateway -> Transaction (P1)
- 1:M RegistrationItem -> Attendee (P1)
- M:M Team <-> User via team_user (P2)
- 1:M Team -> TeamSetting (P2)
- M:M Event <-> StaffRole via event_staff (P2)
- 1:1 Attendee -> Certificate (P3)
- 1:M Event -> KitItem (P3)
- M:M Attendee <-> KitItem via handover_logs (P3)
- 1:M Event -> EngagementItem (Q&A, Polls, etc.) (P4)
- 1:M Attendee -> EngagementResponse (P4)
- 1:M Event -> PaymentGatewayConfig (P4)
- 1:M Attendee -> RFID_Scan (P4)
- 1:M User -> ApiToken (P5)
- 1:M PartnerSystem -> WebhookEndpoint (P5)
- 1:M Event -> WebhookLog / Delivery (P5)
- 1:M Attendee -> PointTransaction (P6)
- M:M Attendee <-> Badge via attendee_badges (P6)
- 1:M Leaderboard -> LeaderboardEntry (P6)
- 1:M IndustryCategory -> Event / UserProfile (P6)

## Phase-aligned schema

Below are the recommended tables per phase. Adopt names as specified to keep consistency. If an equivalent already exists under a different name, document the mapping in migrations.

### Phase 0 — Platform foundation

Already present (baseline): users, permissions/roles (Spatie), audits, authentication_log, media, features (Pennant), jobs/cache, teams, team_user, telescope. Continue to ensure policies, indices, and auditing are enabled.

Recommended additions (guidance only):
- organization_settings: id, organization_id FK, key (string), value (json), unique(organization_id, key)
- team_settings: id, team_id FK, key (string), value (json), unique(team_id, key)

Billing & subscriptions (platform-level):
- subscription_plans: id, key (string), name, price_amount (bigint), price_currency (char[3]), interval (enum: monthly, yearly), is_enterprise (bool), features (json), active (bool)
- organization_subscriptions: id, organization_id, plan_id, status (enum: trialing, active, past_due, canceled), trial_ends_at, current_period_start, current_period_end, cancel_at (nullable), canceled_at (nullable), meta (json)
- subscription_invoices: id, organization_id, subscription_id, amount, currency, status (enum: draft, open, paid, void, uncollectible), provider (string), provider_ref (string), issued_at, due_at (nullable), paid_at (nullable), meta (json)
- subscription_payments: id, invoice_id, amount, currency, provider (string), provider_ref (string), status (enum: initiated, succeeded, failed, refunded), occurred_at, payload (json)

### Phase 1 — MVP parity (Events, Tickets, Registration, Payments, Admission)

Core tables (some may exist already; align to contracts):
- events: id, organization_id, name, slug (unique per organization), status (enum), visibility, starts_at, ends_at, venue fields (optional), settings (json), soft deletes, indexes: (organization_id, status, starts_at)
- ticket_types: id, organization_id, event_id, name, code, price_amount (bigint), price_currency, quantity_total, quantity_sold, sales_start, sales_end, status (enum), meta (json), unique(event_id, code)
- registrations: id, organization_id, event_id, user_id (nullable, buyer), status (enum: pending, paid, cancelled, refunded), total_amount, currency, reference (public), gateway (string), meta (json), paid_at
- registration_items: id, organization_id, registration_id, event_id, ticket_type_id, unit_price_amount, qty, total_amount, status (enum), meta (json)
- transactions: id, organization_id, registration_id, amount, currency, gateway (string), gateway_ref, status (enum: initiated, succeeded, failed, refunded), payload (json), occurred_at
- payment_gateways: id, key (string), name, provider (string), active (bool), meta (json) — catalog of providers (e.g., bayarcash, stripe)
- attendees: id, organization_id, event_id, registration_item_id (nullable if walk-in), user_id (nullable), first_name, last_name, email, phone, status (enum: registered, checked_in, cancelled), meta (json)
- ticket_instances (optional but recommended): id, organization_id, event_id, ticket_type_id, registration_item_id, attendee_id (nullable until assigned), serial (unique), qr_code, status (enum: issued, void, used)
- check_ins: id, organization_id, event_id, attendee_id, ticket_instance_id (nullable), method (enum: qr, manual, rfid), occurred_at, by_user_id, by_team_id (nullable, who performed), location/meta (json)

Registration forms (dynamic fields):
- registration_forms: id, organization_id, event_id, name, is_default, meta
- registration_form_fields: id, form_id, type (enum), key, label, required (bool), options (json), order, validation (json)
- attendee_responses: id, organization_id, attendee_id, field_id, value (text/json)

Taxonomy and discovery:
- categories: id, organization_id (nullable for global), slug (unique per organization), name
- event_category: event_id, category_id (pivot, unique)
- tags: id, organization_id (nullable), slug, name
- event_tag: event_id, tag_id (pivot, unique)

Discounts and price rules (optional for MVP):
- coupons: id, organization_id, event_id (nullable), code (unique per organization), type (enum: percent, fixed), value, usage_limit, usage_count, valid_from, valid_until, status
- registration_discounts: id, registration_id, coupon_id, amount_applied

### Phase 2 — Teams & roles

- event_team (pivot): event_id, team_id, unique(event_id, team_id)
- event_members: id, organization_id, event_id, user_id, role (enum: owner, manager, crew, finance, checkin), team_id (nullable, if member acts via a team), unique(event_id, user_id)

### Phase 3 — Premium operations

Certifications:
- certificate_templates: id, organization_id, event_id (nullable), name, template_path/media, meta (json)
- certificate_issues: id, organization_id, event_id, attendee_id, template_id, issued_at, serial (unique), verify_token (unique), media_id (nullable)

Kit handover:
- kit_items: id, organization_id, event_id, sku, name, meta (json), unique(event_id, sku)
- kit_allocations: id, organization_id, event_id, attendee_id, kit_item_id, qty
- kit_handover_logs: id, organization_id, event_id, attendee_id, kit_item_id, qty, handed_over_at, by_user_id, method (enum: scan/manual)

Analytics (snapshots/rollups):
- event_metrics: id, organization_id, event_id, captured_at, metrics (json)

Email campaigns:
- email_campaigns: id, organization_id, event_id (nullable), name, subject, body_template, status (draft/scheduled/sent), scheduled_at, meta (json)
- email_messages: id, campaign_id, to_email, to_name, status, sent_at, provider_id, provider_payload (json), failure_reason (nullable)
- email_bounces: id, organization_id, email, occurred_at, reason, meta (json)

Branding:
- organization_branding (optional): id, organization_id, theme (json), logo_media_id (nullable)
- team_branding: id, team_id, theme (json), logo_media_id (nullable), primary_color, secondary_color
- event_branding: id, organization_id, event_id, theme (json), logo_media_id (nullable)

### Phase 4 — Engagement & hardware

Live Q&A and engagement:
- qa_questions: id, organization_id, event_id, user_id (nullable), attendee_id (nullable), message, status (enum: pending, approved, rejected), votes_count, created_at
- qa_votes: id, question_id, by_user_id (nullable), by_attendee_id (nullable), unique(question_id, by_*)
- polls: id, organization_id, event_id, question, status (draft/open/closed)
- poll_options: id, poll_id, label, order
- poll_votes: id, poll_id, option_id, attendee_id (nullable), user_id (nullable), unique(poll_id, voter*)
- announcements: id, organization_id, event_id, message, level (info/warn), published_at, published_by

Lucky draw:
- draw_pools: id, organization_id, event_id, name, criteria (json)
- draw_entries: id, pool_id, attendee_id, weight (int)
- draw_results: id, pool_id, attendee_id, drawn_at, by_user_id

RFID checkpoints:
- rfid_checkpoints: id, organization_id, event_id, code, name, location (json), unique(event_id, code)
- rfid_devices: id, organization_id, event_id (nullable), serial, registered_at, meta (json)
- rfid_reads: id, organization_id, event_id, checkpoint_id, attendee_id (nullable), tag_uid, read_at, device_id, meta (json)

Payments (multi-gateway config):
- payment_gateways: id, key (string), name, provider (string), active (bool), meta (json) — catalog of providers (e.g., bayarcash, stripe)
- platform_gateway_configs: id, gateway (string), credentials (encrypted json), active (bool), is_default (bool), unique(gateway, is_default where true)
- payment_gateway_configs (enterprise only): id, organization_id, event_id (nullable), gateway (string), credentials (encrypted json), active (bool), unique(organization_id, event_id, gateway)

Policy:
- If an organization’s active plan is not enterprise (or lacks feature org_gateway), use platform_gateway_configs (default active) for all checkouts.
- If enterprise and an org/event config exists and active, prefer that; otherwise fall back to platform defaults.

### Phase 5 — APIs & webhooks

- api_tokens: id, user_id, name, token_hash, abilities (json), last_used_at, expires_at, unique(user_id, name)
- partner_systems: id, organization_id, name, meta (json)
- webhook_endpoints: id, organization_id, partner_system_id (nullable), url, secret, events (json), active (bool)
- webhook_events: id, organization_id, event (string), payload (json), created_at
- webhook_deliveries: id, endpoint_id, event_id, status (pending/succeeded/failed), response_code, response_body (text), attempts, last_attempt_at

### Phase 6 — Gamification & community

- profiles: id, user_id (nullable), attendee_id (nullable), display_name, bio, industry_category_id (nullable), meta (json)
- points_ledger: id, organization_id, profile_id (or attendee_id), points (int), reason (string), occurred_at, ref_type/ref_id (morph), balance_after (int)
- badges: id, organization_id (nullable), slug (unique per organization), name, description, meta (json)
- attendee_badges: attendee_id, badge_id, awarded_at, awarded_by, unique(attendee_id, badge_id)
- leaderboards: id, organization_id (nullable), scope (enum: global/industry/event), key (string), period (enum: all-time, monthly, etc.)
- leaderboard_entries: id, leaderboard_id, profile_id (or attendee_id), score (int), rank (int), unique(leaderboard_id, profile_id)
- industry_categories: id, slug (unique), name

## Foreign keys, indexes, and constraints

- Always add FKs with ON UPDATE CASCADE and ON DELETE RESTRICT (or CASCADE only when business rules demand cascading deletes). For soft-deleted parents, prefer RESTRICT and handle deletes at app level.
- Unique constraints:
	- events: unique(organization_id, slug)
	- event_team: unique(event_id, team_id)
	- ticket_types: unique(event_id, code)
	- ticket_instances: unique(serial)
	- categories/tags: unique(organization_id, slug)
	- event_members: unique(event_id, user_id)
	- coupons: unique(organization_id, code)
	- subscription_plans: unique(key)
	- organization_subscriptions: unique(organization_id, plan_id, current_period_start) (optional for historical periods)
	- platform_gateway_configs: unique(gateway, is_default where true)
	- payment_gateway_configs: unique(organization_id, event_id, gateway)
- Performance indexes (examples):
	- registrations: (organization_id, event_id, status), (reference), (user_id)
	- transactions: (registration_id, status), (gateway, gateway_ref)
	- attendees: (organization_id, event_id, status, email)
	- check_ins: (event_id, occurred_at), (attendee_id, occurred_at)
	- organization_subscriptions: (organization_id, status), (current_period_end)
	- subscription_invoices: (organization_id, status), (issued_at), (paid_at)

## Data types & storage guidance

- Monetary: `bigInteger` for amounts, string(3) for currency.
- Date/time: Use timezone-aware columns (timestampsTz) and store UTC.
- JSON columns: Use for metadata, credentials, payloads; validate structure in app layer.
- Media: Store only IDs/relations to Spatie Media; avoid file paths in business tables.

## Migration authoring checklist

- Create table with UUID pk, timestampsTz, soft deletes when applicable.
- Add `organization_id` where multi-tenant and appropriate FKs; include `team_id` where access-control scoping is required.
- Define enums as string columns and validate via PHP enums in code.
- Add necessary unique constraints and indexes.
- Include `meta` JSON when future-proofing is needed (but avoid overuse).
- Provide factories and seeders for core tables.

## Acceptance for MVP database completeness

For Phase 1 to be considered complete, the following tables must exist with basic columns and constraints: events, ticket_types, registrations, registration_items, transactions, attendees, ticket_instances (or an equivalent issuance model), check_ins, registration_forms, registration_form_fields, attendee_responses, categories, event_category (pivot), tags, event_tag (pivot).

All subsequent phases can be delivered incrementally as additive migrations following the standards above.
