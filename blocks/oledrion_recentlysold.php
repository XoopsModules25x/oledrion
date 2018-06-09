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
 * This block shows the products that were recently sold
 * @param  array $options [0] = Nombre maximum de produits à voir
 * @return array
 */
function b_oledrion_recentlysold_show($options)
{
    global $xoopsConfig, $xoTheme;
    require XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
    $categoryId = 0;
    $start      = 0;
    $limit      = $options[0];
    $shelfParameters->resetDefaultValues()->setProductsType('recentlysold')->setStart($start)->setLimit($limit);
    $products = $shelf->getProducts($shelfParameters);
    if (isset($products['lastTitle'])) {
        unset($products['lastTitle']);
    }
    if (count($products) > 0) {
        $block                   = [];
        $block['nostock_msg']    = Oledrion\Utility::getModuleOption('nostock_msg');
        $block['block_products'] = $products;
        $xoTheme->addStylesheet(OLEDRION_URL . 'assets/css/oledrion.css');

        return $block;
    } else {
        return false;
    }
}

/**
 * Edition des paramètres du blocs
 *
 * @param  array $options [0] = Nombre maximum de produits à voir
 * @return string
 */
function b_oledrion_recentlysold_edit($options)
{
    require XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
    $form = '';
    $form .= "<table border='0'>";
    $form .= '<tr><td>' . _MB_OLEDRION_PRODUCTS_CNT . "</td><td><input type='text' name='options[]' id='options' value='" . $options[0] . "'></td></tr>";
    $form .= '</table>';

    return $form;
}

/**
 * Bloc à la volée
 * @param  string|array $options
 * @return string
 */
function b_oledrion_recentlysold_duplicatable($options)
{
    $options = explode('|', $options);
    $block   = b_oledrion_bestsales_show($options);

    $tpl = new \XoopsTpl();
    $tpl->assign('block', $block);
    $tpl->display('db:oledrion_block_recentlysold.tpl');
}
