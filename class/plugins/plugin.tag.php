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
 * @param $items
 */
function oledrion_tag_iteminfo(&$items)
{
    include XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
    $items_id = array();
    foreach (array_keys($items) as $cat_id) {
        foreach (array_keys($items[$cat_id]) as $item_id) {
            $items_id[] = (int)$item_id;
        }
    }
    $items_obj = $h_oledrion_products->getItemsFromIds($items_id);

    foreach (array_keys($items) as $cat_id) {
        foreach (array_keys($items[$cat_id]) as $item_id) {
            $item_obj                 = $items_obj[$item_id];
            $items[$cat_id][$item_id] = array(
                'title'   => $item_obj->getVar('product_title'),
                'uid'     => $item_obj->getVar('product_submitter'),
                'link'    => $item_obj->getLink(0, '', true),
                'time'    => $item_obj->getVar('product_submitted'),
                'tags'    => '', // optional
                'content' => ''
            );
        }
    }
    unset($items_obj);
}

/** Remove orphan tag-item links *
 * @param $mid
 */
function oledrion_tag_synchronization($mid)
{
    global $xoopsDB;
    $itemHandler_keyName = 'product_id';
    $itemHandler_table   = $xoopsDB->prefix('oledrion_products');
    $linkHandler         = xoops_getModuleHandler('link', 'tag');
    $where               = '1=1';
    $where1              = '1=1';

    /* clear tag-item links */
    if ($linkHandler->mysql_major_version() >= 4):
        $sql = "    DELETE FROM {$linkHandler->table}"
               . '   WHERE '
               . "       tag_modid = {$mid}"
               . '       AND '
               . '       ( tag_itemid NOT IN '
               . "           ( SELECT DISTINCT {$itemHandler_keyName} "
               . "               FROM {$itemHandler_table} "
               . "               WHERE $where"
               . '           ) '
               . '       )';
    else:
        $sql = "    DELETE {$linkHandler->table} FROM {$linkHandler->table}"
               . "   LEFT JOIN {$itemHandler_table} AS aa ON {$linkHandler->table}.tag_itemid = aa.{$itemHandler_keyName} "
               . '   WHERE '
               . "       tag_modid = {$mid}"
               . '       AND '
               . "       ( aa.{$itemHandler_keyName} IS NULL"
               . "           OR $where1"
               . '       )';
    endif;
    if (!$linkHandler->db->queryF($sql)) {
        trigger_error($linkHandler->db->error());
    }
}
