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
 * @version     $Id$
 */

/**
 * Page appelée par la passerelle de paiement dans le cas de l'annulation d'une commande
 */
require 'header.php';
$GLOBALS['current_category'] = -1;
$xoopsOption['template_main'] = 'oledrion_cancelpurchase.html';
require_once XOOPS_ROOT_PATH . '/header.php';

// On donne la possibilité à la passerelle d'annuler la commande
$gateway = null;
$gateway = oledrion_gateways::getGatewayObject();
if (is_object($gateway) && method_exists($gateway, 'cancelOrder')) {
    if (!file_exists(OLEDRION_GATEWAY_LOG_PATH)) {
        file_put_contents(OLEDRION_GATEWAY_LOG_PATH, '<?php exit(); ?>');
    }
    $gateway->cancelOrder(OLEDRION_GATEWAY_LOG_PATH);
    unset($gateway);
} elseif (isset($_GET['id'])) {
    $order = null;
    $order = $h_oledrion_commands->getOrderFromCancelPassword($_GET['id']);
    if (is_object($order)) {
        $h_oledrion_commands->setOrderCanceled($order);
    }
}
$h_oledrion_caddy->emptyCart();
$xoopsTpl->assign('mod_pref', $mod_pref);
$xoopsTpl->assign('breadcrumb', oledrion_utils::breadcrumb(array(OLEDRION_URL . basename(__FILE__) => _OLEDRION_ORDER_CANCELED)));

$title = _OLEDRION_ORDER_CANCELED . ' - ' . oledrion_utils::getModuleName();
oledrion_utils::setMetas($title, $title);
oledrion_utils::setCSS();
oledrion_utils::setLocalCSS($xoopsConfig['language']);
require_once(XOOPS_ROOT_PATH . '/footer.php');
