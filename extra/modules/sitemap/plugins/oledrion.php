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

use XoopsModules\Oledrion;

/**
 * @return array
 */
function b_sitemap_oledrion()
{
    require_once  dirname(__DIR__) . '/oledrion/header.php';
    global $sitemap_configs;
    $xoopsDB    = \XoopsDatabaseFactory::getDatabaseConnection();
    $table      = $xoopsDB->prefix('oledrion_cat');
    $id_name    = 'cat_cid';
    $pid_name   = 'cat_pid';
    $title_name = 'cat_title';
    $url        = 'category.php?cat_cid=';
    $order      = 'cat_title';

    require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
    $mytree  = new \XoopsTree($table, $id_name, $pid_name);
    $xoopsDB = \XoopsDatabaseFactory::getDatabaseConnection();

    $sitemap = [];
    $myts    = \MyTextSanitizer::getInstance();

    $i   = 0;
    $sql = "SELECT `$id_name`,`$title_name` FROM `$table` WHERE `$pid_name`=0";
    if ('' !== $order) {
        $sql .= " ORDER BY `$order`";
    }
    $result = $xoopsDB->query($sql);
    while (false !== (list($catid, $name) = $xoopsDB->fetchRow($result))) {
        $sitemap['parent'][$i]['id']    = $catid;
        $sitemap['parent'][$i]['title'] = $myts->htmlSpecialChars($name);
        if (1 == Oledrion\Utility::getModuleOption('urlrewriting')) { // On utilise l'url rewriting
            $url = 'category' . '-' . (int)$catid . Oledrion\Utility::makeSeoUrl($name) . '.html';
        } else { // Pas d'utilisation de l'url rewriting
            $url = 'category.php?cat_cid=' . (int)$catid;
        }
        $sitemap['parent'][$i]['url'] = $url;

        if (@$sitemap_configs['show_subcategoris']) {
            $j         = 0;
            $child_ary = $mytree->getChildTreeArray($catid, $order);
            foreach ($child_ary as $child) {
                $count                                       = strlen($child['prefix']) + 1;
                $sitemap['parent'][$i]['child'][$j]['id']    = $child[$id_name];
                $sitemap['parent'][$i]['child'][$j]['title'] = $myts->htmlSpecialChars($child[$title_name]);
                $sitemap['parent'][$i]['child'][$j]['image'] = (($count > 3) ? 4 : $count);
                if (1 == Oledrion\Utility::getModuleOption('urlrewriting')) { // On utilise l'url rewriting
                    $url = 'category' . '-' . (int)$child[$id_name] . Oledrion\Utility::makeSeoUrl($child[$title_name]) . '.html';
                } else { // Pas d'utilisation de l'url rewriting
                    $url = 'category.php?cat_cid=' . (int)$child[$id_name];
                }
                $sitemap['parent'][$i]['child'][$j]['url'] = $url;

                ++$j;
            }
        }
        ++$i;
    }

    return $sitemap;
}
