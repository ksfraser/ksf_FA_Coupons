<?php
declare(strict_types=1);
require_once __DIR__ . '/ComposerDependencies.php';
\Ksfraser\FrontAccounting\FA\Coupons\Utils\ComposerDependencies::ensure(__DIR__);
$autoload_path = dirname(__FILE__) . '/vendor/autoload.php';
if (file_exists($autoload_path)) { require_once $autoload_path; }
require_once __DIR__ . '/includes/common.inc';
require_once __DIR__ . '/includes/events.inc';
use ksfraser\RelatedTo\DTO\RelatedRecordDto;
define('SS_ksf_FA_Coupons', 157 << 8);
if (!defined('SA_COUPONS_VIEW')) {
    define('SA_COUPONS_VIEW', SS_ksf_FA_Coupons | 1);
    define('SA_CRM_MANAGE', SS_ksf_FA_Coupons | 2);
    define('SA_CRM_ADMIN', SS_ksf_FA_Coupons | 3);
}
class hooks_ksf_FA_Coupons extends hooks
{
    const RESPONDERS = array('RELATED_TO_FIELDS', 'COUPONS_RESOLVE', 'COUPONS_FIELDS', 'COUPONS_LIST');
    var $module_name = 'ksf_FA_Coupons';
    var $version = '2.4.0';
    function install_tabs($app) {
        if ($app->id == 'orders') {
            $this->add_module_app('coupons', _("Coupons"), 'modules/ksf_FA_Coupons/pages/coupons.php', SA_COUPONS_VIEW);
        }
    }
    function install_options($app) { }
    function install_access() {
        $security_sections[SS_ksf_FA_Coupons] = _("Coupons & Discounts");
        $security_areas['SA_COUPONS_VIEW'] = array(SA_COUPONS_VIEW, _("View coupons"));
        $security_areas['SA_COUPONS_MANAGE'] = array(SA_CRM_MANAGE, _("Manage coupons"));
        $security_areas['SA_CRM_ADMIN'] = array(SA_CRM_ADMIN, _("Administer coupons"));
        return array($security_areas, $security_sections);
    }
    function activate_extension($company, $check_only = true) {
        if ($check_only) { return true; }
        \Ksfraser\FrontAccounting\FA\Coupons\Utils\ComposerDependencies::ensure(__DIR__);
        $this->update_databases($company, array(
            'ksf_coupons_coupon.sql' => array('ksf_coupons_coupon', 'ksf_coupons_coupon_usage'),
        ), false);
        return true;
    }
    private function ensure_composer_dependencies() {
        \Ksfraser\FrontAccounting\FA\Coupons\Utils\ComposerDependencies::ensure(__DIR__);
    }
    public function getModuleConstants(&$data, $opts = null) {
        $data['constants'] = array(
            'SS_ksf_FA_Coupons' => SS_ksf_FA_Coupons,
            'SA_COUPONS_VIEW' => SA_COUPONS_VIEW,
            'SA_CRM_MANAGE' => SA_CRM_MANAGE,
            'SA_CRM_ADMIN' => SA_CRM_ADMIN,
        );
        return true;
    }
    public function getModuleCapabilities(&$data, $opts = null) {
        $data['capabilities'] = self::RESPONDERS;
        return true;
    }
    public function hasCapability(&$data, $opts) {
        $capability = is_array($opts) && isset($opts['capability']) ? $opts['capability'] : null;
        if ($capability === null && isset($data['capability'])) { $capability = $data['capability']; }
        $data['has_capability'] = in_array($capability, self::RESPONDERS, true);
        return $data['has_capability'];
    }
    public function respondToCapabilityRequest(&$data, $opts = null) {
        $request = is_array($opts) && isset($opts['request']) ? $opts['request'] : (isset($data['request']) ? $data['request'] : 'capabilities');
        $data['request'] = $request;
        $data['module'] = $this->module_name;
        if (strpos($request, 'has:') === 0) { return $this->hasCapability($data, array('capability' => substr($request, 4))); }
        switch ($request) {
            case 'capabilities': return $this->getModuleCapabilities($data, $opts);
            case 'constants': return $this->getModuleConstants($data, $opts);
            default: $data['error'] = 'Unknown request type: ' . $request; return null;
        }
    }
    public function RELATED_TO_FIELDS(&$data, $opts = null) {
        if (!isset($data['module']) || !is_string($data['module'])) { $data['error'] = 'RELATED_TO_FIELDS requires data[module]'; return null; }
        if (!isset($data['type']) || !is_string($data['type'])) { $data['error'] = 'RELATED_TO_FIELDS requires data[type]'; return null; }
        if ($data['type'] !== 'ksf_coupons_coupon') { return null; }
        if (!isset($data['fields']) || !is_array($data['fields'])) { $data['fields'] = array(); }
        $data['fields'] = array_merge($data['fields'], array(
            'coupon_code' => _('Coupon Code'),
            'discount_type' => _('Discount Type'),
            'discount_value' => _('Discount Value'),
            'valid_from' => _('Valid From'),
            'valid_to' => _('Valid To'),
            'applies_to_type' => _('Applies To Type'),
            'applies_to_id' => _('Applies To ID'),
            'apply_order' => _('Apply Order'),
            'stackable' => _('Stackable'),
            'apply_to_original' => _('Apply to Original Price'),
        ));
        return null;
    }
    public function COUPONS_RESOLVE(&$data, $opts = null) {
        if (!isset($data['coupon_code']) || !is_string($data['coupon_code'])) { $data['error'] = 'COUPONS_RESOLVE requires data[coupon_code]'; return null; }
        if (!isset($data['dto']) || !$data['dto'] instanceof RelatedRecordDto) { $data['error'] = 'COUPONS_RESOLVE requires data[dto] as RelatedRecordDto'; return null; }
        $service = new \Ksfraser\FrontAccounting\FA\Coupons\Service\CouponService();
        $data['result'] = $service->resolveByCode((string) $data['coupon_code'], $data['dto']);
        return null;
    }
    public function COUPONS_FIELDS(&$data, $opts = null) {
        $data['fields'] = array(
            'coupon_code' => _('Coupon Code'),
            'discount_type' => _('Discount Type'),
            'discount_value' => _('Discount Value'),
            'valid_from' => _('Valid From'),
            'valid_to' => _('Valid To'),
            'applies_to_type' => _('Applies To Type'),
            'applies_to_id' => _('Applies To ID'),
            'apply_order' => _('Apply Order'),
            'stackable' => _('Stackable'),
            'apply_to_original' => _('Apply to Original Price'),
        );
        return null;
    }
    public function COUPONS_LIST(&$data, $opts = null) {
        $service = new \Ksfraser\FrontAccounting\FA\Coupons\Service\CouponService();
        $data['result'] = $service->listAll();
        return null;
    }
}
