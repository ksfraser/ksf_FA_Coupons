# ksf_FA_Coupons - Business Requirements Document

**Document Version:** 1.0  
**Date:** May 13, 2026  
**Module:** FA_Coupons (FrontAccounting Coupon Management)  
**Status:** Approved

---

## 1. Introduction

### 1.1 Purpose

The FA_Coupons module provides comprehensive coupon and discount management capabilities for FrontAccounting. It enables businesses to create, manage, and track promotional discounts including percentage-based and fixed-amount coupons with usage limits, validity periods, and customer tracking.

### 1.2 Problem Statement

Organizations using FrontAccounting for sales operations require the ability to apply promotional discounts at the point of sale. The core FrontAccounting system lacks built-in coupon functionality, forcing businesses to:

- Manually calculate and apply discounts
- Track coupon usage through spreadsheets or external systems
- Lack visibility into promotional campaign effectiveness
- Risk overselling discounted inventory or exceeding budget allocations

### 1.3 Scope

This module addresses the coupon management gap by providing:

1. **Coupon Creation and Maintenance** - Full CRUD operations for discount coupons
2. **Discount Types** - Support for percentage and fixed amount discounts
3. **Usage Tracking** - Comprehensive tracking of coupon redemption
4. **Validity Management** - Date-based coupon activation and expiration
5. **Integration with Sales** - Seamless application at invoice creation

---

## 2. Module Overview

### 2.1 Core Features

| Feature | Description | Priority |
|---------|-------------|----------|
| Coupon Creation | Create new coupons with code, type, and value | Critical |
| Coupon Editing | Modify existing coupon parameters | Critical |
| Coupon Deactivation | Disable coupons without deletion | High |
| Usage Limits | Set maximum redemption count per coupon | High |
| Validity Periods | Define start and end dates for promotions | High |
| Usage History | Track all coupon redemptions | High |
| Code Generation | Unique coupon code creation | Medium |
| Customer Linking | Associate coupons with specific customers | Medium |

### 2.2 Discount Types

#### Percentage Discounts
- Apply a percentage off the total order value
- Example: 15% off all orders over $100

#### Fixed Amount Discounts
- Subtract a fixed monetary value from the order
- Example: $25 off any order

### 2.3 Coupon States

| State | Description |
|-------|-------------|
| Active | Coupon can be used |
| Inactive | Coupon cannot be used (manually disabled) |
| Expired | Coupon past validity end date |
| Exhausted | Coupon reached maximum usage limit |

---

## 3. User Stories

### 3.1 Marketing Manager

> As a Marketing Manager, I want to create promotional coupons so that I can run limited-time offers to drive customer purchases.

**Acceptance Criteria:**
- Can create coupons with percentage or fixed discounts
- Can set validity dates for time-limited promotions
- Can define maximum usage limits for budget control
- Can view usage statistics for campaign analysis

### 3.2 Sales Representative

> As a Sales Representative, I want to apply coupon codes at point of sale so that customers can receive their promotional discounts.

**Acceptance Criteria:**
- Can enter coupon code during invoice creation
- System validates coupon before applying discount
- Discount appears clearly on invoice
- Invalid coupons show appropriate error messages

### 3.3 Finance Manager

> As a Finance Manager, I want to track coupon usage for reconciliation so that I can ensure accurate discount accounting.

**Acceptance Criteria:**
- Can view complete usage history by coupon
- Can filter usage by customer, date range, and invoice
- Can export usage data for external analysis
- Can verify total discount amounts match invoices

---

## 4. Integration Dependencies

### 4.1 Required Modules

| Module | Dependency Type | Purpose |
|--------|-----------------|---------|
| ksf_FA_CRM | Required | Customer/person data storage and linking |
| FrontAccounting Core | Required | Invoice processing, database, UI framework |

### 4.2 Optional Integrations

| Integration | Purpose |
|-------------|---------|
| ksf_FA_DynamicPricing | Combine with dynamic pricing rules |
| ksf_FA_Forms | Customer feedback on promotions |

### 4.3 Data Dependencies

| External Table | Relationship | Purpose |
|---------------|--------------|---------|
| `{PREFIX}creditors` | Via CRM | Supplier data (future) |
| `{PREFIX}debtors` | Via CRM | Customer/debtor master |
| `{PREFIX}person_types` | Via CRM | Person type classification |

---

## 5. Database Schema

### 5.1 Primary Tables

#### `fa_coupons`
Stores master coupon data.

| Column | Type | Description |
|--------|------|-------------|
| `coupon_id` | INT (PK) | Primary key |
| `coupon_code` | VARCHAR(30) | Unique coupon code |
| `discount_type` | VARCHAR(20) | 'Percentage' or 'Fixed' |
| `discount_value` | DECIMAL(15,2) | Discount amount |
| `valid_from` | DATE | Start validity date |
| `valid_to` | DATE | End validity date |
| `max_uses` | INT | Maximum redemptions (0=unlimited) |
| `used_count` | INT | Current redemption count |
| `is_active` | TINYINT(1) | Active flag |
| `created_at` | TIMESTAMP | Creation timestamp |

#### `fa_coupon_usage`
Tracks individual coupon redemptions.

| Column | Type | Description |
|--------|------|-------------|
| `usage_id` | INT (PK) | Primary key |
| `coupon_id` | INT (FK) | Reference to fa_coupons |
| `customer_person_id` | INT | Customer person reference |
| `invoice_id` | INT | Invoice reference |
| `discount_amount` | DECIMAL(15,2) | Actual discount applied |
| `used_at` | TIMESTAMP | Redemption timestamp |

---

## 6. Security Model

### 6.1 Security Areas

| Area | Code | Description |
|------|------|-------------|
| SS_COUPONS | 117 << 8 | Coupon Management section |
| SA_COUPONSVIEW | SS_COUPONS \| 1 | View coupons |
| SA_COUPONSCREATE | SS_COUPONS \| 2 | Create new coupons |
| SA_COUPONSMAINTENANCE | SS_COUPONS \| 3 | Edit/delete coupons |

### 6.2 Access Control

- View access: Sales, Marketing, Finance roles
- Create access: Marketing, Admin roles
- Maintenance access: Admin, Finance roles

---

## 7. Success Metrics

| Metric | Target | Measurement |
|--------|--------|-------------|
| Coupon creation time | < 2 minutes | User testing |
| Validation response | < 200ms | Performance testing |
| Usage tracking accuracy | 100% | Reconciliation audit |
| Integration compatibility | FrontAccounting 2.4+ | QA testing |

---

## 8. Future Enhancements

1. **Coupon Categories** - Group coupons by campaign type
2. **Auto-generation** - Bulk coupon code generation
3. **Conditional Rules** - Minimum order amount requirements
4. **Customer Restrictions** - First-time buyer only coupons
5. **Stackable Coupons** - Allow multiple coupons per order
6. **API Access** - RESTful API for external integrations