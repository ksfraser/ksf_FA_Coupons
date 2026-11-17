# ksf_FA_Coupons - Requirements

## Purpose
Manage coupon codes and discount rules for the FrontAccounting system. Supports fixed-amount and percentage discounts with expiry dates, usage limits, and applicability rules.

## Business Rules
- A coupon has a unique code, discount type (percentage/fixed), discount value, validity dates, usage limits, and active status.
- Coupon codes must be unique.
- Coupons can be active or inactive.
- Usage count is tracked per coupon.
- Coupons can be restricted by date range (valid_from/valid_to).
- Coupons can have a maximum number of uses (0 = unlimited).

## Functional Requirements
- FR-CP-01: Create a coupon with code, type, value, dates, and limits.
- FR-CP-02: List all coupons (summary table).
- FR-CP-03: Resolve a coupon by code for application at checkout.
- FR-CP-04: Update coupon details (code, type, value, dates, limits, status).
- FR-CP-05: Deactivate/activate a coupon.
- FR-CP-06: Track coupon usage (which invoice/customer used which coupon).
- FR-CP-07: Validate a coupon code (existence, active status, date validity, usage limits).

## Non-Functional Requirements
- NF-CP-01: All database access uses native FA db_* calls.
- NF-CP-02: PHP 7.3 compatible runtime.
- NF-CP-03: Unit tests cover DTO round-trip and label resolution.