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
 * @copyright   The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author      Hervé Thouzard (http://www.herve-thouzard.com/)
 * @version     $Id: thankyou.php 12290 2014-02-07 11:05:17Z beckmi $
 */

/**
 * Page appelée par la passerelle après le paiement en ligne
 */
require 'header.php';
$GLOBALS['current_category'] = -1;
$success = true;

$xoopsOption['template_main'] = 'oledrion_thankyou.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
$h_oledrion_caddy->emptyCart();

// On donne la possibilité à la passerelle de traiter la commande
$gateway = null;
$gateway = oledrion_gateways::getGatewayObject();
if (is_object($gateway) && method_exists($gateway, 'thankYou')) {
    if (!file_exists(OLEDRION_GATEWAY_LOG_PATH)) {
        file_put_contents(OLEDRION_GATEWAY_LOG_PATH, '<?php exit(); ?>');
    }
    $gateway->thankYou(OLEDRION_GATEWAY_LOG_PATH);
    unset($gateway);
}
$xoopsTpl->assign('success', $success);
$xoopsTpl->assign('global_advert', oledrion_utils::getModuleOption('advertisement'));
$xoopsTpl->assign('breadcrumb', oledrion_utils::breadcrumb(array(OLEDRION_URL . basename(__FILE__) => _OLEDRION_PURCHASE_FINSISHED)));

$title = _OLEDRION_PURCHASE_FINSISHED . ' - ' . oledrion_utils::getModuleName();
oledrion_utils::setMetas($title, $title);
oledrion_utils::setCSS();
oledrion_utils::setLocalCSS($xoopsConfig['language']);
require_once(XOOPS_ROOT_PATH . '/footer.php');
