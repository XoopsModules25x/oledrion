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
 * Gestion des passerelles de paiement
 */
if (!defined('OLEDRION_ADMIN')) {
    exit();
}

global $baseurl; // Pour faire taire les warnings de Zend Studio

switch ($action) {
    // ****************************************************************************************************************
    case 'default': // Liste des passerelles de paiement installés
        // ****************************************************************************************************************
        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation('index.php?op=gateways');

        global $xoopsConfig;
        //        OledrionUtility::htitle(_AM_OLEDRION_INSTALLED_GATEWAYS, 4);
        if (file_exists(OLEDRION_GATEWAY_LOG_PATH)) {
            echo "<a href='" . $baseurl . "?op=gateways&action=seelog'>" . _AM_OLEDRION_GATEWAYS_SEELOG . '</a><br>';
        }
        $currentGateway = Oledrion_gateways::getCurrentGateway();
        $class          = '';
        echo "<form method='post' action='" . $baseurl . "'><input type='hidden' name='op' id='op' value='gateways'><input type='hidden' name='action' id='action' value='setDefaultGateway'>";
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>\n";
        echo "<tr><th align='center'>"
             . _AM_OLEDRION_GATEWAYS_NAME
             . "</th><th align='center'>"
             . _AM_OLEDRION_GATEWAYS_VERSION
             . "</th><th align='center'>"
             . _AM_OLEDRION_GATEWAYS_DESCRIPTION
             . "</th><th align='center'>"
             . _AM_OLEDRION_GATEWAYS_AUTHOR
             . "</th><th align='center'>"
             . _AM_OLEDRION_GATEWAYS_DATE
             . "</th><th align='center'>"
             . _AM_OLEDRION_GATEWAYS_USED
             . "</th></tr>\n";
        $installedGateways = Oledrion_gateways::getInstalledGatewaysList();
        $gatewaysCount     = 0;

        foreach ($installedGateways as $installedGateway) {
            if (Oledrion_gateways::gatewayClassFileExists($installedGateway)) { // Il y a une classe donc c'est bon
                if (!Oledrion_gateways::loadGatewaysLanguageDefines($installedGateway)) { // On n'a pas réussi à charger le fichier de traduction
                    continue;
                }
                Oledrion_gateways::includeGatewayClass($installedGateway); // Chargement du fichier de la classe
                if (Oledrion_gateways::gatewayClassExists($installedGateway)) {
                    $gatewayClassName = Oledrion_gateways::gatewayClassName($installedGateway);
                    $temporaryGateway = new $gatewayClassName();
                    if (is_object($temporaryGateway)) {
                        ++$gatewaysCount;
                        $gatewayInformation = $temporaryGateway->getGatewayInformation();
                        $class              = ($class === 'even') ? 'odd' : 'even';
                        echo "<tr class='" . $class . "'>\n";
                        echo '<td>' . $gatewayInformation['name'] . "</td>\n";
                        echo "<td align='center'>" . $gatewayInformation['version'] . "</td>\n";
                        echo '<td>' . $gatewayInformation['description'] . "</td>\n";
                        echo '<td>' . $gatewayInformation['author'];
                        if (xoops_trim($gatewayInformation['credits']) != '') {
                            echo '<br>' . _AM_OLEDRION_GATEWAYS_CREDITS . $gatewayInformation['credits'];
                        }
                        echo "</td>\n";
                        echo "<td align='center'>" . formatTimestamp(strtotime($gatewayInformation['releaseDate']), 's') . "</td>\n";
                        echo "<td align='center'>";
                        $checked          = '';
                        $isCurrentGateway = false;
                        if ($currentGateway == $installedGateway) {
                            $checked          = 'checked';
                            $isCurrentGateway = true;
                        }
                        echo "<input type='radio' name='gateway' id='gateway' $checked value='" . $installedGateway . "'>";
                        if ($isCurrentGateway) {
                            echo "<br><a href='" . $baseurl . '?op=gateways&action=parameters&gateway=' . $gatewayInformation['foldername'] . "'>" . _AM_OLEDRION_GATEWAYS_PARAMETERS . '</a>';
                        }
                        echo "</td>\n";
                        echo "</tr>\n";
                    }
                    unset($temporaryGateway);
                }
            }
        }
        if ($gatewaysCount > 0) {
            $class = ($class === 'even') ? 'odd' : 'even';
            echo "<tr class='" . $class . "'>\n";
            echo "<td colspan='6' align='center'><br><input type='submit' name='btngot' id='btngo' value='" . _AM_OLEDRION_GATEWAYS_UPDATE . "'><br><br></form></td></tr>\n";
            echo "</tr>\n";
        }
        echo "</table>\n";
        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    // ****************************************************************************************************************
    case 'seelog': // Voir le contenu du fichier log
        // ****************************************************************************************************************
        xoops_cp_header();
        global $xoopsConfig;
        OledrionUtility::htitle(_AM_OLEDRION_INSTALLED_GATEWAYS, 4);
        $opRedirect = '?op=gateways';
        if (!file_exists(OLEDRION_GATEWAY_LOG_PATH)) {
            OledrionUtility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl . $opRedirect, 4);
        }
        $logContent = nl2br(file_get_contents(OLEDRION_GATEWAY_LOG_PATH));
        echo '<div id="logContent" style="width: 1024px; max-width! 1024px; height: 400px; overflow: auto;">';
        echo "<pre>\n";
        echo $logContent;
        echo "</pre>\n";
        echo "</div>\n";
        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    // ****************************************************************************************************************
    case 'setDefaultGateway': // Choix de la passerelle de paiement par défaut
        // ****************************************************************************************************************
        xoops_cp_header();
        oledrion_adminMenu(12);
        $opRedirect = '?op=gateways';
        $gateway    = isset($_POST['gateway']) ? strtolower($_POST['gateway']) : '';
        $gateway    = Oledrion_gateways::purifyGatewayName($gateway);
        if (empty($gateway)) {
            OledrionUtility::redirect(_AM_OLEDRION_ERROR_1, $baseurl . $opRedirect, 5);
        }
        if (oledrion_set_module_option('used_gateway', $gateway)) {
            OledrionUtility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . $opRedirect, 1);
        } else {
            OledrionUtility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . $opRedirect, 4);
        }
        break;

    // ****************************************************************************************************************
    case 'parameters': // Paramètres de la passerelle de paiement sélectionnée
        // ****************************************************************************************************************
        xoops_cp_header();
        oledrion_adminMenu(12);
        OledrionUtility::htitle(_AM_OLEDRION_INSTALLED_GATEWAYS, 4);
        $opRedirect = '?op=gateways';
        $gateway    = isset($_GET['gateway']) ? strtolower($_GET['gateway']) : '';
        $gateway    = Oledrion_gateways::purifyGatewayName($gateway);
        if (empty($gateway)) {
            OledrionUtility::redirect(_AM_OLEDRION_ERROR_1, $baseurl . $opRedirect, 5);
        }
        if (Oledrion_gateways::gatewayClassFileExists($gateway)) { // Il y a une classe donc c'est bon
            $languageFilename     = '';
            $languageFileIncluded = Oledrion_gateways::loadGatewaysLanguageDefines($gateway, $languageFilename);
            if (!$languageFileIncluded) {
                OledrionUtility::redirect(_AM_OLEDRION_GATEWAYS_ERROR2, $baseurl . $opRedirect, 4);
            }
            Oledrion_gateways::includeGatewayClass($gateway);
            if (Oledrion_gateways::gatewayClassExists($gateway)) {
                $gatewayClassName = Oledrion_gateways::gatewayClassName($gateway);
                $temporaryGateway = new $gatewayClassName();
                if (!Oledrion_gateways::asGoodAncestor($temporaryGateway)) {
                    OledrionUtility::redirect(_AM_OLEDRION_GATEWAYS_ERROR4, $baseurl . $opRedirect, 4);
                }
                $temporaryGateway->languageFilename = $languageFilename;
                $form                               = $temporaryGateway->getParametersForm($baseurl . $opRedirect . '&action=saveparameters');
                $form                               =& OledrionUtility::formMarkRequiredFields($form);
                $form->display();
            } else {
                OledrionUtility::redirect(_AM_OLEDRION_GATEWAYS_ERROR3, $baseurl . $opRedirect, 4);
            }
        } else {
            OledrionUtility::redirect(_AM_OLEDRION_GATEWAYS_ERROR1, $baseurl . $opRedirect, 4);
        }
        break;

    // ****************************************************************************************************************
    case 'saveparameters': // Enregistrement des paramètres de la passerelle de paiement
        // ****************************************************************************************************************
        xoops_cp_header();
        oledrion_adminMenu(12);
        $opRedirect = '?op=gateways';
        $gateway    = isset($_POST['gateway']) ? strtolower($_POST['gateway']) : '';
        $gateway    = Oledrion_gateways::purifyGatewayName($gateway);
        if (empty($gateway)) {
            OledrionUtility::redirect(_AM_OLEDRION_ERROR_1, $baseurl . $opRedirect, 5);
        }
        if (!Oledrion_gateways::isInstalledGatewayName($gateway)) {
            OledrionUtility::redirect(_AM_OLEDRION_GATEWAYS_ERROR5, $baseurl . $opRedirect, 4);
        }
        if (Oledrion_gateways::gatewayClassFileExists($gateway)) {
            if (!Oledrion_gateways::loadGatewaysLanguageDefines($gateway)) { // Le chargement des traductions a échoué
                OledrionUtility::redirect(_AM_OLEDRION_GATEWAYS_ERROR2, $baseurl . $opRedirect, 4);
            }
            Oledrion_gateways::includeGatewayClass($gateway);
            if (Oledrion_gateways::gatewayClassExists($gateway)) {
                $gatewayClassName = Oledrion_gateways::gatewayClassName($gateway);
                $temporaryGateway = new $gatewayClassName();
                if (!Oledrion_gateways::asGoodAncestor($temporaryGateway)) {
                    OledrionUtility::redirect(_AM_OLEDRION_GATEWAYS_ERROR4, $baseurl . $opRedirect, 4);
                }
                if ($temporaryGateway->saveParametersForm($_POST)) {
                    OledrionUtility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . $opRedirect, 2);
                } else {
                    OledrionUtility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . $opRedirect, 4);
                }
            } else {
                OledrionUtility::redirect(_AM_OLEDRION_GATEWAYS_ERROR3, $baseurl . $opRedirect, 4);
            }
        } else {
            OledrionUtility::redirect(_AM_OLEDRION_GATEWAYS_ERROR1, $baseurl . $opRedirect, 4);
        }
        break;
}
