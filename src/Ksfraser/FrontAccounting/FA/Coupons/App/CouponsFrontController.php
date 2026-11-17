<?php

declare(strict_types=1);

namespace Ksfraser\FrontAccounting\FA\Coupons\App;

use ksfraser\FrontAccounting\Common\App\AbstractFrontController;

class CouponsFrontController extends AbstractFrontController
{
    public function __construct(
        CouponsAppShell $appShell,
        string $fallbackSecurityArea,
        string $path_to_root
    ) {
        parent::__construct($appShell, $fallbackSecurityArea, $path_to_root);
    }
}