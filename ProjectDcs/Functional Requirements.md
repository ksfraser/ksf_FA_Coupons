# ksf_FA_Coupons - Functional Requirements Document

**Document Version:** 1.0  
**Date:** May 13, 2026  
**Module:** FA_Coupons (FrontAccounting Coupon Management)  
**Status:** Approved

---

## 1. Introduction

### 1.1 Purpose

This document details the functional requirements for the FA_Coupons module, providing precise specifications for all features, behaviors, and acceptance criteria.

### 1.2 Scope

Functional requirements cover:
- Coupon CRUD operations
- Discount calculation
- Validation logic
- Usage tracking
- Integration with FrontAccounting Sales

---

## 2. Functional Requirements

### 2.1 Coupon Management

#### FR-CPN-001: Create Coupon

**Description:** Create a new coupon with all required parameters.

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| coupon_code | VARCHAR(30) | Yes | Unique, alphanumeric, 3-30 chars |
| discount_type | ENUM | Yes | 'Percentage' or 'Fixed' |
| discount_value | DECIMAL(15,2) | Yes | > 0 |
| valid_from | DATE | No | Valid date |
| valid_to | DATE | No | >= valid_from if provided |
| max_uses | INT | No | >= 0, 0 = unlimited |

**Business Rules:**
- Coupon code must be unique across all coupons
- If valid_to is specified, valid_from must also be specified
- Discount value for percentage type must be <= 100

**Acceptance Criteria:**
- [ ] Admin can create percentage discount coupon
- [ ] Admin can create fixed amount discount coupon
- [ ] System rejects duplicate coupon codes
- [ ] System validates all required fields
- [ ] Created coupon is immediately available

---

#### FR-CPN-002: Edit Coupon

**Description:** Modify existing coupon parameters.

**Editable Fields:**
- discount_value
- valid_from
- valid_to
- max_uses
- is_active

**Non-Editable Fields:**
- coupon_code (once created)
- discount_type (once created)

**Business Rules:**
- Cannot set max_uses below current used_count
- Cannot set valid_to before valid_from
- Editing to inactive does not affect existing usage records

**Acceptance Criteria:**
- [ ] Admin can modify discount value
- [ ] Admin can adjust validity dates
- [ ] Admin can change max_uses
- [ ] Admin can activate/deactivate coupon
- [ ] System prevents editing coupon_code

---

#### FR-CPN-003: Delete Coupon

**Description:** Permanently remove a coupon from the system.

**Business Rules:**
- Coupon with existing usage can be deleted
- Usage records are deleted via CASCADE
- Deletion is irreversible

**Acceptance Criteria:**
- [ ] Admin can delete coupon with no usage
- [ ] Admin can delete coupon with existing usage
- [ ] Usage records are removed with coupon
- [ ] Deleted coupon cannot be recovered

---

#### FR-CPN-004: View Coupon List

**Description:** Display all coupons with filtering and sorting options.

**Default View Columns:**
- Coupon Code
- Discount Type
- Discount Value
- Valid From
- Valid To
- Max Uses
- Used Count
- Status (Active/Inactive)

**Filter Options:**
- Status (Active, Inactive, All)
- Discount Type (Percentage, Fixed, All)
- Date Range

**Sorting Options:**
- Code (A-Z, Z-A)
- Created Date (Newest, Oldest)
- Usage Count (High to Low, Low to High)

**Acceptance Criteria:**
- [ ] System displays all coupons in paginated list
- [ ] Admin can filter by status
- [ ] Admin can filter by discount type
- [ ] Admin can sort by any column
- [ ] Pagination shows 20 coupons per page

---

#### FR-CPN-005: View Coupon Details

**Description:** Display detailed information for a single coupon.

**Displayed Information:**
- All coupon fields
- Usage statistics (total used, total discount given)
- Recent usage history (last 10 redemptions)

**Acceptance Criteria:**
- [ ] Admin can view full coupon details
- [ ] Usage statistics are accurate
- [ ] Recent history shows correct records

---

### 2.2 Discount Validation

#### FR-CPN-006: Validate Coupon Code

**Description:** Verify coupon code exists and is currently valid.

**Validation Checks:**
1. Coupon code exists in database
2. Coupon is_active = 1
3. Current date >= valid_from (if set)
4. Current date <= valid_to (if set)
5. used_count < max_uses (if max_uses > 0)

**Response:**
- Valid: Return coupon details
- Invalid: Return specific error code and message

**Acceptance Criteria:**
- [ ] Valid coupon returns success
- [ ] Invalid code returns "COUPON001"
- [ ] Expired coupon returns "COUPON002"
- [ ] Exhausted coupon returns "COUPON003"
- [ ] Inactive coupon returns "COUPON004"

---

#### FR-CPN-007: Calculate Discount Amount

**Description:** Compute the discount amount for a given order total.

**Calculation Rules:**

For Percentage Discount:
```
discount = order_total * (discount_value / 100)
```

For Fixed Discount:
```
discount = min(discount_value, order_total)
```

**Business Rules:**
- Discount cannot exceed order total
- Minimum discount amount is $0.01 (for percentage > 0)

**Acceptance Criteria:**
- [ ] Percentage discount calculates correctly
- [ ] Fixed discount calculates correctly
- [ ] Discount never exceeds order total
- [ ] Discount is rounded to 2 decimal places

---

### 2.3 Usage Tracking

#### FR-CPN-008: Record Coupon Usage

**Description:** Log each coupon redemption with full details.

**Logged Data:**
- coupon_id
- customer_person_id (if available)
- invoice_id
- discount_amount (actual amount applied)
- used_at (timestamp)

