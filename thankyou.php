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

/**
 * Page appelée par la passerelle après le paiement en ligne
 */
require_once __DIR__ . '/header.php';
$GLOBALS['current_category'] = -1;
$success                     = true;

$GLOBALS['xoopsOption']['template_main'] = 'oledrion_thankyou.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
$h_oledrion_caddy->emptyCart();

// On donne la possibilité à la passerelle de traiter la commande
$gateway = null;
$gateway = Oledrion_gateways::getGatewayObject();
if (is_object($gateway) && method_exists($gateway, 'thankYou')) {
    if (!file_exists(OLEDRION_GATEWAY_LOG_PATH)) {
        file_put_contents(OLEDRION_GATEWAY_LOG_PATH, '<?php exit(); ?>');
    }
    $gateway->thankYou(OLEDRION_GATEWAY_LOG_PATH);
    unset($gateway);
}
$xoopsTpl->assign('success', $success);
$xoopsTpl->assign('global_advert', OledrionUtility::getModuleOption('advertisement'));
$xoopsTpl->assign('breadcrumb', OledrionUtility::breadcrumb([OLEDRION_URL . basename(__FILE__) => _OLEDRION_PURCHASE_FINSISHED]));

$title = _OLEDRION_PURCHASE_FINSISHED . ' - ' . OledrionUtility::getModuleName();
OledrionUtility::setMetas($title, $title);
OledrionUtility::setCSS();
OledrionUtility::setLocalCSS($xoopsConfig['language']);
require_once XOOPS_ROOT_PATH . '/footer.php';
