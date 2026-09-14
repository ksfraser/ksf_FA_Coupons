# ksf_FA_Coupons - Test Plan Document

**Document Version:** 1.0  
**Date:** May 13, 2026  
**Module:** FA_Coupons (FrontAccounting Coupon Management)  
**Status:** Approved

---

## 1. Introduction

### 1.1 Purpose

This test plan defines the comprehensive testing strategy for the FA_Coupons module, ensuring all functional requirements are verified through systematic test scenarios.

### 1.2 Scope

Testing covers:
- Coupon CRUD operations
- Discount validation and calculation
- Usage tracking
- Security and access control
- Integration with Sales module

### 1.3 Test Environment

| Component | Version |
|-----------|---------|
| FrontAccounting | 2.4+ |
| PHP | 7.3+ |
| MySQL | 5.7+ |
| Browser | Chrome 90+, Firefox 88+ |

---

## 2. Test Categories

### 2.1 Unit Testing

Individual component testing:
- Database access functions
- Validation logic
- Discount calculations

### 2.2 Integration Testing

Module interaction testing:
- FA hook integration
- CRM data linking
- Sales invoice integration

### 2.3 System Testing

End-to-end workflow testing:
- Complete coupon lifecycle
- Invoice with discount flow

---

## 3. Test Scenarios

### 3.1 Coupon Creation Tests

**TC-CPN-001: Create Percentage Coupon**
```
Test ID: TC-CPN-001
Feature: FR-CPN-001
Title: Create percentage discount coupon
Priority: Critical

Preconditions:
- Admin user authenticated
- SA_COUPONSCREATE permission granted

Test Steps:
1. Navigate to Coupon Management
2. Click "Create New Coupon"
3. Enter coupon code: "SUMMER20"
4. Select discount type: Percentage
5. Enter discount value: 20
6. Set valid_from: today
7. Set valid_to: 90 days from today
8. Set max_uses: 100
9. Click "Save"

Expected Results:
- Coupon created successfully
- Coupon appears in list
- Code "SUMMER20" is unique
- Discount type shows "Percentage"
- Status is "Active"

Pass Criteria: Coupon exists in database with all specified values
```

**TC-CPN-002: Create Fixed Amount Coupon**
```
Test ID: TC-CPN-002
Feature: FR-CPN-001
Title: Create fixed amount discount coupon
Priority: Critical

Test Steps:
1. Create coupon with:
   - Code: "FLAT25"
   - Type: Fixed
   - Value: 25.00
   - Valid dates: 30 days
   - Max uses: 50

Expected Results:
- Coupon created with type "Fixed"
- Discount value stored as 25.00

Pass Criteria: Coupon created with correct type and value
```

**TC-CPN-003: Duplicate Code Rejection**
```
Test ID: TC-CPN-003
Feature: FR-CPN-001
Title: Reject duplicate coupon codes
Priority: High

Test Steps:
1. Create coupon with code "TESTCODE"
2. Attempt to create another coupon with code "TESTCODE"

Expected Results:
- Second creation fails
- Error message: "Coupon code already exists"
- First coupon remains unchanged

Pass Criteria: Duplicate rejected with error V001
```

**TC-CPN-004: Invalid Date Range Rejection**
```
Test ID: TC-CPN-004
Feature: FR-CPN-001
Title: Reject invalid date ranges
Priority: High

Test Steps:
1. Create coupon with valid_from: tomorrow
2. Set valid_to: today (before valid_from)

Expected Results:
- Validation fails
- Error message: "End date must be after start date"

Pass Criteria: Error V002 displayed
```

**TC-CPN-005: Percentage Over 100 Rejection**
```
Test ID: TC-CPN-005
Feature: FR-CPN-001
Title: Reject percentage values over 100
Priority: High

Test Steps:
1. Select percentage discount type
2. Enter value: 150

Expected Results:
- Validation fails
- Error: "Percentage cannot exceed 100%"

Pass Criteria: Error V004 displayed
```

---

### 3.2 Coupon Editing Tests

**TC-CPN-006: Edit Discount Value**
```
Test ID: TC-CPN-006
Feature: FR-CPN-002
Title: Modify coupon discount value
Priority: High

Test Steps:
1. Select existing coupon
2. Click "Edit"
3. Change discount_value from 20 to 15
4. Save changes

Expected Results:
- Coupon updated with new value
- Existing usage records unchanged
- Change timestamp updated

Pass Criteria: New value persisted
```

**TC-CPN-007: Modify Validity Dates**
```
Test ID: TC-CPN-007
Feature: FR-CPN-002
Title: Extend coupon validity period
Priority: High

Test Steps:
1. Edit coupon with valid_to = tomorrow
2. Extend valid_to to 60 days from today
3. Save

Expected Results:
- New valid_to saved
- Coupon becomes valid for extended period

Pass Criteria: Extended date persisted
```

