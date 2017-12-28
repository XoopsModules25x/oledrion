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

use Xoopsmodules\oledrion;

/**
 * Plan des catégories
 */
require_once __DIR__ . '/header.php';
$GLOBALS['current_category']             = -1;
$GLOBALS['xoopsOption']['template_main'] = 'oledrion_map.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
// require_once OLEDRION_PATH . 'class/tree.php';

$xoopsTpl->assign('mod_pref', $mod_pref); // Préférences du module
$categories = [];
$categories = $categoryHandler->getAllCategories(new oledrion\Parameters());
$mytree     = new oledrion\XoopsObjectTree($categories, 'cat_cid', 'cat_pid');
$tree       = $mytree->makeTreeAsArray('cat_title', '-');
foreach ($tree as $key => $value) {
    if (isset($categories[$key])) {
        $category = $categories[$key];
        $xoopsTpl->append('categories', [
            'cat_url_rewrited' => $category->getLink(),
            'cat_href_title'   => $category->getHrefTitle(),
            'cat_title'        => $value
        ]);
    }
}

oledrion\Utility::setCSS();
oledrion\Utility::setLocalCSS($xoopsConfig['language']);
$helper->loadLanguage('modinfo');

$xoopsTpl->assign('global_advert', oledrion\Utility::getModuleOption('advertisement'));
$xoopsTpl->assign('breadcrumb', oledrion\Utility::breadcrumb([OLEDRION_URL . basename(__FILE__) => _MI_OLEDRION_SMNAME4]));

$title = _MI_OLEDRION_SMNAME4 . ' - ' . oledrion\Utility::getModuleName();
oledrion\Utility::setMetas($title, $title);
require_once XOOPS_ROOT_PATH . '/footer.php';
