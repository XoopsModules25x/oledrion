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

require_once __DIR__ . '/header.php';
$GLOBALS['current_category'] = -1;

$op = isset($_GET['op']) ? $_GET['op'] : 'default';

if (isset($_GET['id'])) {
    $cmdId = (int)$_GET['id'];
} else {
    OledrionUtility::redirect(_OLEDRION_ERROR11, 'index.php', 6);
}

if (isset($_GET['pass'])) {
    $pass = $_GET['pass'];
} else {
    if (!OledrionUtility::isAdmin()) {
        OledrionUtility::redirect(_OLEDRION_ERROR11, 'index.php', 6);
    }
}

$order = null;
$order = $h_oledrion_commands->get($cmdId);
if (!is_object($order)) {
    OledrionUtility::redirect(_OLEDRION_ERROR11, 'index.php', 6);
}

// Vérification du mot de passe (si pas admin)
if (!OledrionUtility::isAdmin()) {
    if ($pass != $order->getVar('cmd_password')) {
        OledrionUtility::redirect(_OLEDRION_ERROR11, 'index.php', 6);
    }
}

// Vérification de la validité de la facture (si pas admin)
/* if (!OledrionUtility::isAdmin()) {
    if ($order->getVar('cmd_state') != OLEDRION_STATE_VALIDATED) { // Commande non validée
        OledrionUtility::redirect(_OLEDRION_ERROR12, 'index.php', 6);
    }
} */

$caddy = $tmp = $products = $vats = $manufacturers = $tmp2 = $manufacturers = $productsManufacturers = array();

// Récupération des TVA
$vats = $h_oledrion_vat->getAllVats(new Oledrion_parameters());

// Récupération des caddy associés
$caddy = $h_oledrion_caddy->getCaddyFromCommand($cmdId);
if (count($caddy) == 0) {
    OledrionUtility::redirect(_OLEDRION_ERROR11, 'index.php', 6);
}

// Récupération de la liste des produits associés
foreach ($caddy as $item) {
    $tmp[] = $item->getVar('caddy_product_id');
}

// Recherche des produits ***********************************************************************************************
$products = $h_oledrion_products->getProductsFromIDs($tmp, true);

// Recherche des fabricants **********************************************************************************************
$tmp2 = $h_oledrion_productsmanu->getFromProductsIds($tmp);
$tmp  = array();
foreach ($tmp2 as $item) {
    $tmp[]                                                   = $item->getVar('pm_manu_id');
    $productsManufacturers[$item->getVar('pm_product_id')][] = $item;
}
$manufacturers = $h_oledrion_manufacturer->getManufacturersFromIds($tmp);

switch ($op) {
    case 'print':
        require_once XOOPS_ROOT_PATH . '/header.php';

        // Informations sur la commande ***************************************************************************************
        $xoopsTpl->assign('order', $order->toArray());
        $xoopsTpl->assign('ask_vatnumber', OledrionUtility::getModuleOption('ask_vatnumber'));
        $handlers = OledrionHandler::getInstance();

        // Boucle sur le caddy ************************************************************************************************
        foreach ($caddy as $itemCaddy) {
            $productForTemplate = $tblJoin = $productManufacturers = $productAttributes = array();
            $product            = $products[$itemCaddy->getVar('caddy_product_id')];
            $productForTemplate = $product->toArray(); // Produit
            // Est-ce qu'il y a des attributs ?
            if ($handlers->h_oledrion_caddy_attributes->getAttributesCountForCaddy($itemCaddy->getVar('caddy_id')) > 0) {
                $productAttributes = $handlers->h_oledrion_caddy_attributes->getFormatedAttributesForCaddy($itemCaddy->getVar('caddy_id'), $product);
            }
            $productForTemplate['product_attributes'] = $productAttributes;

            $productManufacturers = $productsManufacturers[$product->getVar('product_id')];
            foreach ($productManufacturers as $oledrion_productsmanu) {
                if (isset($manufacturers[$oledrion_productsmanu->getVar('pm_manu_id')])) {
                    $manufacturer = $manufacturers[$oledrion_productsmanu->getVar('pm_manu_id')];
                    $tblJoin[]    = $manufacturer->getVar('manu_commercialname') . ' ' . $manufacturer->getVar('manu_name');
                }
            }
            if (count($tblJoin) > 0) {
                $productForTemplate['product_joined_manufacturers'] = implode(', ', $tblJoin);
            }
            $productForTemplate['product_caddy'] = $itemCaddy->toArray();
            $xoopsTpl->append('products', $productForTemplate);
        }
        // Display print page
        echo $xoopsTpl->fetch(OLEDRION_PATH . '/templates/oledrion_bill_print.tpl');
        break;

    case 'default':
    default:
        /**
         * Visualisation d'une facture à l'écran
         */
        $GLOBALS['xoopsOption']['template_main'] = 'oledrion_bill.tpl';
        require_once XOOPS_ROOT_PATH . '/header.php';

        // Informations sur la commande ***************************************************************************************
        $xoopsTpl->assign('order', $order->toArray());
        $xoopsTpl->assign('ask_vatnumber', OledrionUtility::getModuleOption('ask_vatnumber'));
        $xoopsTpl->assign('printurl', OLEDRION_URL . basename(__FILE__) . '?op=print&id=' . $order->getVar('cmd_id') . '&pass=' . $order->getVar('cmd_password'));

        $handlers = OledrionHandler::getInstance();

        // Boucle sur le caddy ************************************************************************************************
        foreach ($caddy as $itemCaddy) {
            $productForTemplate = $tblJoin = $productManufacturers = $productAttributes = array();
            $product            = $products[$itemCaddy->getVar('caddy_product_id')];
            $productForTemplate = $product->toArray(); // Produit
            // Est-ce qu'il y a des attributs ?
            if ($handlers->h_oledrion_caddy_attributes->getAttributesCountForCaddy($itemCaddy->getVar('caddy_id')) > 0) {
                $productAttributes = $handlers->h_oledrion_caddy_attributes->getFormatedAttributesForCaddy($itemCaddy->getVar('caddy_id'), $product);
            }
            $productForTemplate['product_attributes'] = $productAttributes;

            $productManufacturers = $productsManufacturers[$product->getVar('product_id')];
            foreach ($productManufacturers as $oledrion_productsmanu) {
                if (isset($manufacturers[$oledrion_productsmanu->getVar('pm_manu_id')])) {
                    $manufacturer = $manufacturers[$oledrion_productsmanu->getVar('pm_manu_id')];
                    $tblJoin[]    = $manufacturer->getVar('manu_commercialname') . ' ' . $manufacturer->getVar('manu_name');
                }
            }
            if (count($tblJoin) > 0) {
                $productForTemplate['product_joined_manufacturers'] = implode(', ', $tblJoin);
            }
            $productForTemplate['product_caddy'] = $itemCaddy->toArray();
            $xoopsTpl->append('products', $productForTemplate);
        }

        OledrionUtility::setCSS();
        OledrionUtility::setLocalCSS($xoopsConfig['language']);
        $title = _OLEDRION_BILL . ' - ' . OledrionUtility::getModuleName();
        OledrionUtility::setMetas($title, $title);
        require_once XOOPS_ROOT_PATH . '/footer.php';
        break;
}
