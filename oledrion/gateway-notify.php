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
 * Page appelée par la passerelle de paiement dans le cas de l'utilisation de l'IPN (ou d'une méthode similaire)
 * Dialogue entre le site et la passerelle
 */
@error_reporting(0);
@$xoopsLogger->activated = false;
require 'header.php';
@error_reporting(0);
@$xoopsLogger->activated = false;
$gateway = oledrion_gateways::getCurrentGateway();
$temporaryGateway = null;
$temporaryGateway = oledrion_gateways::getGatewayObject();
if (is_object($temporaryGateway)) {
    if (!file_exists(OLEDRION_GATEWAY_LOG_PATH)) {
        file_put_contents(OLEDRION_GATEWAY_LOG_PATH, '<?php exit(); ?>');
    }
    $user_log = $temporaryGateway->gatewayNotify(OLEDRION_GATEWAY_LOG_PATH);
    unset($temporaryGateway);
    echo $user_log;
}