**TC-CPN-008: Prevent Max Uses Below Current Usage**
```
Test ID: TC-CPN-008
Feature: FR-CPN-002
Title: Prevent reducing max_uses below used_count
Priority: High

Preconditions:
- Coupon with max_uses = 50
- Coupon with used_count = 30

Test Steps:
1. Edit coupon
2. Set max_uses = 20 (below used_count)

Expected Results:
- Validation fails
- Error: "Cannot set max uses below current usage"

Pass Criteria: Error V003 displayed, change rejected
```

---

### 3.3 Coupon Deletion Tests

**TC-CPN-009: Delete Coupon Without Usage**
```
Test ID: TC-CPN-009
Feature: FR-CPN-003
Title: Delete unused coupon
Priority: High

Test Steps:
1. Select coupon with used_count = 0
2. Click "Delete"
3. Confirm deletion

Expected Results:
- Coupon removed from database
- Coupon no longer in list

Pass Criteria: Coupon deleted completely
```

**TC-CPN-010: Delete Coupon With Usage**
```
Test ID: TC-CPN-010
Feature: FR-CPN-003
Title: Delete coupon with existing usage
Priority: High

Preconditions:
- Coupon with used_count = 25

Test Steps:
1. Delete coupon with usage records
2. Confirm deletion

Expected Results:
- Coupon deleted
- All usage records deleted (CASCADE)

Pass Criteria: Coupon and usage records deleted
```

---

### 3.4 Coupon Validation Tests

**TC-CPN-011: Validate Valid Coupon**
```
Test ID: TC-CPN-011
Feature: FR-CPN-006
Title: Validate active, in-date coupon
Priority: Critical

Preconditions:
- Coupon exists with code "VALID123"
- Coupon is_active = 1
- valid_from = today
- valid_to = 30 days from today
- used_count = 5, max_uses = 50

Test Steps:
1. Enter code "VALID123"
2. Submit for validation

Expected Results:
- Validation returns success
- Coupon details returned
- No error codes

Pass Criteria: Valid result returned
```

**TC-CPN-012: Validate Non-Existent Code**
```
Test ID: TC-CPN-012
Feature: FR-CPN-006
Title: Validate non-existent coupon code
Priority: Critical

Test Steps:
1. Enter code "NOTEXIST"
2. Submit for validation

Expected Results:
- Validation fails
- Error code: COUPON001
- Message: "Coupon code not found"

Pass Criteria: COUPON001 error returned
```

**TC-CPN-013: Validate Expired Coupon**
```
Test ID: TC-CPN-013
Feature: FR-CPN-006
Title: Validate expired coupon
Priority: Critical

Preconditions:
- Coupon with valid_to = yesterday

Test Steps:
1. Enter expired coupon code
2. Submit for validation

Expected Results:
- Validation fails
- Error code: COUPON002
- Message: "This coupon has expired"

Pass Criteria: COUPON002 error returned
```

**TC-CPN-014: Validate Exhausted Coupon**
```
Test ID: TC-CPN-014
Feature: FR-CPN-006
Title: Validate coupon with exhausted usage
Priority: Critical

Preconditions:
- Coupon with used_count = 50, max_uses = 50

Test Steps:
1. Enter coupon code
2. Submit for validation

Expected Results:
- Validation fails
- Error code: COUPON003
- Message: "This coupon has reached its usage limit"

Pass Criteria: COUPON003 error returned
```

**TC-CPN-015: Validate Inactive Coupon**
```
Test ID: TC-CPN-015
Feature: FR-CPN-006
Title: Validate manually deactivated coupon
Priority: Critical

Preconditions:
- Coupon with is_active = 0

Test Steps:
1. Enter coupon code
2. Submit for validation

Expected Results:
- Validation fails
- Error code: COUPON004
- Message: "This coupon is not active"

Pass Criteria: COUPON004 error returned
```

---

### 3.5 Discount Calculation Tests

**TC-CPN-016: Calculate Percentage Discount**
```
Test ID: TC-CPN-016
Feature: FR-CPN-007
Title: Calculate percentage discount correctly
Priority: Critical

Test Data:
- Coupon: 20% off
- Order total: $150.00

Expected Calculation:
discount = 150.00 * (20 / 100) = $30.00

Pass Criteria: $30.00 returned
```

**TC-CPN-017: Calculate Fixed Discount**
```
Test ID: TC-CPN-017
Feature: FR-CPN-007
Title: Calculate fixed amount discount
Priority: Critical

Test Data:
- Coupon: $25 fixed discount
- Order total: $100.00

Expected Calculation:
discount = $25.00 (fixed amount)

Pass Criteria: $25.00 returned
```

**TC-CPN-018: Fixed Discount Exceeds Order**
```
Test ID: TC-CPN-018
Feature: FR-CPN-007
Title: Cap discount at order total
Priority: Critical

Test Data:
- Coupon: $50 fixed discount
- Order total: $30.00

Expected Calculation:
discount = min(50, 30) = $30.00

Pass Criteria: Discount capped at order total ($30.00)
```

**TC-CPN-019: Discount Rounding**
```
Test ID: TC-CPN-019
Feature: FR-CPN-007
Title: Round discount to 2 decimal places
Priority: Medium

Test Data:
- Coupon: 15% off
- Order total: $33.33

Expected Calculation:
discount = 33.33 * 0.15 = 4.9995 → rounded to $5.00

Pass Criteria: $5.00 returned (rounded)
```

