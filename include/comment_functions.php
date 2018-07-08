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
 * @param $product_id
 * @param $total_num
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

function oledrion_com_update($product_id, $total_num)
{
    require_once XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
    global $productsHandler;
    if (!is_object($productsHandler)) {
        //        $handlers            = HandlerManager::getInstance();
        $productsHandler = $handlers->oledrion_products;
    }
    $productsHandler->updateCommentsCount($product_id, $total_num);
}

/**
 * @param $comment
 */
function oledrion_com_approve(&$comment)
{
    // notification mail here
}
