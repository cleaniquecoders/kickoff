# Architecture

This section covers the internal architecture and design of the Kickoff package.

## Contents

1. [Overview](./01-overview.md) - High-level architecture and package structure
2. [Bootstrap Process](./02-bootstrap-process.md) - How the kickoff command works
3. [Stubs System](./03-stubs-system.md) - Template system and placeholder replacement
4. [Helper Functions](./04-helper-functions.md) - Utility functions and their purposes

## Key Concepts

### Package vs. Generated Projects

Kickoff consists of two separate codebases:

- **The Package** (`src/`, `support/`, `bin/kickoff`): Symfony Console application
- **The Stubs** (`stubs/`): Complete Laravel project template

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
- [CLAUDE.md](../../CLAUDE.md) - Complete architecture reference
