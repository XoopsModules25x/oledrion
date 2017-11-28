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
 * Notation d'un produit
 */
require_once __DIR__ . '/header.php';
\Xoopsmodules\oledrion\Utility::redirect(_OLEDRION_NORATE, 'index.php', 5);
/*
$product_id = 0;
// Les tests **************************************************************************************
// Peut on voter ?
if (\Xoopsmodules\oledrion\Utility::getModuleOption('rateproducts') == 0 ) {
    \Xoopsmodules\oledrion\Utility::redirect(_OLEDRION_NORATE, 'index.php', 5);
}
// Recherche du n° du produit
if (isset($_GET['product_id'])) {
    $product_id = (int) $_GET['product_id'];
} elseif (isset($_POST['product_id'])) {
    $product_id = (int) $_POST['product_id'];
} else {
    \Xoopsmodules\oledrion\Utility::redirect(_OLEDRION_ERROR1, 'index.php', 5);
}
// Le produit existe ?
$product = null;
$product = $h_oledrion_products->get($product_id);
if (!is_object($product)) {
    \Xoopsmodules\oledrion\Utility::redirect(_OLEDRION_ERROR1, 'index.php', 5);
}

// Le produit est il online ?
if ($product->getVar('product_online') == 0) {
    \Xoopsmodules\oledrion\Utility::redirect(_OLEDRION_ERROR2, 'index.php', 5);
}

// Le produit est il publié ?
if (\Xoopsmodules\oledrion\Utility::getModuleOption('show_unpublished') == 0 && $product->getVar('product_submitted') > time()) {
    \Xoopsmodules\oledrion\Utility::redirect(_OLEDRION_ERROR3, 'index.php', 5);
}

// Faut il afficher les produits même lorsqu'ils ne sont plus en stock ?
if (\Xoopsmodules\oledrion\Utility::getModuleOption('nostock_display') == 0 && $product->getVar('product_stock') == 0) {
    if (xoops_trim(\Xoopsmodules\oledrion\Utility::getModuleOption('nostock_display')) != '') {
        \Xoopsmodules\oledrion\Utility::redirect(\Xoopsmodules\oledrion\Utility::getModuleOption('nostock_display'), 'main.php', 5);
    }
}

// Fin des tests, si on est encore là c'est que tout est bon **************************************
if (!empty($_POST['btnsubmit'])) {
    $GLOBALS['current_category'] = -1;

    $ratinguser = \Xoopsmodules\oledrion\Utility::getCurrentUserID();
    $canRate = true;
    if ($ratinguser != 0) {
        if ($h_oledrion_votedata->hasUserAlreadyVoted($ratinguser, $product->getVar('product_id'))) {
            $canRate = false;
        }
    } else {
        if ($h_oledrion_votedata->hasAnonymousAlreadyVoted('', $product->getVar('product_id'))) {
            $canRate = false;
        }
    }
    if ($canRate) {
        if ($_POST['rating'] == '--') {
            \Xoopsmodules\oledrion\Utility::redirect(_OLEDRION_NORATING, OLEDRION_URL.'product.php?product_id='.$product->getVar('product_id'),4);
        }
        $rating = (int) $_POST['rating'];
        if ($rating <1 || $rating > 10) {
            exit(_ERRORS);
        }
        $result = $h_oledrion_votedata->createRating($product->getVar('product_id'), $ratinguser, $rating);

        // Calcul du nombre de votes et du total des votes pour mettre à jour les informations du produit
        $totalVotes = 0;
        $sumRating = 0;
        $ret = 0;
        $ret = $h_oledrion_votedata->getCountRecordSumRating($product->getVar('product_id'), $totalVotes, $sumRating);

        $finalrating = $sumRating / $totalVotes;
        $finalrating = number_format($finalrating, 4);
        $h_oledrion_products->updateRating($product_id, $finalrating, $totalVotes);
        $ratemessage = _OLEDRION_VOTEAPPRE.'<br>'.sprintf(_OLEDRION_THANKYOU,$xoopsConfig['sitename']);
        \Xoopsmodules\oledrion\Utility::redirect($ratemessage, OLEDRION_URL.'product.php?product_id='.$product->getVar('product_id'), 2);
    } else {
        \Xoopsmodules\oledrion\Utility::redirect(_OLEDRION_VOTEONCE, OLEDRION_URL.'product.php?product_id='.$product->getVar('product_id'),5);
    }
} else {    // Affichage du formulaire
    $GLOBALS['current_category'] = $product->getVar('product_cid');
    $GLOBALS['xoopsOption']['template_main'] = 'oledrion_rate_product.html';
    require_once XOOPS_ROOT_PATH.'/header.php';
    $xoopsTpl->assign('mod_pref', $mod_pref);   // Préférences du module
    $xoopsTpl->assign('product', $product->toArray());

    $xoopsTpl->assign('global_advert', \Xoopsmodules\oledrion\Utility::getModuleOption('advertisement'));
    $breadcrumb = array( $product->getLink() => $product->getVar('product_title'),
                OLEDRION_URL.basename(__FILE__) => _OLEDRION_RATETHISPRODUCT);
    $xoopsTpl->assign('breadcrumb', \Xoopsmodules\oledrion\Utility::breadcrumb($breadcrumb));

    $title = _OLEDRION_RATETHISPRODUCT.' : '.strip_tags($product->getVar('product_title')).' - '.\Xoopsmodules\oledrion\Utility::getModuleName();
    \Xoopsmodules\oledrion\Utility::setMetas($title, $title);
    \Xoopsmodules\oledrion\Utility::setCSS();
\Xoopsmodules\oledrion\Utility::setLocalCSS($xoopsConfig['language']);
}
*/

require_once XOOPS_ROOT_PATH . '/footer.php';
