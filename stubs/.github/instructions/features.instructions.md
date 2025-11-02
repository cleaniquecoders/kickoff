---
applyTo: '**'
---
Temula Features Instructions

This guide defines Temula’s feature set and responsibilities from five perspectives—Organizer (Organization), Team Member, Vendor, Participant, and System Owner—plus how features are gated by subscription plans and implemented safely (Pennant, Policies, and Quotas). It complements `database.instructions.md` and `billing-and-subscriptions.instructions.md`.

## Ground rules (cross-cutting)

- Tenancy: Organizer = Organization (`organization_id`). Teams belong to an Organization. Events belong to an Organization. Events may have zero or many Teams via `event_team` pivot.
- Access control: Use Spatie Permissions (roles/policies). Prefer policy checks per action. Include `team_id` on actions where “acting via team” matters (e.g., check-ins).
- Feature gating: Use Laravel Pennant to turn premium features on/off per Organization, resolved from the active subscription plan.
- Quotas: Enforce limits via a centralized Quota service; block or warn with upgrade CTA.
- UUIDs & soft deletes: All user-facing tables use UUIDs and soft deletes (see database doc).
- Money and gateways: Amounts in minor units (bigint). Gateways: platform defaults for all; org/event credentials Enterprise-only.
- No raw SQL or debug helpers in app code. Use Eloquent/Query Builder. Follow architecture tests.

## Perspectives and capabilities

### Organizer (Organization)

Core
- Manage organization profile and branding (org-level; Pro/Enterprise for advanced branding).
- Create and manage events (any type: webinar, training, class, sports, conference, hybrid).
- Assign teams to events (zero or many) and invite members with roles.
- Configure event settings: visibility, sales windows, capacity, categories/tags.

Tickets & Registration
- Define ticket types, quotas, pricing, sale windows; enable/disable coupons (Pro+).
- Configure registration forms (fields, validation, order) and required consents.
- View registrations, revenue, refunds; download exports (attendees, sales, tickets).

Payments
- Use platform-level payment gateways by default.
- Enterprise-only: add organization/event-specific gateway credentials; fallback to platform default when missing.

Admission
- Generate tickets with QR; manage check-in policies; enable per-session attendance (future).

Premium operations (Pro/Enterprise)
- Certifications: templates, rules, issuance, verification URLs.
- Kits: items, allocations, handover logs.
- Analytics: dashboards (sales, attendance, engagement) and CSV/Excel.

Engagement & hardware (Enterprise)
- Live Q&A moderation, polls, lucky draw, announcements.
- RFID checkpoints and device management.

APIs & webhooks (Enterprise)
- API tokens, scopes; webhook endpoints; event/webhook logs.

Gamification (Enterprise; optional Pro beta)
- Participant profiles, badges, points, leaderboards (org and industry scopes).

### Team Member (per event, via Organization)

Roles (suggested; enforce via policies and Spatie roles)
- Owner/Manager: full event management within organization scope.
- Finance: view orders/transactions, manage refunds, exports (no destructive event actions).
- Crew/Check-in: perform check-ins (QR/RFID/manual), view attendee info.
- Moderator: manage Q&A, polls, announcements; ban/silence when needed.

Capabilities
- Operate only on assigned events; actions recorded with `by_user_id` and optional `by_team_id`.
- Use check-in terminal; see real-time status; limited scope to assigned events.
- Upload/download media as allowed by policy (tickets, certificates); secure URLs only.

### Vendor (Exhibitor/Service Partner)

Two vendor modes (feature-flagged)
- Exhibitor/Vendor at an event (Enterprise):
	- Access vendor portal for assigned events; manage booth staff; view schedules.
	- Lead capture (QR scans) and exports (if enabled); limited analytics for booth.
	- Upload booth branding assets (subject to moderation).
- Service partner (Enterprise):
	- Integration via Partner System + Webhooks; receives scoped events (e.g., streaming provider, RFID operator).
	- Device registration/health (RFID), delivery confirmations (webhooks).

All vendor features are opt-in per event and gated by plan and policies.

### Participant (Attendee)

Core
- Discover public events; register and purchase tickets; receive email confirmations.
- Access tickets (download PDF, view QR); manage profile and consents.
- Check-in at venue (QR/RFID/manual by staff); view attendance status.

Post-event
- Download certificates (if issued); verify authenticity.
- View kit pickup status; receive notifications; participate in surveys.

Engagement (Enterprise)
- Submit questions, vote in polls, join lucky draws; receive announcements.

Gamification (Enterprise; optional Pro beta)
- Earn points and badges; appear on leaderboards (org/industry/global per settings).

### System Owner (Platform Admin)

Operations
- Manage subscription plans and features; set quotas; adjust plan flags.
- Seed and manage platform-level gateway configs; rotate credentials.
- Monitor queues (Horizon) and application insights (Telescope); audit logs.
- Manage organizations and impersonate for support.

Compliance & safety
- Enforce architecture rules (no raw DB/url/debug in app); run static analysis and tests.
- Monitor webhook delivery health and payment callback logs.

## Feature catalog by domain

Events
- Creation wizard, duplication, categories/tags, visibility, scheduling, presets per event type.

Tickets & Registration
- Ticket types, quotas, discounts (Pro+), form builder, order/registration records, exports.

