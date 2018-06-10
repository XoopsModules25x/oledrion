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

use XoopsModules\Oledrion;
use XoopsModules\Oledrion\Constants;

require_once __DIR__ . '/header.php';
$GLOBALS['current_category']             = -1;
$GLOBALS['xoopsOption']['template_main'] = 'oledrion_mylists.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

$uid = Oledrion\Utility::getCurrentUserID();
if (0 == $uid) {
    Oledrion\Utility::redirect(_OLEDRION_ERROR23, XOOPS_URL . '/register.php', 4);
}

$baseurl = OLEDRION_URL . basename(__FILE__); // URL de ce script
//$handlers = HandlerManager::getInstance();

$op = \Xmf\Request::getCmd('op', 'default');

$xoopsTpl->assign('baseurl', $baseurl);
$helper->loadLanguage('modinfo');
$helper->loadLanguage('admin');
$breadcrumb = '';

/**
 * @param                        $op
 * @param  int                   $product_id
 * @return \XoopsThemeForm
 */
function listForm($op, $product_id = 0)
{
    global $baseurl;

    $db           = \XoopsDatabaseFactory::getDatabaseConnection();
    $listsHandler = new Oledrion\ListsHandler($db);

    if ('edit' === $op) {
        $title        = _OLEDRION_EDIT_LIST;
        $label_submit = _AM_OLEDRION_MODIFY;
        $list_id      = \Xmf\Request::getInt('list_id', 0, 'GET');
        if (empty($list_id)) {
            Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_12, $baseurl, 5);
        }
        $item = null;
        $item = $listsHandler->get($list_id);
        if (!is_object($item)) {
            Oledrion\Utility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
        }
        // Vérification, est-ce que l'utilisateur courant est bien le propriétaire de cette liste ?
        if (!$listsHandler->isThisMyList($list_id)) {
            Oledrion\Utility::redirect(_OLEDRION_ERROR25, $baseurl, 8);
        }
        $edit         = true;
        $label_submit = _AM_OLEDRION_MODIFY;
    } else {
        $title        = _OLEDRION_ADD_LIST;
        $label_submit = _AM_OLEDRION_ADD;
        $item         = $listsHandler->create(true);
        $edit         = false;
    }

    $sform = new \XoopsThemeForm($title, 'frmList', $baseurl);
    $sform->addElement(new \XoopsFormHidden('op', 'save'));
    $sform->addElement(new \XoopsFormHidden('list_id', $item->getVar('list_id')));
    $sform->addElement(new \XoopsFormText(_AM_OLEDRION_TITLE, 'list_title', 50, 255, $item->getVar('list_title', 'e')), true);
    //$sform->addElement(new \XoopsFormText(_OLEDRION_LIST_PASSWORD, 'list_password', 50, 50, $item->getVar('list_password','e')), false);
    $selectTypes = \XoopsModules\Oledrion\Lists::getTypesArray();
    $selectType  = new \XoopsFormSelect(_OLEDRION_LIST_TYPE, 'list_type', $item->getVar('list_type', 'e'));
    $selectType->addOptionArray($selectTypes);
    $sform->addElement($selectType, true);
    $sform->addElement(new \XoopsFormTextArea(_OLEDRION_DESCRIPTION, 'list_description', $item->getVar('list_description', 'e'), 7, 60), false);
    $listProducts = [];
    if ($edit) {
        $listProducts = $listsHandler->getListProducts($item);
        if (count($listProducts) > 0) {
            $productsTray = new \XoopsFormElementTray(_OLEDRION_PROD_IN_THIS_LIST, '<br>');
            $productsTray->addElement(new \XoopsFormLabel(_OLEDRION_CHECK_PRODUCTS), false);
            foreach ($listProducts as $product) {
                $caption  = "<a target='_blank' href='" . $product->getLink() . "'>" . $product->getVar('product_title') . '</a>';
                $checkbox = new \XoopsFormCheckBox($caption, 'productsList[]');
                $checkbox->addOption($product->getVar('product_id'), _DELETE);
                $productsTray->addElement($checkbox);
                unset($caption, $checkbox);
            }
            $sform->addElement($productsTray, false);
        }
    }
    if ($product_id > 0) {
        $product = null;
        $product = $productsHandler->get($product_id);
        if (is_object($product) && $product->isProductVisible()) {
            $content = "<a target='_blank' href='" . $product->getLink() . "'>" . $product->getVar('product_title') . '</a>';
            $sform->addElement(new \XoopsFormLabel(_OLEDRION_PRODUCT_DO_ADD, $content));
            $sform->addElement(new \XoopsFormHidden('product_id', $product_id));
        }
    }
    $button_tray = new \XoopsFormElementTray('', '');
    $submit_btn  = new \XoopsFormButton('', 'post', $label_submit, 'submit');
    $button_tray->addElement($submit_btn);
    $sform->addElement($button_tray);

    $sform = Oledrion\Utility::formMarkRequiredFields($sform);

    return $sform;
}

