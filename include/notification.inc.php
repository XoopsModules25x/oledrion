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
 * @param $category
 * @param $item_id
 * @return mixed
 */

function oledrion_notify_iteminfo($category, $item_id)
{
    global $xoopsModule, $xoopsModuleConfig;
    $item_id = (int)$item_id;

    if (empty($xoopsModule) || $xoopsModule->getVar('dirname') !== 'oledrion') {
        /** @var XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $module         = $moduleHandler->getByDirname('oledrion');
        $configHandler = xoops_getHandler('config');
        $config         = $configHandler->getConfigsByCat(0, $module->getVar('mid'));
    } else {
        $module = $xoopsModule;
        // TODO: Jamais utilisé !!!
        $config = $xoopsModuleConfig;
    }

    if ($category === 'global') {
        $item['name'] = '';
        $item['url']  = '';

        return $item;
    }

    if ($category === 'new_category') {
        include OLEDRION_PATH . 'include/common.php';
        $category = null;
        $category = $h_oledrion_cat->get($item_id);
        if (is_object($category)) {
            $item['name'] = $category->getVar('cat_title');
            $item['url']  = OLEDRION_URL . 'category.php?cat_cid=' . $item_id;
        }

        return $item;
    }

    if ($category === 'new_product') {
        include OLEDRION_PATH . 'include/common.php';
        $product = null;
        $product = $h_oledrion_products->get($item_id);
        if (is_object($product)) {
            $item['name'] = $product->getVar('product_title');
            $item['url']  = OLEDRION_URL . 'product.php?product_id=' . $item_id;
        }

        return $item;
    }
}
