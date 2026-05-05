<?php
/**
 * FA_Coupons Module Hooks for FrontAccounting
 * Coupon Management (depends on ksf_FA_CRM, used by Sales)
 */

define('SS_COUPONS', 117 << 8);

class hooks_fa_coupons extends hooks {
    var $module_name = 'fa_coupons';

    function install_options($app) {
        global $path_to_root;

        switch($app->id) {
            case 'CRM':
                $app->add_rapp_function(0, _("Coupons"),
                    $path_to_root."/modules/".$this->module_name."/coupons.php", 'SA_COUPONSVIEW', MENU_ENTRY);
                break;
            case 'Sales':
                $app->add_lapp_function(0, _("Coupons"),
                    $path_to_root."/modules/".$this->module_name."/coupons.php", 'SA_COUPONSVIEW', MENU_ENTRY);
                break;
        }
    }

    function install_access() {
        $security_sections[SS_COUPONS] = _("Coupon Management");
        $security_areas['SA_COUPONSVIEW'] = array(SS_COUPONS | 1, _("View Coupons"));
        $security_areas['SA_COUPONSCREATE'] = array(SS_COUPONS | 2, _("Create Coupons"));
        $security_areas['SA_COUPONSMAINTENANCE'] = array(SS_COUPONS | 3, _("Manage Coupons"));
        return array($security_areas, $security_sections);
    }

    function activate_extension($company, $check_only=true) {
        $updates = array(
            'sql/fa_coupons.sql' => array($this->module_name),
            'sql/fa_coupon_usage.sql' => array($this->module_name)
        );
        return $this->update_databases($company, $updates, $check_only);
    }

    function db_prevoid($trans_type, $trans_no) {
        // Handle voiding if coupons module tracks usage
    }
}
?>
