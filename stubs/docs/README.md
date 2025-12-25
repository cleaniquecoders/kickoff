# Documentation

Welcome to the project documentation. This guide provides comprehensive information about installation, development, architecture, and deployment.

## Quick Navigation

### Getting Started

New to the project? Start here:

- [Installation Guide](01-getting-started/01-installation.md) - Set up your development environment
- [First Steps](01-getting-started/02-setup.md) - Initial configuration and basics
- [Docker Setup](01-getting-started/03-docker.md) - Run with Docker

### Development

Building features and working with the codebase:

- [Development Overview](02-development/README.md) - Development workflows and patterns
- [Database](02-development/01-database.md) - Database schema and migrations
- [Livewire Components](02-development/02-livewire.md) - Building interactive components
- [API Development](02-development/03-api.md) - Creating and documenting APIs
- [Artisan Commands](02-development/04-commands.md) - Custom commands
- [Datatable](02-development/05-datatable.md) - Working with datatables
- [Access Control](02-development/06-access-control.md) - Permissions and roles
- [File Uploads](02-development/07-upload-helper.md) - File handling utilities
- [Secure File Access](02-development/08-secure-file-access.md) - Protected file serving
- [Sidebar Navigation](02-development/09-sidebar.md) - Menu configuration
- [Toast Notifications](02-development/10-toast-notifications.md) - User notifications

### Architecture

Understanding the codebase structure and standards:

- [Code Quality](03-architecture/01-code-quality.md) - Linting, testing, and analysis standards

### Deployment

Getting your application to production:

- [Deployment Guide](04-deployment/01-deployment.md) - Production deployment process

## Documentation Structure

This documentation follows a progressive disclosure approach:

1. **01-getting-started/** - Installation and initial setup
2. **02-development/** - Day-to-day development workflows
3. **03-architecture/** - Code standards and patterns
4. **04-deployment/** - Production deployment

Each section builds upon previous knowledge, with clear cross-references where topics intersect.

## Contributing to Documentation

When adding or updating documentation:

1. Place files in the appropriate numbered folder
2. Use descriptive filenames with number prefixes (e.g., `01-topic.md`)
3. Update the relevant README.md table of contents
4. Run `markdownlint docs/**/*.md` to validate
5. Fix issues with `markdownlint --fix docs/**/*.md`

## Need Help?

- Check the README.md in each section for detailed context
- Follow cross-references to related topics
- Refer to code examples in each guide
