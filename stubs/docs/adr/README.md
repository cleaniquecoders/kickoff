# Architecture Decision Records

ADRs capture significant architectural choices that shape this project. One file per decision. Status moves Proposed → Accepted → Superseded (never edited in place once Accepted; instead, write a successor ADR).

| ID  | Title | Status |
|-----|-------|--------|
| 001 | _Example: Dual key (id + uuid) for public-facing identifiers_ | Proposed |

> Replace the example row with your first real ADR. Number files sequentially: `001-short-slug.md`, `002-…`, etc.

## When to write an ADR

Write one when a decision is:

- **Architectural** — affects how modules talk to each other, where state lives, or which abstraction is swappable.
- **Hard to reverse** — would cost more than a sprint to undo (schema choices, identity provider, money representation, audit strategy).
- **Cross-cutting** — touches multiple modules or teams.

Skip ADRs for routine code style, library upgrades, or single-file refactors.

## Template

```markdown
# ADR-NNN — Title

- **Status:** Proposed | Accepted | Superseded by ADR-XXX
- **Date:** YYYY-MM-DD
- **Deciders:** names / roles

## Context
Why we needed to decide. The forces at play.

## Decision
What we chose.

## Consequences
What changes as a result — positive and negative.

## Alternatives considered
What we evaluated and rejected, and why.
```

## Conventions

- One decision per file. If the scope grows, split into successor ADRs.
- Link related ADRs at the bottom (`See also: ADR-002, ADR-005`).
- Update this index table whenever you add or supersede an ADR.
- Reference ADRs from `CLAUDE.md` and code comments where they explain a non-obvious constraint (e.g. `// see ADR-004: cents-only money arithmetic`).
