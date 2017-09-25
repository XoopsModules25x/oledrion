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
 * @author      Hossein Azizabadi (azizabadi@faragostaresh.com)
 */

/**
 * Check is admin
 */
if (!defined('OLEDRION_ADMIN')) {
    exit();
}

switch ($action) {
    case 'default':
        xoops_cp_header();
        xoops_confirm(['op' => 'maintain', 'action' => 'confirm'], 'index.php', _AM_OLEDRION_CONF_MAINTAIN);
        break;

    case 'confirm':
        xoops_cp_header();
        require OLEDRION_PATH . 'xoops_version.php';
        $tables = [];
        foreach ($modversion['tables'] as $table) {
            $tables[] = $xoopsDB->prefix($table);
        }
        if (count($tables) > 0) {
            $list = implode(',', $tables);
            $xoopsDB->queryF('CHECK TABLE ' . $list);
            $xoopsDB->queryF('ANALYZE TABLE ' . $list);
            $xoopsDB->queryF('OPTIMIZE TABLE ' . $list);
        }
        OledrionUtility::updateCache();
        $h_oledrion_products->forceCacheClean();
        OledrionUtility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl, 2);
        break;

    case 'import':
        xoops_cp_header();
        $categories = $h_oledrion_cat->getCategoriesCount();
        if (0 == $categories) {
            xoops_confirm(['op' => 'maintain', 'action' => 'doimport'], 'index.php', _AM_OLEDRION_IMPORT_CONF);
        } else {
            OledrionUtility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl, 2);
        }
        break;

    case 'doimport':
        xoops_cp_header();
        $categories = $h_oledrion_cat->getCategoriesCount();
        if (0 == $categories) {
            $cat_array = ['cat_cid' => 1, 'cat_pid' => 0, 'cat_title' => 'Test category'];
            $cat       = $h_oledrion_cat->create();
            $cat->setVars($cat_array);
            $res = $h_oledrion_cat->insert($cat);

            $manufacturer_array = ['manu_id' => 1, 'manu_name' => 'Test manufacturer'];
            $manufacturer       = $h_oledrion_manufacturer->create(true);
            $manufacturer->setVars($manufacturer_array);
            $res = $h_oledrion_manufacturer->insert($manufacturer);

            $product_array = [
                'product_id'        => 1,
                'product_cid'       => 1,
                'product_title'     => 'Test product',
                'product_vendor_id' => 1,
                'product_submitter' => 1,
                'product_online'    => 1,
                'product_submitted' => time(),
                'product_price'     => '100',
                'product_summary'   => 'Test test test test test test test test test test test test test test test test test',
                'product_vat_id'    => 1,
                'product_stock'     => 100
            ];
            $product       = $h_oledrion_products->create(true);
            $product->setVars($product_array);
            $res = $h_oledrion_products->insert($product);

            $productsmanu_array = ['pm_id' => 1, 'pm_id' => 1, 'pm_manu_id' => 1];
            $productsmanu       = $h_oledrion_productsmanu->create(true);
            $productsmanu->setVars($productsmanu_array);
            $res = $h_oledrion_products->insert($productsmanu);

            $vat_array = ['vat_id' => 1, 'vat_rate' => '0.00', 'vat_country' => 'us'];
            $vat       = $h_oledrion_vat->create(true);
            $vat->setVars($vat_array);
            $res = $h_oledrion_vat->insert($vat);

            $vendor_array = ['vendor_id' => 1, 'vendor_name' => 'Test vendor'];
            $vendor       = $h_oledrion_vendors->create(true);
            $vendor->setVars($vendor_array);
            $res = $h_oledrion_vendors->insert($vendor);
        }
        OledrionUtility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl, 2);
        break;
}
