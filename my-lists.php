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
 * Liste des listes de l'utilisateur
 */
require_once __DIR__ . '/header.php';
$GLOBALS['current_category']             = -1;
$GLOBALS['xoopsOption']['template_main'] = 'oledrion_mylists.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

$uid = OledrionUtility::getCurrentUserID();
if ($uid == 0) {
    OledrionUtility::redirect(_OLEDRION_ERROR23, XOOPS_URL . '/register.php', 4);
}

$baseurl  = OLEDRION_URL . basename(__FILE__); // URL de ce script
$handlers = OledrionHandler::getInstance();
if (isset($_GET['op'])) {
    $op = $_GET['op'];
} elseif (isset($_POST['op'])) {
    $op = $_POST['op'];
} else {
    $op = 'default';
}
$xoopsTpl->assign('baseurl', $baseurl);
OledrionUtility::loadLanguageFile('modinfo.php');
OledrionUtility::loadLanguageFile('admin.php');
$breadcrumb = '';

/**
 * @param                        $op
 * @param  int                   $product_id
 * @return object|XoopsThemeForm
 */
function listForm($op, $product_id = 0)
{
    global $handlers, $baseurl;
    if ($op === 'edit') {
        $title        = _OLEDRION_EDIT_LIST;
        $label_submit = _AM_OLEDRION_MODIFY;
        $list_id      = isset($_GET['list_id']) ? (int)$_GET['list_id'] : 0;
        if (empty($list_id)) {
            OledrionUtility::redirect(_AM_OLEDRION_ERROR_21, $baseurl, 5);
        }
        $item = null;
        $item = $handlers->h_oledrion_lists->get($list_id);
        if (!is_object($item)) {
            OledrionUtility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
        }
        // Vérification, est-ce que l'utilisateur courant est bien le propriétaire de cette liste ?
        if (!$handlers->h_oledrion_lists->isThisMyList($list_id)) {
            OledrionUtility::redirect(_OLEDRION_ERROR25, $baseurl, 8);
        }
        $edit         = true;
        $label_submit = _AM_OLEDRION_MODIFY;
    } else {
        $title        = _OLEDRION_ADD_LIST;
        $label_submit = _AM_OLEDRION_ADD;
        $item         = $handlers->h_oledrion_lists->create(true);
        $edit         = false;
    }

    $sform = new XoopsThemeForm($title, 'frmList', $baseurl);
    $sform->addElement(new XoopsFormHidden('op', 'save'));
    $sform->addElement(new XoopsFormHidden('list_id', $item->getVar('list_id')));
    $sform->addElement(new XoopsFormText(_AM_OLEDRION_TITLE, 'list_title', 50, 255, $item->getVar('list_title', 'e')), true);
    //$sform->addElement(new XoopsFormText(_OLEDRION_LIST_PASSWORD, 'list_password', 50, 50, $item->getVar('list_password','e')), false);
    $selectTypes = Oledrion_lists::getTypesArray();
    $selectType  = new XoopsFormSelect(_OLEDRION_LIST_TYPE, 'list_type', $item->getVar('list_type', 'e'));
    $selectType->addOptionArray($selectTypes);
    $sform->addElement($selectType, true);
    $sform->addElement(new XoopsFormTextArea(_OLEDRION_DESCRIPTION, 'list_description', $item->getVar('list_description', 'e'), 7, 60), false);
    $listProducts = array();
    if ($edit) {
        $listProducts = $handlers->h_oledrion_lists->getListProducts($item);
        if (count($listProducts) > 0) {
            $productsTray = new XoopsFormElementTray(_OLEDRION_PROD_IN_THIS_LIST, '<br>');
            $productsTray->addElement(new XoopsFormLabel(_OLEDRION_CHECK_PRODUCTS), false);
            foreach ($listProducts as $product) {
                $caption  = "<a target='_blank' href='" . $product->getLink() . "'>" . $product->getVar('product_title') . '</a>';
                $checkbox = new XoopsFormCheckBox($caption, 'productsList[]');
                $checkbox->addOption($product->getVar('product_id'), _DELETE);
                $productsTray->addElement($checkbox);
                unset($caption, $checkbox);
            }
            $sform->addElement($productsTray, false);
        }
    }
    if ($product_id > 0) {
        $product = null;
        $product = $handlers->h_oledrion_products->get($product_id);
        if (is_object($product) && $product->isProductVisible()) {
            $content = "<a target='_blank' href='" . $product->getLink() . "'>" . $product->getVar('product_title') . '</a>';
            $sform->addElement(new XoopsFormLabel(_OLEDRION_PRODUCT_DO_ADD, $content));
            $sform->addElement(new XoopsFormHidden('product_id', $product_id));
        }
    }
    $button_tray = new XoopsFormElementTray('', '');
    $submit_btn  = new XoopsFormButton('', 'post', $label_submit, 'submit');
    $button_tray->addElement($submit_btn);
    $sform->addElement($button_tray);

    $sform =& OledrionUtility::formMarkRequiredFields($sform);

    return $sform;
}