switch ($op) {
    // ************************************************************************
    case 'default': // Liste de toutes les listes de l'utilisateur ************
        // ************************************************************************
        $xoopsTpl->assign('op', $op);
        $lists   = [];
        $start   = $limit = 0;
        $idAsKey = true;
        $lists   = $listsHandler->getRecentLists(new Oledrion\Parameters([
                                                                             'start'    => $start,
                                                                             'limit'    => $limit,
                                                                             'sort'     => 'list_title',
                                                                             'order'    => 'ASC',
                                                                             'idAsKey'  => $idAsKey,
                                                                             'listType' => Constants::OLEDRION_LISTS_ALL,
                                                                             'list_uid' => $uid
                                                                         ]));
        if (count($lists) > 0) {
            foreach ($lists as $list) {
                $xoopsTpl->append('lists', $list->toArray());
            }
        }
        $breadcrumb = [
            OLEDRION_URL . 'all-lists.php'    => _MI_OLEDRION_SMNAME11,
            OLEDRION_URL . basename(__FILE__) => _MI_OLEDRION_SMNAME10
        ];
        break;

    // ************************************************************************
    case 'addProduct': // Ajout d'un produit à une liste *********************
        // ************************************************************************
        $xoopsTpl->assign('op', $op);
        $product_id = \Xmf\Request::getInt('product_id', 0, 'GET');
        if (0 == $product_id) {
            Oledrion\Utility::redirect(_OLEDRION_ERROR14, $baseurl, 4);
        }
        $userListsCount = $listsHandler->getRecentListsCount(Constants::OLEDRION_LISTS_ALL, $uid);
        $xoopsTpl->assign('userListsCount', $userListsCount);
        $xoopsTpl->assign('product_id', $product_id);
        if ($userListsCount > 0) {
            $userLists = $listsHandler->getRecentLists(new Oledrion\Parameters([
                                                                                   'start'    => 0,
                                                                                   'limit'    => 0,
                                                                                   'sort'     => 'list_title',
                                                                                   'order'    => 'ASC',
                                                                                   'idAsKey'  => true,
                                                                                   'listType' => Constants::OLEDRION_LISTS_ALL,
                                                                                   'list_uid' => $uid
                                                                               ]));
            foreach ($userLists as $list) {
                $xoopsTpl->append('lists', $list->toArray());
            }
            $breadcrumb = [
                OLEDRION_URL . 'all-lists.php'    => _MI_OLEDRION_SMNAME11,
                OLEDRION_URL . basename(__FILE__) => _MI_OLEDRION_SMNAME10,
                OLEDRION_URL                      => _OLEDRION_ADD_PRODUCT_LIST
            ];
            $product    = null;
            $product    = $productsHandler->get($product_id);
            if (is_object($product) && $product->isProductVisible()) {
                $xoopsTpl->assign('product', $product->toArray());
            } else {
                Oledrion\Utility::redirect(_OLEDRION_ERROR1, $baseurl, 4);
            }
        } else {
            $sform      = listForm('addList', $product_id);
            $title      = _OLEDRION_ADD_LIST;
            $breadcrumb = [
                OLEDRION_URL . 'all-lists.php'    => _MI_OLEDRION_SMNAME11,
                OLEDRION_URL . basename(__FILE__) => _MI_OLEDRION_SMNAME10,
                OLEDRION_URL                      => $title
            ];
            $xoopsTpl->assign('form', $sform->render());
        }
        break;

    // ************************************************************************
    case 'addProductToList': // Ajout d'un produit à une liste, sélection de la liste
        // ************************************************************************
        $xoopsTpl->assign('op', $op);
        $product_id = \Xmf\Request::getInt('product_id', 0, 'POST');
        if (0 == $product_id) {
            Oledrion\Utility::redirect(_OLEDRION_ERROR14, $baseurl, 4);
        }
        $product = null;
        $product = $productsHandler->get($product_id);
        if (is_object($product) && $product->isProductVisible()) {
            $xoopsTpl->assign('product', $product->toArray());
        } else {
            Oledrion\Utility::redirect(_OLEDRION_ERROR1, $baseurl, 4);
        }

        $list_id = \Xmf\Request::getInt('list_id', 0, 'POST');
        if (0 == $list_id) { // Ajouter à une nouvelle liste
            $sform      = listForm('addList', $product_id);
            $title      = _OLEDRION_ADD_LIST;
            $breadcrumb = [
                OLEDRION_URL . 'all-lists.php'    => _MI_OLEDRION_SMNAME11,
                OLEDRION_URL . basename(__FILE__) => _MI_OLEDRION_SMNAME10,
                OLEDRION_URL                      => $title
            ];
            $xoopsTpl->assign('form', $sform->render());
            $xoopsTpl->assign('op', 'addList');
        } else { // Ajouter à une liste existante
            if (!$listsHandler->isThisMyList($list_id)) {
                Oledrion\Utility::redirect(_OLEDRION_ERROR25, $baseurl, 8);
            }
            if ($productsListHandler->isProductAlreadyInList($list_id, $product_id)) {
                Oledrion\Utility::redirect(_OLEDRION_ERROR26, $baseurl . '?op=addProduct&product_id=' . $product_id, 4);
            } else {
                $res = $productsListHandler->addProductToUserList($list_id, $product_id);
                if ($res) {
                    $list = null;
                    $list = $listsHandler->get($list_id);
                    if (is_object($list)) {
                        $listsHandler->incrementListProductsCount($list);
                    }
                    Oledrion\Utility::updateCache();
                    Oledrion\Utility::redirect(_OLEDRION_PRODUCT_LIST_ADD_OK, $product->getLink(), 2);
                } else {
                    Oledrion\Utility::redirect(_OLEDRION_ERROR27, $product->getLink(), 4);
                }
            }
        }
        break;

    // ************************************************************************
    case 'delete': // Suppression d'une liste ********************************
        // ************************************************************************
        $xoopsTpl->assign('op', $op);
        $list_id = \Xmf\Request::getInt('list_id', 0, 'GET');
        if (0 == $list_id) {
            Oledrion\Utility::redirect(_OLEDRION_ERROR21, $baseurl, 4);
        }
        // Vérification, est-ce que l'utilisateur courant est bien le propriétaire de cette liste ?
        if (!$listsHandler->isThisMyList($list_id)) {
            Oledrion\Utility::redirect(_OLEDRION_ERROR25, $baseurl, 8);
        }
        $item = $listsHandler->get($list_id);
        if (!is_object($item)) {
            Oledrion\Utility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
        }
        xoops_confirm(['op' => 'reallyDelete', 'list_id' => $list_id], $baseurl, _OLEDRION_DELETE_LIST . '<br>' . $item->getVar('list_title'));
        break;

    // ************************************************************************
    case 'reallyDelete': // Suppression effective d'une liste **************
        // ************************************************************************
        $list_id = \Xmf\Request::getInt('list_id', 0, 'POST');
        if (0 == $list_id) {
            Oledrion\Utility::redirect(_OLEDRION_ERROR21, $baseurl, 4);
        }
        // Vérification, est-ce que l'utilisateur courant est bien le propriétaire de cette liste ?
        if (!$listsHandler->isThisMyList($list_id)) {
            Oledrion\Utility::redirect(_OLEDRION_ERROR25, $baseurl, 8);
        }
        $item = $listsHandler->get($list_id);
        if (!is_object($item)) {
            Oledrion\Utility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
        }
        if ($listsHandler->deleteList($item)) {
            Oledrion\Utility::updateCache();
            Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl, 2);
        } else {
            Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl, 5);
        }
        break;

    // ************************************************************************
    case 'save': // Sauvegarde d'une liste *********************************
        // ************************************************************************
        $list_id = \Xmf\Request::getInt('list_id', 0, 'POST');
        if (!empty($list_id)) {
            // Vérification, est-ce que l'utilisateur courant est bien le propriétaire de cette liste ?
            if (!$listsHandler->isThisMyList($list_id)) {
                Oledrion\Utility::redirect(_OLEDRION_ERROR25, $baseurl, 8);
            }
            $edit = true;
            $item = $listsHandler->get($list_id);
            if (!is_object($item)) {
                Oledrion\Utility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $item->unsetNew();
            $edit = true;
        } else {
            $item = $listsHandler->create(true);
            $edit = false;
        }
        // Contrôle sur le titre
        if (!isset($_POST['list_title']) || (isset($_POST['list_title']) && '' === xoops_trim($_POST['list_title']))) {
            Oledrion\Utility::redirect(_OLEDRION_ERROR24, $baseurl, 5);
        }
        $item->setVars($_POST);
        if (!$edit) {
            $item->setVar('list_date', time());
            $item->setVar('list_uid', $uid);
        }
        if (\Xmf\Request::hasVar('productsList', 'POST')) {
            $productsDeletedCount = 0;
            foreach ($_POST['productsList'] as $productId) {
                $res = $productsListHandler->deleteProductFromList($list_id, (int)$productId);
                if ($res) {
                    ++$productsDeletedCount;
                }
            }
            if ($productsDeletedCount > 0) {
                $productsListHandler->decrementListProductsCount($productsDeletedCount);
            }
        }
        $res = $listsHandler->insert($item);
        if ($res) {
            if (\Xmf\Request::hasVar('product_id', 'POST')) {
                $product_id = \Xmf\Request::getInt('product_id', 0, 'POST');
                if ($product_id > 0) {
                    $product = null;
                    $product = $productsHandler->get($product_id);
                    if (is_object($product)
                        && $product->isProductVisible()) { // On peut ajouter le produit à cette nouvelle liste
                        $res = $productsListHandler->addProductToUserList($item->getVar('list_id'), $product_id);
                        if ($res) { // Mise à jour du nombre de produits de la liste
                            $listsHandler->incrementListProductsCount($item);
                            Oledrion\Utility::updateCache();
                            Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_OK, $product->getLink(), 2);
                        }
                    }
                }
            }
            Oledrion\Utility::updateCache();
            Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl, 2);
        } else {
            Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl, 5);
        }
        break;

    // ************************************************************************
    case 'edit': // Edition d'une liste ***************************************
    case 'addList': // Ajout d'une liste **************************************
        // ************************************************************************
        $xoopsTpl->assign('op', $op);
        $sform = listForm($op, 0);
        $title = _OLEDRION_ADD_LIST;
        if ('edit' === $op) {
            $title = _OLEDRION_EDIT_LIST;
        }
        $breadcrumb = [
            OLEDRION_URL . 'all-lists.php'    => _MI_OLEDRION_SMNAME11,
            OLEDRION_URL . basename(__FILE__) => _MI_OLEDRION_SMNAME10,
            OLEDRION_URL                      => $title
        ];

        $xoopsTpl->assign('form', $sform->render());
        break;
}

Oledrion\Utility::setCSS();
Oledrion\Utility::setLocalCSS($xoopsConfig['language']);

$xoopsTpl->assign('mod_pref', $mod_pref);
$xoopsTpl->assign('breadcrumb', Oledrion\Utility::breadcrumb($breadcrumb));

$title = _MI_OLEDRION_SMNAME10 . ' - ' . Oledrion\Utility::getModuleName();
Oledrion\Utility::setMetas($title, $title);
require_once XOOPS_ROOT_PATH . '/footer.php';
