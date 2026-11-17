<?php

declare(strict_types=1);

namespace Ksfraser\FrontAccounting\FA\Coupons\App;

use ksfraser\FrontAccounting\Common\App\AbstractAppShell;
use ksfraser\FrontAccounting\Common\App\TabRegistration;
use Ksfraser\FrontAccounting\FA\Coupons\Controller\CouponsTabController;

class CouponsAppShell extends AbstractAppShell
{
    public function __construct()
    {
        parent::__construct('coupons', 'index.php', 'view', 'coupons');

        $this->add_module(_("Coupons"));

        $menu = new FAModuleMenu(
            'modules/ksf_FA_Coupons/index.php',
            'view',
            ''
        );

        $menu->addItem('coupons', _("Coupons"), MENU_INQUIRY)
             ->registerWithApp($this, 'SA_COUPONS_VIEW');

        $this->add_extensions();
    }
}