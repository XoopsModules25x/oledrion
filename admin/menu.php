<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * oledrion
 *
 * @copyright   {@link https://xoops.org/ XOOPS Project}
 * @license     {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author      Hervé Thouzard (http://www.herve-thouzard.com/)
 */

$moduleDirName = basename(dirname(__DIR__));

if (false !== ($moduleHelper = Xmf\Module\Helper::getHelper($moduleDirName))) {
} else {
    $moduleHelper = Xmf\Module\Helper::getHelper('system');
}
$adminObject = \Xmf\Module\Admin::getInstance();

$pathIcon32    = \Xmf\Module\Admin::menuIconPath('');
$pathModIcon32 = $moduleHelper->getModule()->getInfo('modicons32');

$moduleHelper->loadLanguage('modinfo');

$adminmenu              = array();
$i                      = 0;
$adminmenu[$i]['title'] = _MI_OLEDRION_ADMENU10;
$adminmenu[$i]['link']  = 'admin/index.php?op=dashboard';
$adminmenu[$i]['icon']  = $pathIcon32 . '/home.png';
++$i;
$adminmenu[$i]['title'] = _MI_OLEDRION_ADMENU0;
$adminmenu[$i]['link']  = 'admin/index.php?op=vendors';
$adminmenu[$i]['icon']  = $pathIcon32 . '/user-icon.png';
++$i;
$adminmenu[$i]['title'] = _MI_OLEDRION_ADMENU1;
$adminmenu[$i]['link']  = 'admin/index.php?op=vat';
$adminmenu[$i]['icon']  = $pathModIcon32 . '/money_delete.png';
++$i;
$adminmenu[$i]['title'] = _MI_OLEDRION_ADMENU2;
$adminmenu[$i]['link']  = 'admin/index.php?op=categories';
$adminmenu[$i]['icon']  = $pathIcon32 . '/category.png';
++$i;
$adminmenu[$i]['title'] = _MI_OLEDRION_ADMENU3;
$adminmenu[$i]['link']  = 'admin/index.php?op=manufacturers';
$adminmenu[$i]['icon']  = $pathModIcon32 . '/factory.png';
++$i;
$adminmenu[$i]['title'] = _MI_OLEDRION_ADMENU4;
$adminmenu[$i]['link']  = 'admin/index.php?op=products';
$adminmenu[$i]['icon']  = $pathIcon32 . '/fileshare.png';
++$i;
$adminmenu[$i]['title'] = _MI_OLEDRION_ADMENU13;
$adminmenu[$i]['link']  = 'admin/index.php?op=attributes';
$adminmenu[$i]['icon']  = $pathIcon32 . '/highlight.png';
++$i;
$adminmenu[$i]['title'] = _MI_OLEDRION_ADMENU17;
$adminmenu[$i]['link']  = 'admin/index.php?op=property';
$adminmenu[$i]['icon']  = $pathIcon32 . '/view_detailed.png';
++$i;
$adminmenu[$i]['title'] = _MI_OLEDRION_ADMENU5;
$adminmenu[$i]['link']  = 'admin/index.php?op=orders';
$adminmenu[$i]['icon']  = $pathIcon32 . '/cart_add.png';
++$i;
$adminmenu[$i]['title'] = _MI_OLEDRION_ADMENU6;
$adminmenu[$i]['link']  = 'admin/index.php?op=discounts';
$adminmenu[$i]['icon']  = $pathIcon32 . '/discount.png';
++$i;
$adminmenu[$i]['title'] = _MI_OLEDRION_ADMENU7;
$adminmenu[$i]['link']  = 'admin/index.php?op=newsletter';
$adminmenu[$i]['icon']  = $pathIcon32 . '/newsletter.png';
++$i;
$adminmenu[$i]['title'] = _MI_OLEDRION_ADMENU8;
$adminmenu[$i]['link']  = 'admin/index.php?op=texts';
$adminmenu[$i]['icon']  = $pathIcon32 . '/content.png';
++$i;
$adminmenu[$i]['title'] = _MI_OLEDRION_ADMENU9;
$adminmenu[$i]['link']  = 'admin/index.php?op=lowstock';
$adminmenu[$i]['icon']  = $pathIcon32 . '/alert.png';
++$i;
$adminmenu[$i]['title'] = _MI_OLEDRION_ADMENU11;
$adminmenu[$i]['link']  = 'admin/index.php?op=files';
$adminmenu[$i]['icon']  = $pathIcon32 . '/attach.png';
++$i;
$adminmenu[$i]['title'] = _MI_OLEDRION_ADMENU12;
$adminmenu[$i]['link']  = 'admin/index.php?op=gateways';
$adminmenu[$i]['icon']  = $pathIcon32 . '/export.png';
++$i;
$adminmenu[$i]['title'] = _MI_OLEDRION_ADMENU15;
$adminmenu[$i]['link']  = 'admin/index.php?op=lists';
$adminmenu[$i]['icon']  = $pathIcon32 . '/index.png';
++$i;
$adminmenu[$i]['title'] = _MI_OLEDRION_ADMENU18;
$adminmenu[$i]['link']  = 'admin/index.php?op=packing';
$adminmenu[$i]['icon']  = $pathModIcon32 . '/package.png';
++$i;
$adminmenu[$i]['title'] = _MI_OLEDRION_ADMENU19;
$adminmenu[$i]['link']  = 'admin/index.php?op=location';
$adminmenu[$i]['icon']  = $pathIcon32 . '/globe.png';
++$i;
$adminmenu[$i]['title'] = _MI_OLEDRION_ADMENU20;
$adminmenu[$i]['link']  = 'admin/index.php?op=delivery';
$adminmenu[$i]['icon']  = $pathIcon32 . '/delivery.png';
++$i;
$adminmenu[$i]['title'] = _MI_OLEDRION_ADMENU21;
$adminmenu[$i]['link']  = 'admin/index.php?op=payment';
$adminmenu[$i]['icon']  = $pathIcon32 . '/cash_stack.png';
++$i;
$adminmenu[$i]['title'] = _MI_OLEDRION_ADMENU16;
$adminmenu[$i]['link']  = 'admin/index.php?op=maintain';
$adminmenu[$i]['icon']  = $pathIcon32 . '/synchronized.png';
++$i;
$adminmenu[$i]['title'] = _AM_MODULEADMIN_ABOUT;
$adminmenu[$i]['link']  = 'admin/about.php';
$adminmenu[$i]['icon']  = $pathIcon32 . '/about.png';
