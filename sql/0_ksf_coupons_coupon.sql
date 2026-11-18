-- ksf_FA_Coupons: Coupons table
-- Stores coupon codes, discount types, values, dates, usage limits, and applicability rules

CREATE TABLE IF NOT EXISTS `0_ksf_coupons_coupon` (
  `coupon_code`   VARCHAR(30) NOT NULL COMMENT 'Unique coupon code',
  `discount_type` VARCHAR(20) NOT NULL DEFAULT 'percentage' COMMENT 'percentage or fixed',
  `discount_value` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Discount value (percentage or fixed amount)',
  `valid_from`    DATE NOT NULL COMMENT 'Start date for coupon validity',
  `valid_to`      DATE NOT NULL COMMENT 'End date for coupon validity',
  `applies_to_type` VARCHAR(32) NOT NULL DEFAULT 'cart_wide' COMMENT 'sku, sku_family, cart_wide, category',
  `applies_to_id`   INT(11) DEFAULT NULL COMMENT 'FK to stock_id (sku) or category_id, depending on applies_to_type',
  `apply_order`     INT(11) NOT NULL DEFAULT 0 COMMENT 'Order of application when multiple discounts apply',
  `stackable`       TINYINT(1) NOT NULL DEFAULT 1 COMMENT '0 = cannot combine with other discounts, 1 = can stack',
  `apply_to_original` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = apply to original price, 0 = apply to discounted price',
  `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`coupon_code`),
  KEY `idx_type` (`applies_to_type`),
  KEY `idx_type_id` (`applies_to_type`, `applies_to_id`),
  KEY `idx_apply_order` (`apply_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tracks coupon usage (which coupon used on which invoice/customer)
CREATE TABLE IF NOT EXISTS `0_ksf_coupons_coupon_usage` (
  `usage_id`        INT(11) NOT NULL AUTO_INCREMENT,
  `coupon_code`     VARCHAR(30) NOT NULL,
  `customer_id`     INT(11) DEFAULT NULL COMMENT 'FK to debtors_master.debtor_no',
  `invoice_no`      INT(11) DEFAULT NULL COMMENT 'FK to debtor_trans.trans_no',
  `discount_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Actual discount amount applied',
  `used_at`         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`usage_id`),
  KEY `idx_coupon` (`coupon_code`),
  KEY `idx_customer` (`customer_id`),
  KEY `idx_invoice` (`invoice_no`),
  CONSTRAINT `fk_coupons_coupon_usage_coupon` FOREIGN KEY (`coupon_code`) REFERENCES `0_ksf_coupons_coupon`(`coupon_code`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
