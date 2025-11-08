# UAT Test Scripts Generation Instructions

## 📋 Overview

This document provides comprehensive instructions for creating and organizing User Acceptance Testing (UAT) scripts. UAT scripts are organized by **user roles and scenarios** to ensure comprehensive coverage of all business workflows and use cases.

## 🔗 Coordination with doc.prompt.md

This UAT structure works **in conjunction** with the Documentation Management Agent (doc.prompt.md). Key points:

- **UAT location**: UAT tests should be placed in `docs/uat/` or `docs/NN-uat/` (where NN is assigned by doc agent)
- **Numbering flexibility**: While doc.prompt.md uses numbered folders at the top level (01-architecture, 02-development), UAT internally uses numbered role folders (01-admin, 02-user)
- **Cross-references**: When linking to other documentation, use the actual numbered paths created by doc.prompt.md
- **Consistency**: UAT test files follow kebab-case naming with prefix numbering, matching doc.prompt.md standards

**Example integrated structure**:
```
docs/
├── README.md
├── 01-architecture/
├── 02-development/
├── 03-deployment/
├── 04-scenarios/        # Business scenarios (if present)
└── 05-uat/             # UAT tests (number assigned by doc agent)
    ├── README.md
    ├── 01-admin/       # Role-based folders (UAT internal structure)
    ├── 02-user/
    └── _shared/
```

## 🏗️ Directory Structure: Role-Based Organization

**Note**: This structure should be placed within your project's `docs/` directory. The doc.prompt.md agent may organize it as `docs/05-uat/` or `docs/uat/` depending on documentation priorities. The internal structure below remains the same regardless of the parent folder naming.

```
docs/uat/ (or docs/05-uat/ if numbered by doc.prompt.md)
├── README.md (Master UAT Plan - index of all roles)
├── 01-role-name/
│   ├── README.md (index of UAT tests for this role)
│   ├── 01-SC-ROLE-001-feature-description.md
│   ├── 02-SC-ROLE-002-feature-description.md
│   ├── 03-SC-ROLE-003-feature-description.md
│   └── ...
├── 02-role-name/
│   ├── README.md
│   ├── 01-SC-ROLE-001-feature-description.md
│   ├── 02-SC-ROLE-002-feature-description.md
│   └── ...
├── 03-role-name/
│   ├── README.md
│   └── ...
└── _shared/
    ├── authentication-scenarios.md
    ├── integration-scenarios.md
    └── common-features.md
```

## 📝 File Naming Convention

### Format
```
NN-SC-ROLE-NNN-kebab-case-title.md
```

### Components

- **NN**: Sequential priority number (01-20, where 01 is highest priority)
- **SC**: Stands for "Scenario" (fixed prefix)
- **ROLE**: Role abbreviation (3-6 chars, uppercase)
  - Examples: ADMIN, USER, MGR, DEV, GUEST
- **NNN**: Use case sequence within role (001-999)
- **kebab-case-title**: Descriptive title (lowercase, hyphens)

### Examples

- `01-SC-ADMIN-001-user-management.md` - First critical scenario for Admin
- `02-SC-USER-001-profile-update.md` - First scenario for User
- `05-SC-DEV-002-api-integration.md` - Second scenario for Developer
- `12-SC-GUEST-001-registration.md` - Guest registration

## 🎯 Use Case ID Format

### Standard Format
```
SC-ROLE-FUNCTION-NUMBER
```

**Components:**
- **SC**: Scenario (fixed prefix)
- **ROLE**: Role abbreviation (3-6 uppercase chars)
- **FUNCTION**: Function/feature abbreviation (3-6 uppercase chars)
- **NUMBER**: Sequential number (001-999)

### Examples
- `SC-ADMIN-USER-001` (Admin - User management)
- `SC-DEV-API-001` (Developer - API integration)
- `SC-USER-PROF-001` (User - Profile management)
- `SC-MGR-RPT-001` (Manager - Reporting)