Payments
- Platform gateways (default). Enterprise: org/event gateway configs; refunds and reconciliation.

Admission
- Ticket PDFs with QR, check-in terminal, real-time updates; RFID (Enterprise).

Communications
- Email notifications (all plans); email campaigns v1 (Pro+); SES-ready.

Premium Ops (Pro/Enterprise)
- Certifications, kit handover, advanced analytics, white-label.

Engagement & Hardware (Enterprise)
- Q&A, polls, announcements, lucky draw, RFID checkpoints/devices.

APIs & Webhooks (Enterprise)
- API tokens/scopes, webhook endpoints, delivery logs.

Gamification (Enterprise; optional Pro beta)
- Profiles, points, badges, leaderboards.

## Feature flags (Pennant) and plan mapping

- org_gateway (Enterprise)
- email_campaigns (Pro, Enterprise)
- certifications (Pro, Enterprise)
- kit_handover (Pro, Enterprise)
- white_label (Pro, Enterprise)
- analytics_advanced (Pro, Enterprise)
- rfid (Enterprise)
- qa_live (Enterprise)
- polls (Enterprise)
- lucky_draw (Enterprise)
- api_access (Enterprise)
- webhooks (Enterprise)
- gamification (Enterprise; optional Pro beta)
- sms_notifications (Enterprise; add-on)
- multi_gateway (Enterprise)

Use `Feature::for($organization)->active('flag')` or helper wrappers. Guard at policy/middleware and UI levels.

## Quotas & UX enforcement

Recommended `subscription_plans.features` JSON keys: `limit_active_events`, `limit_attendees_per_event`, `limit_collaborators_per_event`, `limit_email_campaigns_per_month`, `limit_api_rate_rpm`, `limit_webhook_endpoints`, `limit_branding_themes`.

Patterns
- Policy/middleware: `EnsureFeature:flag`, `EnsureQuota:key`.
- UI: disable/hide action, show upgrade CTA with plan comparison.
- Services: central `Quota` and `Authorization` helpers.

## Safety & quality gates

- Validation via Form Requests; consistent exception handling with custom exceptions.
- Auditing for sensitive changes; Authentication Log for security events; Impersonation for support.
- Architecture tests ban `dd`, `dump`, `ray`, raw DB, and `url()`; `env()` only in config.
- Static analysis (Larastan), formatting (Pint), tests (Pest), refactors (Rector).

## Acceptance checkpoints by phase (summary)

- Phase 1 (MVP): Create event → define tickets → register + pay (platform gateway) → issue PDF+QR → check-in → exports; policies/flags enforced.
- Phase 2: Event-team membership and role-restricted UIs; check-in terminal; moderation roles.
- Phase 3: Certificates issued on attendance; kit handover logs; analytics dashboards; email campaigns (Pro+).
- Phase 4: Live Q&A/polls/lucky draw; RFID reads; multi-gateway support (Enterprise).
- Phase 5: Public API + webhooks; delivery reliability and signing.
- Phase 6: Gamification primitives and leaderboards.

## Appendix: Feature-to-Table mapping

- Events and Teams
	- Events: events
	- Event ↔ Team assignment: event_team (pivot), teams, team_user, event_members, team_settings
	- Taxonomy: categories, event_category (pivot), tags, event_tag (pivot)
	- Branding: organization_branding, team_branding, event_branding

- Tickets & Registration
	- Ticket setup: ticket_types
	- Orders: registrations, registration_items
	- Attendees: attendees
	- Ticket issuance: ticket_instances
	- Custom forms: registration_forms, registration_form_fields, attendee_responses
	- Discounts: coupons, registration_discounts

- Payments & Financials
	- Transactions: transactions
	- Gateways catalog: payment_gateways
	- Platform credentials: platform_gateway_configs
	- Org/Event credentials (Enterprise): payment_gateway_configs

- Admission & Attendance
	- Check-ins: check_ins

- Communications
	- Campaigns: email_campaigns
	- Messages: email_messages
	- Bounces: email_bounces

- Premium Operations
	- Certifications: certificate_templates, certificate_issues
	- Kits & handover: kit_items, kit_allocations, kit_handover_logs
	- Analytics snapshots: event_metrics

- Engagement & In-Event Features (Enterprise)
	- Q&A: qa_questions, qa_votes
	- Polls: polls, poll_options, poll_votes
	- Announcements: announcements
	- Lucky draw: draw_pools, draw_entries, draw_results
	- RFID: rfid_checkpoints, rfid_devices, rfid_reads

- APIs & Webhooks (Enterprise)
	- API tokens: api_tokens
	- Partners: partner_systems
	- Webhooks: webhook_endpoints, webhook_events, webhook_deliveries

- Gamification (Enterprise; optional Pro beta)
	- Profiles: profiles
	- Badges: badges, attendee_badges
	- Points: points_ledger
	- Leaderboards: leaderboards, leaderboard_entries
	- Industry taxonomy: industry_categories

- Billing & Subscriptions
	- Plans: subscription_plans
	- Org subscriptions: organization_subscriptions
	- Invoices: subscription_invoices
	- Payments: subscription_payments

## References

- Database: `/.github/instructions/database.instructions.md`
- Billing: `/.github/instructions/billing-and-subscriptions.instructions.md`
- Project: `/.github/copilot-instructions.md`
