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
 * Affichage d'un produit
 */

use XoopsModules\Oledrion;

require_once __DIR__ . '/header.php';
require_once XOOPS_ROOT_PATH . '/class/tree.php';

$product_id = 0;
// Les tests **************************************************************************************
// Recherche du n° de produit
if (\Xmf\Request::hasVar('product_id', 'GET')) {
    $product_id = \Xmf\Request::getInt('product_id', 0, 'GET');
} else {
    Oledrion\Utility::redirect(_OLEDRION_ERROR1, 'index.php', 5);
}
// Le produit existe ?
$product = null;
$product = $productsHandler->get($product_id);
if (!is_object($product)) {
    Oledrion\Utility::redirect(_OLEDRION_ERROR1, 'index.php', 5);
}

// Le produit est en ligne ?
if (0 == $product->getVar('product_online')) {
    Oledrion\Utility::redirect(_OLEDRION_ERROR2, 'index.php', 5);
}

// Le produit est publié ?
if (0 == Oledrion\Utility::getModuleOption('show_unpublished') && $product->getVar('product_submitted') > time()) {
    Oledrion\Utility::redirect(_OLEDRION_ERROR3, 'index.php', 5);
}

// Faut il afficher les produit même lorsqu'ils ne sont plus en stock ?
if (0 == Oledrion\Utility::getModuleOption('nostock_display') && 0 == $product->getVar('product_stock')) {
    if ('' !== xoops_trim(Oledrion\Utility::getModuleOption('nostock_display'))) {
        Oledrion\Utility::redirect(Oledrion\Utility::getModuleOption('nostock_display'), 'main.php', 5);
    }
}