## 📋 UAT Test Template Requirements

Every UAT test script MUST include the following sections:

### 1. Header & Metadata
```markdown
# Use Case: [SC-ROLE-NNN] - Descriptive Title

## Business Objective
- Clear statement of what is being tested
- Expected business outcome
- User roles involved
- Business value delivered

## Prerequisites
- System state requirements
- User roles/permissions needed
- Test data prepared
- External system dependencies (if any)
```

### 2. Test Scenario Setup
```markdown
## Test Scenario: High-level description

### Test Case Metadata
- Use Case ID: SC-ROLE-NNN
- Priority: Critical/High/Medium/Low
- User Role: [Role name]
- Test Type: Functional/Integration/End-to-End
- Estimated Duration: X minutes
- Business Requirements: [Reference to requirements doc]
```

### 3. Step-by-Step Execution
```markdown
### Steps to Execute

1. **First Action**
   - Detailed sub-steps with verification
   - Expected state after step

2. **Second Action**
   - Continue with detailed procedures
   - Include UI navigation paths

[Continue 10-14 steps total]
```

### 4. Outcome Documentation
```markdown
### Expected Outcome
- Business result achieved
- System behavior verified
- Data state consistent
- User feedback messages

### Actual Outcome
- [ ] Pass / [ ] Fail
- Date Tested: [YYYY-MM-DD]
- Tested By: [Name/Role]
- Notes: [Any observations]
```

### 5. Business Rules & Acceptance Criteria
```markdown
## Business Rules Validated
- BR-XXX-001: [Rule description]
- BR-XXX-002: [Rule description]

### Acceptance Criteria Verification
- [ ] **AC1**: [Criteria description]
- [ ] **AC2**: [Criteria description]
[6-8 total criteria]
```

### 6. UX & Impact Assessment
```markdown
### User Experience Observations
[Observations about usability, clarity, performance, accessibility]

### Business Impact Assessment
- **Impact if Failed**: [Consequences]
- **Risk Level**: Critical/High/Medium
- **Mitigation**: [Preventive measures]
```

### 7. Edge Cases & Integration
```markdown
### Edge Cases and Alternative Flows

#### Edge Case 1: [Description]
- **Steps**: [How to trigger]
- **Expected**: [Expected outcome]
- **Actual**: [Test result placeholder]

#### Edge Case 2: [Description]
[Similar structure]

### Integration Points
- **Related Systems**: [List external systems]
- **Dependencies**: [Other UAT scenarios needed first]

### Automated Test Coverage
- **Feature Test**: [Path to test file]
- **Unit Test**: [Path to test file]

### Regression Notes
- Include in regression for: [What changes require re-testing]
```

### 8. Footer
```markdown
## Related Use Cases / Scenarios
- Link to prerequisite scenarios
- Link to dependent scenarios
- Link to related scenarios in other roles

---

**Test Case Version**: 1.0
**Last Updated**: [Date]
**Created By**: [Author]
```

## 🔄 Implementation Phases

### Phase 1: Critical Foundation (Week 1)
Priority: **CRITICAL** - Must complete before UAT sign-off

**Focus:**
- Core user authentication and authorization
- Primary business workflows
- Critical data operations
- System configuration essentials

**Target Test Scripts:** 8-10 critical scenarios
**Success Criteria:** All critical workflows testable

### Phase 2: High Priority (Week 2)
Priority: **HIGH** - Should complete for full feature coverage

**Focus:**
- Secondary business workflows
- User management and permissions
- Reporting and analytics
- Standard operations

**Target Test Scripts:** 6-8 high-priority scenarios
**Success Criteria:** All standard workflows tested

### Phase 3: Integration (Week 3)
Priority: **MEDIUM** - Integration and cross-system testing

**Focus:**
- External system integrations
- End-to-end business processes
- Data synchronization
- Third-party service interactions

