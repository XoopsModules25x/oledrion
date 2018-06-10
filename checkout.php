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
 * Saisie des données du client + affichage des informations saisies pour validation avec redirection vers la passerelle de paiement
 */

use XoopsModules\Oledrion;
use XoopsModules\Oledrion\Constants;

require_once __DIR__ . '/header.php';
$GLOBALS['current_category']             = -1;
$GLOBALS['xoopsOption']['template_main'] = 'oledrion_command.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
require_once XOOPS_ROOT_PATH . '/class/tree.php';
// require_once OLEDRION_PATH . 'class/Registryfile.php';

// Get user id
$uid = Oledrion\Utility::getCurrentUserID();
// Get checkout level
$checkout_level = Oledrion\Utility::getModuleOption('checkout_level');
// Passage de commandes réservé aux utilisateurs enregistrés
if (1 == Oledrion\Utility::getModuleOption('restrict_orders', false) && 0 == $uid) {
    $registry = new Oledrion\Registryfile();
    $text     = $registry->getfile(OLEDRION_TEXTFILE5);
    Oledrion\Utility::redirect(xoops_trim($text), 'index.php', 5);
}
// Get option
$op = \Xmf\Request::getCmd('op', 'default');
// Get action
$action = 'default';
if (\Xmf\Request::hasVar('action', 'POST')) {
    $action = $_POST['action'];
} elseif (\Xmf\Request::hasVar('action', 'GET')) {
    $action = $_GET['action'];
}
// Get commend id
if (\Xmf\Request::hasVar('commend_id', 'POST')) {
    $commend_id = \Xmf\Request::getInt('commend_id', 0, 'POST');
} else {
    $commend_id = \Xmf\Request::getInt('commend_id', 0, 'GET');
}

$xoopsTpl->assign('op', $op);
$xoopsTpl->assign('mod_pref', $mod_pref);
$cartForTemplate      = [];
$emptyCart            = false;
$shippingAmount       = $commandAmount = $vatAmount = $commandAmountTTC = $discountsCount = $commandAmountVAT = 0;
$goOn                 = '';
$discountsDescription = [];
// B.R. New
$checkoutAttributes = [];
// Assume optional checkout steps skipped (listCart updates)
$checkoutAttributes['skip_packing']  = 1;
$checkoutAttributes['skip_location'] = 1;
$checkoutAttributes['skip_delivery'] = 1;
// B.R. End

function listCart()
{
    // B.R. global $cartForTemplate, $emptyCart, $shippingAmount, $commandAmount, $vatAmount, $goOn, $commandAmountTTC, $discountsDescription;
    global $cartForTemplate, $emptyCart, $shippingAmount, $commandAmount, $vatAmount, $goOn, $commandAmountTTC, $discountsDescription, $checkoutAttributes;
    $reductions = new Oledrion\Reductions();
    // B.R. $reductions->computeCart($cartForTemplate, $emptyCart, $shippingAmount, $commandAmount, $vatAmount, $goOn, $commandAmountTTC, $discountsDescription, $discountsCount);
    $reductions->computeCart($cartForTemplate, $emptyCart, $shippingAmount, $commandAmount, $vatAmount, $goOn, $commandAmountTTC, $discountsDescription, $discountsCount, $checkoutAttributes);
}

$oledrionCurrency = Oledrion\Currency::getInstance();
$countries         = Oledrion\Utility::getCountriesList();

// B.R. New
listCart();
// End New

