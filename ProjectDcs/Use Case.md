# ksf_FA_Coupons - Use Case Document

**Document Version:** 1.0  
**Date:** May 13, 2026  
**Module:** FA_Coupons (FrontAccounting Coupon Management)  
**Status:** Approved

---

## 1. Introduction

### 1.1 Purpose

This document captures all use cases for the FA_Coupons module, detailing actors, preconditions, postconditions, and step-by-step flows for each scenario.

### 1.2 Actor Definitions

| Actor | Description | Access Level |
|-------|-------------|--------------|
| Marketing Manager | Creates and manages promotional coupons | SA_COUPONSCREATE |
| Sales Representative | Applies coupons during invoice creation | SA_COUPONSVIEW |
| Finance Manager | Reviews usage reports and statistics | SA_COUPONSVIEW |
| System Administrator | Full access to all coupon functions | SA_COUPONSMAINTENANCE |

---

## 2. Use Case Catalog

### 2.1 Coupon Management Use Cases

| Use Case ID | Use Case Name |
|-------------|---------------|
| UC-CPN-001 | Create New Coupon |
| UC-CPN-002 | Edit Coupon |
| UC-CPN-003 | Delete Coupon |
| UC-CPN-004 | View Coupon List |
| UC-CPN-005 | View Coupon Details |
| UC-CPN-006 | Activate/Deactivate Coupon |

### 2.2 Validation Use Cases

| Use Case ID | Use Case Name |
|-------------|---------------|
| UC-CPN-007 | Validate Coupon Code |
| UC-CPN-008 | Calculate Discount |

### 2.3 Usage Tracking Use Cases

| Use Case ID | Use Case Name |
|-------------|---------------|
| UC-CPN-009 | Record Coupon Usage |
| UC-CPN-010 | View Usage History |
| UC-CPN-011 | View Usage Statistics |

### 2.4 Integration Use Cases

| Use Case ID | Use Case Name |
|-------------|---------------|
| UC-CPN-012 | Apply Coupon to Invoice |
| UC-CPN-013 | Link Usage to Customer |

---

## 3. Detailed Use Cases

---

### UC-CPN-001: Create New Coupon

**Primary Actor:** Marketing Manager  
**Secondary Actors:** System  
**Trigger:** Marketing Manager clicks "Create Coupon" button  

**Preconditions:**
1. User is authenticated
2. User has SA_COUPONSCREATE permission
3. System displays Coupon Management page

**Postconditions (Success):**
1. New coupon is created in database
2. Coupon appears in list with default active status
3. Success message displayed

**Postconditions (Failure):**
1. No coupon created
2. Error message displayed with details
3. Form remains open for correction

**Basic Flow:**
```
1. User clicks "Create New Coupon" button
2. System displays coupon creation form
3. User enters coupon code
4. User selects discount type (Percentage/Fixed)
5. User enters discount value
6. User optionally sets validity dates
7. User optionally sets max uses
8. User clicks "Save"
9. System validates all inputs
10. System checks coupon code uniqueness
11. System inserts coupon into database
12. System displays success message
13. System refreshes coupon list
```

**Alternative Flows:**

A1: Duplicate Coupon Code
```
9a. User enters existing code
9b. System detects duplicate
9c. System displays error: "Coupon code already exists"
9d. User enters new code
9e. Continue from step 9
```

A2: Invalid Date Range
```
6a. User sets valid_from and valid_to
6b. valid_to < valid_from
6c. System displays error: "End date must be after start date"
6d. User corrects dates
6e. Continue from step 8
```

A3: Percentage Over 100
```
5a. User selects Percentage type
5b. User enters value > 100
5c. System displays error: "Percentage cannot exceed 100%"
5d. User corrects value
5e. Continue from step 8
```

---

### UC-CPN-002: Edit Coupon

**Primary Actor:** Marketing Manager, System Administrator  
**Secondary Actors:** None  
**Trigger:** User clicks "Edit" on a coupon in the list  

**Preconditions:**
1. User is authenticated
2. User has SA_COUPONSMAINTENANCE permission
3. Selected coupon exists in database

**Postconditions (Success):**
1. Coupon details are updated
2. Changes are persisted to database
3. Success message displayed

**Postconditions (Failure):**
1. No changes saved
2. Error message displayed

**Basic Flow:**
```
1. User clicks "Edit" on a coupon
2. System loads coupon details into edit form
3. User modifies desired fields
4. User clicks "Save"
5. System validates changed fields
6. System updates database record
7. System displays success message
8. System refreshes detail view
```

**Alternative Flow:**