**Target Test Scripts:** 6-8 integration scenarios
**Success Criteria:** All system integrations validated

### Phase 4: Edge Cases & Performance (Week 4)
Priority: **NICE-TO-HAVE** - Boundary conditions and performance

**Focus:**
- Error handling and validation failures
- Boundary conditions (limits, quotas, field lengths)
- Concurrent operations
- High-volume operations
- System recovery and resilience

**Target Test Scripts:** 6-8 edge case scenarios
**Success Criteria:** System handles edge cases gracefully

## 📊 Role-Based UAT Coverage Matrix

| Role | Critical | High | Medium | Total |
|------|----------|------|--------|-------|
| Role 1 | 2-3 | 2 | 2 | 6-7 |
| Role 2 | 3-4 | 3 | 2 | 8-9 |
| Role 3 | 1 | 2 | 2 | 5 |
| Role 4 | 1 | 2 | 2 | 5 |
| **Shared** | 2-3 | 2 | 2 | 6-7 |
| **TOTAL** | **10-15** | **11-15** | **10-14** | **31-44** |

*Adjust matrix based on your project's specific roles and requirements*

## ✅ Quality Standards for Each Test Script

Every UAT test script MUST meet these criteria:

### Content Requirements
- ✅ Clear business objective (1-3 sentences)
- ✅ All prerequisites explicitly listed
- ✅ 10-14 detailed, numbered execution steps
- ✅ Expected outcomes for each major step
- ✅ 4-6 business rules validated
- ✅ 6-8 acceptance criteria
- ✅ 3-4 edge cases documented
- ✅ Integration points identified
- ✅ Related test scripts cross-referenced

### Quality Metrics
- ✅ Minimum 250 lines of content
- ✅ No grammatical errors
- ✅ Consistent formatting (markdown standards)
- ✅ All links and references valid
- ✅ Screenshots/diagrams where helpful
- ✅ Clear pass/fail recording section

### Traceability
- ✅ Mapped to Business Requirements
- ✅ Mapped to User Scenarios
- ✅ References to automated tests (if exist)
- ✅ Cross-links to related UAT tests

## 🎯 Mapping: Scenarios → UAT Tests → Automated Tests

```
docs/
├── 03-scenarios/ (or scenarios/)
│   └── role-scenarios.md
│       └── Scenario 1.1: Feature Description
└── 05-uat/ (or uat/)
    └── 01-role-name/
        └── 01-SC-ROLE-001-feature.md
            └── References: tests/Feature/FeatureTest.php
```

**Note**: Actual folder numbering (01-, 02-, 03-) will be determined by the doc.prompt.md agent based on documentation priority and reading order.

## 📚 Writing Guidelines

### For Each Test Script

1. **Business Objective**
   - Write for non-technical stakeholders
   - Include business value, not technical details
   - Explain "why" we're testing this

2. **Prerequisites**
   - List every assumption
   - Specify required data
   - Include system state requirements
   - Test account credentials needed

3. **Execution Steps**
   - Number all steps (1-14)
   - Use active voice ("Click X", not "X should be clicked")
   - Include verification after each major action
   - Reference UI elements clearly (buttons, fields, menus)

4. **Expected Outcome**
   - State both business and system outcomes
   - Include data validations
   - Mention audit/logging expectations
   - Describe user feedback (messages, redirects)

5. **Edge Cases**
   - Describe boundary conditions
   - Include error scenarios
   - Test with invalid data
   - Consider timing issues

## 🔗 Cross-References

### Within Test Scripts
```markdown
# Example cross-references within UAT documentation
# (Adjust folder numbers based on actual doc.prompt.md structure)

- [SC-ROLE-001](../01-role-name/01-SC-ROLE-001-feature.md)
- Related: [SC-ROLE-002](./02-SC-ROLE-002-feature.md)
- Prerequisite: [SC-ADMIN-001](../02-admin/01-SC-ADMIN-001.md)
```

