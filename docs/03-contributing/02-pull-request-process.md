# Pull Request Process

This guide explains how to submit and manage pull requests for Kickoff.

## Before Creating PR

### 1. Quality Checklist

Ensure all checks pass:

```bash
# Format code
composer lint

# Run static analysis
composer analyse

# Run tests
composer test

# Sandbox validation
bin/sandbox run
cd test-output/sandbox
# Verify changes work
cd ../..
bin/sandbox reset
```

### 2. Update Documentation

If your changes affect:

- Architecture → Update `docs/01-architecture/`
- Usage → Update `README.md`
- Development → Update `docs/02-development/`
- AI context → Update `CLAUDE.md`

### 3. Update Changelog

Add entry to `CHANGELOG.md` under `## [Unreleased]`:

```markdown
## [Unreleased]

### Added

- New feature description

### Fixed

- Bug fix description

### Changed

- Breaking change description
```

## Creating Pull Request

### 1. Push Changes

```bash
git push origin your-branch-name
```

### 2. Open PR on GitHub

1. Visit your fork on GitHub
2. Click "Compare & pull request"
3. Fill out PR template

### 3. PR Title Format

Use conventional commit format:

```text
feat: add support for custom environment variables
fix: resolve placeholder replacement in nested files
docs: improve architecture documentation
```

### 4. PR Description Template

```markdown
## Description

Brief description of changes and why they're needed.

## Type of Change

- [ ] Bug fix (non-breaking change fixing an issue)
- [ ] New feature (non-breaking change adding functionality)
- [ ] Breaking change (fix or feature causing existing functionality to change)
- [ ] Documentation update

## How Has This Been Tested?

Describe the tests you ran to verify your changes.

- [ ] Unit tests (`composer test`)
- [ ] Sandbox testing (`bin/sandbox run`)
- [ ] Manual testing in real Laravel project

## Checklist

- [ ] Code follows project style (`composer lint`)
- [ ] Static analysis passes (`composer analyse`)
- [ ] All tests pass (`composer test`)
- [ ] Sandbox validation successful
- [ ] Documentation updated if needed
- [ ] CHANGELOG.md updated
- [ ] No breaking changes (or documented if unavoidable)

## Screenshots (if applicable)

Add screenshots for UI changes or terminal output.

## Related Issues

Closes #123
Fixes #456
```

## After Submitting PR

### 1. CI Checks

Wait for automated checks to complete:

- ✅ Pint formatting
- ✅ PHPStan analysis
- ✅ Test suite
- ✅ Rector checks

If checks fail, fix issues and push again.

### 2. Code Review

Maintainers will review your PR. Be responsive to:

- Questions about implementation
- Requests for changes
- Suggestions for improvements

### 3. Address Feedback

If changes requested:

```bash
# Make changes
# Edit relevant files

# Commit
git add .
git commit -m "refactor: address review feedback"

# Push
git push origin your-branch-name
```

PR will automatically update.

## Review Process

### What Reviewers Look For

**Code Quality**:

- Follows existing patterns
- Properly typed
- Well-documented
- No unnecessary complexity

**Testing**:

- Tests included for new features
- Bug fixes have regression tests
- Sandbox testing performed

**Documentation**:

- README updated if needed
- CLAUDE.md updated for architecture changes
- Code comments for complex logic

**Breaking Changes**:

- Documented in CHANGELOG
- Migration guide provided
- Justification for breaking change

## Common Review Feedback

### "Please add tests"

Add tests to `tests/StartCommandTest.php`:

```php
public function test_your_new_feature()
{
    // Test implementation
}
```

### "Please run Pint"

```bash
composer lint
git add .
git commit -m "style: format code with pint"
git push
```

### "Please update documentation"

Update relevant docs and push:

```bash
# Edit docs
git add docs/
git commit -m "docs: document new feature"
git push
```

### "Please test with sandbox"

```bash
bin/sandbox run
# Verify feature works
# Take screenshots if needed
# Add results to PR comments
```

## Merging

### When PR is Approved

Maintainers will merge your PR when:

- All checks pass
- Code review approved
- No unresolved comments
- No merge conflicts

### After Merge

Your changes will be included in the next release.

Thank you for contributing!

## Tips for Success

### Keep PRs Focused

- One feature/fix per PR
- Smaller PRs get reviewed faster
- Easier to understand and test

### Communicate

- Explain your reasoning
- Ask questions if unclear
- Be open to feedback

### Be Patient

- Reviews take time
- Maintainers are volunteers
- Follow up politely if needed

## Troubleshooting

### CI Checks Failing

**Pint fails**:

```bash
composer lint
git add .
git commit -m "style: fix formatting"
git push
```

**PHPStan fails**:

```bash
composer analyse
# Fix reported issues
git add .
git commit -m "fix: resolve type errors"
git push
```

**Tests fail**:

```bash
composer test
# Fix failing tests
git add .
git commit -m "test: fix failing tests"
git push
```

### Merge Conflicts

```bash
# Update your branch
git fetch upstream
git rebase upstream/main

# Resolve conflicts
# Edit conflicting files

# Continue rebase
git add .
git rebase --continue

# Force push
git push origin your-branch-name --force
```

### Need Help?

- Comment on your PR with questions
- Tag maintainers if urgent
- Check existing PRs for examples

## Next Steps

- [Code Review Guidelines](03-code-review.md) - What to expect during review
- [Getting Started](01-getting-started.md) - Return to contribution setup
- [Code Style](../02-development/03-code-style.md) - Ensure code quality
