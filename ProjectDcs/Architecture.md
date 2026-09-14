# ksf_FA_Coupons - Architecture Document

**Document Version:** 1.0  
**Date:** May 13, 2026  
**Module:** FA_Coupons (FrontAccounting Coupon Management)  
**Status:** Approved

---

## 1. Architecture Overview

### 1.1 Module Purpose

The FA_Coupons module provides a FrontAccounting platform adapter for coupon management functionality. It bridges the business logic layer (ksf_CRM_Core) with FrontAccounting's UI framework, database conventions, and security model.

### 1.2 Architecture Pattern

The module follows the **Business Logic + Platform Adapter** pattern as specified in AGENTS.md:

```
ksf_FA_Coupons/         # Platform-specific UI & DB adapters
    └── Ksfraser\FA\Coupons\
```

This separates business rules from platform-specific implementation, allowing core logic to be reused across different systems.

---

## 2. Component Architecture

### 2.1 Module Structure

```
ksf_FA_Coupons/
├── sql/                          # Database schemas
│   ├── fa_coupons.sql           # Coupon master table
│   └── fa_coupon_usage.sql      # Usage tracking table
├── includes/                     # FA-specific database classes
│   ├── coupons_db.inc           # Coupon CRUD operations
│   └── coupon_usage_db.inc      # Usage logging
├── pages/                        # FA admin UI pages
│   └── coupons.php              # Main coupon management page
├── hooks.php                     # FA module hooks
└── ProjectDcs/                  # Project documentation
```

### 2.2 Core Components

| Component | Type | Purpose |
|-----------|------|---------|
| `hooks_fa_coupons` | Class | FA module integration hooks |
| `coupons_db` | Include | Database access layer |
| `coupon_usage_db` | Include | Usage tracking data access |
| `coupons.php` | Page | Admin UI for coupon management |

---

## 3. Class Diagram

```
┌─────────────────────────────────────────────────────────┐
│                    FA Module Layer                       │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  ┌─────────────────────────────────────────────────┐   │
│  │           hooks_fa_coupons                      │   │
│  │           extends hooks                         │   │
│  ├─────────────────────────────────────────────────┤   │
│  │  - module_name: string                          │   │
│  │  + install_options($app): void                  │   │
│  │  + install_access(): array                      │   │
│  │  + activate_extension(): bool                  │   │
│  │  + db_prevoid(): void                          │   │
│  └─────────────────────────────────────────────────┘   │
│                          │                               │
│                          ▼                               │
│  ┌─────────────────────────────────────────────────┐   │
│  │              FA UI Layer                         │   │
│  ├─────────────────────────────────────────────────┤   │
│  │  coupons.php                                    │   │
│  │  - Security: SA_COUPONSVIEW, SA_COUPONSMAINTENANCE│   │
│  │  - Sections: List, Create, Edit, Validate       │   │
│  └─────────────────────────────────────────────────┘   │
│                          │                               │
│                          ▼                               │
│  ┌─────────────────────────────────────────────────┐   │
│  │            FA Database Layer                     │   │
│  ├─────────────────────────────────────────────────┤   │
│  │  coupons_db.inc                                 │   │
│  │  coupon_usage_db.inc                            │   │
│  └─────────────────────────────────────────────────┘   │
│                                                         │
└─────────────────────────────────────────────────────────┘
          │                    ▲
          │                    │
          ▼                    │
┌─────────────────────────────────────────────────┐
│              Business Logic Layer                │
├─────────────────────────────────────────────────┤
│  ksf_Coupons_Core (Future)                       │
│  ┌─────────────────────────────────────────┐   │
│  │  CouponService                          │   │
│  │  - create(): Coupon                     │   │
│  │  - validate(): ValidationResult         │   │
│  │  - apply(): DiscountResult              │   │
│  └─────────────────────────────────────────┘   │
└─────────────────────────────────────────────────┘
```

---

## 4. Database Architecture

### 4.1 Entity Relationship Diagram

