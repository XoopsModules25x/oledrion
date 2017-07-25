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

require_once __DIR__ . '/../../mainfile.php';
require_once __DIR__ . '/header.php';
$com_itemid = isset($_GET['com_itemid']) ? (int)$_GET['com_itemid'] : 0;
if ($com_itemid > 0) {
    include XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
    $product = null;
    $product = $h_oledrion_products->get($com_itemid);
    if (is_object($product)) {
        $com_replytitle = $product->getVar('product_title');
        require XOOPS_ROOT_PATH . '/include/comment_new.php';
    } else {
        exit();
    }
}
