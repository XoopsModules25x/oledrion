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
 * @author      Hossein Azizabadi (AKA Voltan)
 */

$productid      = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$com_replytitle = '';
if ($product_id > 0) {
    require_once XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
    $product = null;
    $product = $h_oledrion_products->get($product_id);
    if (is_object($product)) {
        $com_replytitle = $product->getVar('product_title');
    }
}