```
┌────────────────────┐         ┌────────────────────┐
│    fa_coupons      │         │   fa_coupon_usage  │
├────────────────────┤         ├────────────────────┤
│ PK coupon_id       │──┐      │ PK usage_id        │
│    coupon_code     │  │      │ FK coupon_id  ─────┘
│    discount_type   │  │      │    customer_person_id
│    discount_value  │  │      │    invoice_id
│    valid_from      │  │      │    discount_amount
│    valid_to        │  │      │    used_at
│    max_uses        │  │      └────────────────────┘
│    used_count      │  │
│    is_active       │  │
│    created_at     │  │
└────────────────────┘  │
         │              │
         │              │
         ▼              ▼
┌────────────────────┐ ┌────────────────────┐
│    CRM Linkages    │
├────────────────────┤
│  Via ksf_FA_CRM:   │
│    - debtors       │
│    - person_types  │
│    - contacts      │
└────────────────────┘
```

### 4.2 Table Specifications

#### fa_coupons

```sql
CREATE TABLE `fa_coupons` (
    `coupon_id` INT(11) NOT NULL AUTO_INCREMENT,
    `coupon_code` VARCHAR(30) NOT NULL,
    `discount_type` VARCHAR(20) DEFAULT 'Percentage',
    `discount_value` DECIMAL(15,2) DEFAULT 0,
    `valid_from` DATE DEFAULT NULL,
    `valid_to` DATE DEFAULT NULL,
    `max_uses` INT(11) DEFAULT 0,
    `used_count` INT(11) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`coupon_id`),
    UNIQUE KEY `idx_code` (`coupon_code`)
) ENGINE=InnoDB;
```

#### fa_coupon_usage

```sql
CREATE TABLE `fa_coupon_usage` (
    `usage_id` INT(11) NOT NULL AUTO_INCREMENT,
    `coupon_id` INT(11) NOT NULL,
    `customer_person_id` INT(11) DEFAULT NULL,
    `invoice_id` INT(11) DEFAULT NULL,
    `discount_amount` DECIMAL(15,2) DEFAULT 0,
    `used_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`usage_id`),
    KEY `idx_coupon` (`coupon_id`),
    KEY `idx_customer` (`customer_person_id`),
    KEY `idx_invoice` (`invoice_id`),
    CONSTRAINT `fk_coupon_usage_coupon` 
        FOREIGN KEY (`coupon_id`) 
        REFERENCES `fa_coupons`(`coupon_id`) 
        ON DELETE CASCADE
) ENGINE=InnoDB;
```

---

## 5. Data Flow

### 5.1 Coupon Creation Flow

```
[Admin User]
     │
     ▼
[coupons.php] ──Create Form──► [Input Validation]
     │                              │
     ▼                              │
[coupons_db.inc] ◄──────────────────┘
     │
     ▼
[fa_coupons INSERT]
     │
     ▼
[Return Success / Error]
```

### 5.2 Coupon Validation Flow (At Point of Sale)

```
[Sales User] ──Enter Code──► [coupons.php validate]
     │                            │
     ▼                            ▼
[Query fa_coupons]    ──► [Check Validations]
     │                         │
     │  ┌──────────────────────┘
     ▼  ▼
[Validate Rules:]
 - is_active = 1
 - valid_from <= TODAY
 - valid_to >= TODAY
 - used_count < max_uses
     │
     ▼
[Return Valid/Invalid + Message]
```

### 5.3 Coupon Application Flow

```
[Valid Coupon] ──Calculate Discount──► [Apply to Invoice]
     │                                       │
     ▼                                       ▼
[Log to fa_coupon_usage] ──► [Increment used_count]
     │
     ▼
