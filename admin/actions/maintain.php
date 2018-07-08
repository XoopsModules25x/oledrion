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

use XoopsModules\Oledrion;

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
        require_once OLEDRION_PATH . 'xoops_version.php';
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
        Oledrion\Utility::updateCache();
        $productsHandler->forceCacheClean();
        Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl, 2);

        break;
    case 'import':

        xoops_cp_header();
        $categories = $categoryHandler->getCategoriesCount();
        if (0 == $categories) {
            xoops_confirm(['op' => 'maintain', 'action' => 'doimport'], 'index.php', _AM_OLEDRION_IMPORT_CONF);
        } else {
            Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl, 2);
        }

        break;
    case 'doimport':

        xoops_cp_header();
        $categories = $categoryHandler->getCategoriesCount();
        if (0 == $categories) {
            $cat_array = ['cat_cid' => 1, 'cat_pid' => 0, 'cat_title' => 'Test category'];
            $cat       = $categoryHandler->create();
            $cat->setVars($cat_array);
            $res = $categoryHandler->insert($cat);

            $manufacturer_array = ['manu_id' => 1, 'manu_name' => 'Test manufacturer'];
            $manufacturer       = $manufacturerHandler->create(true);
            $manufacturer->setVars($manufacturer_array);
            $res = $manufacturerHandler->insert($manufacturer);

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
                'product_stock'     => 100,
            ];
            $product       = $productsHandler->create(true);
            $product->setVars($product_array);
            $res = $productsHandler->insert($product);

            $productsmanu_array = ['pm_id' => 1, 'pm_id' => 1, 'pm_manu_id' => 1];
            $productsmanu       = $productsmanuHandler->create(true);
            $productsmanu->setVars($productsmanu_array);
            $res = $productsHandler->insert($productsmanu);

            $vat_array = ['vat_id' => 1, 'vat_rate' => '0.00', 'vat_country' => 'us'];
            $vat       = $vatHandler->create(true);
            $vat->setVars($vat_array);
            $res = $vatHandler->insert($vat);

            $vendor_array = ['vendor_id' => 1, 'vendor_name' => 'Test vendor'];
            $vendor       = $vendorsHandler->create(true);
            $vendor->setVars($vendor_array);
            $res = $vendorsHandler->insert($vendor);
        }
        Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl, 2);

        break;
}
