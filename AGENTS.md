# AGENTS.md - ksf_FA_Coupons#

## Architecture Overview#

This repository implements **Coupon Management** with percentage/fixed discounts, usage tracking, and validation - similar to WooCommerce coupon system.

### Core Principles#
- **SOLID**: Single Responsibility, Open/Closed, Liskov Substitution, Interface Segregation, Dependency Inversion#
- **DRY**: Don't Repeat Yourself - extract reusable logic#
- **TDD**: Test-Driven Development - write tests first#
- **DI**: Dependency Injection - inject dependencies, don't hardcode#
- **SRP**: Single Responsibility Principle - each class has one reason to change#

## Repository Structure#

```
ksf_FA_Coupons/
├── sql/                    # Database schemas (FA TB_PREF tables)#
│   ├── fa_coupons.sql#
│   ├── fa_coupon_usage.sql#
│   └── fa_coupon_rules.sql#
├── includes/              # FA-specific DB classes#
│   ├── coupons_db.inc#
│   ├── coupon_usage_db.inc#
│   └── ...#
├── pages/                 # UI pages (FA admin)#
├── hooks.php              # FA module hooks#
├── composer.json#
└── ProjectDocs/           # Project documentation#
    ├── Requirements.md#
    ├── RTM.md            # Requirements Traceability Matrix#
    ├── BABOK.md         # Business Analysis Body of Knowledge#
    └── UML.md           # UML diagrams#
```

## Dependencies#

- **ksf_FA_Coupons_Core** (business logic - framework-agnostic)#
- **ksf_FA_CRM** (customer contacts)#
- **FrontAccounting 2.4+** (FA core)#