[Return Modified Invoice Total]
```

---

## 6. Security Architecture

### 6.1 Module Security Registration

The module registers security areas during installation:

```php
function install_access() {
    $security_sections[SS_COUPONS] = _("Coupon Management");
    $security_areas['SA_COUPONSVIEW'] = array(SS_COUPONS | 1, _("View Coupons"));
    $security_areas['SA_COUPONSCREATE'] = array(SS_COUPONS | 2, _("Create Coupons"));
    $security_areas['SA_COUPONSMAINTENANCE'] = array(SS_COUPONS | 3, _("Manage Coupons"));
    return array($security_areas, $security_sections);
}
```

### 6.2 Access Levels

| Security Area | Access Level | Assigned To |
|--------------|--------------|-------------|
| SA_COUPONSVIEW | 1 | Sales, Marketing, Finance |
| SA_COUPONSCREATE | 2 | Marketing, Admin |
| SA_COUPONSMAINTENANCE | 3 | Admin |

### 6.3 Page Security

```php
$page_security = 'SA_COUPONSVIEW'; // or SA_COUPONSMAINTENANCE
include_once($path_to_root . "/includes/session.inc");
```

---

## 7. Menu Integration

### 7.1 Menu Placement

The module adds menu entries to both CRM and Sales applications:

```php
function install_options($app) {
    switch($app->id) {
        case 'CRM':
            $app->add_rapp_function(0, _("Coupons"),
                $path_to_root."/modules/".$this->module_name."/coupons.php", 
                'SA_COUPONSVIEW', MENU_ENTRY);
            break;
        case 'Sales':
            $app->add_lapp_function(0, _("Coupons"),
                $path_to_root."/modules/".$this->module_name."/coupons.php", 
                'SA_COUPONSVIEW', MENU_ENTRY);
            break;
    }
}
```

---

## 8. Extension Activation

### 8.1 Database Installation

The module activates extensions by installing required SQL files:

```php
function activate_extension($company, $check_only=true) {
    $updates = array(
        'sql/fa_coupons.sql' => array($this->module_name),
        'sql/fa_coupon_usage.sql' => array($this->module_name)
    );
    return $this->update_databases($company, $updates, $check_only);
}
```

### 8.2 Activation Flow

1. Check if tables exist (check_only = true)
2. If missing, create tables via SQL files
3. Register module in fa_modules table
4. Return success/failure status

---

## 9. Future Architecture (KSF II)

For future versions (KSF II), the architecture will evolve to:

```
┌─────────────────────────────────────────────────────────┐
│                 FrontAccounting Adapter                  │
├─────────────────────────────────────────────────────────┤
│  hooks_fa_coupons  │  FA_UI  │  FA_DB_Adapter          │
└──────────┬─────────────────────────────┬────────────────┘
           │                             │
           ▼                             ▼
┌─────────────────────────────────────────────────────────┐
│                  Core Business Layer                     │
├─────────────────────────────────────────────────────────┤
│  Ksfraser\CouponManager\Services\CouponService          │
│  Ksfraser\CouponManager\Entities\Coupon                 │
│  Ksfraser\CouponManager\Contracts\CouponRepository      │
└─────────────────────────────────────────────────────────┘
           │
           ▼
┌─────────────────────────────────────────────────────────┐
│                    Core/Base Layer                       │
├─────────────────────────────────────────────────────────┤
│  Ksfraser\Core\BaseEntity                               │
│  Ksfraser\Traits\ValidatableTrait                       │
│  Ksfraser\Traits\TimestampTrait                         │
│  Ksfraser\Traits\EntityStateTrait                       │
└─────────────────────────────────────────────────────────┘
```

---

## 10. Technology Stack

| Component | Technology | Version |
|-----------|------------|---------|
| Framework | FrontAccounting | 2.4+ |
| Database | MySQL/MariaDB | 5.7+ |
| PHP | PHP | 7.3+ |
| Frontend | FA UI Framework | Native |
| Template | FA Themes | Default |

---

## 11. Error Handling

### 11.1 Validation Errors

| Error Code | Message | Action |
|------------|---------|--------|
| COUPON001 | Invalid coupon code | Display validation message |
| COUPON002 | Coupon expired | Show expiration date |
| COUPON003 | Usage limit reached | Show remaining uses |
| COUPON004 | Coupon inactive | Enable or create new |

### 11.2 Database Errors

| Error | Handling |
|-------|----------|
| Duplicate code | Reject with message |
| FK violation | Cascade delete or reject |
| Connection failure | Log error, show generic message |

---

## 12. Logging

The module should implement logging for:

1. **Coupon Creation** - Who created, when, parameters
2. **Coupon Validation** - Validation attempts, results
3. **Coupon Usage** - Redemption events, amounts
4. **Errors** - All exception cases

Logs should use FrontAccounting's logging mechanism (`display_db_error`).