<?php
declare(strict_types=1);

use Ksfraser\FrontAccounting\FA\Coupons\Controller\CouponsTabController;

$path_to_root = '../..';

require_once($path_to_root . '/includes/session.inc');

if (!function_exists('page_security')) {
    define('page_security', 'SA_COUPONS_MANAGE');
}

$controller = new CouponsTabController(null, array());
$controller->renderForm();