switch ($op) {
    case 'save':
        if (empty($_POST)) {
            Oledrion\Utility::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
        }
        if ($caddyHandler->isCartEmpty()) {
            Oledrion\Utility::redirect(_OLEDRION_CART_IS_EMPTY, OLEDRION_URL, 4);
        }
        // B.R. listCart();

        switch ($action) {
            case 'make':
                $commandAmountTTC += $commandAmountVAT;
                $password         = md5(xoops_makepass());
                $passwordCancel   = md5(xoops_makepass());
                $commande         = $commandsHandler->create(true);
                $commande->setVars($_POST);
                $commande->setVar('cmd_uid', $uid);
                $commande->setVar('cmd_date', date('Y-m-d'));
                $commande->setVar('cmd_create', time());
                $commande->setVar('cmd_state', Constants::OLEDRION_STATE_NOINFORMATION);
                $commande->setVar('cmd_ip', Oledrion\Utility::IP());
                $commande->setVar('cmd_articles_count', count($cartForTemplate));
                $commande->setVar('cmd_total', Oledrion\Utility::formatFloatForDB($commandAmountTTC));
                $commande->setVar('cmd_shipping', Oledrion\Utility::formatFloatForDB($shippingAmount));
                $commande->setVar('cmd_password', $password);
                $commande->setVar('cmd_cancel', $passwordCancel);
                $commande->setVar('cmd_text', implode("\n", $discountsDescription));
                $commande->setVar('cmd_status', 2);
                $res = $commandsHandler->insert($commande, true);
                if (!$res) {
                    Oledrion\Utility::redirect(_OLEDRION_ERROR10, OLEDRION_URL, 6);
                }
                // Check checkout level
                if (1 == $checkout_level) {
                    Oledrion\Utility::redirect(_OLEDRION_FINAL_CHECKOUT, OLEDRION_URL . 'checkout.php?op=confirm&commend_id=' . $commande->getVar('cmd_id'), 1);
                } elseif (2 == $checkout_level) {
                    Oledrion\Utility::redirect(_OLEDRION_SELECT_LOCATION, OLEDRION_URL . 'checkout.php?op=location&commend_id=' . $commande->getVar('cmd_id'), 1);
                } else {
                    Oledrion\Utility::redirect(_OLEDRION_SELECT_PACKING, OLEDRION_URL . 'checkout.php?op=packing&commend_id=' . $commande->getVar('cmd_id'), 1);
                }
                break;

            case 'find':
                if (0 == $commend_id) {
                    Oledrion\Utility::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
                }
                $commandAmountTTC += $commandAmountVAT;
                $commande         = $commandsHandler->get($commend_id);
                $commande->setVars($_POST);
                $commande->setVar('cmd_state', Constants::OLEDRION_STATE_NOINFORMATION);
                $commande->setVar('cmd_ip', Oledrion\Utility::IP());
                $commande->setVar('cmd_articles_count', count($cartForTemplate));
                $commande->setVar('cmd_total', Oledrion\Utility::formatFloatForDB($commandAmountTTC));
                $commande->setVar('cmd_shipping', Oledrion\Utility::formatFloatForDB($shippingAmount));
                $commande->setVar('cmd_text', implode("\n", $discountsDescription));
                $commande->setVar('cmd_status', 2);
                $res = $commandsHandler->insert($commande, true);
                if (!$res) {
                    Oledrion\Utility::redirect(_OLEDRION_ERROR10, OLEDRION_URL, 6);
                }
                Oledrion\Utility::redirect(_OLEDRION_SELECT_PACKING, OLEDRION_URL . 'checkout.php?op=packing&commend_id=' . $commande->getVar('cmd_id'), 1);
                break;

            case 'packing':

                $packing_id = \Xmf\Request::getInt('packing_id', 0, 'POST');

                if (0 == $packing_id) {
                    Oledrion\Utility::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
                }
                if (0 == $commend_id) {
                    Oledrion\Utility::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
                }
                // Check checkout level
                if (1 == $checkout_level) {
                    Oledrion\Utility::redirect(_OLEDRION_FINAL_CHECKOUT, OLEDRION_URL . 'checkout.php?op=confirm&commend_id=' . $commend_id, 1);
                } elseif (2 == $checkout_level) {
                    Oledrion\Utility::redirect(_OLEDRION_SELECT_LOCATION, OLEDRION_URL . 'checkout.php?op=location&commend_id=' . $commend_id, 1);
                }

                $packing = $packingHandler->get($packing_id);
                if (!$packing->getVar('packing_id')) {
                    Oledrion\Utility::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
                }
                $commande = $commandsHandler->get($commend_id);
                $commande->setVar('cmd_packing', $packing->getVar('packing_title'));
                $commande->setVar('cmd_packing_id', $packing->getVar('packing_id'));
                $commande->setVar('cmd_packing_price', $packing->getVar('packing_price'));
                $res = $commandsHandler->insert($commande, true);
                if (!$res) {
                    Oledrion\Utility::redirect(_OLEDRION_ERROR10, OLEDRION_URL, 6);
                }
                Oledrion\Utility::redirect(_OLEDRION_SELECT_LOCATION, OLEDRION_URL . 'checkout.php?op=location&commend_id=' . $commande->getVar('cmd_id'), 1);
                break;

            case 'location':

                $location_id = \Xmf\Request::getInt('location_id', 0, 'POST');

                if (0 == $location_id) {
                    Oledrion\Utility::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
                }
                if (0 == $commend_id) {
                    Oledrion\Utility::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
                }
                // Check checkout level
                if (1 == $checkout_level) {
                    Oledrion\Utility::redirect(_OLEDRION_FINAL_CHECKOUT, OLEDRION_URL . 'checkout.php?op=confirm&commend_id=' . $commend_id, 1);
                }
                $location = $locationHandler->get($location_id);
                $commande = $commandsHandler->get($commend_id);
                $commande->setVar('cmd_location', $location->getVar('location_title'));
                $commande->setVar('cmd_location_id', $location->getVar('location_id'));
                $res = $commandsHandler->insert($commande, true);
                if (!$res) {
                    Oledrion\Utility::redirect(_OLEDRION_ERROR10, OLEDRION_URL, 6);
                }

                if ($locationHandler->haveChild($location->getVar('location_id'))) {
                    Oledrion\Utility::redirect(_OLEDRION_SELECT_LOCATION, OLEDRION_URL . 'checkout.php?op=location&action=select&commend_id=' . $commande->getVar('cmd_id'), 1);
                } else {
                    Oledrion\Utility::redirect(_OLEDRION_SELECT_DELIVERY, OLEDRION_URL . 'checkout.php?op=delivery&commend_id=' . $commande->getVar('cmd_id'), 1);
                }
                break;

            case 'delivery':

                $delivery_id = \Xmf\Request::getInt('delivery_id', 0, 'POST');

                if (0 == $delivery_id) {
                    Oledrion\Utility::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
                }
                if (0 == $commend_id) {
                    Oledrion\Utility::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
                }
                // Check checkout level
                if (1 == $checkout_level) {
                    Oledrion\Utility::redirect(_OLEDRION_FINAL_CHECKOUT, OLEDRION_URL . 'checkout.php?op=confirm&commend_id=' . $commend_id, 1);
                }
                $commande = $commandsHandler->get($commend_id);
                $delivery = $deliveryHandler->getThisLocationThisDelivery($delivery_id, $commande->getVar('cmd_location_id'));

                $shipping_price    = '';
                $shipping_price_op = Oledrion\Utility::getModuleOption('checkout_shipping', false);
                switch ($shipping_price_op) {
                    case 1:
                        $shipping_price = $shippingAmount + $delivery['delivery_price'];
                        break;

                    case 2:
                        $shipping_price = $shippingAmount;
                        break;

                    case 3:
                        $shipping_price = $delivery['delivery_price'];
                        break;

                    case 4:
                        $shipping_price = 0;
                        break;
                }
                $commande->setVar('cmd_delivery', $delivery['delivery_title']);
                $commande->setVar('cmd_delivery_id', $delivery['delivery_id']);
                $commande->setVar('cmd_shipping', $shipping_price);
                $res = $commandsHandler->insert($commande, true);
                if (!$res) {
                    Oledrion\Utility::redirect(_OLEDRION_ERROR10, OLEDRION_URL, 6);
                }
                Oledrion\Utility::redirect(_OLEDRION_SELECT_PAYMENT, OLEDRION_URL . 'checkout.php?op=payment&commend_id=' . $commande->getVar('cmd_id'), 1);
                break;

            case 'payment':

                $payment_id = \Xmf\Request::getInt('payment_id', 0, 'POST');

                if (0 == $payment_id) {
                    Oledrion\Utility::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
                }
                if (0 == $commend_id) {
                    Oledrion\Utility::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
                }
                // Check checkout level
                if (1 == $checkout_level) {
                    Oledrion\Utility::redirect(_OLEDRION_FINAL_CHECKOUT, OLEDRION_URL . 'checkout.php?op=confirm&commend_id=' . $commend_id, 1);
                }
                $payment  = $paymentHandler->get($payment_id);
                $commande = $commandsHandler->get($commend_id);
                $commande->setVar('cmd_payment', $payment->getVar('payment_title'));
                $commande->setVar('cmd_payment_id', $payment->getVar('payment_id'));
                $res = $commandsHandler->insert($commande, true);
                if (!$res) {
                    Oledrion\Utility::redirect(_OLEDRION_ERROR10, OLEDRION_URL, 6);
                }
                Oledrion\Utility::redirect(_OLEDRION_FINAL_CHECKOUT, OLEDRION_URL . 'checkout.php?op=confirm&commend_id=' . $commande->getVar('cmd_id'), 1);
                break;
        }

        break;

    // ****************************************************************************************************************
    case 'default':
        // Présentation du formulaire
        // ****************************************************************************************************************
        if ($caddyHandler->isCartEmpty()) {
            Oledrion\Utility::redirect(_OLEDRION_CART_IS_EMPTY, OLEDRION_URL, 4);
        }
        // B.R. listCart();
        $notFound = true;
        $commande = null;

        if ($uid > 0) {
            // Si c'est un utlisateur enregistré, on recherche dans les anciennes commandes pour pré-remplir les champs
            $commande = $commandsHandler->getLastUserOrder($uid);
            if (is_object($commande)) {
                $notFound = false;
            }
        }

        if ($notFound) {
            $commande = $commandsHandler->create(true);
            $commande->setVar('cmd_country', OLEDRION_DEFAULT_COUNTRY);
        }

        // texte à afficher
        $registry = new Oledrion\Registryfile();
        $text     = $registry->getfile(OLEDRION_TEXTFILE6);
        $xoopsTpl->assign('text', xoops_trim($text));

        $sform = new \XoopsThemeForm(_OLEDRION_PLEASE_ENTER, 'informationfrm', OLEDRION_URL . 'checkout.php', 'post', true);
        $sform->addElement(new \XoopsFormHidden('op', 'save'));
        if ($commande->getVar('cmd_id') && $commande->getVar('cmd_id') > 0) {
            $sform->addElement(new \XoopsFormHidden('action', 'find'));
            $sform->addElement(new \XoopsFormHidden('commend_id', $commande->getVar('cmd_id')));
        } else {
            $sform->addElement(new \XoopsFormHidden('action', 'make'));
        }
        $sform->addElement(new \XoopsFormLabel(_OLEDRION_TOTAL, $oledrionCurrency->amountForDisplay($commandAmountTTC)));
        // By voltan
        if (in_array(Oledrion\Utility::getModuleOption('checkout_shipping'), [1, 2]) && $shippingAmount > 0) {
            $sform->addElement(new \XoopsFormLabel(_OLEDRION_SHIPPING_PRICE, $oledrionCurrency->amountForDisplay($shippingAmount)));
        }
        $sform->addElement(new \XoopsFormText(_OLEDRION_LASTNAME, 'cmd_lastname', 50, 255, $commande->getVar('cmd_lastname', 'e')), true);
        // B.R. New
        if (0 == $checkoutAttributes['skip_delivery']) {
            // Assume that select delivery implies also need first name, physical address and phone numbers
            $mandatory = true;
        } else {
            $mandatory = false;
        }
        // B.R. $sform->addElement(new \XoopsFormText(_OLEDRION_FIRSTNAME, 'cmd_firstname', 50, 255, $commande->getVar('cmd_firstname', 'e')), false);
        $sform->addElement(new \XoopsFormText(_OLEDRION_FIRSTNAME, 'cmd_firstname', 50, 255, $commande->getVar('cmd_firstname', 'e')), $mandatory);
        if ($uid > 0) {
            $sform->addElement(new \XoopsFormText(_OLEDRION_EMAIL, 'cmd_email', 50, 255, $xoopsUser->getVar('email', 'e')), true);
        } else {
            $sform->addElement(new \XoopsFormText(_OLEDRION_EMAIL, 'cmd_email', 50, 255, ''), true);
        }
        $sform->addElement(new \XoopsFormText(_OLEDRION_CITY, 'cmd_town', 50, 255, $commande->getVar('cmd_town', 'e')), true);
        // By voltan
        if (Oledrion\Utility::getModuleOption('checkout_country')) {
            $countriesList = new \XoopsFormSelect(_OLEDRION_COUNTRY, 'cmd_country', $commande->getVar('cmd_country', ' e'));
            $countriesList->addOptionArray($countries);
            $sform->addElement($countriesList, true);
        } else {
            $sform->addElement(new \XoopsFormHidden('cmd_country', OLEDRION_DEFAULT_COUNTRY));
        }
        $sform->addElement(new \XoopsFormText(_OLEDRION_CP, 'cmd_zip', 15, 30, $commande->getVar('cmd_zip', 'e')), true);
        // B.R. $sform->addElement(new \XoopsFormText(_OLEDRION_MOBILE, 'cmd_mobile', 15, 50, $commande->getVar('cmd_mobile', 'e')), true);
        // B.R. $sform->addElement(new \XoopsFormText(_OLEDRION_PHONE, 'cmd_telephone', 15, 50, $commande->getVar('cmd_telephone', 'e')), true);
        $sform->addElement(new \XoopsFormText(_OLEDRION_MOBILE, 'cmd_mobile', 15, 50, $commande->getVar('cmd_mobile', 'e')), $mandatory);
        $sform->addElement(new \XoopsFormText(_OLEDRION_PHONE, 'cmd_telephone', 15, 50, $commande->getVar('cmd_telephone', 'e')), $mandatory);
        if (Oledrion\Utility::getModuleOption('ask_vatnumber')) {
            $sform->addElement(new \XoopsFormText(_OLEDRION_VAT_NUMBER, 'cmd_vat_number', 50, 255, $commande->getVar('cmd_vat_number', 'e')), false);
        }
        if (Oledrion\Utility::getModuleOption('ask_bill')) {
            // B.R. $sform->addElement(new \XoopsFormRadioYN(_OLEDRION_INVOICE, 'cmd_bill', 0), true);
            $sform->addElement(new \XoopsFormRadioYN(_OLEDRION_INVOICE, 'cmd_bill', 0), false);
        }
        // B.R. $sform->addElement(new XoopsFormTextArea(_OLEDRION_STREET, 'cmd_adress', $commande->getVar('cmd_adress', 'e'), 3, 50), true);
        $sform->addElement(new \XoopsFormTextArea(_OLEDRION_STREET, 'cmd_adress', $commande->getVar('cmd_adress', 'e'), 3, 50), $mandatory);
        $sform->addElement(new \XoopsFormText(_OLEDRION_GIFT, 'cmd_gift', 15, 30, $commande->getVar('cmd_gift', 'e')), false);
        $button_tray = new \XoopsFormElementTray('', '');
        $submit_btn  = new \XoopsFormButton('', 'post', _OLEDRION_SAVE_NEXT, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        $sform = Oledrion\Utility::formMarkRequiredFields($sform);
        $xoopsTpl->assign('form', $sform->render());
        break;

    case 'packing':
        if ($caddyHandler->isCartEmpty()) {
            Oledrion\Utility::redirect(_OLEDRION_CART_IS_EMPTY, OLEDRION_URL, 4);
        }
        if (0 == $commend_id) {
            Oledrion\Utility::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
        }
        // Check checkout level
        if (1 == $checkout_level) {
            Oledrion\Utility::redirect(_OLEDRION_FINAL_CHECKOUT, OLEDRION_URL . 'checkout.php?op=confirm&commend_id=' . $commend_id, 1);
        // B.R. Start
        } elseif (1 == $checkoutAttributes['skip_packing']) {
            Oledrion\Utility::redirect(_OLEDRION_SELECT_LOCATION, OLEDRION_URL . 'checkout.php?op=location&commend_id=' . $commend_id, 1);
        // B.R. End
        } elseif (2 == $checkout_level) {
            Oledrion\Utility::redirect(_OLEDRION_SELECT_LOCATION, OLEDRION_URL . 'checkout.php?op=location&commend_id=' . $commend_id, 1);
        }
        // B.R. listCart();
        $packings = $packingHandler->getPacking();

        $sform = new \XoopsThemeForm(_OLEDRION_PACKING_FORM, 'informationfrm', OLEDRION_URL . 'checkout.php', 'post', true);
        $sform->addElement(new \XoopsFormHidden('op', 'save'));
        $sform->addElement(new \XoopsFormHidden('action', 'packing'));
        $sform->addElement(new \XoopsFormHidden('commend_id', $commend_id));
        $packingSelect = new \XoopsFormRadio(_OLEDRION_SELECT_PACKING, 'packing_id', '');
        foreach ($packings as $packing) {
            $packingSelect->addOption($packing['packing_id'], Oledrion\Utility::packingHtmlSelect($packing));
        }
        $sform->addElement($packingSelect, true);
        $sform->addElement(new \XoopsFormButton('', 'post', _OLEDRION_SAVE_NEXT, 'submit'));
        $sform = Oledrion\Utility::formMarkRequiredFields($sform);
        $xoopsTpl->assign('form', $sform->render());

        // texte à afficher
        $registry = new Oledrion\Registryfile();
        $text     = $registry->getfile(OLEDRION_TEXTFILE6);
        $xoopsTpl->assign('text', xoops_trim($text));
        break;

    case 'location':
        if ($caddyHandler->isCartEmpty()) {
            Oledrion\Utility::redirect(_OLEDRION_CART_IS_EMPTY, OLEDRION_URL, 4);
        }
        if (0 == $commend_id) {
            Oledrion\Utility::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
        }
        // Check checkout level
        if (1 == $checkout_level) {
            Oledrion\Utility::redirect(_OLEDRION_FINAL_CHECKOUT, OLEDRION_URL . 'checkout.php?op=confirm&commend_id=' . $commend_id, 1);
        // B.R. Start
        } elseif (1 == $checkoutAttributes['skip_location']) {
            //$commande = $h_oledrion_commands->get($commend_id);
            //Oledrion\Utility::redirect(_OLEDRION_SELECT_DELIVERY, OLEDRION_URL . 'checkout.php?op=delivery&commend_id=' . $commande->getVar('cmd_id'), 1);
            Oledrion\Utility::redirect(_OLEDRION_SELECT_DELIVERY, OLEDRION_URL . 'checkout.php?op=delivery&commend_id=' . $commend_id, 1);
            // B.R. End
        }
        // B.R. listCart();
        switch ($action) {
            case 'default':
                $sform = new \XoopsThemeForm(_OLEDRION_LOCATION_FORM, 'informationfrm', OLEDRION_URL . 'checkout.php', 'post', true);
                $sform->addElement(new \XoopsFormHidden('op', 'save'));
                $sform->addElement(new \XoopsFormHidden('action', 'location'));
                $sform->addElement(new \XoopsFormHidden('commend_id', $commend_id));
                $pids         = $locationHandler->getAllPid(new Oledrion\Parameters());
                $location_pid = new \XoopsFormRadio(_OLEDRION_SELECT_LOCATION, 'location_id');
                foreach ($pids as $pid) {
                    $location_pid->addOption($pid->getVar('location_id'), $pid->getVar('location_title'));
                }
                $sform->addElement($location_pid, true);
                $sform->addElement(new \XoopsFormButton('', 'post', _OLEDRION_SAVE_NEXT, 'submit'));
                $sform = Oledrion\Utility::formMarkRequiredFields($sform);
                $xoopsTpl->assign('form', $sform->render());
                break;

            case 'select':
                $commande = $commandsHandler->get($commend_id);
                $sform    = new \XoopsThemeForm(_OLEDRION_LOCATION_FORM, 'informationfrm', OLEDRION_URL . 'checkout.php', 'post', true);
                $sform->addElement(new \XoopsFormHidden('op', 'save'));
                $sform->addElement(new \XoopsFormHidden('action', 'location'));
                $sform->addElement(new \XoopsFormHidden('commend_id', $commend_id));
                $locations       = $locationHandler->getLocation($commande->getVar('cmd_location_id'));
                $location_select = new \XoopsFormSelect(_OLEDRION_SELECT_LOCATION, 'location_id', '');
                foreach ($locations as $location) {
                    $location_select->addOption($location->getVar('location_id'), $location->getVar('location_title'));
                }
                $sform->addElement($location_select, true);
                $sform->addElement(new \XoopsFormButton('', 'post', _OLEDRION_SAVE_NEXT, 'submit'));
                $sform = Oledrion\Utility::formMarkRequiredFields($sform);
                $xoopsTpl->assign('form', $sform->render());
                break;
        }

        // texte à afficher
        $registry = new Oledrion\Registryfile();
        $text     = $registry->getfile(OLEDRION_TEXTFILE6);
        $xoopsTpl->assign('text', xoops_trim($text));
        break;

    case 'delivery':
        if ($caddyHandler->isCartEmpty()) {
            Oledrion\Utility::redirect(_OLEDRION_CART_IS_EMPTY, OLEDRION_URL, 4);
        }
        if (0 == $commend_id) {
            Oledrion\Utility::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
        }
        // Check checkout level
        if (1 == $checkout_level) {
            Oledrion\Utility::redirect(_OLEDRION_FINAL_CHECKOUT, OLEDRION_URL . 'checkout.php?op=confirm&commend_id=' . $commend_id, 1);
        // B.R. Start
        } elseif (1 == $checkoutAttributes['skip_delivery']) {
            //$commande = $h_oledrion_commands->get($commend_id);
            //Oledrion\Utility::redirect(_OLEDRION_SELECT_PAYMENT, OLEDRION_URL . 'checkout.php?op=payment&commend_id=' . $commande->getVar('cmd_id'), 1);
            Oledrion\Utility::redirect(_OLEDRION_SELECT_PAYMENT, OLEDRION_URL . 'checkout.php?op=payment&commend_id=' . $commend_id, 1);
            // B.R. End
        }
        // B.R. listCart();
        $commande    = $commandsHandler->get($commend_id);
        $location_id = $commande->getVar('cmd_location_id');
        $deliveres   = $deliveryHandler->getThisLocationDelivery($location_id);

        $sform = new \XoopsThemeForm(_OLEDRION_DELIVERY_FORM, 'informationfrm', OLEDRION_URL . 'checkout.php', 'post', true);
        $sform->addElement(new \XoopsFormHidden('op', 'save'));
        $sform->addElement(new \XoopsFormHidden('action', 'delivery'));
        $sform->addElement(new \XoopsFormHidden('commend_id', $commend_id));
        $delivery_options = new \XoopsFormRadio(_OLEDRION_SELECT_DELIVERY, 'delivery_id');
        foreach ($deliveres as $delivery) {
            $delivery_options->addOption($delivery['delivery_id'], Oledrion\Utility::deliveryHtmlSelect($delivery));
        }
        $sform->addElement($delivery_options, true);
        $sform->addElement(new \XoopsFormButton('', 'post', _OLEDRION_SAVE_NEXT, 'submit'));
        $sform = Oledrion\Utility::formMarkRequiredFields($sform);
        $xoopsTpl->assign('form', $sform->render());

        // texte à afficher
        $registry = new Oledrion\Registryfile();
        $text     = $registry->getfile(OLEDRION_TEXTFILE6);
        $xoopsTpl->assign('text', xoops_trim($text));
        break;

    case 'payment':
        if ($caddyHandler->isCartEmpty()) {
            Oledrion\Utility::redirect(_OLEDRION_CART_IS_EMPTY, OLEDRION_URL, 4);
        }
        if (0 == $commend_id) {
            Oledrion\Utility::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
        }
        // Check checkout level
        if (1 == $checkout_level) {
            Oledrion\Utility::redirect(_OLEDRION_FINAL_CHECKOUT, OLEDRION_URL . 'checkout.php?op=confirm&commend_id=' . $commend_id, 1);
        }
        // B.R. listCart();
        // B.R. Start
        $commande    = $commandsHandler->get($commend_id);
        if (1 == $checkoutAttributes['skip_delivery']) {
            // Assumes first deliery method is free shipping (else, why skip?)
            // TODO: Consider pre-configuring free shipping as #1
            $delivery_id = 1;
        } else {
            // B.R. End
            $delivery_id = $commande->getVar('cmd_delivery_id');
        }
        $payments    = $paymentHandler->getThisDeliveryPayment($delivery_id);

        $sform = new \XoopsThemeForm(_OLEDRION_PAYMENT_FORM, 'informationfrm', OLEDRION_URL . 'checkout.php', 'post', true);
        $sform->addElement(new \XoopsFormHidden('op', 'save'));
        $sform->addElement(new \XoopsFormHidden('action', 'payment'));
        $sform->addElement(new \XoopsFormHidden('commend_id', $commend_id));
        $payment_options = new \XoopsFormRadio(_OLEDRION_SELECT_PAYMENT, 'payment_id');
        foreach ($payments as $payment) {
            $payment_options->addOption($payment['payment_id'], Oledrion\Utility::paymentHtmlSelect($payment));
        }
        $sform->addElement($payment_options, true);
        $sform->addElement(new \XoopsFormButton('', 'post', _OLEDRION_SAVE_CONFIRM, 'submit'));
        $sform = Oledrion\Utility::formMarkRequiredFields($sform);
        $xoopsTpl->assign('form', $sform->render());

        // texte à afficher
        $registry = new Oledrion\Registryfile();
        $text     = $registry->getfile(OLEDRION_TEXTFILE6);
        $xoopsTpl->assign('text', xoops_trim($text));
        break;

    // ****************************************************************************************************************
    case 'confirm':
        // Validation finale avant envoi sur la passerelle de paiement (ou arrêt)
        // ****************************************************************************************************************
        if ($caddyHandler->isCartEmpty()) {
            Oledrion\Utility::redirect(_OLEDRION_CART_IS_EMPTY, OLEDRION_URL, 4);
        }
        if (0 == $commend_id) {
            Oledrion\Utility::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
        }
        // B.R. listCart();

        $commandAmountTTC += $commandAmountVAT;

        $commande = $commandsHandler->get($commend_id);
        if (1 == $commande->getVar('cmd_status')) {
            Oledrion\Utility::redirect(_OLEDRION_ERROR10, OLEDRION_URL . 'invoice.php?id=' . $commande->getVar('cmd_id') . '&pass=' . $commande->getVar('cmd_password'), 6);
        }
        $commande->setVar('cmd_create', time());
        $commande->setVar('cmd_date', date('Y-m-d'));
        $commande->setVar('cmd_state', Constants::OLEDRION_STATE_NOINFORMATION);
        $commande->setVar('cmd_ip', Oledrion\Utility::IP());
        $commande->setVar('cmd_status', 1);
        $res = $commandsHandler->insert($commande, true);
        if (!$res) {
            Oledrion\Utility::redirect(_OLEDRION_ERROR10, OLEDRION_URL, 6);
        }

        // Save command and empty cart
        $caddyHandler->emptyCart();

        // Enregistrement du panier
        $msgCommande = '';
        //        $handlers    = HandlerManager::getInstance();
        foreach ($cartForTemplate as $line) {
            $panier = $caddyHandler->create(true);
            $panier->setVar('caddy_product_id', $line['product_id']);
            $panier->setVar('caddy_qte', $line['product_qty']);
            $panier->setVar('caddy_price', Oledrion\Utility::formatFloatForDB($line['totalPrice']));
            // Attention, prix TTC avec frais de port
            $panier->setVar('caddy_cmd_id', $commande->getVar('cmd_id'));
            $panier->setVar('caddy_shipping', Oledrion\Utility::formatFloatForDB($line['discountedShipping']));
            $panier->setVar('caddy_pass', md5(xoops_makepass()));
            // Pour le téléchargement
            $res = $caddyHandler->insert($panier, true);
            // Make msg
            $cat         = $categoryHandler->get($line['product_cid'])->toArray();
            $msgCommande .= str_pad($line['product_id'], 5, ' ') . ' ';
            $msgCommande .= str_pad($cat['cat_title'], 10, ' ', STR_PAD_LEFT) . ' ';
            $msgCommande .= str_pad($line['product_title'], 19, ' ', STR_PAD_LEFT) . ' ';
            $msgCommande .= str_pad($line['product_qty'], 8, ' ', STR_PAD_LEFT) . ' ';
            $msgCommande .= str_pad($oledrionCurrency->amountForDisplay($line['product_price']), 15, ' ', STR_PAD_LEFT) . ' ';
            //$msgCommande .= str_pad($line['totalPriceFormated'],10,' ', STR_PAD_LEFT) . ' ';
            $msgCommande .= "\n";
            // Attributs
            if ($res && is_array($line['attributes']) && count($line['attributes']) > 0) {
                // Enregistrement des attributs pour ce produit
                foreach ($line['attributes'] as $attributeId => $attributeInformation) {
                    $caddyAttribute = $caddyAttributesHandler->create(true);
                    $caddyAttribute->setVar('ca_cmd_id', $commande->getVar('cmd_id'));
                    $caddyAttribute->setVar('ca_caddy_id', $panier->getVar('caddy_id'));
                    $caddyAttribute->setVar('ca_attribute_id', $attributeId);
                    $selectedOptions = $attributeInformation['attribute_options'];
                    $msgCommande     .= '- ' . $attributeInformation['attribute_title'] . "\n";
                    foreach ($selectedOptions as $selectedOption) {
                        $caddyAttribute->addOption($selectedOption['option_name'], $selectedOption['option_value'], $selectedOption['option_price']);
                        $msgCommande .= '    ' . $selectedOption['option_name'] . ' : ' . $selectedOption['option_ttc_formated'] . "\n";
                    }
                    $caddyAttributesHandler->insert($caddyAttribute, true);
                }
            }
        }

        // Totaux généraux
        //$msgCommande .= "\n\n"._OLEDRION_SHIPPING_PRICE.' '.$oledrionCurrency->amountForDisplay($shippingAmount)."\n";
        $msgCommande .= "\n\n" . _OLEDRION_TOTAL . ' ' . $oledrionCurrency->amountForDisplay($commandAmountTTC) . "\n";
        if (count($discountsDescription) > 0) {
            $msgCommande .= "\n\n" . _OLEDRION_CART4 . "\n";
            $msgCommande .= implode("\n", $discountsDescription);
            $msgCommande .= "\n";
        }

        $msg                 = [];
        $msg['COMMANDE']     = $msgCommande;
        $msg['NUM_COMMANDE'] = $commande->getVar('cmd_id');
        $msg['NOM']          = $commande->getVar('cmd_lastname');
        $msg['PRENOM']       = $commande->getVar('cmd_firstname');
        $msg['ADRESSE']      = $commande->getVar('cmd_adress', 'n');
        $msg['CP']           = $commande->getVar('cmd_zip');
        $msg['VILLE']        = $commande->getVar('cmd_town');
        $msg['PAYS']         = $countries[$commande->getVar('cmd_country')];
        $msg['TELEPHONE']    = $commande->getVar('cmd_telephone');
        $msg['EMAIL']        = $commande->getVar('cmd_email');
        $msg['URL_BILL']     = OLEDRION_URL . 'invoice.php?id=' . $commande->getVar('cmd_id') . '&pass=' . $commande->getVar('cmd_password');
        $msg['IP']           = Oledrion\Utility::IP();
        if (1 == $commande->getVar('cmd_bill')) {
            $msg['FACTURE'] = _YES;
        } else {
            $msg['FACTURE'] = _NO;
        }
        // Send mail to client
        // B.R. New Rather than sending message before payment approval, save parameters in OLEDRION_UPLOAD_PATH/${cmd_id}_conf_email.serialize
        // TODO: Make a configuration option?
        // Then, based on payment approval / disapproval, send email at payment gatewayNotify callback
        $email_name = sprintf('%s/%d%s', OLEDRION_UPLOAD_PATH, $commande->getVar('cmd_id'), OLEDRION_CONFIRMATION_EMAIL_FILENAME_SUFFIX);
        file_put_contents($email_name, serialize($msg));
        //Oledrion\Utility::sendEmailFromTpl('command_client.tpl', $commande->getVar('cmd_email'), sprintf(_OLEDRION_THANKYOU_CMD, $xoopsConfig['sitename']), $msg);
        // Send mail to admin
        //Oledrion\Utility::sendEmailFromTpl('command_shop.tpl', Oledrion\Utility::getEmailsFromGroup(Oledrion\Utility::getModuleOption('grp_sold')), _OLEDRION_NEW_COMMAND, $msg);
        // End New

        // Présentation du formulaire pour envoi à la passerelle de paiement
        // Présentation finale avec panier en variables cachées ******************************
        $registry = new Oledrion\Registryfile();
        $text     = $registry->getfile(OLEDRION_TEXTFILE7);
        $xoopsTpl->assign('text', xoops_trim($text));

        if (1 == $checkout_level) {
            $text = $registry->getfile(OLEDRION_TEXTFILE4);
            $xoopsTpl->append('text', '<br>' . xoops_trim($text));
            $payURL = OLEDRION_URL . 'invoice.php?id=' . $commande->getVar('cmd_id') . '&pass=' . $commande->getVar('cmd_password');
            $sform  = new \XoopsThemeForm(_OLEDRION_FINISH, 'payform', $payURL, 'post', true);
        } else {
            // B.R. New
            $payment_id = 1; // TODO: figure out how to get
            $payment    = $h_oledrion_payment->get($payment_id);
            // End new
            // B.R. if (!isset($payment) || $payment['payment_type'] === 'offline' || $commandAmountTTC == 0) {
            if (!isset($payment) || 'offline' === $payment->getVar('payment_type') || 0 == $commandAmountTTC) {
                $text = $registry->getfile(OLEDRION_TEXTFILE4);
                $xoopsTpl->append('text', '<br>' . xoops_trim($text));
                $payURL = OLEDRION_URL . 'invoice.php?id=' . $commande->getVar('cmd_id') . '&pass=' . $commande->getVar('cmd_password');
                $sform  = new \XoopsThemeForm(_OLEDRION_FINISH, 'payform', $payURL, 'post', true);
            } else {
                // Set gateway
                // B.R. $gateway = \XoopsModules\Oledrion\Gateways::getGatewayObject($payment['payment_gateway']);
                $gateway = \XoopsModules\Oledrion\Gateways::getGatewayObject($payment->getVar('payment_gateway'));
                if (!is_object($gateway)) {
                    die(_OLEDRION_ERROR20);
                }
                if (is_object($gateway)) {
                    $payURL = $gateway->getRedirectURL($commande->getVar('cmd_total'), $commande->getVar('cmd_id'));
                } else {
                    $payURL = OLEDRION_URL . 'invoice.php?id=' . $commande->getVar('cmd_id') . '&pass=' . $commande->getVar('cmd_password');
                }
                $sform    = new \XoopsThemeForm(_OLEDRION_PAY_GATEWAY, 'payform', $payURL, 'post', true);
                $elements = [];
                if (is_object($gateway)) {
                    $elements = $gateway->getCheckoutFormContent($commande);
                }
                foreach ($elements as $key => $value) {
                    $sform->addElement(new \XoopsFormHidden($key, $value));
                }
            }
        }

        $sform->addElement(new \XoopsFormLabel(_OLEDRION_AMOUNT_PRICE, $oledrionCurrency->amountForDisplay($commandAmountTTC)));
        if ($commande->getVar('cmd_shipping') > 0) {
            $sform->addElement(new \XoopsFormLabel(_OLEDRION_SHIPPING_PRICE, $oledrionCurrency->amountForDisplay($commande->getVar('cmd_shipping'))));
        }
        if ($commande->getVar('cmd_packing_price') > 0) {
            $sform->addElement(new \XoopsFormLabel(_OLEDRION_PACKING_PRICE, $oledrionCurrency->amountForDisplay($commande->getVar('cmd_packing_price'))));
        }
        $sform->addElement(new \XoopsFormLabel(_OLEDRION_TOTAL, $oledrionCurrency->amountForDisplay($commandAmountTTC + $commande->getVar('cmd_shipping') + $commande->getVar('cmd_packing_price'))));
        $sform->addElement(new \XoopsFormLabel(_OLEDRION_LASTNAME, $commande->getVar('cmd_lastname')));
        $sform->addElement(new \XoopsFormLabel(_OLEDRION_FIRSTNAME, $commande->getVar('cmd_firstname')));
        $sform->addElement(new \XoopsFormLabel(_OLEDRION_STREET, $commande->getVar('cmd_adress')));
        $sform->addElement(new \XoopsFormLabel(_OLEDRION_CP, $commande->getVar('cmd_zip')));
        $sform->addElement(new \XoopsFormLabel(_OLEDRION_CITY, $commande->getVar('cmd_town')));
        if (Oledrion\Utility::getModuleOption('checkout_country')) {
            $sform->addElement(new \XoopsFormLabel(_OLEDRION_COUNTRY, $countries[$commande->getVar('cmd_country')]));
        }
        $sform->addElement(new \XoopsFormLabel(_OLEDRION_PHONE, $commande->getVar('cmd_telephone')));
        $sform->addElement(new \XoopsFormLabel(_OLEDRION_MOBILE, $commande->getVar('cmd_mobile')));
        $sform->addElement(new \XoopsFormLabel(_OLEDRION_EMAIL, $commande->getVar('cmd_email')));
        $sform->addElement(new \XoopsFormLabel(_OLEDRION_GIFT, $commande->getVar('cmd_gift')));
        if ($commande->getVar('cmd_packing')) {
            $sform->addElement(new \XoopsFormLabel(_OLEDRION_PACKING, $commande->getVar('cmd_packing')));
        }
        if ($commande->getVar('cmd_location')) {
            $sform->addElement(new \XoopsFormLabel(_OLEDRION_LOCATION, $commande->getVar('cmd_location')));
        }
        if ($commande->getVar('cmd_delivery')) {
            $sform->addElement(new \XoopsFormLabel(_OLEDRION_DELIVERY, $commande->getVar('cmd_delivery')));
        }
        if ($commande->getVar('cmd_payment')) {
            $sform->addElement(new \XoopsFormLabel(_OLEDRION_PAYMENT, $commande->getVar('cmd_payment')));
        }
        if (Oledrion\Utility::getModuleOption('ask_vatnumber')) {
            $sform->addElement(new \XoopsFormLabel(_OLEDRION_VAT_NUMBER, $commande->getVar('cmd_vat_number')));
        }
        if (Oledrion\Utility::getModuleOption('ask_bill')) {
            if (0 == $commande->getVar('cmd_bill')) {
                $sform->addElement(new \XoopsFormLabel(_OLEDRION_INVOICE, _NO));
            } else {
                $sform->addElement(new \XoopsFormLabel(_OLEDRION_INVOICE, _YES));
            }
        }
        $button_tray = new \XoopsFormElementTray('', '');
        //B.R. if (!isset($payment) || $payment['payment_type'] === 'offline' || $commandAmountTTC == 0 || $checkout_level == 1 ) {
        if (!isset($payment) || 'offline' === $payment->getVar('payment_type') || 0 == $commandAmountTTC || 1 == $checkout_level) {
            $submit_btn = new \XoopsFormButton('', 'post', _OLEDRION_FINISH, 'submit');
        } else {
            $submit_btn = new \XoopsFormButton('', 'post', _OLEDRION_PAY_GATEWAY, 'submit');
        }
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        $xoopsTpl->assign('form', $sform->render());

        // Send sms
        if (Oledrion\Utility::getModuleOption('sms_checkout')) {
            $information['to']   = ltrim($commande->getVar('cmd_mobile'), 0);
            $information['text'] = Oledrion\Utility::getModuleOption('sms_checkout_text');
            $sms                 = \XoopsModules\Oledrion\Sms::sendSms($information);
        }
        break;
}

$xoopsTpl->assign('breadcrumb', Oledrion\Utility::breadcrumb([OLEDRION_URL . basename(__FILE__) => _OLEDRION_VALIDATE_CMD]));

// Image icons
if (file_exists(OLEDRION_PATH . 'language/' . $xoopsConfig['language'] . '/image/step1.png')) {
    $step1 = OLEDRION_URL . 'language/' . $xoopsConfig['language'] . '/image/step1.png';
    $step2 = OLEDRION_URL . 'language/' . $xoopsConfig['language'] . '/image/step2.png';
    $step3 = OLEDRION_URL . 'language/' . $xoopsConfig['language'] . '/image/step3.png';
} else {
    // Fallback
    $step1 = OLEDRION_URL . 'language/english/image/step1.png';
    $step2 = OLEDRION_URL . 'language/english/image/step2.png';
    $step3 = OLEDRION_URL . 'language/english/image/step3.png';
}
$xoopsTpl->assign('step1', $step1);
$xoopsTpl->assign('step2', $step2);
$xoopsTpl->assign('step3', $step3);

$title = _OLEDRION_VALIDATE_CMD . ' - ' . Oledrion\Utility::getModuleName();
Oledrion\Utility::setMetas($title, $title);
Oledrion\Utility::setCSS();
Oledrion\Utility::setLocalCSS($xoopsConfig['language']);
require_once XOOPS_ROOT_PATH . '/footer.php';
