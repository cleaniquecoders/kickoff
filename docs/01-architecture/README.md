# Architecture

This section covers the internal architecture and design of the Kickoff package.

## Overview

Kickoff consists of two separate codebases:

- **The Package** (`src/`, `support/`, `bin/kickoff`): Symfony Console application that orchestrates the bootstrap process
- **The Stubs** (`stubs/`): Complete Laravel project template that gets copied to target projects

## Table of Contents

### [1. Overview](01-overview.md)

High-level architecture, package structure, and core components.

### [2. Bootstrap Process](02-bootstrap-process.md)

How the `kickoff start` command works, step-by-step execution flow.

### [3. Stubs System](03-stubs-system.md)

Template system, placeholder replacement, and stub structure.

### [4. Helper Functions](04-helper-functions.md)

CLI utility functions: `step()`, `runCommand()`, `copyRecursively()`, and more.

## Key Concepts

### Placeholder System

Kickoff uses a placeholder replacement system:

- `${PROJECT_NAME}`: Replaced with project name
- `${OWNER}`: Replaced with project owner

### Command Flow

```text
kickoff start owner name [path]
    ↓
Validate Laravel project
    ↓
Copy stubs to project
    ↓
Update composer.json
    ↓
Replace placeholders
    ↓
Install packages
    ↓
Run setup tasks
```

## Related Documentation

- [Development Guide](../02-development/README.md)
- [Contributing](../03-contributing/README.md)
- [CLAUDE.md](../../CLAUDE.md) - Complete architecture reference