**Business Rules:**
- Usage is recorded AFTER successful invoice posting
- discount_amount reflects actual discount applied
- used_count in fa_coupons is incremented

**Acceptance Criteria:**
- [ ] System records usage on invoice completion
- [ ] Usage includes all required fields
- [ ] used_count is updated atomically
- [ ] Usage timestamp is accurate

---

#### FR-CPN-009: View Usage History

**Description:** View complete usage history for a coupon.

**Display Columns:**
- Customer Name
- Invoice Number
- Discount Amount
- Used Date/Time

**Filter Options:**
- Date Range
- Customer

**Acceptance Criteria:**
- [ ] All usages are displayed in chronological order
- [ ] Filters work correctly
- [ ] Export to CSV is available

---

#### FR-CPN-010: Usage Statistics

**Description:** Generate statistics for coupon usage.

**Statistics:**
- Total redemptions
- Total discount amount
- Average discount per use
- Most used coupon
- Most recent usage

**Acceptance Criteria:**
- [ ] Statistics are accurate
- [ ] Can filter by date range
- [ ] Can filter by coupon

---

### 2.4 Integration Requirements

#### FR-CPN-011: Sales Integration

**Description:** Apply coupon discount during invoice creation.

**Integration Points:**
1. Invoice entry screen - coupon code input
2. Before finalizing - validate coupon
3. After adding all items - apply discount
4. Invoice display - show discount line item

**Acceptance Criteria:**
- [ ] Coupon field appears on invoice entry
- [ ] Discount appears as separate line item
- [ ] Invoice total reflects discount
- [ ] Negative line items are formatted correctly

---

#### FR-CPN-012: CRM Integration

**Description:** Link coupon usage to customer records.

**Integration:**
- customer_person_id in usage records
- Link to debtor via CRM module

**Acceptance Criteria:**
- [ ] Usage records include customer reference
- [ ] Customer can view their coupon history
- [ ] Marketing can segment by coupon usage

---

## 3. User Interface Requirements

### 3.1 Coupon List Page

**URL:** `/modules/fa_coupons/coupons.php`

**Components:**
- Header with "Coupon Management" title
- Filter bar with status, type, date filters
- Data table with sortable columns
- Pagination controls
- "Create New Coupon" button

**States:**
- Empty state: No coupons message
- Loading state: Spinner
- Error state: Error message with retry

### 3.2 Create/Edit Coupon Form

**Fields:**
- Coupon Code (text input, required)
- Discount Type (radio buttons)
- Discount Value (number input, required)
- Valid From (date picker, optional)
- Valid To (date picker, optional)
- Max Uses (number input, default 0)

**Buttons:**
- Save (primary)
- Cancel (secondary)

**Validation:**
- Client-side validation on submit
- Server-side validation on process

---

## 4. Data Validation Rules

### 4.1 Input Validation

| Field | Rules |
|-------|-------|
| coupon_code | 3-30 alphanumeric characters, unique |
| discount_value | Positive number, <= 100 for percentage |
| valid_from | Valid date, not in past (optional) |
| valid_to | Valid date, >= valid_from (optional) |
| max_uses | Non-negative integer |

### 4.2 Business Validation

| Rule | Error Code | Message |
|------|------------|---------|
| Code already exists | V001 | Coupon code already exists |
| Invalid date range | V002 | End date must be after start date |
| Max uses below used | V003 | Cannot set max uses below current usage |
| Percentage > 100 | V004 | Percentage cannot exceed 100% |

---

## 5. Non-Functional Requirements

### 5.1 Performance

| Metric | Target |
|--------|--------|
| Coupon validation | < 200ms |
| List page load | < 1s |
| Create/Edit operation | < 500ms |

### 5.2 Scalability

- Support up to 100,000 coupons
- Support up to 1,000,000 usage records
- Handle 100 concurrent users

### 5.3 Availability

- Module operates during standard business hours
- Graceful degradation if CRM unavailable

---

## 6. Requirements Traceability

| Requirement ID | Source | Priority | Status |
|----------------|--------|----------|--------|
| FR-CPN-001 | BR-001 | Critical | Pending |
| FR-CPN-002 | BR-002 | High | Pending |
| FR-CPN-003 | BR-003 | High | Pending |
| FR-CPN-004 | BR-004 | High | Pending |
| FR-CPN-005 | BR-005 | Medium | Pending |
| FR-CPN-006 | BR-006 | Critical | Pending |
| FR-CPN-007 | BR-007 | Critical | Pending |
| FR-CPN-008 | BR-008 | High | Pending |
| FR-CPN-009 | BR-009 | Medium | Pending |
| FR-CPN-010 | BR-010 | Medium | Pending |
| FR-CPN-011 | BR-011 | Critical | Pending |
| FR-CPN-012 | BR-012 | High | Pending |

---

## 7. Dependencies

### 7.1 External Dependencies

| System | Interface | Purpose |
|--------|-----------|---------|
| FrontAccounting Core | DB, UI, Hooks | Platform foundation |
| ksf_FA_CRM | Data Access | Customer/person data |

### 7.2 Internal Dependencies

| Module | Dependency Type |
|--------|-----------------|
| FA_Coupons | Self-contained |
| FA_CRM | Customer linking |

---

## 8. Assumptions

1. Users have basic computer literacy
2. Internet connection is available (for updates)
3. MySQL database is properly backed up
4. FrontAccounting 2.4+ is installed and functioning

---

## 9. Constraints

1. Must use FrontAccounting TB_PREF prefix convention
2. Must follow FA security model
3. Must use FA UI components and themes
4. PHP 7.3 minimum compatibility