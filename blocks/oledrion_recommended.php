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
 * Renvoie la liste des produits recommandés
 * @param $options
 * @return array|bool
 */
function b_oledrion_recomm_show($options)
{
    // '10|0';  // Voir 10 produits, pour toutes les catégories ou une catégorie particulière
    global $xoopsConfig, $xoTheme;
    include XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
    $products   = $block = [];
    $start      = 0;
    $limit      = $options[0];
    $categoyrId = $options[1];

    $shelfParameters->resetDefaultValues()->setProductsType('recommended')->setStart($start)->setLimit($limit)->setSort('product_recommended')->setOrder('DESC')->setCategory($categoyrId);
    $products = $shelf->getProducts($shelfParameters);

    if ($productsHandler->getRecommendedCount() > $limit) { // Il y a plus de produits recommandés dans la BDD que dans le bloc, on affiche donc un lien vers la page des produits recommandés
        $block['showMore'] = true;
    }
    if (isset($products['lastTitle'])) {
        unset($products['lastTitle']);
    }
    if (count($products) > 0) {
        $block['nostock_msg']    = Oledrion\Utility::getModuleOption('nostock_msg');
        $block['block_products'] = $products;
        $xoTheme->addStylesheet(OLEDRION_URL . 'assets/css/oledrion.css');

        return $block;
    } else { // Pas de produits recommandés

        return false;
    }
}

/**
 * Paramètres du bloc
 * @param $options
 * @return string
 */
function b_oledrion_recomm_edit($options)
{
    // '10|0';  // Voir 10 produits, pour toutes les catégories
    global $xoopsConfig;
    include XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
    // require_once OLEDRION_PATH . 'class/tree.php';
    $tblCategories         = [];
    $tblCategories         = $categoryHandler->getAllCategories(new Oledrion\Parameters());
    $mytree                = new Oledrion\XoopsObjectTree($tblCategories, 'cat_cid', 'cat_pid');
    $form                  = '';
    $checkeds              = ['', ''];
    $checkeds[$options[1]] = 'checked';
    $form                  .= "<table border='0'>";
    $form                  .= '<tr><td>' . _MB_OLEDRION_PRODUCTS_CNT . "</td><td><input type='text' name='options[]' id='options' value='" . $options[0] . "'></td></tr>";

    //$select                = $mytree->makeSelBox('options[]', 'cat_title', '-', $options[1], _MB_OLEDRION_ALL_CATEGORIES);

    if (Oledrion\Utility::checkVerXoops($GLOBALS['xoopsModule'], '2.5.9')) {
        $select0 = $mytree->makeSelectElement('options[]', 'cat_title', '-', $options[1], true, 0, '', _MB_OLEDRION_ALL_CATEGORIES);
        $select  = $select0->render();
    } else {
        $select = $mytree->makeSelBox('options[]', 'cat_title', '-', $options[1], _MB_OLEDRION_ALL_CATEGORIES);
    }

    $form .= '<tr><td>' . _MB_OLEDRION_CATEGORY . '</td><td>' . $select . '</td></tr>';
    $form .= '</table>';

    return $form;
}

/**
 * Bloc à la volée
 * @param $options
 */
function b_oledrion_recomm_show_duplicatable($options)
{
    $options = explode('|', $options);
    $block   = b_oledrion_recomm_show($options);

    $tpl = new \XoopsTpl();
    $tpl->assign('block', $block);
    $tpl->display('db:oledrion_block_recommended.tpl');
}
