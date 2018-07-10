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
 * @param int list_id    Identifiant de la liste
 */

use XoopsModules\Oledrion;
use XoopsModules\Oledrion\Constants;

require_once __DIR__ . '/header.php';
$GLOBALS['current_category']             = -1;
$GLOBALS['xoopsOption']['template_main'] = 'oledrion_list.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

if (\Xmf\Request::hasVar('list_id', 'GET')) {
    $list_id = \Xmf\Request::getInt('list_id', 0, 'GET');
} else {
    Oledrion\Utility::redirect(_OLEDRION_ERROR21, 'index.php', 5);
}
//$handlers = HandlerManager::getInstance();
$db           = \XoopsDatabaseFactory::getDatabaseConnection();
$listsHandler = new Oledrion\ListsHandler($db);

// La liste existe ?
/** @var Oledrion\Lists $list */
$list = null;
$list = $listsHandler->get($list_id);
if (!is_object($list)) {
    Oledrion\Utility::redirect(_OLEDRION_ERROR21, 'index.php', 5);
}

// Vérification du type de liste (publique/privée)
if (!$list->isSuitableForCurrentUser()) {
    Oledrion\Utility::redirect(_OLEDRION_ERROR22, 'index.php', 5);
}
$xoopsTpl->assign('mod_pref', $mod_pref); // Préférences du module
$xoopsTpl->assign('columnsCount', Oledrion\Utility::getModuleOption('category_colums'));
$xoopsTpl->assign('list', $list->toArray());

// TVA
$vatArray = [];
$vatArray = $vatHandler->getAllVats(new Oledrion\Parameters());

// Recherche des produits de la liste
$products = $listsHandler->getListProducts($list);
if (count($products) > 0) {
    /** @var Oledrion\Products $product */
    foreach ($products as $product) {
        $xoopsTpl->append('products', $product->toArray());
    }
}

// Mise à jour du compte de vues
$listsHandler->incrementListViews($list);

// Recherce des autres listes de cet utilisateur
if ($listsHandler->getRecentListsCount(Constants::OLEDRION_LISTS_ALL_PUBLIC, Oledrion\Utility::getCurrentUserID()) > 1) {
    $otherUserLists = $listsHandler->getRecentLists(new Oledrion\Parameters([
                                                                                'start'    => 0,
                                                                                'limit'    => 0,
                                                                                'sort'     => 'list_date',
                                                                                'order'    => 'DESC',
                                                                                'idAsKey'  => true,
                                                                                'listType' => Constants::OLEDRION_LISTS_ALL_PUBLIC,
                                                                                'list_uid' => Oledrion\Utility::getCurrentUserID(),
                                                                            ]));
    if (count($otherUserLists) > 0) {
        foreach ($otherUserLists as $oneOtherList) {
            $xoopsTpl->append('otherUserLists', $oneOtherList->toArray());
        }
    }
}

Oledrion\Utility::setCSS();
Oledrion\Utility::setLocalCSS($xoopsConfig['language']);
$helper->loadLanguage('modinfo');

$breadcrumb = [
    OLEDRION_URL . 'all-lists.php'    => _MI_OLEDRION_SMNAME11,
    OLEDRION_URL . basename(__FILE__) => $list->getVar('list_title'),
];
$xoopsTpl->assign('breadcrumb', Oledrion\Utility::breadcrumb($breadcrumb));

$title = $list->getVar('list_title') . ' - ' . Oledrion\Utility::getModuleName();
Oledrion\Utility::setMetas($title, $title, Oledrion\Utility::createMetaKeywords($list->getVar('list_description', 'n') . ' ' . $list->getVar('list_title', 'n')));
require_once XOOPS_ROOT_PATH . '/footer.php';
