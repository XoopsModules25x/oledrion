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
 * @copyright   The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author      Hervé Thouzard (http://www.herve-thouzard.com/)
 * @version     $Id: lists.php 12290 2014-02-07 11:05:17Z beckmi $
 */

/**
 * Gestion des listes
 *
 * @since 2.3.2009.06.13
 */
if (!defined("OLEDRION_ADMIN")) exit();
global $baseurl; // Pour faire taire les warnings de Zend Studio
$operation = 'lists';

switch ($action) {
    // ****************************************************************************************************************
    case 'default': // Liste des listes
        // ****************************************************************************************************************
        xoops_cp_header();
        global $xoopsConfig;
        $items = $usersList = array();
        $class = '';

        oledrion_utils::htitle(_MI_OLEDRION_ADMENU15, 4);

        $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
        $itemsCount = $oledrion_handlers->h_oledrion_lists->getRecentListsCount();
        if ($itemsCount > $limit) {
            $pagenav = new XoopsPageNav($itemsCount, $limit, $start, 'start', 'op=' . $operation);
        }
        $items = $oledrion_handlers->h_oledrion_lists->getRecentLists(new oledrion_parameters(array('start' => $start, 'limit' => $limit)));
        if (count($items) > 0) {
            $usersList = $oledrion_handlers->h_oledrion_lists->getUsersFromLists($items);
        }
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        if (isset($pagenav) && is_object($pagenav)) {
            echo "<tr><td colspan='2' align='left'>" . $pagenav->renderNav() . "</td><td align='right' colspan='3'>&nbsp;</td></tr>\n";
        }
        echo "<tr><th align='center'>" . _AM_OLEDRION_ID . "</th><th align='center'>" . _AM_OLEDRION_TITLE . "</th><th align='center'>" . _AM_OLEDRION_USER . "</th><th align='center'>" . _AM_OLEDRION_DATE . "</th><th align='center'>" . _AM_OLEDRION_TYPE . "</th><th align='center'>" . _AM_OLEDRION_ACTION . "</th></tr>";
        foreach ($items as $item) {
            $class = ($class == 'even') ? 'odd' : 'even';
            $id = $item->getVar('list_id');
            $actions = array();
            $actions[] = "<a href='$baseurl?op=$operation&action=delete&id=" . $id . "' title='" . _OLEDRION_DELETE . "'" . $conf_msg . ">" . $icones['delete'] . '</a>';
            $userName = isset($usersList[$item->list_uid]) ? $usersList[$item->list_uid]->getVar('uname') : _AM_OLEDRION_ANONYMOUS;
            echo "<tr class='" . $class . "'>\n";
            echo "<td align='center'>" . $id . "</td>";
            echo "<td align='left'><a target='blank' href='" . $item->getLink() . "'>" . $item->getVar('list_title') . "</a></td>";
            echo "<td align='center'><a target='_blank' href='" . XOOPS_URL . '/userinfo.php?uid=' . $item->list_uid . "'>" . $userName . "</td>";
            echo "<td align='center'>" . $item->getFormatedDate() . "</td>";
            echo "<td align='center'>" . $item->getListTypeDescription() . "</td>";
            echo "<td align='center'>" . implode(' ', $actions) . "</td>\n";
            echo "<tr>\n";
        }
        echo '</table>';
        if (isset($pagenav) && is_object($pagenav)) {
            echo "<div align='right'>" . $pagenav->renderNav() . "</div>";
        }
        include_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    // ****************************************************************************************************************
    case 'delete': // Suppression d'une liste
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (empty($id)) {
            oledrion_utils::redirect(_AM_OLEDRION_ERROR_1, $baseurl . '?op=' . $operation, 5);
        }
        $list = null;
        $list = $oledrion_handlers->h_oledrion_lists->get($id);
        if (!is_object($list)) {
            oledrion_utils::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl . '?op=' . $operation, 5);
        }
        if ($oledrion_handlers->h_oledrion_lists->deleteList($list)) {
            oledrion_utils::updateCache();
            oledrion_utils::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $operation, 2);
        } else {
            oledrion_utils::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $operation, 5);
        }
        break;
}
