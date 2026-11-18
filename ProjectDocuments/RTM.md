# ksf_FA_Coupons - Requirements Traceability Matrix

| Req ID | Requirement | Source | Implemented In | Verified By |
|--------|-------------|--------|----------------|-------------|
| FR-CP-01 | Create coupon | Requirements | CouponRepository::insertCoupon | CouponDtoTest |
| FR-CP-02 | List coupons | Requirements | CouponService::listAll | (tab controller) |
| FR-CP-03 | Resolve coupon by code | Requirements | CouponService::resolveByCode | (capability hook) |
| FR-CP-04 | Update coupon | Requirements | CouponService::updateCoupon | (state transitions) |
| FR-CP-05 | Deactivate/activate coupon | Requirements | CouponService::deleteCoupon | (state transitions) |
| FR-CP-06 | Track coupon usage | Requirements | CouponService::insertCouponUsage | (service) |
| FR-CP-07 | Validate coupon | Requirements | CouponService::validateCoupon | (capability hook) |
| NF-CP-01 | Native db_* calls | AGENTS_ARCH | repository | code review |
| NF-CP-02 | PHP 7.3 compatible | AGENTS_ARCH | all src | code review |
| NF-CP-03 | Unit tests | AGENTS_ARCH | tests/Unit | phpunit |