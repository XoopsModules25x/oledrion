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
 * @version     $Id: categories-map.php 12290 2014-02-07 11:05:17Z beckmi $
 */

/**
 * Plan des catégories
 */
require 'header.php';
$GLOBALS['current_category'] = -1;
$xoopsOption['template_main'] = 'oledrion_map.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once OLEDRION_PATH . 'class/tree.php';

$xoopsTpl->assign('mod_pref', $mod_pref); // Préférences du module
$categories = array();
$categories = $h_oledrion_cat->getAllCategories(new oledrion_parameters());
$mytree = new Oledrion_XoopsObjectTree($categories, 'cat_cid', 'cat_pid');
$tree = $mytree->makeTreeAsArray('cat_title', '-');
foreach ($tree as $key => $value) {
    if (isset($categories[$key])) {
        $category = $categories[$key];
        $xoopsTpl->append('categories', array('cat_url_rewrited' => $category->getLink(), 'cat_href_title' => $category->getHrefTitle(), 'cat_title' => $value));
    }
}

oledrion_utils::setCSS();
oledrion_utils::setLocalCSS($xoopsConfig['language']);
oledrion_utils::loadLanguageFile('modinfo.php');

$xoopsTpl->assign('global_advert', oledrion_utils::getModuleOption('advertisement'));
$xoopsTpl->assign('breadcrumb', oledrion_utils::breadcrumb(array(OLEDRION_URL . basename(__FILE__) => _MI_OLEDRION_SMNAME4)));

$title = _MI_OLEDRION_SMNAME4 . ' - ' . oledrion_utils::getModuleName();
oledrion_utils::setMetas($title, $title);
require_once XOOPS_ROOT_PATH . '/footer.php';
