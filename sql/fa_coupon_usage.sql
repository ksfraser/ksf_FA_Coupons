-- fa_coupon_usage table
-- Track coupon usage per invoice/customer

CREATE TABLE IF NOT EXISTS `fa_coupon_usage` (
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
    CONSTRAINT `fk_coupon_usage_coupon` FOREIGN KEY (`coupon_id`) REFERENCES `fa_coupons`(`coupon_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