A1: Max Uses Below Current Usage
```
5a. User changes max_uses to value < used_count
5b. System displays error: "Cannot set max uses below current usage"
5c. User cancels or adjusts value
5d. Continue from step 5
```

---

### UC-CPN-003: Delete Coupon

**Primary Actor:** System Administrator  
**Secondary Actors:** None  
**Trigger:** User clicks "Delete" on a coupon  

**Preconditions:**
1. User is authenticated
2. User has SA_COUPONSMAINTENANCE permission
3. Selected coupon exists

**Postconditions (Success):**
1. Coupon and all usage records are deleted
2. Coupon no longer appears in list
3. Confirmation message displayed

**Postconditions (Failure):**
1. Coupon remains in system
2. Error message displayed

**Basic Flow:**
```
1. User clicks "Delete" on a coupon
2. System displays confirmation dialog
3. User confirms deletion
4. System deletes from fa_coupons (cascade to usage)
5. System displays success message
6. System refreshes coupon list
```

---

### UC-CPN-004: View Coupon List

**Primary Actor:** All Users  
**Secondary Actors:** None  
**Trigger:** User navigates to Coupon Management page  

**Preconditions:**
1. User is authenticated
2. User has SA_COUPONSVIEW permission

**Postconditions:**
1. List of coupons is displayed
2. User can interact with list

**Basic Flow:**
```
1. User accesses Coupon Management page
2. System retrieves all active coupons
3. System displays paginated list (20 per page)
4. User can sort by columns (click header)
5. User can filter using filter controls
6. User can navigate pages
```

**Filter Options:**
- Status: Active | Inactive | All
- Type: Percentage | Fixed | All
- Date Range: Custom or preset

---

### UC-CPN-005: View Coupon Details

**Primary Actor:** All Users  
**Secondary Actors:** None  
**Trigger:** User clicks "View" on a coupon  

**Preconditions:**
1. User has SA_COUPONSVIEW permission
2. Coupon exists

**Postconditions:**
1. Full coupon details are displayed
2. Usage statistics shown
3. Recent usage history displayed

**Basic Flow:**
```
1. User clicks "View" on a coupon
2. System loads coupon record
3. System calculates usage statistics
4. System retrieves recent usage (last 10)
5. System displays detail page
```

---

### UC-CPN-006: Activate/Deactivate Coupon

**Primary Actor:** Marketing Manager  
**Secondary Actors:** None  
**Trigger:** User toggles active status  

**Preconditions:**
1. User has SA_COUPONSMAINTENANCE permission
2. Coupon exists

**Postconditions (Activate):**
1. Coupon.is_active = 1
2. Coupon can be used

**Postconditions (Deactivate):**
1. Coupon.is_active = 0
2. Coupon cannot be used
3. Existing usage records preserved

**Basic Flow:**
```
1. User clicks status toggle (Active/Inactive)
2. System updates is_active field
3. System records change timestamp
4. System displays updated status
```

---

### UC-CPN-007: Validate Coupon Code

**Primary Actor:** Sales Representative, System  
**Secondary Actors:** None  
**Trigger:** Coupon code entered at point of sale  

**Preconditions:**
1. Coupon code has been entered
2. System has access to coupon database

**Postconditions (Valid):**
1. Validation result = true
2. Coupon details returned
3. Discount can be calculated

**Postconditions (Invalid):**
1. Validation result = false
2. Error code and message returned

**Basic Flow:**
```
1. User/System enters coupon code
2. System queries fa_coupons for code
3. If not found → Return error COUPON001
4. If found, check is_active = 1
5. If inactive → Return error COUPON004
6. Check current date >= valid_from (if set)
7. If before valid_from → Return error COUPON002
8. Check current date <= valid_to (if set)
9. If after valid_to → Return error COUPON002
10. Check used_count < max_uses (if max_uses > 0)
11. If exhausted → Return error COUPON003
12. Return success with coupon details
```

---

### UC-CPN-008: Calculate Discount

**Primary Actor:** System  
**Secondary Actors:** None  
**Trigger:** Valid coupon confirmed for order  

**Preconditions:**
1. Validated coupon details available
2. Order total calculated

**Postconditions:**
1. Discount amount calculated
2. Discount amount returned

**Basic Flow:**
```
1. System receives coupon details and order total
2. If discount_type = 'Percentage':
   a. discount = order_total * (discount_value / 100)
3. If discount_type = 'Fixed':
   a. discount = discount_value
4. If discount > order_total:
   a. discount = order_total
5. Round discount to 2 decimal places
6. Return discount amount
```

---

### UC-CPN-009: Record Coupon Usage

**Primary Actor:** System  
**Secondary Actors:** Sales Representative  
**Trigger:** Invoice with coupon is completed  