// Fin des tests, si on est encore là c'est que tout est bon **************************************
//$title = strip_tags($product->getVar('product_title')) . ' - ' . Oledrion\Utility::getModuleName();
$title = strip_tags($product->getVar('product_title'));
//$handlers = HandlerManager::getInstance();
$db           = \XoopsDatabaseFactory::getDatabaseConnection();
$caddyHandler = new Oledrion\CaddyHandler($db);
$op           = isset($_GET['op']) ? $_GET['op'] : 'default';
switch ($op) {
    // product Print
    case 'print':

        require_once XOOPS_ROOT_PATH . '/header.php';

        $GLOBALS['current_category'] = 0;
        $xoopsConfig['sitename']     = $title;
        // product to array
        $product = $product->toArray();
        // Set local style
        if (file_exists(XOOPS_ROOT_PATH . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/style.css')) {
            $xoopsTpl->assign('localstyle', XOOPS_URL . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/style.css');
        } else {
            $xoopsTpl->assign('localstyle', XOOPS_URL . '/language/english/style.css');
        }
        // Index Variable
        $xoopsTpl->assign('xoops_sitename', $xoopsConfig['sitename']);
        $xoopsTpl->assign('xoops_pagetitle', $title);
        $xoopsTpl->assign('product', $product);
        // Display print page
        echo $xoopsTpl->fetch(OLEDRION_PATH . '/templates/oledrion_product_print.tpl');

        break;
    // product view
    case 'default':

    default:
        // Lecture des TVA ********************************************************************************

        $vatArray = [];
        $vatArray = $vatHandler->getAllVats(new Oledrion\Parameters());

        $GLOBALS['xoopsOption']['template_main'] = 'oledrion_product.tpl';
        $GLOBALS['current_category']             = $product->getVar('product_cid');
        require_once XOOPS_ROOT_PATH . '/header.php';

        if (!OLEDRION_MY_THEME_USES_JQUERY) {
            $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
        }
        //Oledrion\Utility::callJavascriptFile('noconflict.js');
        // Add lightbox
        //$xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.lightbox.js');
        //$xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/lightbox.css');

        if (\Xmf\Request::hasVar('stock', 'GET') && 'add' === $_GET['stock'] && Oledrion\Utility::isMemberOfGroup(Oledrion\Utility::getModuleOption('grp_qty'))) {
            $productsHandler->increaseStock($product);
        }

        if (\Xmf\Request::hasVar('stock', 'GET') && 'substract' === $_GET['stock'] && Oledrion\Utility::isMemberOfGroup(Oledrion\Utility::getModuleOption('grp_qty'))) {
            $productsHandler->decreaseStock($product);
            $productsHandler->verifyLowStock($product);
        }

        $currentUser = Oledrion\Utility::getCurrentUserID();
        $xoopsTpl->assign('currentUserId', $currentUser);

        $baseurl = OLEDRION_URL . basename(__FILE__) . '?product_id=' . $product->getVar('product_id');

        if (Oledrion\Utility::getModuleOption('use_tags')) {
            require_once XOOPS_ROOT_PATH . '/modules/tag/include/tagbar.php';
            $xoopsTpl->assign('tagbar', tagBar($product_id, 0));
        }

        // Quelques options pour le template
        $xoopsTpl->assign('baseurl', $baseurl);
        $xoopsTpl->assign('nostock_msg', Oledrion\Utility::getModuleOption('nostock_msg'));
        $xoopsTpl->assign('mod_pref', $mod_pref);
        // Préférences du module
        $xoopsTpl->assign('columnsCount', Oledrion\Utility::getModuleOption('category_colums'));
        $xoopsTpl->assign('icons', $icons);
        $xoopsTpl->assign('canRateProducts', Oledrion\Utility::getModuleOption('rateproducts'));
        // Préférences du module
        $xoopsTpl->assign('mail_link', 'mailto:?subject=' . sprintf(_OLEDRION_INTARTICLE, $xoopsConfig['sitename']) . '&amp;body=' . sprintf(_OLEDRION_INTARTFOUND, $xoopsConfig['sitename']) . ':  ' . XOOPS_URL . '/modules/oledrion/product.php?product_id=' . $product_id);
        $xoopsTpl->assign('canChangeQuantity', Oledrion\Utility::isMemberOfGroup(Oledrion\Utility::getModuleOption('grp_qty')));
        // Groupe autorisé à modifier les quantités depuis la page
        $xoopsTpl->assign('ProductStockQuantity', sprintf(_OLEDRION_QUANTITY_STOCK, $product->getVar('product_stock')));

        // Recherche de la catégorie du produit
        $tbl_tmp          = $tbl_categories = $tbl_ancestors = [];
        $tbl_categories   = $categoryHandler->getAllCategories(new Oledrion\Parameters());
        $product_category = null;
        $product_category = isset($tbl_categories[$product->getVar('product_cid')]) ? $tbl_categories[$product->getVar('product_cid')] : null;
        if (!is_object($product_category)) {
            Oledrion\Utility::redirect(_OLEDRION_ERROR4, 'index.php', 5);
        }

        // Recherche de sa langue
        $product_vendor = null;
        $product_vendor = $vendorsHandler->get($product->getVar('product_vendor_id'));
        if (!is_object($product_vendor)) {
            Oledrion\Utility::redirect(_OLEDRION_ERROR5, 'index.php', 5);
        }

        // Chargement de toutes les TVA
        $tblVat = [];
        $tblVat = $vatHandler->getAllVats(new Oledrion\Parameters());

        // Recherche de sa TVA
        $product_vat = null;
        if (isset($tblVat[$product->getVar('product_vat_id')])) {
            $product_vat = $tblVat[$product->getVar('product_vat_id')];
        }
        if (!is_object($product_vat) && Oledrion\Utility::getModuleOption('use_price')) {
            Oledrion\Utility::redirect(_OLEDRION_ERROR6, 'index.php', 5);
        }

        // Recherche de l'utilisateur qui a soumit ce produit
        $product_user = null;
        $userHandler  = $memberHandler = xoops_getHandler('user');
        $product_user = $userHandler->get($product->getVar('product_submitter'), true);
        $xoopsTpl->assign('product_submitter', $product_user);

        // Image du bouton "Ajouter au panier"
        if (file_exists(OLEDRION_PATH . 'language/' . $xoopsConfig['language'] . '/image/addtocart.png')) {
            $addToCart     = OLEDRION_URL . 'language/' . $xoopsConfig['language'] . '/image/addtocart.png';
            $addToWishList = OLEDRION_URL . 'language/' . $xoopsConfig['language'] . '/image/addtowishlist.png';
        } else {
            // Fallback
            $addToCart     = OLEDRION_URL . 'language/english/image/addtocart.png';
            $addToWishList = OLEDRION_URL . 'language/english/image/addtowishlist.png';
        }
        $xoopsTpl->assign('addToCartImage', $addToCart);
        $xoopsTpl->assign('addToWishList', $addToWishList);

        // Recherche des fabricants du produit **********************************************
        $tbl_auteurs = $tbl_translators = $tbl_tmp = $tbl_tmp2 = $tbl_join1 = $tbl_join2 = [];
        $criteria    = new \Criteria('pm_product_id', $product->getVar('product_id'), '=');
        $tbl_tmp     = $productsmanuHandler->getObjects($criteria, true);
        foreach ($tbl_tmp as $id => $item) {
            $tbl_tmp2[] = $item->getVar('pm_manu_id');
        }
        if (count($tbl_tmp2) > 0) {
            $tbl_product_manufacturers = [];
            $tbl_auteurs               = $manufacturerHandler->getObjects(new \Criteria('manu_id', '(' . implode(',', $tbl_tmp2) . ')', 'IN'), true);
            foreach ($tbl_auteurs as $item) {
                $xoopsTpl->append('product_manufacturers', $item->toArray());
                $tbl_join1[] = "<a href='" . $item->getLink() . "' title='" . Oledrion\Utility::makeHrefTitle($item->getVar('manu_commercialname') . ' ' . $item->getVar('manu_name')) . "'>" . $item->getVar('manu_commercialname') . ' ' . $item->getVar('manu_name') . '</a>';
            }
        }
        if (count($tbl_join1) > 0) {
            $xoopsTpl->assign('product_joined_manufacturers', implode(', ', $tbl_join1));
        }
        if (count($tbl_join2) > 0) {
            $xoopsTpl->assign('product_joined_vendors', implode(', ', $tbl_join2));
        }

        // Recherche des produits relatifs ******************************************************************
        $revertRelated = false;
        $tbl_tmp       = $tbl_tmp2 = [];
        $criteria      = new \Criteria('related_product_id', $product->getVar('product_id'), '=');
        $tbl_tmp       = $relatedHandler->getObjects($criteria);

        // S'il n'y a pas de produits relatifs et que la bonne option est activée, on recherche les produits relatfis "dans l'autre sens" (les cas où le produit courant est marqué comme produit relatif)
        if (OLEDRION_RELATED_BOTH && 0 === count($tbl_tmp)) {
            unset($criteria);
            $tbl_tmp       = [];
            $criteria      = new \Criteria('related_product_related', $product->getVar('product_id'), '=');
            $tbl_tmp       = $relatedHandler->getObjects($criteria);
            $revertRelated = true;
        }

        if (count($tbl_tmp) > 0) {
            foreach ($tbl_tmp as $item) {
                if (!$revertRelated) {
                    $tbl_tmp2[] = $item->getVar('related_product_related');
                } else {
                    $tbl_tmp2[] = $item->getVar('related_product_id');
                }
            }
            $criteria = new \Criteria('product_id', '(' . implode(',', $tbl_tmp2) . ')', 'IN');
            $criteria->setLimit(Oledrion\Utility::getModuleOption('related_limit'));
            $criteria->setOrder('DESC');
            $criteria->setSort('product_id');
            $tbl_related_products = [];
            $tbl_related_products = $productsHandler->getObjects($criteria, true);
            if (count($tbl_related_products) > 0) {
                $cpt = 1;
                foreach ($tbl_related_products as $item) {
                    $tbl_tmp                     = $item->toArray();
                    $tbl_tmp['count']            = $cpt;
                    $tbl_tmp['product_category'] = isset($tbl_categories[$item->getVar('product_cid')]) ? $tbl_categories[$item->getVar('product_cid')]->toArray() : null;
                    $xoopsTpl->append('product_related_products', $tbl_tmp);
                    ++$cpt;
                }
            }
        }

        // Recherche des fichiers attachés au produit *******************************************************
        $attachedFiles      = $mp3AttachedFilesList = $attachedFilesForTemplate = [];
        $attachedFilesCount = $filesHandler->getProductFilesCount($product->getVar('product_id'));
        if ($attachedFilesCount > 0) {
            $attachedFiles = $filesHandler->getProductFiles($product->getVar('product_id'));
            foreach ($attachedFiles as $attachedFile) {
                // Recherche de fichiers MP3
                if ($attachedFile->isMP3()) {
                    $mp3AttachedFilesList[] = $attachedFile->getURL();
                }
                $attachedFilesForTemplate[] = $attachedFile->toArray();
            }
            if (count($mp3AttachedFilesList) > 0) {
                Oledrion\Utility::callJavascriptFile('jquery.swfobject/jquery.swfobject.min.js');
                $xoopsTpl->assign('mp3FilesList', implode('|', $mp3AttachedFilesList));
            }
        }

        // Informations du produit **************************************************************************
        $tbl_tmp = [];
        $tbl_tmp = $product->toArray();
        // Fichiers attachés
        $tbl_tmp['attached_mp3_count']     = count($mp3AttachedFilesList);
        $tbl_tmp['attached_non_mp3_count'] = count($attachedFilesForTemplate) - count($mp3AttachedFilesList);
        $tbl_tmp['attached_files']         = $attachedFilesForTemplate;
        // La liste complète de tous les fichiers attachés

        $tbl_tmp['product_category'] = $product_category->toArray();
        $tbl_tmp['product_vendor']   = $product_vendor->toArray();
        if ('' !== xoops_trim($product_user->getVar('name'))) {
            $name = $product_user->getVar('name');
        } else {
            $name = $product_user->getVar('uname');
        }
        $tbl_tmp['product_submiter_name'] = $name;
        $linkeduser                       = '<a href="' . XOOPS_URL . '/userinfo.php?uid=' . $product_user->getVar('uid') . '">' . $name . '</a>';
        $tbl_tmp['product_submiter_link'] = $name;
        if (is_object($product_vat)) {
            $tbl_tmp['product_vat_rate'] = $product_vat->toArray();
        }

        $tbl_tmp['product_rating_formated'] = number_format($product->getVar('product_rating'), 2);
        if (1 == $product->getVar('product_votes')) {
            $tbl_tmp['product_votes_count'] = _OLEDRION_ONEVOTE;
        } else {
            $tbl_tmp['product_votes_count'] = sprintf(_OLEDRION_NUMVOTES, $product->getVar('product_votes'));
        }
        // Attributs
        if ($attributesHandler->getProductAttributesCount($product_id) > 0) {
            $attributes           = [];
            $mandatoryFieldsCount = 0;
            if ($caddyHandler->isInCart($product_id)) {
            }
            $attributes = $attributesHandler->constructHtmlProductAttributes($product, $mandatoryFieldsCount);
            if (count($attributes) > 0) {
                Oledrion\Utility::callJavascriptFile('validate/jquery.validate.min.js');
                Oledrion\Utility::setCSS(OLEDRION_URL . 'assets/css/validate.css');
                $tbl_tmp['product_attributes'] = $attributes;
                $xoopsTpl->assign('mandatoryFieldsCount', $mandatoryFieldsCount);
            }
        }
        // Product
        $tbl_tmp['product_property1_title']  = Oledrion\Utility::getModuleOption('product_property1_title');
        $tbl_tmp['product_property2_title']  = Oledrion\Utility::getModuleOption('product_property2_title');
        $tbl_tmp['product_property3_title']  = Oledrion\Utility::getModuleOption('product_property3_title');
        $tbl_tmp['product_property4_title']  = Oledrion\Utility::getModuleOption('product_property4_title');
        $tbl_tmp['product_property5_title']  = Oledrion\Utility::getModuleOption('product_property5_title');
        $tbl_tmp['product_property6_title']  = Oledrion\Utility::getModuleOption('product_property6_title');
        $tbl_tmp['product_property7_title']  = Oledrion\Utility::getModuleOption('product_property7_title');
        $tbl_tmp['product_property8_title']  = Oledrion\Utility::getModuleOption('product_property8_title');
        $tbl_tmp['product_property9_title']  = Oledrion\Utility::getModuleOption('product_property9_title');
        $tbl_tmp['product_property10_title'] = Oledrion\Utility::getModuleOption('product_property10_title');

        $xoopsTpl->assign('product', $tbl_tmp);

        // Breadcrumb *************************************************************************************
        $tbl_tmp       = [];
        $mytree        = new Oledrion\XoopsObjectTree($tbl_categories, 'cat_cid', 'cat_pid');
        $tbl_ancestors = array_reverse($mytree->getAllParent($product->getVar('product_cid')));
        $tbl_tmp[]     = "<a href='" . OLEDRION_URL . "index.php' title='" . Oledrion\Utility::makeHrefTitle(Oledrion\Utility::getModuleName()) . "'>" . Oledrion\Utility::getModuleName() . '</a>';
        foreach ($tbl_ancestors as $item) {
            $tbl_tmp[] = "<a href='" . $item->getLink() . "' title='" . Oledrion\Utility::makeHrefTitle($item->getVar('cat_title')) . "'>" . $item->getVar('cat_title') . '</a>';
        }
        // Ajout de la catégorie courante
        $tbl_tmp[]  = "<a href='" . $product_category->getLink() . "' title='" . Oledrion\Utility::makeHrefTitle($product_category->getVar('cat_title')) . "'>" . $product_category->getVar('cat_title') . '</a>';
        $tbl_tmp[]  = $product->getVar('product_title');
        $breadcrumb = implode(' &raquo; ', $tbl_tmp);
        $xoopsTpl->assign('breadcrumb', $breadcrumb);

        // Maj compteur de lectures ***********************************************************************
        if ($product->getVar('product_submitter') != $currentUser) {
            $productsHandler->addCounter($product_id);
        }

        // produits précédents et suivants ******************************************************************
        if (1 == Oledrion\Utility::getModuleOption('showprevnextlink')) {
            $xoopsTpl->assign('showprevnextlink', true);
            // Recherche du produit suivant le produit en cours.
            $criteria = new \CriteriaCompo();
            $criteria->add(new \Criteria('product_online', 1, '='));
            if (0 == Oledrion\Utility::getModuleOption('show_unpublished')) {
                // Ne pas afficher les produits qui ne sont pas publiés
                $criteria->add(new \Criteria('product_submitted', time(), '<='));
            }
            if (0 == Oledrion\Utility::getModuleOption('nostock_display')) {
                // Se limiter aux seuls produits encore en stock
                $criteria->add(new \Criteria('product_stock', 0, '>'));
            }
            $criteria->add(new \Criteria('product_id', $product->getVar('product_id'), '>'));
            $criteria->setOrder('DESC');
            $criteria->setSort('product_submitted');
            $criteria->setLimit(1);
            $tbl = [];
            $tbl = $productsHandler->getObjects($criteria);
            if (1 == count($tbl)) {
                // Trouvé
                $tmpProduct = null;
                $tmpProduct = $tbl[0];
                $xoopsTpl->assign('next_product_id', $tmpProduct->getVar('product_id'));
                $xoopsTpl->assign('next_product_title', $tmpProduct->getVar('product_title'));
                $xoopsTpl->assign('next_product_url_rewrited', $tmpProduct->getLink());
                $xoopsTpl->assign('next_product_href_title', Oledrion\Utility::makeHrefTitle($tmpProduct->getVar('product_title')));
            } else {
                $xoopsTpl->assign('next_product_id', 0);
            }

            // Recherche du produit précédant le produit en cours.
            $criteria = new \CriteriaCompo();
            $criteria->add(new \Criteria('product_online', 1, '='));
            if (0 == Oledrion\Utility::getModuleOption('show_unpublished')) {
                // Ne pas afficher les produits qui ne sont pas publiés
                $criteria->add(new \Criteria('product_submitted', time(), '<='));
            }
            if (0 == Oledrion\Utility::getModuleOption('nostock_display')) {
                // Se limiter aux seuls produits encore en stock
                $criteria->add(new \Criteria('product_stock', 0, '>'));
            }
            $criteria->add(new \Criteria('product_id', $product->getVar('product_id'), '<'));
            $criteria->setOrder('DESC');
            $criteria->setSort('product_submitted');
            $criteria->setLimit(1);
            $tbl = [];
            $tbl = $productsHandler->getObjects($criteria);
            if (1 == count($tbl)) {
                // Trouvé
                $tmpProduct = null;
                $tmpProduct = $tbl[0];
                $xoopsTpl->assign('previous_product_id', $tmpProduct->getVar('product_id'));
                $xoopsTpl->assign('previous_product_title', $tmpProduct->getVar('product_title'));
                $xoopsTpl->assign('previous_product_url_rewrited', $tmpProduct->getLink());
                $xoopsTpl->assign('previous_product_href_title', Oledrion\Utility::makeHrefTitle($tmpProduct->getVar('product_title')));
            } else {
                $xoopsTpl->assign('previous_product_id', 0);
            }
        } else {
            $xoopsTpl->assign('showprevnextlink', false);
        }
        // x derniers produits toutes catégories confondues *************************************************
        $count = Oledrion\Utility::getModuleOption('summarylast');
        $xoopsTpl->assign('summarylast', $count);
        if ($count > 0) {
            $tblTmp = [];
            $tblTmp = $productsHandler->getRecentProducts(new Oledrion\Parameters([
                                                                                      'start'    => 0,
                                                                                      'limit'    => $count,
                                                                                      'category' => 0,
                                                                                      'sort'     => 'product_submitted DESC, product_title',
                                                                                      'order'    => '',
                                                                                      'excluded' => $product_id,
                                                                                  ]));
            foreach ($tblTmp as $item) {
                $product_price     = $item->getVar('product_price');
                $product_price_ttc = Oledrion\Utility::getTTC($item->getVar('product_price'), 0);
                if ($attributesHandler->getProductAttributesCount($item->getVar('product_id')) > 0) {
                    $criteria = new \CriteriaCompo();
                    $criteria->add(new \Criteria('attribute_product_id', $item->getVar('product_id')));
                    $attribute = $attributesHandler->getObjects($criteria, false);
                    foreach ($attribute as $root) {
                        $product_price     = $root->getVar('attribute_default_value');
                        $product_price_ttc = Oledrion\Utility::getTTC($root->getVar('attribute_default_value'), 0);
                    }
                }
                $datas = [
                    'last_categ_product_title'        => $item->getVar('product_title'),
                    'last_categ_product_url_rewrited' => $item->getLink(),
                    'last_categ_product_href_title'   => Oledrion\Utility::makeHrefTitle($item->getVar('product_title')),
                    'product_thumb_url'               => $item->getVar('product_thumb_url'),
                    'product_thumb_full_url'          => $item->getThumbUrl(),
                    'product_url_rewrited'            => $item->getLink(),
                    'product_href_title'              => Oledrion\Utility::makeHrefTitle($item->getVar('product_title')),
                    'product_title'                   => $item->getVar('product_title'),
                    'product_property1'               => $item->getVar('product_property1'),
                    'product_property2'               => $item->getVar('product_property2'),
                    'product_property3'               => $item->getVar('product_property3'),
                    'product_property4'               => $item->getVar('product_property4'),
                    'product_property5'               => $item->getVar('product_property5'),
                    'product_property6'               => $item->getVar('product_property6'),
                    'product_property7'               => $item->getVar('product_property7'),
                    'product_property8'               => $item->getVar('product_property8'),
                    'product_property9'               => $item->getVar('product_property9'),
                    'product_property10'              => $item->getVar('product_property10'),
                    'product_id'                      => $item->getVar('product_id'),
                    'product_new'                     => $item->isNewProduct(),
                    'product_stock'                   => $item->getVar('product_stock'),
                    'product_price'                   => $product_price,
                    'product_price_ttc'               => $product_price_ttc,
                ];
                $xoopsTpl->append('product_all_categs', $datas);
            }
            unset($tblTmp);
        }

        // x derniers produits dans cette catégorie *********************************************************
        $count = Oledrion\Utility::getModuleOption('summarycategory');
        $xoopsTpl->assign('summarycategory', $count);
        if ($count > 0) {
            $tblTmp = [];
            $tblTmp = $productsHandler->getRecentProducts(new Oledrion\Parameters([
                                                                                      'start'    => 0,
                                                                                      'limit'    => $count,
                                                                                      'category' => $product->getVar('product_cid'),
                                                                                      'sort'     => 'product_submitted DESC, product_title',
                                                                                      'order'    => '',
                                                                                      'excluded' => $product_id,
                                                                                  ]));
            foreach ($tblTmp as $item) {
                $product_price     = $item->getVar('product_price');
                $product_price_ttc = Oledrion\Utility::getTTC($item->getVar('product_price'), 0);
                if ($attributesHandler->getProductAttributesCount($item->getVar('product_id')) > 0) {
                    $criteria = new \CriteriaCompo();
                    $criteria->add(new \Criteria('attribute_product_id', $item->getVar('product_id')));
                    $attribute = $attributesHandler->getObjects($criteria, false);
                    foreach ($attribute as $root) {
                        $product_price     = $root->getVar('attribute_default_value');
                        $product_price_ttc = Oledrion\Utility::getTTC($root->getVar('attribute_default_value'), 0);
                    }
                }
                $datas = [
                    'last_categ_product_title'        => $item->getVar('product_title'),
                    'last_categ_product_url_rewrited' => $item->getLink(),
                    'last_categ_product_href_title'   => Oledrion\Utility::makeHrefTitle($item->getVar('product_title')),
                    'product_thumb_url'               => $item->getVar('product_thumb_url'),
                    'product_thumb_full_url'          => $item->getThumbUrl(),
                    'product_url_rewrited'            => $item->getLink(),
                    'product_href_title'              => Oledrion\Utility::makeHrefTitle($item->getVar('product_title')),
                    'product_title'                   => $item->getVar('product_title'),
                    'product_property1'               => $item->getVar('product_property1'),
                    'product_property2'               => $item->getVar('product_property2'),
                    'product_property3'               => $item->getVar('product_property3'),
                    'product_property4'               => $item->getVar('product_property4'),
                    'product_property5'               => $item->getVar('product_property5'),
                    'product_property6'               => $item->getVar('product_property6'),
                    'product_property7'               => $item->getVar('product_property7'),
                    'product_property8'               => $item->getVar('product_property8'),
                    'product_property9'               => $item->getVar('product_property9'),
                    'product_property10'              => $item->getVar('product_property10'),
                    'product_id'                      => $item->getVar('product_id'),
                    'product_new'                     => $item->isNewProduct(),
                    'product_stock'                   => $item->getVar('product_stock'),
                    'product_price'                   => $product_price,
                    'product_price_ttc'               => $product_price_ttc,
                ];
                $xoopsTpl->append('product_current_categ', $datas);
            }
            unset($tblTmp);
        }

        // Deux c'est mieux *******************************************************************************
        $count = Oledrion\Utility::getModuleOption('better_together');
        $xoopsTpl->assign('better_together', $count);
        if ($count > 0) {
            $productWith = 0;
            // On recherche le produit qui s'est le plus vendu avec ce produit
            $productWith = $caddyHandler->getBestWith($product->getVar('product_id'));
            if ($productWith > 0) {
                $tmpProduct = null;
                $tmpProduct = $productsHandler->get($productWith);
                if (is_object($tmpProduct)) {
                    $tmp                               = [];
                    $tmp                               = $tmpProduct->toArray();
                    $tmp['product_price_ttc']          = Oledrion\Utility::getTTC($tmpProduct->getVar('product_price'), $tblVat[$tmpProduct->getVar('product_vat_id')]->getVar('vat_rate'));
                    $tmp['product_discount_price_ttc'] = Oledrion\Utility::getTTC($tmpProduct->getVar('product_discount_price'), $tblVat[$tmpProduct->getVar('product_vat_id')]->getVar('vat_rate'));
                    $xoopsTpl->assign('bestwith', $tmp);
                }
            }
        }

        // Notation produit *********************************************************************************
        if (1 == Oledrion\Utility::getModuleOption('rateproducts')) {
            $canRate = true;
            if (0 != $currentUser) {
                $canRate = !$votedataHandler->hasUserAlreadyVoted($currentUser, $product->getVar('product_id'));
            } else {
                $canRate = !$votedataHandler->hasAnonymousAlreadyVoted('', $product->getVar('product_id'));
            }
            $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
            Oledrion\Utility::callJavascriptFile('rateit.js');
            Oledrion\Utility::setCSS(OLEDRION_URL . 'assets/css/rateit.css');

            $xoopsTpl->assign('userCanRate', $canRate);
        }

        // Meta et CSS ************************************************************************************
        Oledrion\Utility::setCSS();
        Oledrion\Utility::setLocalCSS($xoopsConfig['language']);
        if (Oledrion\Utility::getModuleOption('manual_meta')) {
            $pageTitle       = '' === xoops_trim($product->getVar('product_metatitle')) ? $title : $product->getVar('product_metatitle');
            $metaDescription = '' !== xoops_trim($product->getVar('product_metadescription')) ? $product->getVar('product_metadescription') : $title;
            $metaKeywords    = '' !== xoops_trim($product->getVar('product_metakeywords')) ? $product->getVar('product_metakeywords') : Oledrion\Utility::createMetaKeywords($product->getVar('product_title') . ' ' . $product->getVar('product_summary') . ' ' . $product->getVar('product_description'));
            Oledrion\Utility::setMetas($pageTitle, $metaDescription, $metaKeywords);
        } else {
            Oledrion\Utility::setMetas($title, $title, Oledrion\Utility::createMetaKeywords($product->getVar('product_title') . ' ' . $product->getVar('product_summary') . ' ' . $product->getVar('product_description')));
        }

        require_once XOOPS_ROOT_PATH . '/include/comment_view.php';
        require_once XOOPS_ROOT_PATH . '/footer.php';

        break;
}
