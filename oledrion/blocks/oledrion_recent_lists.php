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
 * @copyright   The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author      Hervé Thouzard (http://www.herve-thouzard.com/)
 * @version     $Id$
 */
/**
 * Affichage des dernières listes utilisateurs
 *
 * @param array $options [0] = Nombre maximum de listes à voir, [1] = Type de listes (0 = les 2, 1 = liste cadeaux, 2 = produits recommandés)
 * @return array
 */
function b_oledrion_recent_lists_show($options)
{
    require XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
    oledrion_utils::loadLanguageFile('main.php');
    $start = 0;
    $limit = intval($options[0]);
    $listType = intval($options[1]);
    $block = array();
    $handlers = oledrion_handler::getInstance();
    $items = array();
    //$items = $handlers->h_oledrion_lists->getRecentLists(new oledrion_parameters(array('start' => $start, 'limit' => $limit, 'sort' => 'list_date', 'order' => 'DESC', 'idAsKey' => true, 'listType'  => $listType)));
    $items = $handlers->h_oledrion_lists->getRecentLists(new oledrion_parameters(array('start' => $start, 'limit' => $limit, 'sort' => 'list_date', 'order' => 'DESC', 'idAsKey' => true, 'listType' => OLEDRION_LISTS_ALL_PUBLIC)));
    if (count($items) > 0) {
        foreach ($items as $item) {
            $block['recent_lists'][] = $item->toArray();
        }
    }
    return $block;
}

/**
 * Edition des paramètres du bloc
 *
 * @param array $options [0] = Nombre maximum de listes à voir, [1] = Type de listes (0 = les 2, 1 = liste cadeaux, 2 = produits recommandés)
 * @return array
 */
function b_oledrion_recent_lists_edit($options)
{
    include XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
    $form = '';
    $form .= "<table border='0'>";
    $form .= '<tr><td>' . _MB_OLEDRION_LISTS_COUNT . "</td><td><input type='text' name='options[]' id='options' value='" . intval($options[0]) . "' /></td></tr>";
    $listTypes = oledrion_lists::getTypesArray();
    $listTypeSelect = oledrion_utils::htmlSelect('options[]', $listTypes, intval($options[1]), false);
    $form .= '<tr><td>' . _MB_OLEDRION_LISTS_TYPE . "</td><td>" . $listTypeSelect . "</td></tr>";
    $form .= '</table>';
    return $form;
}

/**
 * Bloc à la volée
 */
function b_oledrion_recent_lists_duplicatable($options)
{
    $options = explode('|', $options);
    $block = b_oledrion_recent_lists_show($options);

    $tpl = new XoopsTpl();
    $tpl->assign('block', $block);
    $tpl->display('db:oledrion_block_recent_lists.html');
}
