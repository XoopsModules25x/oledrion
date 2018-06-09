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

/**
 * Flux RSS pour suivre les derniers produits
 */

use XoopsModules\Oledrion;

require_once __DIR__ . '/header.php';
require_once XOOPS_ROOT_PATH . '/class/template.php';

if (0 == Oledrion\Utility::getModuleOption('use_rss')) {
    exit;
}
// Paramètre, soit rien auquel cas on prend tous les produits récents soit cat_cid
$cat_cid = \Xmf\Request::getInt('cat_cid', 0, 'GET');
if (function_exists('mb_http_output')) {
    mb_http_output('pass');
}
$charset = 'utf-8';
header('Content-Type:text/xml; charset=' . $charset);
$tpl = new \XoopsTpl();

$tpl->caching = 2; // 1 = Cache global, 2 = Cache individuel (par template)
$tpl->xoops_setCacheTime(OLEDRION_RSS_CACHE); // Temps de cache en secondes

if (!$tpl->is_cached('db:oledrion_rss.tpl', $cat_cid)) {
    $categoryTitle = '';
    if (!empty($cat_cid)) {
        $category = null;
        $category = $categoryHandler->get($cat_cid);
        if (is_object($category)) {
            $categoryTitle = $category->getVar('cat_title');
        }
    }
    $sitename = htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES);
    $email    = checkEmail($xoopsConfig['adminmail'], true);
    $slogan   = htmlspecialchars($xoopsConfig['slogan'], ENT_QUOTES);
    $limit    = Oledrion\Utility::getModuleOption('perpage');

    $tpl->assign('charset', $charset);
    $tpl->assign('channel_title', xoops_utf8_encode($sitename));
    $tpl->assign('channel_link', XOOPS_URL . '/');
    $tpl->assign('channel_desc', xoops_utf8_encode($slogan));
    $tpl->assign('channel_lastbuild', formatTimestamp(time(), 'rss'));
    $tpl->assign('channel_webmaster', xoops_utf8_encode($email));
    $tpl->assign('channel_editor', xoops_utf8_encode($email));
    $tpl->assign('channel_category', xoops_utf8_encode($categoryTitle));
    $temp = Oledrion\Utility::getModuleName();
    $tpl->assign('channel_generator', xoops_utf8_encode($temp));
    $tpl->assign('channel_language', _LANGCODE);
    $tpl->assign('image_url', XOOPS_URL . '/images/logo.png');
    $dimention = getimagesize(XOOPS_ROOT_PATH . '/images/logo.png');
    if (empty($dimention[0])) {
        $width = 88;
    } else {
        $width = ($dimention[0] > 144) ? 144 : $dimention[0];
    }
    if (empty($dimention[1])) {
        $height = 31;
    } else {
        $height = ($dimention[1] > 400) ? 400 : $dimention[1];
    }
    $tpl->assign('image_width', $width);
    $tpl->assign('image_height', $height);

    $products = $productsHandler->getRecentProducts(new Oledrion\Parameters([
                                                                                'start'    => 0,
                                                                                'limit'    => $limit,
                                                                                'category' => $cat_cid
                                                                            ]));
    foreach ($products as $item) {
        $title       = htmlspecialchars($item->getVar('product_title'), ENT_QUOTES);
        $description = htmlspecialchars(strip_tags($item->getVar('product_summary')), ENT_QUOTES);
        $link        = $item->getLink();
        $tpl->append('items', [
            'title'       => xoops_utf8_encode($title),
            'link'        => $link,
            'guid'        => $link,
            'pubdate'     => formatTimestamp($item->getVar('product_submitted'), 'rss'),
            'description' => xoops_utf8_encode($description)
        ]);
    }
}
$tpl->display('db:oledrion_rss.tpl', $cat_cid);