### To Scenarios
```markdown
# Link to scenario documentation (adjust folder number as needed)
Based on: [Role Scenarios](../../03-scenarios/role-scenarios.md#scenario-11-feature-name)
# Or if not numbered:
Based on: [Role Scenarios](../../scenarios/role-scenarios.md#scenario-11-feature-name)
```

### To Automated Tests
```markdown
# Tests remain in standard Laravel structure
Automated Test: `tests/Feature/FeatureTest.php::test_user_can_perform_action`
```

**Important**: The doc.prompt.md agent will organize docs/ with numbered folders. When creating UAT tests, check the actual documentation structure and adjust cross-reference paths accordingly.

## 📋 Checklist for New UAT Test Scripts

Before marking a test as complete:

- [ ] File created in correct directory with proper naming
- [ ] Use Case ID matches file name
- [ ] Business Objective is clear and measurable
- [ ] All prerequisites explicitly listed
- [ ] 10+ detailed execution steps
- [ ] Expected outcomes documented
- [ ] 4-6 business rules mapped
- [ ] 6-8 acceptance criteria defined
- [ ] 3-4 edge cases included
- [ ] Integration points identified
- [ ] Related tests cross-referenced
- [ ] Minimum 250 lines of content
- [ ] No markdown linting errors
- [ ] Pass/Fail recording section included
- [ ] Version and author information included

## 🚀 Getting Started

**Prerequisites**: Ensure your documentation structure is set up according to doc.prompt.md standards (numbered folders, kebab-case filenames).

1. **Identify roles**: Define all user roles in your system
2. **Create role directories**: Set up `docs/uat/NN-role-name/` folders (or `docs/05-uat/NN-role-name/` if numbered by doc agent)
3. **Map scenarios**: Review business scenarios for each role (check `docs/03-scenarios/` or similar)
4. **Create UAT tests**: Use template and naming convention
5. **Map to requirements**: Add references to requirements documents (check `docs/02-requirements/` or similar)
6. **Define edge cases**: Think about failure modes
7. **Document integration**: Identify system touchpoints
8. **Create role README**: Index all tests for that role
9. **Update master README**: Link all role directories

**Important**: When the doc.prompt.md agent reorganizes documentation, it may renumber folders. Update your cross-references accordingly.

## 📞 Resources Template

Customize these paths for your project (following doc.prompt.md numbered folder structure):

- **UAT Documentation**: `docs/05-uat/` or `docs/uat/`
- **Scenario Documentation**: `docs/03-scenarios/` or `docs/scenarios/`
- **Requirements**: `docs/02-requirements/` or `docs/requirements/`
- **Technical Documentation**: `docs/01-architecture/` (architecture, design patterns)
- **Development Guides**: `docs/02-development/` (setup, workflows, testing)
- **Deployment Guides**: `docs/03-deployment/` (environments, production)
- **API Documentation**: `docs/04-api/` (endpoints, authentication)
- **Automated Tests**: `tests/Feature/` and `tests/Unit/`
- **Feature Documentation**: Within relevant numbered context folders

**Note**: The doc.prompt.md agent will organize documentation using numbered folders (01-, 02-, 03-) with kebab-case filenames. Ensure your cross-references use the actual structure created by the documentation agent.

## 🎓 Best Practices

1. **Start with critical paths**: Focus on must-have functionality first
2. **One scenario per file**: Keep tests focused and maintainable
3. **Clear language**: Write for your audience (business users)
4. **Traceability**: Always link to requirements and scenarios
5. **Reproducibility**: Anyone should be able to execute your test
6. **Version control**: Track changes to test scripts
7. **Regular review**: Update tests as features evolve
8. **Automation mapping**: Link manual tests to automated tests

---

**Document Version**: 1.0
**Last Updated**: [Date]
**Maintained By**: [Team/Department]
