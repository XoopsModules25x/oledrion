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
 * Affichage des listes au hasard
 *
 * @param  array $options [0] = Nombre maximum de listes à voir, [1] = Type de listes (0 = les 2, 1 = liste cadeaux, 2 = produits recommandés)
 * @return array
 */
function b_oledrion_random_lists_show($options)
{
    require XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
    Oledrion_utils::loadLanguageFile('main.php');
    $start    = 0;
    $limit    = (int)$options[0];
    $listType = (int)$options[1];
    $block    = array();
    $handlers = OledrionHandler::getInstance();
    $items    = array();
    //$items = $handlers->h_oledrion_lists->getRecentLists(new Oledrion_parameters(array('start' => $start, 'limit' => $limit, 'sort' => 'RAND()', 'order' => 'DESC', 'idAsKey' => true, 'listType' => $listType)));
    $items = $handlers->h_oledrion_lists->getRecentLists(new Oledrion_parameters(array(
                                                                                     'start'    => $start,
                                                                                     'limit'    => $limit,
                                                                                     'sort'     => 'RAND()',
                                                                                     'order'    => 'DESC',
                                                                                     'idAsKey'  => true,
                                                                                     'listType' => OLEDRION_LISTS_ALL_PUBLIC
                                                                                 )));
    if (count($items) > 0) {
        foreach ($items as $item) {
            $block['random_lists'][] = $item->toArray();
        }
    }

    return $block;
}

/**
 * Edition des paramètres du bloc
 *
 * @param  array $options [0] = Nombre maximum de listes à voir, [1] = Type de listes (0 = les 2, 1 = liste cadeaux, 2 = produits recommandés)
 * @return array
 */
function b_oledrion_random_lists_edit($options)
{
    include XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
    $form = '';
    $form .= "<table border='0'>";
    $form .= '<tr><td>' . _MB_OLEDRION_LISTS_COUNT . "</td><td><input type='text' name='options[]' id='options' value='" . (int)$options[0] . "' /></td></tr>";
    $listTypes      = Oledrion_lists::getTypesArray();
    $listTypeSelect = Oledrion_utils::htmlSelect('options[]', $listTypes, (int)$options[1], false);
    $form .= '<tr><td>' . _MB_OLEDRION_LISTS_TYPE . '</td><td>' . $listTypeSelect . '</td></tr>';
    $form .= '</table>';

    return $form;
}

/**
 * Bloc à la volée
 * @param $options
 */
function b_oledrion_random_lists_duplicatable($options)
{
    $options = explode('|', $options);
    $block   = b_oledrion_random_lists_show($options);

    $tpl = new XoopsTpl();
    $tpl->assign('block', $block);
    $tpl->display('db:oledrion_block_random_lists.tpl');
}
