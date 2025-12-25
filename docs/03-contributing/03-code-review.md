# Code Review Guidelines

This guide explains our code review process and expectations for both reviewers
and contributors.

## For Contributors

### What to Expect

**Review Timeline**:

- Initial review: 1-7 days
- Follow-up reviews: 1-3 days
- May take longer for complex changes

**Review Feedback**:

- Questions about implementation
- Suggestions for improvements
- Requests for tests or documentation
- Approval or changes requested

### Responding to Reviews

**Be Professional**:

- Thank reviewers for their time
- Ask questions if feedback unclear
- Explain your reasoning politely
- Don't take feedback personally

**Address All Comments**:

- Implement requested changes
- Explain if you disagree (politely)
- Mark resolved conversations
- Request re-review when done

**Example Responses**:

```markdown
> Can you add tests for this feature?

Good point! I've added tests in abc123. Let me know if you'd like additional coverage.

> This seems complex, can you simplify?

I've refactored this to use the existing helper function instead. Much cleaner now!

> Why did you choose this approach?

I considered using X, but Y is better here because [reason]. Happy to
change if you prefer X.
```

## For Reviewers

### Review Checklist

**Code Quality**:

- [ ] Follows existing patterns and conventions
- [ ] Properly typed (parameters and returns)
- [ ] Well-organized and readable
- [ ] No unnecessary complexity
- [ ] Comments explain "why", not "what"

**Functionality**:

- [ ] Solves the stated problem
- [ ] No obvious bugs or edge cases
- [ ] Handles errors appropriately
- [ ] Works in sandbox testing

**Testing**:

- [ ] Tests included for new features
- [ ] Bug fixes have regression tests
- [ ] Tests are meaningful and thorough
- [ ] Sandbox validation performed

**Documentation**:

- [ ] README updated if needed
- [ ] CLAUDE.md updated for architecture changes
- [ ] Code comments for complex logic
- [ ] CHANGELOG.md updated

**Breaking Changes**:

- [ ] Documented in CHANGELOG
- [ ] Migration guide provided if needed
- [ ] Justified and necessary

### Providing Feedback

**Be Constructive**:

✅ Good:

```markdown
Consider using the existing `step()` helper here for consistency:
\`\`\`php
step('Description', function () {
    // operation
}, $output, $verbose);
\`\`\`
```

❌ Bad:

```markdown
This is wrong. Fix it.
```

**Be Specific**:

✅ Good:

```markdown
This method should have a return type hint:
\`\`\`php
private function getDatabaseName(): string
```

❌ Bad:

```markdown
Missing types.
```

**Explain Reasoning**:

✅ Good:

```markdown
Let's move this to a helper function - it's used in 3 places and would be
easier to maintain centrally.
```

❌ Bad:

```markdown
Make this a helper.
```

**Suggest, Don't Demand**:

✅ Good:

```markdown
Consider adding verbose logging here? It would help users debug issues.
```

❌ Bad:

```markdown
You must add logging here.
```

### Review Types

**Request Changes**:

Use when issues must be fixed before merge:

- Bugs or incorrect functionality
- Missing tests for new features
- Breaking changes without documentation
- Code style violations

**Comment**:

Use for suggestions and questions:

- Optimization ideas
- Alternative approaches
- Clarification questions
- Non-blocking improvements

**Approve**:

Use when PR is ready to merge:

- All checks pass
- No unresolved issues
- Quality standards met
- Documentation complete

## Common Review Scenarios

### New Feature

Verify:

1. Use case is clear and valuable
2. Implementation follows existing patterns
3. Tests cover happy path and edge cases
4. Documentation explains how to use it
5. Sandbox testing validates end-to-end
6. No breaking changes (or justified)

### Bug Fix

Verify:

1. Bug is clearly explained
2. Fix addresses root cause
3. Regression test prevents recurrence
4. No unintended side effects
5. Sandbox testing confirms fix

### Documentation Update

Verify:

1. Information is accurate
2. Formatting is correct
3. Links work properly
4. Examples are valid
5. Typos fixed

### Stubs Changes

Verify:

1. Changes benefit generated projects
2. Placeholders used correctly
3. No breaking changes for existing users
4. Sandbox testing shows changes work
5. Documentation updated if needed

## Review Priorities

### Critical Issues

Must be fixed before merge:

- Bugs or broken functionality
- Security vulnerabilities
- Breaking changes without migration path
- Missing required tests
- Code style violations

### Important Issues

Should be addressed:

- Missing documentation
- Unclear variable names
- Complex logic without comments
- Incomplete test coverage
- Performance concerns

### Nice-to-Have

Optional improvements:

- Code organization suggestions
- Alternative approaches
- Future enhancements
- Minor optimizations

## Approval Process

### Single Approval

Most PRs need one maintainer approval.

### Multiple Approvals

Major changes may need multiple reviews:

- Breaking changes
- Architecture changes
- New major features
- Security-sensitive code

## After Approval

### Merge Requirements

Before merging:

- [ ] All CI checks pass
- [ ] Required approvals received
- [ ] All conversations resolved
- [ ] No merge conflicts
- [ ] Branch is up to date

### Merge Methods

**Squash and Merge** (default):

- Use for most PRs
- Creates single commit
- Keeps history clean

**Merge Commit**:

- Use for complex PRs with logical commits
- Preserves individual commits
- Use sparingly

**Rebase and Merge**:

- Not typically used
- Only for simple, clean PRs

## Etiquette

### Contributor Etiquette

- ✅ Thank reviewers
- ✅ Be responsive to feedback
- ✅ Ask questions politely
- ✅ Explain your reasoning
- ❌ Argue unnecessarily
- ❌ Ignore feedback
- ❌ Be defensive

### Reviewer Etiquette

- ✅ Be respectful and constructive
- ✅ Explain your reasoning
- ✅ Acknowledge good work
- ✅ Respond promptly
- ❌ Be dismissive
- ❌ Leave vague feedback
- ❌ Delay reviews unnecessarily

## See Also

- [Getting Started](./01-getting-started.md)
- [Pull Request Process](./02-pull-request-process.md)
- [Code Style](../02-development/03-code-style.md)
