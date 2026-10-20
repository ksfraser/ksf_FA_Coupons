<?php

declare(strict_types=1);

namespace Ksfraser\Tests\Unit\FA\Coupons;

use PHPUnit\Framework\TestCase;

/**
 * The coupon tables were created UNPREFIXED (`fa_coupons`,
 * `fa_coupon_usage`) but read as `TB_PREF . "fa_coupons"`, i.e.
 * `0_fa_coupons`. Neither name existed: the schema wrote `fa_coupons`
 * with no company prefix at all, and the code asked for `0_fa_coupons`.
 * Every read and write therefore failed with MySQL error 1146. The schema
 * files were never installed either: activate_extension() only ran
 * sql/install.sql, which was a placeholder comment.
 *
 * A coupon is module-owned data (a discount record), not FA core data, so
 * the tables move into the 0_ksf_coupons_* family.
 */
class CouponTableNameTest extends TestCase
{
    private const COUPON_TABLE = 'ksf_coupons_coupon';

    private const USAGE_TABLE = 'ksf_coupons_coupon_usage';

    private string $moduleDir;

    protected function setUp(): void
    {
        $this->moduleDir = dirname(__DIR__, 2);
    }

    public function testCouponDbDoesNotAddressTheNonExistentFaCouponTables(): void
    {
        $contents = (string) file_get_contents($this->moduleDir . '/includes/coupons_db.inc');

        $this->assertStringNotContainsString(
            'fa_coupons',
            $contents,
            'includes/coupons_db.inc must not address 0_fa_coupons: that table is created by no schema'
        );
    }

    public function testCouponUsageDbDoesNotAddressTheNonExistentFaCouponTables(): void
    {
        $contents = (string) file_get_contents($this->moduleDir . '/includes/coupon_usage_db.inc');

        $this->assertStringNotContainsString('fa_coupons', $contents);
        $this->assertStringNotContainsString('fa_coupon_usage', $contents);
    }

    public function testCouponDbUsesTheModuleOwnedCouponTable(): void
    {
        $contents = (string) file_get_contents($this->moduleDir . '/includes/coupons_db.inc');

        $this->assertSame(
            7,
            substr_count($contents, 'TB_PREF . "' . self::COUPON_TABLE . '`'),
            'every coupon statement must read the module-owned coupon table'
        );
    }

    public function testCouponUsageDbUsesTheModuleOwnedTables(): void
    {
        $contents = (string) file_get_contents($this->moduleDir . '/includes/coupon_usage_db.inc');

        $this->assertSame(
            6,
            substr_count($contents, 'TB_PREF . "' . self::USAGE_TABLE . '`'),
            'every coupon-usage statement must read the module-owned usage table'
        );
        $this->assertSame(
            2,
            substr_count($contents, 'TB_PREF . "' . self::COUPON_TABLE . '`'),
            'the usage list joins the module-owned coupon table'
        );
    }

    public function testSchemaCreatesTheCouponTable(): void
    {
        $contents = (string) file_get_contents($this->moduleDir . '/sql/ksf_coupons_coupon.sql');

        $this->assertStringContainsString(
            'CREATE TABLE IF NOT EXISTS `0_' . self::COUPON_TABLE . '`',
            $contents
        );
    }

    public function testSchemaCreatesTheCouponUsageTable(): void
    {
        $contents = (string) file_get_contents($this->moduleDir . '/sql/ksf_coupons_coupon_usage.sql');

        $this->assertStringContainsString(
            'CREATE TABLE IF NOT EXISTS `0_' . self::USAGE_TABLE . '`',
            $contents
        );
    }

    public function testCouponUsageForeignKeyTargetsTheCouponTable(): void
    {
        $contents = (string) file_get_contents($this->moduleDir . '/sql/ksf_coupons_coupon_usage.sql');

        $this->assertStringContainsString(
            'REFERENCES `0_' . self::COUPON_TABLE . '`',
            $contents,
            'the usage table must reference the coupon table that now exists'
        );
    }

    public function testActivationInstallsBothTables(): void
    {
        $contents = (string) file_get_contents($this->moduleDir . '/hooks.php');

        $this->assertStringContainsString(
            "'ksf_coupons_coupon.sql'        => array('ksf_coupons_coupon')",
            $contents,
            'activate_extension() must install the coupon table'
        );
        $this->assertStringContainsString(
            "'ksf_coupons_coupon_usage.sql' => array('ksf_coupons_coupon_usage')",
            $contents,
            'activate_extension() must install the coupon-usage table'
        );
    }
}