**Preconditions:**
1. Invoice is posted/finalized
2. Coupon has been applied
3. Discount amount calculated

**Postconditions:**
1. Usage record created in fa_coupon_usage
2. Coupon used_count incremented

**Basic Flow:**
```
1. Invoice is finalized
2. System extracts coupon_id, discount_amount
3. System gets customer_person_id if available
4. System inserts record into fa_coupon_usage
5. System increments used_count in fa_coupons
6. Usage operation is atomic
```

---

### UC-CPN-010: View Usage History

**Primary Actor:** Finance Manager, Marketing Manager  
**Secondary Actors:** None  
**Trigger:** User requests usage report  

**Preconditions:**
1. User has SA_COUPONSVIEW permission
2. Coupon exists with usage

**Postconditions:**
1. Usage history displayed
2. User can filter and export

**Basic Flow:**
```
1. User clicks "Usage History" on coupon
2. System retrieves all usage records for coupon
3. System displays list with customer, invoice, amount, date
4. User can apply date filters
5. User can export to CSV
```

---

### UC-CPN-011: View Usage Statistics

**Primary Actor:** Finance Manager  
**Secondary Actors:** None  
**Trigger:** User accesses statistics dashboard  

**Preconditions:**
1. User has SA_COUPONSVIEW permission

**Postconditions:**
1. Statistics displayed
2. User can analyze trends

**Basic Flow:**
```
1. User accesses statistics page
2. System aggregates usage data
3. System calculates:
   - Total redemptions
   - Total discount value
   - Average discount per use
   - Top performing coupons
4. System displays charts and tables
```

---

### UC-CPN-012: Apply Coupon to Invoice

**Primary Actor:** Sales Representative  
**Secondary Actors:** System  
**Trigger:** User enters coupon during invoice creation  

**Preconditions:**
1. User is creating/editing invoice
2. Line items added to invoice
3. User has invoice editing permission

**Postconditions (Success):**
1. Discount line item added
2. Invoice total adjusted
3. Usage recorded

**Postconditions (Failure):**
1. No discount applied
2. Error message shown

**Basic Flow:**
```
1. User enters coupon code in invoice
2. System validates coupon (UC-CPN-007)
3. If invalid → Display error message
4. System calculates discount (UC-CPN-008)
5. System adds discount line item
6. System adjusts invoice total
7. User finalizes invoice
8. System records usage (UC-CPN-009)
```

---

### UC-CPN-013: Link Usage to Customer

**Primary Actor:** System  
**Secondary Actors:** None  
**Trigger:** Usage recorded for invoice with customer  

**Preconditions:**
1. Usage is being recorded
2. Invoice has debtor_no or customer_person_id

**Postconditions:**
1. Usage record includes customer reference
2. Customer history can be queried

**Basic Flow:**
```
1. Usage is recorded
2. System checks invoice for customer reference
3. If customer exists, system populates customer_person_id
4. Usage record saved with customer link
```

---

## 4. Use Case Matrix

| Actor | UC-CPN-001 | UC-CPN-002 | UC-CPN-003 | UC-CPN-004 | UC-CPN-005 | UC-CPN-006 | UC-CPN-007 | UC-CPN-008 | UC-CPN-009 | UC-CPN-010 | UC-CPN-011 | UC-CPN-012 | UC-CPN-013 |
|-------|------------|------------|------------|------------|------------|------------|------------|------------|------------|------------|------------|------------|------------|
| Marketing Manager | ● | ● | ○ | ● | ● | ● | ○ | ○ | ○ | ● | ○ | ○ | ○ |
| Sales Representative | ○ | ○ | ○ | ● | ● | ○ | ● | ○ | ○ | ○ | ○ | ● | ○ |
| Finance Manager | ○ | ○ | ○ | ● | ● | ○ | ○ | ○ | ○ | ● | ● | ○ | ○ |
| System Administrator | ● | ● | ● | ● | ● | ● | ○ | ○ | ○ | ● | ● | ○ | ○ |

● = Primary actor, ○ = Secondary actor

---

## 5. Error Handling Summary

| Error Code | Description | User Message |
|------------|-------------|---------------|
| COUPON001 | Invalid coupon code | "Coupon code not found" |
| COUPON002 | Coupon expired | "This coupon has expired" |
| COUPON003 | Usage limit reached | "This coupon has reached its usage limit" |
| COUPON004 | Coupon inactive | "This coupon is not active" |
| V001 | Duplicate code | "Coupon code already exists" |
| V002 | Invalid date range | "End date must be after start date" |
| V003 | Max uses conflict | "Cannot set max uses below current usage" |
| V004 | Percentage > 100 | "Percentage cannot exceed 100%" |