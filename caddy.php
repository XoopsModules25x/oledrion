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
 * Affichage et gestion du caddy
 */
require_once __DIR__ . '/header.php';
$GLOBALS['current_category']             = -1;
$GLOBALS['xoopsOption']['template_main'] = 'oledrion_caddy.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once OLEDRION_PATH . 'class/registryfile.php';

$xoopsTpl->assign('mod_pref', $mod_pref); // Préférences du module

if (OledrionUtility::getModuleOption('restrict_orders', false)) {
    $registry = new oledrion_registryfile();
    $text     = $registry->getfile(OLEDRION_TEXTFILE5);
    $xoopsTpl->assign('restrict_orders_text', xoops_trim($text));
} else {
    $xoopsTpl->assign('restrict_orders_text', '');
}

$op = 'default';
if (isset($_POST['op'])) {
    $op = $_POST['op'];
} elseif (isset($_GET['op'])) {
    $op = $_GET['op'];
}

$productId = 0;
if (isset($_POST['product_id'])) {
    $productId = (int)$_POST['product_id'];
} elseif (isset($_GET['product_id'])) {
    $productId = (int)$_GET['product_id'];
}

$xoopsTpl->assign('op', $op);
$xoopsTpl->assign('confEmpty', OledrionUtility::javascriptLinkConfirm(_OLEDRION_EMPTY_CART_SURE, true));
$xoopsTpl->assign('confirm_delete_item', OledrionUtility::javascriptLinkConfirm(_OLEDRION_EMPTY_ITEM_SURE, false));

$uid = OledrionUtility::getCurrentUserID();
if ($uid > 0) {
    $xoopsTpl->assign('isCartExists', $h_oledrion_persistent_cart->isCartExists());
} else {
    $xoopsTpl->assign('isCartExists', false);
}

// ********************************************************************************************************************
// Liste le contenu du caddy
// ********************************************************************************************************************
function listCart()
{
    global $xoopsTpl, $uid;
    $cartForTemplate = $discountsDescription = [];
    $emptyCart       = false;
    $shippingAmount  = $commandAmount = $vatAmount = $commandAmountTTC = $discountsCount = $ecotaxeAmount = $discountAmount = $totalSavings = 0;
    $goOn            = '';
    $reductions      = new oledrion_reductions();
    $reductions->computeCart($cartForTemplate, $emptyCart, $shippingAmount, $commandAmount, $vatAmount, $goOn, $commandAmountTTC, $discountsDescription, $discountsCount, $ecotaxeAmount, $discountAmount, $totalSavings);
    $oledrion_Currency = Oledrion_Currency::getInstance();
    $xoopsTpl->assign('emptyCart', $emptyCart);                                            // Caddy Vide ?
    $xoopsTpl->assign('caddieProducts', $cartForTemplate);                                // Produits dans le caddy
    $xoopsTpl->assign('shippingAmount', $oledrion_Currency->amountForDisplay($shippingAmount));        // Montant des frais de port
    $xoopsTpl->assign('ecotaxeAmount', $oledrion_Currency->amountForDisplay($ecotaxeAmount));        // Montant des frais de port
    $xoopsTpl->assign('commandAmount', $oledrion_Currency->amountForDisplay($commandAmount));        // Montant HT de la commande
    $xoopsTpl->assign('discountAmount', $oledrion_Currency->amountForDisplay($discountAmount));        // Total Discount
    $xoopsTpl->assign('totalSavings', $oledrion_Currency->amountForDisplay($totalSavings));        // Total Savings
    $xoopsTpl->assign('vatAmount', $oledrion_Currency->amountForDisplay($vatAmount));                // Montant de la TVA
    $xoopsTpl->assign('discountsCount', $discountsCount);                                // Nombre de réductions appliquées
    $xoopsTpl->assign('goOn', $goOn);                                                    // Adresse à utiliser pour continuer ses achats
    $xoopsTpl->assign('commandAmountTTC', $oledrion_Currency->amountForDisplay($commandAmountTTC, 'l'));    // Montant TTC de la commande
    $xoopsTpl->assign('discountsDescription', $discountsDescription);                    // Liste des réductions accordées
    $showOrderButton   = true;
    $showRegistredOnly = false;
    if (OledrionUtility::getModuleOption('restrict_orders', false) && $uid == 0) {
        $showRegistredOnly = true;
        $showOrderButton   = false;
    }
    $xoopsTpl->assign('showRegistredOnly', $showRegistredOnly);
    $xoopsTpl->assign('showOrderButton', $showOrderButton);
}

