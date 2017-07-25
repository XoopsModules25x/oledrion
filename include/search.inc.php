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
 * @param $queryarray
 * @param $andor
 * @param $limit
 * @param $offset
 * @param $userid
 * @return array
 */

function oledrion_search($queryarray, $andor, $limit, $offset, $userid)
{
    global $xoopsDB;
    require XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
    require_once XOOPS_ROOT_PATH . '/modules/oledrion/class/oledrion_products.php';

    // Recherche dans les produits
    $sql = 'SELECT product_id, product_title, product_submitted, product_submitter FROM ' . $xoopsDB->prefix('oledrion_products') . ' WHERE (product_online = 1';
    if (OledrionUtility::getModuleOption('show_unpublished') == 0) { // Ne pas afficher les produits qui ne sont pas publiés
        $sql .= ' AND product_submitted <= ' . time();
    }
    if (OledrionUtility::getModuleOption('nostock_display') == 0) { // Se limiter aux seuls produits encore en stock
        $sql .= ' AND product_stock > 0';
    }
    if ($userid != 0) {
        $sql .= '  AND product_submitter = ' . $userid;
    }
    $sql .= ') ';

    $tmpObject = new oledrion_products();
    $datas     =& $tmpObject->getVars();
    $tblFields = array();
    $cnt       = 0;
    foreach ($datas as $key => $value) {
        if ($value['data_type'] == XOBJ_DTYPE_TXTBOX || $value['data_type'] == XOBJ_DTYPE_TXTAREA) {
            if ($cnt == 0) {
                $tblFields[] = $key;
            } else {
                $tblFields[] = ' OR ' . $key;
            }
            ++$cnt;
        }
    }

    $count = count($queryarray);
    $more  = '';
    if (is_array($queryarray) && $count > 0) {
        $cnt  = 0;
        $sql  .= ' AND (';
        $more = ')';
        foreach ($queryarray as $oneQuery) {
            $sql  .= '(';
            $cond = " LIKE '%" . $oneQuery . "%' ";
            $sql  .= implode($cond, $tblFields) . $cond . ')';
            ++$cnt;
            if ($cnt != $count) {
                $sql .= ' ' . $andor . ' ';
            }
        }
    }
    $sql    .= $more . ' ORDER BY product_submitted DESC';
    $i      = 0;
    $ret    = array();
    $myts   = MyTextSanitizer::getInstance();
    $result = $xoopsDB->query($sql, $limit, $offset);
    while ($myrow = $xoopsDB->fetchArray($result)) {
        $ret[$i]['image'] = 'assets/images/product.png';
        $ret[$i]['link']  = 'product.php?product_id=' . $myrow['product_id'];
        $ret[$i]['title'] = $myts->htmlSpecialChars($myrow['product_title']);
        $ret[$i]['time']  = $myrow['product_submitted'];
        $ret[$i]['uid']   = $myrow['product_submitter'];
        ++$i;
    }

    return $ret;
}
