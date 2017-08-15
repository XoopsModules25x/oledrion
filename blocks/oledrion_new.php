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
 * Affiche les produits récents
 * @param $options
 * @return array|bool
 */
function b_oledrion_new_show($options)
{
    // '10|0|0';    // Voir 10 produits, pour toutes les catégories ou une catégorie particulière, uniquement les produits du mois ?
    global $xoopsConfig, $xoTheme;
    include XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
    $start         = 0;
    $limit         = $options[0];
    $categoryId    = $options[1];
    $thisMonthOnly = (int)$options[2];

    $oledrion_shelf_parameters->resetDefaultValues()->setProductsType('recent')->setStart($start)->setLimit($limit)->setSort('product_id DESC, product_title')->setCategory($categoryId)->setThisMonthOnly($thisMonthOnly);
    $products = $oledrion_shelf->getProducts($oledrion_shelf_parameters);
    if (isset($products['lastTitle'])) {
        unset($products['lastTitle']);
    }
    if (count($products) > 0) {
        $block                   = [];
        $block['nostock_msg']    = OledrionUtility::getModuleOption('nostock_msg');
        $block['block_products'] = $products;
        $xoTheme->addStylesheet(OLEDRION_URL . 'assets/css/oledrion.css');

        return $block;
    } else {
        return false;
    }
}

/**
 * Paramètres du bloc
 * @param $options
 * @return string
 */
function b_oledrion_new_edit($options)
{
    // '10|0|0';    // Voir 10 produits, pour toutes les catégories, uniquement les produits du mois ?
    global $xoopsConfig;
    include XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
    require_once OLEDRION_PATH . 'class/tree.php';
    $tblCategories = [];
    $tblCategories = $h_oledrion_cat->getAllCategories(new Oledrion_parameters());
    $mytree        = new Oledrion_XoopsObjectTree($tblCategories, 'cat_cid', 'cat_pid');
    $form          = '';
    $form          .= "<table border='0'>";
    $form          .= '<tr><td>' . _MB_OLEDRION_PRODUCTS_CNT . "</td><td><input type='text' name='options[]' id='options' value='" . $options[0] . "'></td></tr>";
    $select        = $mytree->makeSelBox('options[]', 'cat_title', '-', $options[1], _MB_OLEDRION_ALL_CATEGORIES);
    $form          .= '<tr><td>' . _MB_OLEDRION_CATEGORY . '</td><td>' . $select . '</td></tr>';

    $checked              = ['', ''];
    $checked[$options[2]] = 'checked';
    $form                 .= '<tr><td>' . _MB_OLEDRION_THIS_MONTH . "</td><td><input type='radio' name='options[]' id='options' value='1'" . $checked[1] . '>' . _YES . " <input type='radio' name='options[]' id='options' value='0'" . $checked[0] . '>' . _NO . '</td></tr>';

    $form .= '</table>';

    return $form;
}

/**
 * Bloc à la volée
 * @param $options
 */
function b_oledrion_new_show_duplicatable($options)
{
    $options = explode('|', $options);
    $block   = b_oledrion_new_show($options);

    $tpl = new XoopsTpl();
    $tpl->assign('block', $block);
    $tpl->display('db:oledrion_block_new.tpl');
}