---

### 3.6 Usage Tracking Tests

**TC-CPN-020: Record Coupon Usage**
```
Test ID: TC-CPN-020
Feature: FR-CPN-008
Title: Record usage on invoice completion
Priority: Critical

Test Steps:
1. Create invoice with line items
2. Apply valid coupon
3. Complete invoice
4. Check fa_coupon_usage table

Expected Results:
- Usage record created
- Usage includes coupon_id, discount_amount, timestamp
- Coupon used_count incremented

Pass Criteria: Usage recorded, counter updated
```

**TC-CPN-021: View Usage History**
```
Test ID: TC-CPN-021
Feature: FR-CPN-009
Title: Display usage history for coupon
Priority: Medium

Test Steps:
1. Navigate to coupon details
2. Click "Usage History"

Expected Results:
- List of all usages displayed
- Columns: Customer, Invoice, Amount, Date
- Sorted chronologically (newest first)

Pass Criteria: Complete history shown
```

**TC-CPN-022: Usage Statistics Calculation**
```
Test ID: TC-CPN-022
Feature: FR-CPN-010
Title: Calculate accurate usage statistics
Priority: Medium

Test Data:
- Coupon used 5 times
- Discounts: $10, $15, $20, $15, $10 = $70 total

Expected Results:
- Total redemptions: 5
- Total discount: $70.00
- Average discount: $14.00

Pass Criteria: Accurate calculations
```

---

### 3.7 Sales Integration Tests

**TC-CPN-023: Apply Coupon on Invoice**
```
Test ID: TC-CPN-023
Feature: FR-CPN-011
Title: Apply valid coupon during invoice creation
Priority: Critical

Test Steps:
1. Create new invoice
2. Add items totaling $100
3. Enter coupon code
4. Validate coupon
5. Complete invoice

Expected Results:
- Discount line item added
- Invoice total reduced by discount
- Usage recorded

Pass Criteria: Invoice shows discount, total adjusted
```

**TC-CPN-024: Reject Invalid Coupon on Invoice**
```
Test ID: TC-CPN-024
Feature: FR-CPN-011
Title: Prevent invalid coupon on invoice
Priority: Critical

Test Steps:
1. Create invoice
2. Enter invalid coupon code
3. Attempt to apply

Expected Results:
- Error message displayed
- No discount applied
- Invoice unchanged

Pass Criteria: Error shown, no modification
```

---

## 4. Test Data

### 4.1 Coupon Test Data

| Code | Type | Value | Valid From | Valid To | Max Uses |
|------|------|-------|------------|----------|----------|
| TEST10 | Percentage | 10 | 2026-01-01 | 2026-12-31 | 100 |
| FLAT50 | Fixed | 50.00 | 2026-05-01 | 2026-05-31 | 25 |
| EXPIRED | Percentage | 15 | 2025-01-01 | 2025-12-31 | 50 |
| EXHAUSTED | Percentage | 5 | 2026-01-01 | 2026-12-31 | 5 |
| INACTIVE | Fixed | 20.00 | 2026-01-01 | 2026-12-31 | 20 |

### 4.2 Order Test Data

| Order Total | Expected 10% Discount |
|-------------|----------------------|
| $100.00 | $10.00 |
| $150.50 | $15.05 |
| $99.99 | $10.00 |
| $1,000.00 | $100.00 |

---

## 5. Test Execution Schedule

### 5.1 Phase 1: Unit Testing
- **Duration:** 2 days
- **Scope:** Database functions, validation logic
- **Responsible:** Developer

### 5.2 Phase 2: Integration Testing
- **Duration:** 3 days
- **Scope:** FA hooks, CRM integration, Sales integration
- **Responsible:** QA Engineer

### 5.3 Phase 3: System Testing
- **Duration:** 2 days
- **Scope:** End-to-end workflows
- **Responsible:** QA Engineer

### 5.4 Phase 4: UAT
- **Duration:** 5 days
- **Scope:** User acceptance scenarios
- **Responsible:** Business Users

---

## 6. Pass Criteria

### 6.1 Test Completion Criteria

| Test Type | Minimum Pass Rate | Required |
|-----------|-------------------|----------|
| Unit Tests | 100% | Yes |
| Integration Tests | 100% | Yes |
| System Tests | 100% | Yes |
| UAT | 100% | Yes |

### 6.2 Definition of Done

- All test cases executed
- All critical bugs resolved
- No high-severity open issues
- UAT sign-off received
- Documentation updated

---

## 7. Risk Assessment

| Risk | Impact | Likelihood | Mitigation |
|------|--------|------------|-------------|
| CRM integration failure | High | Medium | Mock CRM data in testing |
| Database performance | Medium | Low | Index optimization |
| Browser compatibility | Medium | Low | Cross-browser testing |
| Data migration issues | High | Low | Backup and rollback plan |

---

## 8. Test Deliverables

1. Test cases (this document)
2. Test execution report
3. Bug reports
4. UAT sign-off
5. Test coverage report