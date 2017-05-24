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
 * @copyright   {@link http://xoops.org/ XOOPS Project}
 * @license     {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author      Hervé Thouzard (http://www.herve-thouzard.com/)
 */

/**
 * Affichage du contenu d'une liste
 *
 * @param integer list_id    Identifiant de la liste
 */
require __DIR__ . '/header.php';
$GLOBALS['current_category']             = -1;
$GLOBALS['xoopsOption']['template_main'] = 'oledrion_list.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

if (isset($_GET['list_id'])) {
    $list_id = (int)$_GET['list_id'];
} else {
    Oledrion_utils::redirect(_OLEDRION_ERROR21, 'index.php', 5);
}
$handlers = OledrionHandler::getInstance();

// La liste existe ?
$list = null;
$list = $handlers->h_oledrion_lists->get($list_id);
if (!is_object($list)) {
    Oledrion_utils::redirect(_OLEDRION_ERROR21, 'index.php', 5);
}

// Vérification du type de liste (publique/privée)
if (!$list->isSuitableForCurrentUser()) {
    Oledrion_utils::redirect(_OLEDRION_ERROR22, 'index.php', 5);
}
$xoopsTpl->assign('mod_pref', $mod_pref); // Préférences du module
$xoopsTpl->assign('columnsCount', Oledrion_utils::getModuleOption('catagory_colums'));
$xoopsTpl->assign('list', $list->toArray());

// TVA
$vatArray = array();
$vatArray = $h_oledrion_vat->getAllVats(new Oledrion_parameters());

// Recherche des produits de la liste
$products = $handlers->h_oledrion_lists->getListProducts($list);
if (count($products) > 0) {
    foreach ($products as $product) {
        $xoopsTpl->append('products', $product->toArray());
    }
}

// Mise à jour du compte de vues
$handlers->h_oledrion_lists->incrementListViews($list);

// Recherce des autres listes de cet utilisateur
if ($handlers->h_oledrion_lists->getRecentListsCount(OLEDRION_LISTS_ALL_PUBLIC, Oledrion_utils::getCurrentUserID()) > 1) {
    $otherUserLists = $handlers->h_oledrion_lists->getRecentLists(new Oledrion_parameters(array(
                                                                                              'start'    => 0,
                                                                                              'limit'    => 0,
                                                                                              'sort'     => 'list_date',
                                                                                              'order'    => 'DESC',
                                                                                              'idAsKey'  => true,
                                                                                              'listType' => OLEDRION_LISTS_ALL_PUBLIC,
                                                                                              'list_uid' => Oledrion_utils::getCurrentUserID()
                                                                                          )));
    if (count($otherUserLists) > 0) {
        foreach ($otherUserLists as $oneOtherList) {
            $xoopsTpl->append('otherUserLists', $oneOtherList->toArray());
        }
    }
}

Oledrion_utils::setCSS();
Oledrion_utils::setLocalCSS($xoopsConfig['language']);
Oledrion_utils::loadLanguageFile('modinfo.php');

$breadcrumb = array(
    OLEDRION_URL . 'all-lists.php'    => _MI_OLEDRION_SMNAME11,
    OLEDRION_URL . basename(__FILE__) => $list->getVar('list_title')
);
$xoopsTpl->assign('breadcrumb', Oledrion_utils::breadcrumb($breadcrumb));

$title = $list->getVar('list_title') . ' - ' . Oledrion_utils::getModuleName();
Oledrion_utils::setMetas($title, $title, Oledrion_utils::createMetaKeywords($list->getVar('list_description', 'n') . ' ' . $list->getVar('list_title', 'n')));
require_once XOOPS_ROOT_PATH . '/footer.php';