switch ($op) {
    // ************************************************************************
    case 'default': // Liste de toutes les listes de l'utilisateur ************
        // ************************************************************************
        $xoopsTpl->assign('op', $op);
        $lists   = array();
        $start   = $limit = 0;
        $idAsKey = true;
        $lists   = $handlers->h_oledrion_lists->getRecentLists(new Oledrion_parameters(array(
                                                                                           'start'    => $start,
                                                                                           'limit'    => $limit,
                                                                                           'sort'     => 'list_title',
                                                                                           'order'    => 'ASC',
                                                                                           'idAsKey'  => $idAsKey,
                                                                                           'listType' => OLEDRION_LISTS_ALL,
                                                                                           'list_uid' => $uid
                                                                                       )));
        if (count($lists) > 0) {
            foreach ($lists as $list) {
                $xoopsTpl->append('lists', $list->toArray());
            }
        }
        $breadcrumb = array(
            OLEDRION_URL . 'all-lists.php'    => _MI_OLEDRION_SMNAME11,
            OLEDRION_URL . basename(__FILE__) => _MI_OLEDRION_SMNAME10
        );
        break;

    // ************************************************************************
    case 'addProduct': // Ajout d'un produit à une liste *********************
        // ************************************************************************
        $xoopsTpl->assign('op', $op);
        $product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
        if ($product_id == 0) {
            OledrionUtility::redirect(_OLEDRION_ERROR14, $baseurl, 4);
        }
        $userListsCount = $handlers->h_oledrion_lists->getRecentListsCount(OLEDRION_LISTS_ALL, $uid);
        $xoopsTpl->assign('userListsCount', $userListsCount);
        $xoopsTpl->assign('product_id', $product_id);
        if ($userListsCount > 0) {
            $userLists = $handlers->h_oledrion_lists->getRecentLists(new Oledrion_parameters(array(
                                                                                                 'start'    => 0,
                                                                                                 'limit'    => 0,
                                                                                                 'sort'     => 'list_title',
                                                                                                 'order'    => 'ASC',
                                                                                                 'idAsKey'  => true,
                                                                                                 'listType' => OLEDRION_LISTS_ALL,
                                                                                                 'list_uid' => $uid
                                                                                             )));
            foreach ($userLists as $list) {
                $xoopsTpl->append('lists', $list->toArray());
            }
            $breadcrumb = array(
                OLEDRION_URL . 'all-lists.php'    => _MI_OLEDRION_SMNAME11,
                OLEDRION_URL . basename(__FILE__) => _MI_OLEDRION_SMNAME10,
                OLEDRION_URL                      => _OLEDRION_ADD_PRODUCT_LIST
            );
            $product    = null;
            $product    = $handlers->h_oledrion_products->get($product_id);
            if (is_object($product) && $product->isProductVisible()) {
                $xoopsTpl->assign('product', $product->toArray());
            } else {
                OledrionUtility::redirect(_OLEDRION_ERROR1, $baseurl, 4);
            }
        } else {
            $sform      = listForm('addList', $product_id);
            $title      = _OLEDRION_ADD_LIST;
            $breadcrumb = array(
                OLEDRION_URL . 'all-lists.php'    => _MI_OLEDRION_SMNAME11,
                OLEDRION_URL . basename(__FILE__) => _MI_OLEDRION_SMNAME10,
                OLEDRION_URL                      => $title
            );
            $xoopsTpl->assign('form', $sform->render());
        }
        break;

    // ************************************************************************
    case 'addProductToList': // Ajout d'un produit à une liste, sélection de la liste
        // ************************************************************************
        $xoopsTpl->assign('op', $op);
        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        if ($product_id == 0) {
            OledrionUtility::redirect(_OLEDRION_ERROR14, $baseurl, 4);
        }
        $product = null;
        $product = $handlers->h_oledrion_products->get($product_id);
        if (is_object($product) && $product->isProductVisible()) {
            $xoopsTpl->assign('product', $product->toArray());
        } else {
            OledrionUtility::redirect(_OLEDRION_ERROR1, $baseurl, 4);
        }

        $list_id = isset($_POST['list_id']) ? (int)$_POST['list_id'] : 0;
        if ($list_id == 0) { // Ajouter à une nouvelle liste
            $sform      = listForm('addList', $product_id);
            $title      = _OLEDRION_ADD_LIST;
            $breadcrumb = array(
                OLEDRION_URL . 'all-lists.php'    => _MI_OLEDRION_SMNAME11,
                OLEDRION_URL . basename(__FILE__) => _MI_OLEDRION_SMNAME10,
                OLEDRION_URL                      => $title
            );
            $xoopsTpl->assign('form', $sform->render());
            $xoopsTpl->assign('op', 'addList');
        } else { // Ajouter à une liste existante
            if (!$handlers->h_oledrion_lists->isThisMyList($list_id)) {
                OledrionUtility::redirect(_OLEDRION_ERROR25, $baseurl, 8);
            }
            if ($handlers->h_oledrion_products_list->isProductAlreadyInList($list_id, $product_id)) {
                OledrionUtility::redirect(_OLEDRION_ERROR26, $baseurl . '?op=addProduct&product_id=' . $product_id, 4);
            } else {
                $res = $handlers->h_oledrion_products_list->addProductToUserList($list_id, $product_id);
                if ($res) {
                    $list = null;
                    $list = $handlers->h_oledrion_lists->get($list_id);
                    if (is_object($list)) {
                        $handlers->h_oledrion_lists->incrementListProductsCount($list);
                    }
                    OledrionUtility::updateCache();
                    OledrionUtility::redirect(_OLEDRION_PRODUCT_LIST_ADD_OK, $product->getLink(), 2);
                } else {
                    OledrionUtility::redirect(_OLEDRION_ERROR27, $product->getLink(), 4);
                }
            }
        }
        break;

    // ************************************************************************
    case 'delete': // Suppression d'une liste ********************************
        // ************************************************************************
        $xoopsTpl->assign('op', $op);
        $list_id = isset($_GET['list_id']) ? (int)$_GET['list_id'] : 0;
        if ($list_id == 0) {
            OledrionUtility::redirect(_OLEDRION_ERROR21, $baseurl, 4);
        }
        // Vérification, est-ce que l'utilisateur courant est bien le propriétaire de cette liste ?
        if (!$handlers->h_oledrion_lists->isThisMyList($list_id)) {
            OledrionUtility::redirect(_OLEDRION_ERROR25, $baseurl, 8);
        }
        $item = $handlers->h_oledrion_lists->get($list_id);
        if (!is_object($item)) {
            OledrionUtility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
        }
        xoops_confirm(array('op' => 'reallyDelete', 'list_id' => $list_id), $baseurl, _OLEDRION_DELETE_LIST . '<br>' . $item->getVar('list_title'));
        break;

    // ************************************************************************
    case 'reallyDelete': // Suppression effective d'une liste **************
        // ************************************************************************
        $list_id = isset($_POST['list_id']) ? (int)$_POST['list_id'] : 0;
        if ($list_id == 0) {
            OledrionUtility::redirect(_OLEDRION_ERROR21, $baseurl, 4);
        }
        // Vérification, est-ce que l'utilisateur courant est bien le propriétaire de cette liste ?
        if (!$handlers->h_oledrion_lists->isThisMyList($list_id)) {
            OledrionUtility::redirect(_OLEDRION_ERROR25, $baseurl, 8);
        }
        $item = $handlers->h_oledrion_lists->get($list_id);
        if (!is_object($item)) {
            OledrionUtility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
        }
        if ($handlers->h_oledrion_lists->deleteList($item)) {
            OledrionUtility::updateCache();
            OledrionUtility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl, 2);
        } else {
            OledrionUtility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl, 5);
        }
        break;

    // ************************************************************************
    case 'save': // Sauvegarde d'une liste *********************************
        // ************************************************************************
        $list_id = isset($_POST['list_id']) ? (int)$_POST['list_id'] : 0;
        if (!empty($list_id)) {
            // Vérification, est-ce que l'utilisateur courant est bien le propriétaire de cette liste ?
            if (!$handlers->h_oledrion_lists->isThisMyList($list_id)) {
                OledrionUtility::redirect(_OLEDRION_ERROR25, $baseurl, 8);
            }
            $edit = true;
            $item = $handlers->h_oledrion_lists->get($list_id);
            if (!is_object($item)) {
                OledrionUtility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $item->unsetNew();
            $edit = true;
        } else {
            $item = $handlers->h_oledrion_lists->create(true);
            $edit = false;
        }
        // Contrôle sur le titre
        if (!isset($_POST['list_title']) || (isset($_POST['list_title']) && xoops_trim($_POST['list_title']) == '')) {
            OledrionUtility::redirect(_OLEDRION_ERROR24, $baseurl, 5);
        }
        $item->setVars($_POST);
        if (!$edit) {
            $item->setVar('list_date', time());
            $item->setVar('list_uid', $uid);
        }
        if (isset($_POST['productsList'])) {
            $productsDeletedCount = 0;
            foreach ($_POST['productsList'] as $productId) {
                $res = $handlers->h_oledrion_products_list->deleteProductFromList($list_id, (int)$productId);
                if ($res) {
                    ++$productsDeletedCount;
                }
            }
            if ($productsDeletedCount > 0) {
                $handlers->h_oledrion_products_list->decrementListProductsCount($productsDeletedCount);
            }
        }
        $res = $handlers->h_oledrion_lists->insert($item);
        if ($res) {
            if (isset($_POST['product_id'])) {
                $product_id = (int)$_POST['product_id'];
                if ($product_id > 0) {
                    $product = null;
                    $product = $handlers->h_oledrion_products->get($product_id);
                    if (is_object($product)
                        && $product->isProductVisible()) { // On peut ajouter le produit à cette nouvelle liste
                        $res = $handlers->h_oledrion_products_list->addProductToUserList($item->getVar('list_id'), $product_id);
                        if ($res) { // Mise à jour du nombre de produits de la liste
                            $handlers->h_oledrion_lists->incrementListProductsCount($item);
                            OledrionUtility::updateCache();
                            OledrionUtility::redirect(_AM_OLEDRION_SAVE_OK, $product->getLink(), 2);
                        }
                    }
                }
            }
            OledrionUtility::updateCache();
            OledrionUtility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl, 2);
        } else {
            OledrionUtility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl, 5);
        }
        break;

    // ************************************************************************
    case 'edit': // Edition d'une liste ***************************************
    case 'addList': // Ajout d'une liste **************************************
        // ************************************************************************
        $xoopsTpl->assign('op', $op);
        $sform = listForm($op, 0);
        if ($op === 'edit') {
            $title = _OLEDRION_EDIT_LIST;
        } else {
            $title = _OLEDRION_ADD_LIST;
        }
        $breadcrumb = array(
            OLEDRION_URL . 'all-lists.php'    => _MI_OLEDRION_SMNAME11,
            OLEDRION_URL . basename(__FILE__) => _MI_OLEDRION_SMNAME10,
            OLEDRION_URL                      => $title
        );

        $xoopsTpl->assign('form', $sform->render());
        break;
}

OledrionUtility::setCSS();
OledrionUtility::setLocalCSS($xoopsConfig['language']);

$xoopsTpl->assign('mod_pref', $mod_pref);
$xoopsTpl->assign('breadcrumb', OledrionUtility::breadcrumb($breadcrumb));

$title = _MI_OLEDRION_SMNAME10 . ' - ' . OledrionUtility::getModuleName();
OledrionUtility::setMetas($title, $title);
require_once XOOPS_ROOT_PATH . '/footer.php';
