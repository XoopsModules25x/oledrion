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
 * Affichage du contenu d'une liste
 *
 * @param integer list_id    Identifiant de la liste
 */
require_once __DIR__ . '/header.php';
$GLOBALS['current_category']             = -1;
$GLOBALS['xoopsOption']['template_main'] = 'oledrion_list.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

if (isset($_GET['list_id'])) {
    $list_id = (int)$_GET['list_id'];
} else {
    \Xoopsmodules\oledrion\Utility::redirect(_OLEDRION_ERROR21, 'index.php', 5);
}
$handlers = OledrionHandler::getInstance();

// La liste existe ?
$list = null;
$list = $handlers->h_oledrion_lists->get($list_id);
if (!is_object($list)) {
    \Xoopsmodules\oledrion\Utility::redirect(_OLEDRION_ERROR21, 'index.php', 5);
}

// Vérification du type de liste (publique/privée)
if (!$list->isSuitableForCurrentUser()) {
    \Xoopsmodules\oledrion\Utility::redirect(_OLEDRION_ERROR22, 'index.php', 5);
}
$xoopsTpl->assign('mod_pref', $mod_pref); // Préférences du module
$xoopsTpl->assign('columnsCount', \Xoopsmodules\oledrion\Utility::getModuleOption('category_colums'));
$xoopsTpl->assign('list', $list->toArray());

// TVA
$vatArray = [];
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
if ($handlers->h_oledrion_lists->getRecentListsCount(OLEDRION_LISTS_ALL_PUBLIC, \Xoopsmodules\oledrion\Utility::getCurrentUserID()) > 1) {
    $otherUserLists = $handlers->h_oledrion_lists->getRecentLists(new Oledrion_parameters([
                                                                                              'start'    => 0,
                                                                                              'limit'    => 0,
                                                                                              'sort'     => 'list_date',
                                                                                              'order'    => 'DESC',
                                                                                              'idAsKey'  => true,
                                                                                              'listType' => OLEDRION_LISTS_ALL_PUBLIC,
                                                                                              'list_uid' => \Xoopsmodules\oledrion\Utility::getCurrentUserID()
                                                                                          ]));
    if (count($otherUserLists) > 0) {
        foreach ($otherUserLists as $oneOtherList) {
            $xoopsTpl->append('otherUserLists', $oneOtherList->toArray());
        }
    }
}

\Xoopsmodules\oledrion\Utility::setCSS();
\Xoopsmodules\oledrion\Utility::setLocalCSS($xoopsConfig['language']);
\Xoopsmodules\oledrion\Utility::loadLanguageFile('modinfo.php');

$breadcrumb = [
    OLEDRION_URL . 'all-lists.php'    => _MI_OLEDRION_SMNAME11,
    OLEDRION_URL . basename(__FILE__) => $list->getVar('list_title')
];
$xoopsTpl->assign('breadcrumb', \Xoopsmodules\oledrion\Utility::breadcrumb($breadcrumb));

$title = $list->getVar('list_title') . ' - ' . \Xoopsmodules\oledrion\Utility::getModuleName();
\Xoopsmodules\oledrion\Utility::setMetas($title, $title, \Xoopsmodules\oledrion\Utility::createMetaKeywords($list->getVar('list_description', 'n') . ' ' . $list->getVar('list_title', 'n')));
require_once XOOPS_ROOT_PATH . '/footer.php';
