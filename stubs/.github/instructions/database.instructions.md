---
applyTo: '**'
---
Database Design Instructions

This document defines the database design principles, naming conventions, and baseline schema required to build the application.

## Core principles

- UUID everywhere: All primary keys are ULIDs/UUIDs provided by `App\Models\Base` (via Traitify InteractsWithUuid). No auto-increment IDs in public APIs.
- Soft deletes by default: Add `deleted_at` to all user-facing tables. Use Laravel's `SoftDeletes`.
- Ownership & authz: Enforce access via policies and Spatie Permissions; never rely only on foreign keys.
- Enums: Use string-backed PHP enums (Traitify contract). Store enum values as strings.
- Metadata: For extensibility, provide `meta` JSON where useful; avoid unbounded schemaless usage for core concepts.
- Auditing: Changes to sensitive tables are audited by `owen-it/laravel-auditing`.
- Media: Use Spatie Media Library for file assets; prefer secure URLs (Media Secure).
- Indexing: Add composite indexes for common filters. Add uniqueness where business rules require it.
- No raw queries in app code: Use Eloquent/Query Builder; see architecture tests.

## Standard columns

- id (uuid, pk)
- team_id (uuid, fk -> teams.id) when access-control or operational scoping is needed
- created_at, updated_at (timestampsTz)
- deleted_at (soft deletes)
- meta (json, nullable)

## Relationship overview (contract)

Use the following as the canonical contract:

- 1:M User -> AuditLog
- M:M Role <-> Permission via role_permissions
- M:M Team <-> User via team_user
- 1:M Team -> TeamSetting
- 1:M User -> ApiToken
Database Design Instructions

This document defines the database design principles, naming conventions, and baseline schema required to build the application.

 ## Core principles

- UUID everywhere: All primary keys are ULIDs/UUIDs provided by `App\Models\Base` (via Traitify InteractsWithUuid). No auto-increment IDs in public APIs.
- Soft deletes by default: Add `deleted_at` to all user-facing tables. Use Laravel's `SoftDeletes`.
- Ownership & authz: Enforce access via policies and Spatie Permissions; never rely only on foreign keys.
- Enums: Use string-backed PHP enums (Traitify contract). Store enum values as strings.
- Metadata: For extensibility, provide `meta` JSON where useful; avoid unbounded schemaless usage for core concepts.
- Auditing: Changes to sensitive tables are audited by `owen-it/laravel-auditing`.
- Media: Use Spatie Media Library for file assets; prefer secure URLs (Media Secure).
- Indexing: Add composite indexes for common filters. Add uniqueness where business rules require it.
- No raw queries in app code: Use Eloquent/Query Builder; see architecture tests.
- Ownership & authz: Enforce access via policies and Spatie Permissions; never rely only on foreign keys.
- Money is integer: Store prices/amounts in minor units (e.g., cents) as `bigInteger` to avoid float errors. Currency code (ISO 4217) as string(3).
- Enums: Use string-backed PHP enums (Traitify contract). Store enum values as strings.
- Metadata: For extensibility, provide `meta` JSON where useful; avoid unbounded schemaless usage for core concepts.
- Auditing: Changes to sensitive tables are audited by `owen-it/laravel-auditing`.
- Media: Use Spatie Media Library for file assets; prefer secure URLs (Media Secure).
- Indexing: Add composite indexes for common filters. Add uniqueness where business rules require it.
- No raw queries in app code: Use Eloquent/Query Builder; see architecture tests.

## Standard columns

- id (uuid, pk)
- team_id (uuid, fk -> teams.id) when access-control or operational scoping is needed
- created_at, updated_at (timestampsTz)
- deleted_at (soft deletes)
- meta (json, nullable)

## Relationship overview (contract)

Use the following as the canonical contract:

- 1:M User -> AuditLog
- M:M Role <-> Permission via role_permissions
- M:M Team <-> User via team_user
- 1:M Team -> TeamSetting
- 1:M User -> ApiToken

## Baseline schema

Below are the recommended baseline tables. Adopt names as specified to keep consistency.

### Platform foundation

Already present (baseline): users, permissions/roles (Spatie), audits, authentication_log, media, features (Pennant), jobs/cache, teams, team_user, telescope. Continue to ensure policies, indices, and auditing are enabled.

Recommended additions (guidance only):
- team_settings: id, team_id FK, key (string), value (json), unique(team_id, key)

### API Access

- api_tokens: id, user_id, name, token_hash, abilities (json), last_used_at, expires_at, unique(user_id, name)

## Foreign keys, indexes, and constraints

- Always add FKs with ON UPDATE CASCADE and ON DELETE RESTRICT (or CASCADE only when business rules demand cascading deletes). For soft-deleted parents, prefer RESTRICT and handle deletes at app level.
- Unique constraints:
	- api_tokens: unique(user_id, name)
- Performance indexes (examples):
	- teams: (created_at)
	- team_user: (team_id, user_id)

## Data types & storage guidance

- Date/time: Use timezone-aware columns (timestampsTz) and store UTC.
- JSON columns: Use for metadata, credentials, payloads; validate structure in app layer.
- Media: Store only IDs/relations to Spatie Media; avoid file paths in business tables.

## Migration authoring checklist

- Create table with UUID pk, timestampsTz, soft deletes when applicable.
- Add `team_id` where access-control scoping is required.
- Define enums as string columns and validate via PHP enums in code.
- Add necessary unique constraints and indexes.
- Include `meta` JSON when future-proofing is needed (but avoid overuse).
- Provide factories and seeders for core tables.

## Acceptance for baseline completeness

For the baseline to be considered complete, the following tables must exist with basic columns and constraints: users, teams, team_user, api_tokens, permissions, roles, audits, authentication_log, media.

All future features can be delivered incrementally as additive migrations following the standards above.
