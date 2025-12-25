# Getting Started

Welcome! This section helps you install, configure, and run the project for the first time.

## Prerequisites

Before you begin, ensure you have:

- PHP 8.3+ (compatible with Laravel)
- Composer for PHP dependencies
- MySQL/MariaDB for database
- Node.js and npm for frontend assets
- Git for version control
- Docker (optional, for containerized setup)

## Quick Start Guide

Follow these guides in order for a smooth setup:

### 1. Installation

[Installation Guide](01-installation.md) - Complete installation process including:

- Cloning the repository
- Running the installation script
- Database creation and configuration
- Environment setup
- Dependency installation
- Supervisor configuration

### 2. Initial Setup

[Setup Guide](02-setup.md) - Configure your development environment:

- Environment variables
- Application keys
- Basic configuration
- Running migrations

### 3. Docker Setup (Optional)

[Docker Guide](03-docker.md) - Run the application with Docker:

- Docker Compose configuration
- Container management
- Service orchestration

## What's Next?

After completing the setup, proceed to the [Development](../02-development/README.md) section to start building features.

## Troubleshooting

Common issues and solutions:

- **Permission errors**: Ensure scripts have execute permissions (`chmod +x bin/*`)
- **Database connection**: Verify MySQL credentials in `.env`
- **Port conflicts**: Check if ports 3306, 80, or 443 are already in use
- **Composer/npm errors**: Ensure both are installed and up to date

## Additional Resources

- [Main Documentation](../README.md)
- [Development Workflows](../02-development/README.md)
- [Deployment Guide](../04-deployment/01-deployment.md)
