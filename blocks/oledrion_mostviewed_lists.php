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

use XoopsModules\Oledrion;
use XoopsModules\Oledrion\Constants;

/**
 * Affichage des listes les plus vues
 *
 * @param  array $options [0] = Nombre maximum de listes à voir, [1] = Type de listes (0 = les 2, 1 = liste cadeaux, 2 = produits recommandés)
 * @return array
 */
function b_oledrion_mostviewed_lists_show($options)
{
    require XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
    /** @var \XoopsModules\Oledrion\Helper $helper */
    $helper = \XoopsModules\Oledrion\Helper::getInstance();
    $helper->loadLanguage('main');
    $start    = 0;
    $limit    = (int)$options[0];
    $listType = (int)$options[1];
    $block    = [];
    //    $handlers = HandlerManager::getInstance();
    $db           = \XoopsDatabaseFactory::getDatabaseConnection();
    $listsHandler = new Oledrion\ListsHandler($db);
    $items        = [];
    //$items = $handlers->h_oledrion_lists->getRecentLists(new Oledrion\Parameters(array('start' => $start, 'limit' => $limit, 'sort' => 'list_views', 'order' => 'DESC', 'idAsKey' => true, 'listType' => $listType)));
    $items = $listsHandler->getRecentLists(new Oledrion\Parameters([
                                                                       'start'    => $start,
                                                                       'limit'    => $limit,
                                                                       'sort'     => 'list_views',
                                                                       'order'    => 'DESC',
                                                                       'idAsKey'  => true,
                                                                       'listType' => Constants::OLEDRION_LISTS_ALL_PUBLIC
                                                                   ]));
    if (count($items) > 0) {
        foreach ($items as $item) {
            $block['mostviewd_lists'][] = $item->toArray();
        }
    }

    return $block;
}

/**
 * Edition des paramètres du bloc
 *
 * @param  array $options [0] = Nombre maximum de listes à voir, [1] = Type de listes (0 = les 2, 1 = liste cadeaux, 2 = produits recommandés)
 * @return string
 */
function b_oledrion__mostviewed_lists_edit($options)
{
    include XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
    $form           = '';
    $form           .= "<table border='0'>";
    $form           .= '<tr><td>' . _MB_OLEDRION_LISTS_COUNT . "</td><td><input type='text' name='options[]' id='options' value='" . (int)$options[0] . "'></td></tr>";
    $listTypes      = Oledrion\Lists::getTypesArray();
    $listTypeSelect = Oledrion\Utility::htmlSelect('options[]', $listTypes, (int)$options[1], false);
    $form           .= '<tr><td>' . _MB_OLEDRION_LISTS_TYPE . '</td><td>' . $listTypeSelect . '</td></tr>';
    $form           .= '</table>';

    return $form;
}

/**
 * Bloc à la volée
 * @param $options
 */
function b_oledrion_mostviewed_lists_duplicatable($options)
{
    $options = explode('|', $options);
    $block   = b_oledrion_mostviewed_lists_show($options);

    $tpl = new \XoopsTpl();
    $tpl->assign('block', $block);
    $tpl->display('oledrion_block_mostviewed_lists.tpl');
}
