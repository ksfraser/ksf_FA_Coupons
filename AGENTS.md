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

## Development Workflow

All development is done in the **devel tree** (`~/Documents/ksf_FA_Coupons`). Do **not** edit files in the UAT bind point directly.

### Workflow Steps
1. **Develop** in this repo (feature branches preferred)
2. **Test**: run repo-appropriate tests
3. **Lint**: `php -l` on modified PHP files (no syntax errors)
4. **Commit** and **Push** branch to GitHub
5. **Merge** to `master` when ready
6. **Push** `master` to GitHub
7. **Deploy** to UAT by pulling in the Infrastructure bind point:

   ```
   cd ~/ksf_Infrastructure/fa_modules/ksf_FA_Coupons
   git stash -u
   git pull origin master
   git stash pop
   ```

### UAT Bind Point
| Path | Purpose |
|------|---------|
| `~/Documents/ksf_FA_Coupons` | Devel tree — all development, testing, commits |
| `~/ksf_Infrastructure/fa_modules/ksf_FA_Coupons` | UAT bind point — deployment target, integration testing (if mirrored) |