// ********************************************************************************************************************
// ********************************************************************************************************************
// ********************************************************************************************************************
switch ($op) {
    // ****************************************************************************************************************
    case 'update': // Recalcul des quantités
        // ****************************************************************************************************************
        $h_oledrion_caddy->updateQuantites();
        listCart();
        break;

    // ****************************************************************************************************************
    case 'reload': // Chargement du dernier panier enregistré
        // ****************************************************************************************************************
        $h_oledrion_caddy->reloadPersistentCart();
        listCart();
        break;

    // ****************************************************************************************************************
    case 'delete': // Suppression d'un élément
        // ****************************************************************************************************************
        $productId--;
        $h_oledrion_caddy->deleteProduct($productId);
        listCart();
        break;

    // ****************************************************************************************************************
    case 'addproduct': // Ajout d'un produit
        // ****************************************************************************************************************
        if ($productId == 0) {
            OledrionUtility::redirect(_OLEDRION_ERROR9, 'index.php', 4);
        }
        $product = null;
        $product = $h_oledrion_products->get($productId);
        if (!is_object($product)) {
            OledrionUtility::redirect(_OLEDRION_ERROR9, 'index.php', 4);
        }
        if ($product->getVar('product_online') == 0) {
            OledrionUtility::redirect(_OLEDRION_ERROR2, 'index.php', 4);
        }

        if ($product->getVar('product_stock') - 1 >= 0) {
            // Options
            $userAttributes = [];
            if ($product->productAttributesCount() > 0) { // Si le produit a des attributs
                $productAttributes = [];
                // On commence par vérifier que les attributs obligatoires sont renseignés
                // It starts by checking if mandatory attributes are filled
                if ($product->getProductMandatoryAttributesCount()) {
                    $mandatoryFieldsList = [];
                    $mandatoryFieldsList = $product->getProductMandatoryFieldsList();
                    if (count($mandatoryFieldsList) > 0) {
                        $productUrl = $product->getLink();
                        foreach ($mandatoryFieldsList as $mandatoryField) {
                            $mandatoryFieldKey  = $mandatoryField->getAttributeNameInForm();
                            $mandatoryFieldText = $mandatoryField->getVar('attribute_title');
                            if (!isset($_POST[$mandatoryFieldKey]) && !$mandatoryField->hasDefaultValue()) {
                                OledrionUtility::redirect(sprintf(_OLEDRION_MANDATORY_MISSED, $mandatoryFieldText), $productUrl, 4);
                            }
                        }
                    }
                }
                // Toujours là c'est que le produit a des attributs et qu'ils sont renseignés
                //Checks if the product has more options and if they are set
                $productAttributes = $product->getProductsAttributesList();
                foreach ($productAttributes as $attribute) {
                    $nameInForm = $attribute->getAttributeNameInForm();
                    if (isset($_POST[$nameInForm])) {
                        $userAttributes[$attribute->attribute_id] = $_POST[$nameInForm];
                    } else { // On va chercher sa valeur par défaut
                        if ($attribute->hasDefaultValue()) {
                            $userAttributes[$attribute->attribute_id] = $attribute->getAttributeDefaultValue();
                        }
                    }
                }
            }
            $h_oledrion_caddy->addProduct($productId, 1, $userAttributes);
            $url = OLEDRION_URL . 'caddy.php';
            if (!OLEDRION_CART_BUG) {
                header("Location: $url");
            } else {
                listCart();
            }
        } else {
            OledrionUtility::redirect(_OLEDRION_PROBLEM_QTY, 'index.php', 5); // Plus de stock !
        }
        listCart();
        break;

    // ****************************************************************************************************************
    case 'empty': // Suppression du contenu du caddy
        // ****************************************************************************************************************
        $h_oledrion_caddy->emptyCart();
        listCart();
        break;

    // ****************************************************************************************************************
    case 'default': // Action par défaut
        // ****************************************************************************************************************
        listCart();
        break;
}

// Image icons
if (file_exists(OLEDRION_PATH . 'language/' . $xoopsConfig['language'] . '/image/step1.png')) {
    $step1 = OLEDRION_URL . 'language/' . $xoopsConfig['language'] . '/image/step1.png';
    $step2 = OLEDRION_URL . 'language/' . $xoopsConfig['language'] . '/image/step2.png';
    $step3 = OLEDRION_URL . 'language/' . $xoopsConfig['language'] . '/image/step3.png';
} else { // Fallback
    $step1 = OLEDRION_URL . 'language/english/image/step1.png';
    $step2 = OLEDRION_URL . 'language/english/image/step2.png';
    $step3 = OLEDRION_URL . 'language/english/image/step3.png';
}
$xoopsTpl->assign('step1', $step1);
$xoopsTpl->assign('step2', $step2);
$xoopsTpl->assign('step3', $step3);

OledrionUtility::setCSS();
OledrionUtility::setLocalCSS($xoopsConfig['language']);
OledrionUtility::loadLanguageFile('modinfo.php');

$xoopsTpl->assign('breadcrumb', OledrionUtility::breadcrumb([OLEDRION_URL . basename(__FILE__) => _MI_OLEDRION_SMNAME1]));

$title = _MI_OLEDRION_SMNAME1 . ' - ' . OledrionUtility::getModuleName();
OledrionUtility::setMetas($title, $title);
require_once XOOPS_ROOT_PATH . '/footer.php';
