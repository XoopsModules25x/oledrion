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

/**
 * Affiche les produits les plus vus
 * @param $options
 * @return array|bool
 */
function b_oledrion_top_show($options)
{
    // '10|0';  // Voir 10 produits, pour toutes les catégories ou une catégorie particulière
    global $xoopsConfig, $xoTheme;
    require_once XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
    $products   = $block = [];
    $start      = 0;
    $limit      = $options[0];
    $categoyrId = $options[1];
    $shelfParameters->resetDefaultValues()->setProductsType('mostviewed')->setStart($start)->setLimit($limit)->setSort('product_hits')->setOrder('DESC')->setCategory($categoyrId);
    $products = $shelf->getProducts($shelfParameters);
    if (isset($products['lastTitle'])) {
        unset($products['lastTitle']);
    }
    if (count($products) > 0) {
        $block['nostock_msg']    = Oledrion\Utility::getModuleOption('nostock_msg');
        $block['block_products'] = $products;
        $xoTheme->addStylesheet(OLEDRION_URL . 'assets/css/oledrion.css');

        return $block;
    }

    // La liste des produits est introuvable (on ne trouve pas les produits vendus dans le stock des produits)

    return false;
}

/**
 * Paramètres du bloc
 * @param $options
 * @return string
 */
function b_oledrion_top_edit($options)
{
    // '10|0';  // Voir 10 produits, pour toutes les catégories
    global $xoopsConfig;
    require_once XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
    // require_once OLEDRION_PATH . 'class/tree.php';
    $tblCategories         = [];
    $tblCategories         = $categoryHandler->getAllCategories(new Oledrion\Parameters());
    $mytree                = new Oledrion\XoopsObjectTree($tblCategories, 'cat_cid', 'cat_pid');
    $form                  = '';
    $checkeds              = ['', ''];
    $checkeds[$options[1]] = 'checked';
    $form                  .= "<table border='0'>";
    $form                  .= '<tr><td>' . _MB_OLEDRION_PRODUCTS_CNT . "</td><td><input type='text' name='options[]' id='options' value='" . $options[0] . "'></td></tr>";
    //$form .= '<tr><td>'._MB_OLEDRION_SORT_ORDER."</td><td><input type='radio' name='options[]' id='options[]' value='0' ".$checkeds[0].">"._MB_OLEDRION_SORT_1." <input type='radio' name='options[]' id='options[]' value='1' ".$checkeds[1].">"._MB_OLEDRION_SORT_2.'</td></tr>';

    $select0 = $mytree->makeSelectElement('options[]', 'cat_title', '-', $options[1], true, 0, '', _MB_OLEDRION_ALL_CATEGORIES);
    $select  = $select0->render();

    $form .= '<tr><td>' . _MB_OLEDRION_CATEGORY . '</td><td>' . $select . '</td></tr>';
    $form .= '</table>';

    return $form;
}

/**
 * Bloc à la volée
 * @param $options
 */
function b_oledrion_top_show_duplicatable($options)
{
    $options = explode('|', $options);
    $block   = b_oledrion_top_show($options);

    $tpl = new \XoopsTpl();
    $tpl->assign('block', $block);
    $tpl->display('db:oledrion_block_top.tpl');
}
