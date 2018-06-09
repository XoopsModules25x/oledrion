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

use XoopsModules\Oledrion;

//require_once  dirname(__DIR__) . '/include/common.php';
/** @var Oledrion\Helper $helper */
$helper = Oledrion\Helper::getInstance();

$pathIcon32    = \Xmf\Module\Admin::menuIconPath('');
$pathModIcon32 = $helper->getModule()->getInfo('modicons32');

$adminmenu[] = [
    'title' => _MI_OLEDRION_ADMENU10,
    'link'  => 'admin/index.php?op=dashboard',
    'icon'  => $pathIcon32 . '/home.png',
];

$adminmenu[] = [
    'title' => _MI_OLEDRION_ADMENU0,
    'link'  => 'admin/index.php?op=vendors',
    'icon'  => $pathIcon32 . '/user-icon.png',
];

$adminmenu[] = [
    'title' => _MI_OLEDRION_ADMENU1,
    'link'  => 'admin/index.php?op=vat',
    'icon'  => $pathModIcon32 . '/money_delete.png',
];

$adminmenu[] = [
    'title' => _MI_OLEDRION_ADMENU2,
    'link'  => 'admin/index.php?op=categories',
    'icon'  => $pathIcon32 . '/category.png',
];

$adminmenu[] = [
    'title' => _MI_OLEDRION_ADMENU3,
    'link'  => 'admin/index.php?op=manufacturers',
    'icon'  => $pathModIcon32 . '/factory.png',
];

$adminmenu[] = [
    'title' => _MI_OLEDRION_ADMENU4,
    'link'  => 'admin/index.php?op=products',
    'icon'  => $pathIcon32 . '/fileshare.png',
];

$adminmenu[] = [
    'title' => _MI_OLEDRION_ADMENU13,
    'desc'  => _MI_OLEDRION_ADMENU13_DESC,
    'link'  => 'admin/index.php?op=attributes',
    'icon'  => $pathIcon32 . '/highlight.png',
];

$adminmenu[] = [
    'title' => _MI_OLEDRION_ADMENU17,
    'link'  => 'admin/index.php?op=property',
    'icon'  => $pathIcon32 . '/view_detailed.png',
];

$adminmenu[] = [
    'title' => _MI_OLEDRION_ADMENU5,
    'link'  => 'admin/index.php?op=orders',
    'icon'  => $pathIcon32 . '/cart_add.png',
];

$adminmenu[] = [
    'title' => _MI_OLEDRION_ADMENU6,
    'link'  => 'admin/index.php?op=discounts',
    'icon'  => $pathIcon32 . '/discount.png',
];

$adminmenu[] = [
    'title' => _MI_OLEDRION_ADMENU7,
    'link'  => 'admin/index.php?op=newsletter',
    'icon'  => $pathIcon32 . '/newsletter.png',
];

$adminmenu[] = [
    'title' => _MI_OLEDRION_ADMENU8,
    'link'  => 'admin/index.php?op=texts',
    'icon'  => $pathIcon32 . '/content.png',
];

$adminmenu[] = [
    'title' => _MI_OLEDRION_ADMENU9,
    'link'  => 'admin/index.php?op=lowstock',
    'icon'  => $pathIcon32 . '/alert.png',
];

$adminmenu[] = [
    'title' => _MI_OLEDRION_ADMENU11,
    'link'  => 'admin/index.php?op=files',
    'icon'  => $pathIcon32 . '/attach.png',
];

$adminmenu[] = [
    'title' => _MI_OLEDRION_ADMENU12,
    'link'  => 'admin/index.php?op=gateways',
    'icon'  => $pathIcon32 . '/export.png',
];

$adminmenu[] = [
    'title' => _MI_OLEDRION_ADMENU15,
    'link'  => 'admin/index.php?op=lists',
    'icon'  => $pathIcon32 . '/index.png',
];

$adminmenu[] = [
    'title' => _MI_OLEDRION_ADMENU18,
    'link'  => 'admin/index.php?op=packing',
    'icon'  => $pathModIcon32 . '/package.png',
];

$adminmenu[] = [
    'title' => _MI_OLEDRION_ADMENU19,
    'link'  => 'admin/index.php?op=location',
    'icon'  => $pathIcon32 . '/globe.png',
];

$adminmenu[] = [
    'title' => _MI_OLEDRION_ADMENU20,
    'link'  => 'admin/index.php?op=delivery',
    'icon'  => $pathIcon32 . '/delivery.png',
];

$adminmenu[] = [
    'title' => _MI_OLEDRION_ADMENU21,
    'link'  => 'admin/index.php?op=payment',
    'icon'  => $pathIcon32 . '/cash_stack.png',
];

$adminmenu[] = [
    'title' => _MI_OLEDRION_ADMENU16,
    'link'  => 'admin/index.php?op=maintain',
    'icon'  => $pathIcon32 . '/synchronized.png',
];

$adminmenu[] = [
    'title' => _MI_OLEDRION_ABOUT,
    'link'  => 'admin/about.php',
    'icon'  => $pathIcon32 . '/about.png',
];
