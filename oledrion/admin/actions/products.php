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
 * @version     $Id$
 */

/**
 * Gestion des produits (dans l'administration)
 */
if (!defined("OLEDRION_ADMIN")) exit();

switch ($action) {
    // ****************************************************************************************************************
    case 'default': // Gestion des produits
        // ****************************************************************************************************************
        xoops_cp_header();
        $products = $categories = array();

        // Récupération des données uniques
        $categories = $h_oledrion_cat->getAllCategories(new oledrion_parameters(array('start' => 0, 'limit' => 0, 'sort' => 'cat_title', 'order' => 'ASC', 'idaskey' => true)));

        $form = "<form method='post' action='$baseurl' name='frmadddproduct' id='frmadddproduct'><input type='hidden' name='op' id='op' value='products' /><input type='hidden' name='action' id='action' value='add' /><input type='submit' name='btngo' id='btngo' value='" . _AM_OLEDRION_ADD_ITEM . "' /></form>";
        echo $form;
        echo "<br /><form method='get' action='$baseurl' name='frmaddeditproduct' id='frmaddeditproduct'>" . _OLEDRION_PRODUCT_ID . " <input type='text' name='id' id='id' value='' size='4'/> <input type='hidden' name='op' id='op' value='products' /><input type='radio' name='action' id='action' value='edit' />" . _OLEDRION_EDIT . " <input type='radio' name='action' id='action' value='confdelete' />" . _OLEDRION_DELETE . " <input type='submit' name='btngo' id='btngo' value='" . _GO . "' /></form>";

        $vats = array();
        $vats = $h_oledrion_vat->getAllVats(new oledrion_parameters());

        $start = isset($_GET['start']) ? intval($_GET['start']) : 0;

        $filter_product_id = $filter_product_cid = $filter_product_recommended = $filter_product_price = $filter_product_online = 0;
        $filter_product_title = $filter_product_sku = '';

        $newFilter = false;
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('product_id', 0, '<>'));

        if (isset($_POST['filter_product_id'])) {
            if (intval($_POST['filter_product_id']) != 0) {
                $criteria->add(new Criteria('product_id', intval($_POST['filter_product_id']), '='));
            }
            $filter_product_id = intval($_POST['filter_product_id']);
            $newFilter = true;
        }
        if (isset($_POST['filter_product_cid']) && intval($_POST['filter_product_cid']) != 0) {
            $criteria->add(new Criteria('product_cid', intval($_POST['filter_product_cid']), '='));
            $filter_product_cid = intval($_POST['filter_product_cid']);
            $newFilter = true;
        }
        if (isset($_POST['filter_product_recommended']) && intval($_POST['filter_product_recommended']) != 0) {
            if (intval($_POST['filter_product_recommended']) == 1) {
                $criteria->add(new Criteria('product_recommended', '0000-00-00', '<>'));
            } else {
                $criteria->add(new Criteria('product_recommended', '0000-00-00', '='));
            }
            $filter_product_recommended = intval($_POST['filter_product_recommended']);
            $newFilter = true;
        }
        if (isset($_POST['filter_product_title']) && xoops_trim($_POST['filter_product_title']) != '') {
            $criteria->add(new Criteria('product_title', '%' . $_POST['filter_product_title'] . '%', 'LIKE'));
            $filter_product_title = $_POST['filter_product_title'];
            $newFilter = true;
        }
        if (isset($_POST['filter_product_sku']) && xoops_trim($_POST['filter_product_sku']) != '') {
            $criteria->add(new Criteria('product_sku', '%' . $_POST['filter_product_sku'] . '%', 'LIKE'));
            $filter_product_sku = $_POST['filter_product_sku'];
            $newFilter = true;
        }
        if (isset($_POST['filter_product_online']) && intval($_POST['filter_product_online']) != 0) {
            $criteria->add(new Criteria('product_online', intval($_POST['filter_product_online']) - 1, '='));
            $filter_product_online = intval($_POST['filter_product_online']);
            $newFilter = true;
        }
        if (isset($_POST['filter_product_price']) && intval($_POST['filter_product_price']) != 0) {
            $criteria->add(new Criteria('product_price', intval($_POST['filter_product_price']), '>='));
            $filter_product_price = intval($_POST['filter_product_price']);
            $newFilter = true;
        }
        if ($filter_product_id == 0 && $filter_product_cid == 0 && $filter_product_recommended == 0 && $filter_product_price == 0 && $filter_product_online == 0 && $filter_product_title == '' && $filter_product_sku == '') {
            $newFilter = true;
        }

        if (!$newFilter && isset($_SESSION['oledrion_filter'])) {
            $criteria = unserialize($_SESSION['oledrion_filter']);
            $filter_product_id = $_SESSION['filter_product_id'];
            $filter_product_cid = $_SESSION['filter_product_cid'];
            $filter_product_recommended = $_SESSION['filter_product_recommended'];
            $filter_product_title = $_SESSION['filter_product_title'];
            $filter_product_sku = $_SESSION['filter_product_sku'];
            $filter_product_online = $_SESSION['filter_product_online'];
            $filter_product_price = $_SESSION['filter_product_price'];
        }

        $_SESSION['oledrion_filter'] = serialize($criteria);
        $_SESSION['filter_product_id'] = $filter_product_id;
        $_SESSION['filter_product_cid'] = $filter_product_cid;
        $_SESSION['filter_product_recommended'] = $filter_product_recommended;
        $_SESSION['filter_product_title'] = $filter_product_title;
        $_SESSION['filter_product_sku'] = $filter_product_sku;
        $_SESSION['filter_product_online'] = $filter_product_online;
        $_SESSION['filter_product_price'] = $filter_product_price;

        $itemsCount = $h_oledrion_products->getCount($criteria); // Recherche du nombre total de produits répondants aux critères
        oledrion_utils::htitle(_MI_OLEDRION_ADMENU4 . ' (' . $itemsCount . ')', 4);
        if ($itemsCount > $limit) {
            require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
            $pagenav = new XoopsPageNav($itemsCount, $limit, $start, 'start', 'op=products');
        }
        $mytree = new XoopsObjectTree($categories, 'cat_cid', 'cat_pid');
        $selectCateg = $mytree->makeSelBox('filter_product_cid', 'cat_title', '-', $filter_product_cid, '---', 0, "style='width: 170px; max-width: 170px;'");
        $onlineSelect = oledrion_utils::htmlSelect('filter_product_online', array(2 => _YES, 1 => _NO), $filter_product_online);
        $recommendedSelect = oledrion_utils::htmlSelect('filter_product_recommended', array(1 => _YES, 2 => _NO), $filter_product_recommended);

        $criteria->setLimit($limit);
        $criteria->setStart($start);
        $criteria->setSort('product_id');

        $products = $h_oledrion_products->getObjects($criteria);
        $class = '';
        $span = 8;
        if (isset($pagenav) && is_object($pagenav)) {
            echo "<div align='left'>" . $pagenav->renderNav() . '</div>';
        }
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>" . _AM_OLEDRION_ID . "</th><th align='center'>" . _OLEDRION_TITLE . "</th><th align='center'>" . _OLEDRION_CATEGORY . "</th><th align='center'>" . _OLEDRION_NUMBER . '<br />' . _OLEDRION_EXTRA_ID . "</th><th align='center'>" . _OLEDRION_ONLINE . "</th><th align='center'>" . _AM_OLEDRION_RECOMMENDED . "</th><th align='center'>" . _OLEDRION_DATE . "</th>";
        if (oledrion_utils::getModuleOption('use_price')) {
            echo "<th align='center'>" . _OLEDRION_PRICE . "</th>";
            $span = 9;
        }
        echo "<th align='center'>" . _AM_OLEDRION_ACTION . "</th></tr>";

        echo "<tr><form method='post' action='$baseurl'><th align='center'><input type='text' size='3' name='filter_product_id' id='filter_product_id' value='$filter_product_id' /></th>\n";
        echo "<th align='center'><input type='text' size='25' name='filter_product_title' id='filter_product_title' value='$filter_product_title' /></th>\n";
        echo "<th align='center'>" . $selectCateg . "</th>\n";
        echo "<th align='center'><input type='text' size='25' name='filter_product_sku' id='filter_product_sku' value='$filter_product_sku' /></th>\n";
        echo "<th align='center'>" . $onlineSelect . "</th>\n";
        echo "<th align='center'>" . $recommendedSelect . "</th>\n";
        echo "<th align='center'>&nbsp;</th>\n";
        echo "<th align='center'><input type='text' size='5' name='filter_product_price' id='filter_product_price' value='$filter_product_price' /></th>\n";
        echo "<th align='center'><input type='hidden' name='op' id='op' value='products' /><input type='submit' name='btngo' id='btngo' value='" . _GO . "' /></th></form></tr>\n";
        foreach ($products as $item) {
            $class = ($class == 'even') ? 'odd' : 'even';
            $id = $item->getVar('product_id');
            $recommended = '';
            if ($item->isRecommended()) { // Si le produit est recommandé, on affiche le lien qui permet d'arrêter de le recommander
                $recommended = "<a href='" . $baseurl . "?op=products&action=unrecommend&product_id=" . $id . "' title='" . _AM_OLEDRION_DONOTRECOMMEND_IT . "'><img alt='" . _AM_OLEDRION_DONOTRECOMMEND_IT . "' src='" . OLEDRION_IMAGES_URL . "heart_delete.png' alt='' /></a>";
            } else { // Sinon on affiche le lien qui permet de le recommander
                $recommended = "<a href='" . $baseurl . "?op=products&action=recommend&product_id=" . $id . "' title='" . _AM_OLEDRION_RECOMMEND_IT . "'><img alt='" . _AM_OLEDRION_RECOMMEND_IT . "' src='" . OLEDRION_IMAGES_URL . "heart_add.png' alt='' /></a>";
            }

            $actions = array();
            $actions[] = "<a href='$baseurl?op=products&action=edit&id=" . $id . "' title='" . _OLEDRION_EDIT . "'>" . $icones['edit'] . '</a>';
            $actions[] = "<a href='$baseurl?op=products&action=copy&id=" . $id . "' title='" . _OLEDRION_DUPLICATE_PRODUCT . "'>" . $icones['copy'] . '</a>';
            $actions[] = "<a href='$baseurl?op=products&action=confdelete&id=" . $id . "' title='" . _OLEDRION_DELETE . "'>" . $icones['delete'] . '</a>';
            $online = $item->getVar('product_online') == 1 ? _YES : _NO;
            echo "<tr class='" . $class . "'>\n";
            if (isset($categories[$item->getVar('product_cid')])) {
                $productCategory = $categories[$item->getVar('product_cid')]->getVar('cat_title');
                $categoryUrl = $categories[$item->getVar('product_cid')]->getLink();
            } else {
                $productCategory = '';
                $categoryUrl = '#';
            }
            $productLink = "<a href='" . $item->getLink() . "' target='blank'>" . $item->getVar('product_title') . '</a>';
            if (floatval($item->getVar('product_discount_price')) > 0) {
                $priceLine = '<s>' . $oledrion_Currency->amountForDisplay($item->getVar('product_price')) . '</s>  ' . $oledrion_Currency->amountForDisplay($item->getVar('product_discount_price'));
            } else {
                $priceLine = $oledrion_Currency->amountForDisplay($item->getVar('product_price'));
            }

            echo "<td align='center'>" . $id . "</td><td align ='left'>" . $productLink . "</td><td align='left'><a href='" . $categoryUrl . "' target='blank'>" . $productCategory . "</a></td><td align='center'>" . $item->getVar('product_sku') . ' / ' . $item->getVar('product_extraid') . "</td><td align='center'>" . $online . "</td><td align='center'>" . $recommended . "</td><td align='center'>" . $item->getVar('product_date') . "</td>";
            if (oledrion_utils::getModuleOption('use_price')) {
                echo "<td align='right'>" . $priceLine . "</td>";
            }
            echo "<td align='center'>" . implode(' ', $actions) . "</td>\n";
            echo "<tr>\n";
        }
        $class = ($class == 'even') ? 'odd' : 'even';
        echo "<tr class='" . $class . "'>\n";
        echo "<td colspan='$span' align='center'>" . $form . "</td>\n";
        echo "</tr>\n";
        echo '</table>';
        if (isset($pagenav) && is_object($pagenav)) {
            echo "<div align='right'>" . $pagenav->renderNav() . "</div>";
        }
        //include_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;


    // ****************************************************************************************************************
    case 'unrecommend': // Arrêter de recommander un produit
        // ****************************************************************************************************************
        $opRedirect = '?op=products';
        if (isset($_GET['product_id'])) {
            $product_id = intval($_GET['product_id']);
            $product = null;
            $product = $h_oledrion_products->get($product_id);
            if (is_object($product)) {
                $product->unsetRecommended();
                if ($h_oledrion_products->insert($product, true)) {
                    oledrion_utils::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . $opRedirect, 1);
                } else {
                    oledrion_utils::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . $opRedirect, 4);
                }
            } else {
                oledrion_utils::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl . $opRedirect, 4);
            }
        } else {
            oledrion_utils::redirect(_AM_OLEDRION_ERROR_1, $baseurl . $opRedirect, 4);
        }
        break;


    // ****************************************************************************************************************
    case 'recommend': // Recommander un produit
        // ****************************************************************************************************************
        $opRedirect = '?op=products';
        if (isset($_GET['product_id'])) {
            $product_id = intval($_GET['product_id']);
            $product = null;
            $product = $h_oledrion_products->get($product_id);
            if (is_object($product)) {
                $product->setRecommended();
                if ($h_oledrion_products->insert($product, true)) {
                    oledrion_utils::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . $opRedirect, 1);
                } else {
                    oledrion_utils::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . $opRedirect, 4);
                }
            } else {
                oledrion_utils::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl . $opRedirect, 4);
            }
        } else {
            oledrion_utils::redirect(_AM_OLEDRION_ERROR_1, $baseurl . $opRedirect, 4);
        }
        break;


    // ****************************************************************************************************************
    case 'add': // Ajout d'un produit
    case 'edit': // Edition d'un produit
        // ****************************************************************************************************************
        xoops_cp_header();
        global $xoopsUser;

        if ($action == 'edit') {
            $title = _AM_OLEDRION_EDIT_PRODUCT;
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            if (empty($id)) {
                oledrion_utils::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
            }
            // Item exits ?
            $item = null;
            $item = $h_oledrion_products->get($id);
            if (!is_object($item)) {
                oledrion_utils::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $edit = true;
            $label_submit = _AM_OLEDRION_MODIFY;
        } else {
            $title = _AM_OLEDRION_ADD_PRODUCT;
            $item = $h_oledrion_products->create(true);
            $item->setVar('product_online', 1);
            if (OLEDRION_AUTO_FILL_MANUAL_DATE) {
                $item->setVar('product_date', formatTimestamp(time(), 's'));
            }
            $label_submit = _AM_OLEDRION_ADD;
            $edit = false;
        }

        $categories = $h_oledrion_cat->getAllCategories(new oledrion_parameters());
        if (count($categories) == 0) {
            oledrion_utils::redirect(_AM_OLEDRION_ERROR_8, $baseurl, 5);
        }
        // TVA
        $vats = $vatsForDisplay = array();
        $vats = $h_oledrion_vat->getAllVats(new oledrion_parameters());
        if (count($vats) == 0 && oledrion_utils::getModuleOption('use_price')) {
            oledrion_utils::redirect(_AM_OLEDRION_ERROR_9, $baseurl, 5);
        }
        foreach ($vats as $onevat) {
            $vatsForDisplay[$onevat->getVar('vat_id')] = $onevat->getVar('vat_rate');
        }

        $mytree = new XoopsObjectTree($categories, 'cat_cid', 'cat_pid');
        $select_categ = $mytree->makeSelBox('product_cid', 'cat_title', '-', $item->getVar('product_cid'));

        $sform = new XoopsThemeForm($title, 'frmproduct', $baseurl);
        $sform->setExtra('enctype="multipart/form-data"');
        $sform->addElement(new XoopsFormHidden('op', 'products'));
        $sform->addElement(new XoopsFormHidden('action', 'saveedit'));
        $sform->addElement(new XoopsFormHidden('product_id', $item->getVar('product_id')));
        $sform->addElement(new XoopsFormHidden('product_submitter', $xoopsUser->getVar('uid')));

        $sform->addElement(new XoopsFormText(_OLEDRION_TITLE, 'product_title', 50, 255, $item->getVar('product_title', 'e')), true);

        // Vendeurs *************************************************************
        $vendors = $vendorsForDisplay = array();
        $vendors = $h_oledrion_vendors->getAllVendors(new oledrion_parameters());
        foreach ($vendors as $oneVendor) {
            $vendorsForDisplay[$oneVendor->getVar('vendor_id')] = $oneVendor->getVar('vendor_name');
        }
        $vendorsSelect = new XoopsFormSelect(_OLEDRION_VENDOR, 'product_vendor_id', $item->getVar('product_vendor_id'));
        $vendorsSelect->addOptionArray($vendorsForDisplay);
        $sform->addElement($vendorsSelect, true);

        $sform->addElement(new XoopsFormLabel(_AM_OLEDRION_CATEG_HLP, $select_categ), true);

        $deliveryTime = new XoopsFormText(_OLEDRION_DELIVERY_TIME, 'product_delivery_time', 5, 5, $item->getVar('product_delivery_time', 'e'));
        $deliveryTime->setDescription(_OLEDRION_IN_DAYS);
        $sform->addElement($deliveryTime, false);

        $sform->addElement(new XoopsFormText(_OLEDRION_NUMBER, 'product_sku', 10, 60, $item->getVar('product_sku', 'e')), false);
        $sform->addElement(new XoopsFormText(_OLEDRION_EXTRA_ID, 'product_extraid', 10, 50, $item->getVar('product_extraid', 'e')), false);
        $sform->addElement(new XoopsFormText(_OLEDRION_LENGTH, 'product_length', 10, 50, $item->getVar('product_length', 'e')), false);
        $sform->addElement(new XoopsFormText(_OLEDRION_WIDTH, 'product_width', 10, 50, $item->getVar('product_width', 'e')), false);

        $sform->addElement(new XoopsFormText(_OLEDRION_MEASURE1, 'product_unitmeasure1', 10, 20, $item->getVar('product_unitmeasure1', 'e')), false);
        $sform->addElement(new XoopsFormText(_OLEDRION_WEIGHT, 'product_weight', 10, 20, $item->getVar('product_weight', 'e')), false);
        $sform->addElement(new XoopsFormText(_OLEDRION_MEASURE2, 'product_unitmeasure2', 10, 20, $item->getVar('product_unitmeasure2', 'e')), false);

        $downloadUrl = new XoopsFormText(_OLEDRION_DOWNLOAD_URL, 'product_download_url', 50, 255, $item->getVar('product_download_url', 'e'));
        $downloadUrl->setDescription(_AM_OLEDRION_DOWNLOAD_EXAMPLE . ' ' . XOOPS_UPLOAD_PATH . DIRECTORY_SEPARATOR . 'image.png');
        $sform->addElement($downloadUrl, false);

        $sform->addElement(new XoopsFormText(_AM_OLEDRION_URL_HLP1, 'product_url', 50, 255, $item->getVar('product_url', 'e')), false);
        $sform->addElement(new XoopsFormText(_AM_OLEDRION_URL_HLP2, 'product_url2', 50, 255, $item->getVar('product_url2', 'e')), false);
        $sform->addElement(new XoopsFormText(_AM_OLEDRION_URL_HLP3, 'product_url3', 50, 255, $item->getVar('product_url3', 'e')), false);

        // Images *************************************************************
        if ($action == 'edit' && $item->pictureExists()) {
            $pictureTray = new XoopsFormElementTray(_AM_OLEDRION_IMAGE1_HELP, '<br />');
            $pictureTray->addElement(new XoopsFormLabel('', "<img src='" . $item->getPictureUrl() . "' alt='' border='0' />"));
            $deleteCheckbox = new XoopsFormCheckBox('', 'delpicture1');
            $deleteCheckbox->addOption(1, _DELETE);
            $pictureTray->addElement($deleteCheckbox);
            $sform->addElement($pictureTray);
            unset($pictureTray, $deleteCheckbox);
        }
        $sform->addElement(new XoopsFormFile(_AM_OLEDRION_IMAGE1_CHANGE, 'attachedfile1', oledrion_utils::getModuleOption('maxuploadsize')), false);

        if (!oledrion_utils::getModuleOption('create_thumbs')) { // L'utilisateur se charge de créer la vignette lui même
            if ($action == 'edit' && $item->thumbExists()) {
                $pictureTray = new XoopsFormElementTray(_AM_OLEDRION_IMAGE2_HELP, '<br />');
                $pictureTray->addElement(new XoopsFormLabel('', "<img src='" . $item->getThumbUrl() . "' alt='' border='0' />"));
                $deleteCheckbox = new XoopsFormCheckBox('', 'delpicture2');
                $deleteCheckbox->addOption(1, _DELETE);
                $pictureTray->addElement($deleteCheckbox);
                $sform->addElement($pictureTray);
                unset($pictureTray, $deleteCheckbox);
            }
            $sform->addElement(new XoopsFormFile(_AM_OLEDRION_IMAGE2_CHANGE, 'attachedfile2', oledrion_utils::getModuleOption('maxuploadsize')), false);
        }

        // En ligne ? *********************************************************
        $sform->addElement(new XoopsFormRadioYN(_OLEDRION_ONLINE_HLP, 'product_online', $item->getVar('product_online')), true);
        $sform->addElement(new XoopsFormText(_OLEDRION_DATE, 'product_date', 50, 255, $item->getVar('product_date', 'e')), false);

        $date_submit = new XoopsFormTextDateSelect(_OLEDRION_DATE_SUBMIT, 'product_submitted', 15, $item->getVar('product_submitted', 'e'));
        $date_submit->setDescription(_AM_OLEDRION_SUBDATE_HELP);
        $sform->addElement($date_submit, false);

        $sform->addElement(new XoopsFormHidden('product_hits', $item->getVar('product_hits')));
        $sform->addElement(new XoopsFormHidden('product_rating', $item->getVar('product_rating')));
        $sform->addElement(new XoopsFormHidden('product_votes', $item->getVar('product_votes')));
        $sform->addElement(new XoopsFormHidden('product_comments', $item->getVar('product_comments')));

        // Fabricants ************************************************************
        $manufacturers = $productsManufacturers = $manufacturers_d = $productsManufacturers_d = array();
        // Recherche de tous les fabricants
        $criteria = new Criteria('manu_id', 0, '<>');
        $criteria->setSort('manu_name');
        $manufacturers = $h_oledrion_manufacturer->getObjects($criteria);
        foreach ($manufacturers as $oneitem) {
            $manufacturers_d[$oneitem->getVar('manu_id')] = xoops_trim($oneitem->getVar('manu_name')) . ' ' . xoops_trim($oneitem->getVar('manu_commercialname'));
        }
        // Recherche des fabricants de ce produit
        if ($edit) {
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('pm_product_id', $item->getVar('product_id'), '='));
            $productsManufacturers = $h_oledrion_productsmanu->getObjects($criteria);
            foreach ($productsManufacturers as $oneproduct) {
                $productsManufacturers_d[] = $oneproduct->getVar('pm_manu_id');
            }
        }
        $manufacturersSelect = new XoopsFormSelect(_OLEDRION_MANUFACTURER, 'manufacturers', $productsManufacturers_d, 5, true);
        $manufacturersSelect->addOptionArray($manufacturers_d);
        $manufacturersSelect->setDescription(_AM_OLEDRION_SELECT_HLP);
        $sform->addElement($manufacturersSelect, true);

        // Produits relatifs ****************************************************
        $relatedProducts = $productRelated = $relatedProducts_d = $productRelated_d = array();
        // Recherche de tous les produits sauf celui-là
        $criteria = new Criteria('product_id', $item->getVar('product_id'), '<>');
        $criteria->setSort('product_title');
        $relatedProducts = $h_oledrion_products->getObjects($criteria);
        foreach ($relatedProducts as $oneitem) {
            $relatedProducts_d[$oneitem->getVar('product_id')] = xoops_trim($oneitem->getVar('product_title'));
        }
        // Recherche des produits relatifs à ce produit
        if ($edit) {
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('related_product_id', $item->getVar('product_id'), '='));
            $productRelated = $h_oledrion_related->getObjects($criteria);
            foreach ($productRelated as $oneproduct) {
                $productRelated_d[] = $oneproduct->getVar('related_product_related');
            }
        }
        $productsSelect = $h_oledrion_products->productSelector(new oledrion_parameters(array('caption' => _OLEDRION_RELATED_PRODUCTS, 'name' => 'relatedproducts', 'value' => $productRelated_d, 'size' => 5, 'multiple' => true, 'values' => $productRelated_d, 'showAll' => true, 'sort' => 'product_title', 'order' => 'ASC', 'formName' => 'frmproduct')));
        $sform->addElement($productsSelect);
        // ********************************************************************

        if (oledrion_utils::getModuleOption('use_price')) {
            // TVA ****************************************************************
            $vatSelect = new XoopsFormSelect(_OLEDRION_VAT, 'product_vat_id', $item->getVar('product_vat_id'));
            $vatSelect->addOptionArray($vatsForDisplay);
            $sform->addElement($vatSelect, true);

            $sform->addElement(new XoopsFormText(_OLEDRION_PRICE, 'product_price', 20, 20, $item->getVar('product_price', 'e')), true);
            $sform->addElement(new XoopsFormText(_AM_OLEDRION_DISCOUNT_HLP, 'product_discount_price', 20, 20, $item->getVar('product_discount_price', 'e')), false);

            $sform->addElement(new XoopsFormText(_OLEDRION_SHIPPING_PRICE, 'product_shipping_price', 20, 20, $item->getVar('product_shipping_price', 'e')), false);
            $sform->addElement(new XoopsFormText(_OLEDRION_ECOTAXE, 'product_ecotaxe', 10, 10, $item->getVar('product_ecotaxe', 'e')), false);
        }
        $sform->addElement(new XoopsFormText(_OLEDRION_STOCK_QUANTITY, 'product_stock', 10, 10, $item->getVar('product_stock', 'e')), false);

        $alertStock = new XoopsFormText(_OLEDRION_STOCK_ALERT, 'product_alert_stock', 10, 10, $item->getVar('product_alert_stock', 'e'));
        $alertStock->setDescription(_AM_OLEDRION_STOCK_HLP);
        $sform->addElement($alertStock, false);

        $editor2 = oledrion_utils::getWysiwygForm(_OLEDRION_SUMMARY, 'product_summary', $item->getVar('product_summary', 'e'), 15, 60, 'summary_hidden');
        if ($editor2) {
            $sform->addElement($editor2, false);
        }

        $editor = oledrion_utils::getWysiwygForm(_OLEDRION_DESCRIPTION, 'product_description', $item->getVar('product_description', 'e'), 15, 60, 'description_hidden');
        if ($editor) {
            $sform->addElement($editor, false);
        }

        // Tags
        if (oledrion_utils::getModuleOption('use_tags')) {
            require_once XOOPS_ROOT_PATH . '/modules/tag/include/formtag.php';
            $sform->addElement(new XoopsFormTag('item_tag', 60, 255, $item->getVar('product_id'), 0));
        }

        // META Data
        if ($manual_meta) {
            $sform->addElement(new XoopsFormText(_AM_OLEDRION_META_KEYWORDS, 'product_metakeywords', 50, 255, $item->getVar('product_metakeywords', 'e')), false);
            $sform->addElement(new XoopsFormText(_AM_OLEDRION_META_DESCRIPTION, 'product_metadescription', 50, 255, $item->getVar('product_metadescription', 'e')), false);
            $sform->addElement(new XoopsFormText(_AM_OLEDRION_META_PAGETITLE, 'product_metatitle', 50, 255, $item->getVar('product_metatitle', 'e')), false);
        }
        // Fichier attaché
        if ($action == 'edit' && trim($item->getVar('product_attachment')) != '' && file_exists(XOOPS_UPLOAD_PATH . DIRECTORY_SEPARATOR . trim($item->getVar('product_attachment')))) {
            $pictureTray = new XoopsFormElementTray(_OLEDRION_ATTACHED_FILE, '<br />');
            $pictureTray->addElement(new XoopsFormLabel('', "<a href='" . XOOPS_UPLOAD_URL . '/' . $item->getVar('product_attachment') . "' target='_blank'>" . XOOPS_UPLOAD_URL . '/' . $item->getVar('product_attachment') . "</a>"));
            $deleteCheckbox = new XoopsFormCheckBox('', 'delpicture3');
            $deleteCheckbox->addOption(1, _DELETE);
            $pictureTray->addElement($deleteCheckbox);
            $sform->addElement($pictureTray);
            unset($pictureTray, $deleteCheckbox);
        }
        $downloadFile = new XoopsFormFile(_OLEDRION_ATTACHED_FILE, 'attachedfile3', oledrion_utils::getModuleOption('maxuploadsize'));
        $downloadFile->setDescription(_AM_OLEDRION_ATTACHED_HLP);
        $sform->addElement($downloadFile, false);

        if (oledrion_utils::getModuleOption('product_property1')) {
            $property1select = new XoopsFormSelect(oledrion_utils::getModuleOption('product_property1_title'), 'product_property1', $item->getVar('product_property1'));
            $property1Array = explode('|', oledrion_utils::getModuleOption('product_property1'));
            foreach ($property1Array as $property1) {
                $property1select->addOption($property1);
            }
            $sform->addElement($property1select, false);
        }

        if (oledrion_utils::getModuleOption('product_property2')) {
            $property2select = new XoopsFormSelect(oledrion_utils::getModuleOption('product_property2_title'), 'product_property2', $item->getVar('product_property2'));
            $property2Array = explode('|', oledrion_utils::getModuleOption('product_property2'));
            foreach ($property2Array as $property2) {
                $property2select->addOption($property2);
            }
            $sform->addElement($property2select, false);
        }

        if (oledrion_utils::getModuleOption('product_property3')) {
            $property3select = new XoopsFormSelect(oledrion_utils::getModuleOption('product_property3_title'), 'product_property3', $item->getVar('product_property3'));
            $property3Array = explode('|', oledrion_utils::getModuleOption('product_property3'));
            foreach ($property3Array as $property3) {
                $property3select->addOption($property3);
            }
            $sform->addElement($property3select, false);
        }

        if (oledrion_utils::getModuleOption('product_property4')) {
            $property4select = new XoopsFormSelect(oledrion_utils::getModuleOption('product_property4_title'), 'product_property4', $item->getVar('product_property4'));
            $property4Array = explode('|', oledrion_utils::getModuleOption('product_property4'));
            foreach ($property4Array as $property4) {
                $property4select->addOption($property4);
            }
            $sform->addElement($property4select, false);
        }

        if (oledrion_utils::getModuleOption('product_property5')) {
            $property5select = new XoopsFormSelect(oledrion_utils::getModuleOption('product_property5_title'), 'product_property5', $item->getVar('product_property5'));
            $property5Array = explode('|', oledrion_utils::getModuleOption('product_property5'));
            foreach ($property5Array as $property5) {
                $property5select->addOption($property5);
            }
            $sform->addElement($property5select, false);
        }

        if (oledrion_utils::getModuleOption('product_property6')) {
            $property6select = new XoopsFormSelect(oledrion_utils::getModuleOption('product_property6_title'), 'product_property6', $item->getVar('product_property6'));
            $property6Array = explode('|', oledrion_utils::getModuleOption('product_property6'));
            foreach ($property6Array as $property6) {
                $property6select->addOption($property6);
            }
            $sform->addElement($property6select, false);
        }

        if (oledrion_utils::getModuleOption('product_property7')) {
            $property7select = new XoopsFormSelect(oledrion_utils::getModuleOption('product_property7_title'), 'product_property7', $item->getVar('product_property7'));
            $property7Array = explode('|', oledrion_utils::getModuleOption('product_property7'));
            foreach ($property7Array as $property7) {
                $property7select->addOption($property7);
            }
            $sform->addElement($property7select, false);
        }

        if (oledrion_utils::getModuleOption('product_property8')) {
            $property8select = new XoopsFormSelect(oledrion_utils::getModuleOption('product_property8_title'), 'product_property8', $item->getVar('product_property8'));
            $property8Array = explode('|', oledrion_utils::getModuleOption('product_property8'));
            foreach ($property8Array as $property8) {
                $property8select->addOption($property8);
            }
            $sform->addElement($property8select, false);
        }

        if (oledrion_utils::getModuleOption('product_property9')) {
            $property9select = new XoopsFormSelect(oledrion_utils::getModuleOption('product_property9_title'), 'product_property9', $item->getVar('product_property9'));
            $property9Array = explode('|', oledrion_utils::getModuleOption('product_property9'));
            foreach ($property9Array as $property9) {
                $property9select->addOption($property95);
            }
            $sform->addElement($property9select, false);
        }

        if (oledrion_utils::getModuleOption('product_property10')) {
            $property10select = new XoopsFormSelect(oledrion_utils::getModuleOption('product_property10_title'), 'product_property10', $item->getVar('product_property10'));
            $property10Array = explode('|', oledrion_utils::getModuleOption('product_property10'));
            foreach ($property10Array as $property10) {
                $property10select->addOption($property10);
            }
            $sform->addElement($property10select, false);
        }

        $button_tray = new XoopsFormElementTray('', '');
        $submit_btn = new XoopsFormButton('', 'post', $label_submit, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);

        $sform = oledrion_utils::formMarkRequiredFields($sform);
        $sform->display();
        include_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;


    // ****************************************************************************************************************
    case 'saveedit': // Sauvegarde des informations d'un produit
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        if ($id > 0) {
            $edit = true;
            $item = $h_oledrion_products->get($id);
            if (!is_object($item)) {
                oledrion_utils::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $item->unsetNew();
            $add = false;
        } else {
            $item = $h_oledrion_products->create(true);
            $edit = false;
            $add = true;
        }
        $opRedirect = 'products';
        $item->setVars($_POST);
        $item->setVar('product_submitted', strtotime($_POST['product_submitted']));

        // Suppression de l'image principale
        if (isset($_POST['delpicture1']) && intval($_POST['delpicture1']) == 1) {
            $item->deletePicture();
        }

        // Suppression de la vignette
        if (isset($_POST['delpicture2']) && intval($_POST['delpicture2']) == 1) {
            $item->deleteThumb();
        }
        if (isset($_POST['delpicture3']) && intval($_POST['delpicture3']) == 1) {
            $item->deleteAttachment();
        }

        // Upload de la grande image
        $destname = '';
        $mainPicture = '';
        $res1 = oledrion_utils::uploadFile(0, OLEDRION_PICTURES_PATH);
        if ($res1 === true) {
            $mainPicture = $destname;
            if (oledrion_utils::getModuleOption('resize_main')) { // On redimensionne l'image principale
                oledrion_utils::resizePicture(OLEDRION_PICTURES_PATH . DIRECTORY_SEPARATOR . $destname, OLEDRION_PICTURES_PATH . DIRECTORY_SEPARATOR . $destname, oledrion_utils::getModuleOption('images_width'), oledrion_utils::getModuleOption('images_height'), true);
            }
            $item->setVar('product_image_url', basename($destname));
        } else {
            if ($res1 !== false) {
                echo $res1;
            }
        }

        $indiceAttached = 2;
        // Upload de la vignette
        if (!oledrion_utils::getModuleOption('create_thumbs')) { // L'utilisateur se charge de créer la vignette lui-même
            $destname = '';
            $res2 = oledrion_utils::uploadFile(1, OLEDRION_PICTURES_PATH);
            if ($res2 === true) {
                $item->setVar('product_thumb_url', basename($destname));
            } else {
                if ($res2 !== false) {
                    echo $res2;
                }
            }
        } else { // Il faut créer la vignette pour l'utilisateur
            $indiceAttached = 1;
            if (xoops_trim($mainPicture) != '') {
                $thumbName = OLEDRION_THUMBS_PREFIX . $mainPicture;
                oledrion_utils::resizePicture(OLEDRION_PICTURES_PATH . DIRECTORY_SEPARATOR . $mainPicture, OLEDRION_PICTURES_PATH . DIRECTORY_SEPARATOR . $thumbName, oledrion_utils::getModuleOption('thumbs_width'), oledrion_utils::getModuleOption('thumbs_height'), true);
                $item->setVar('product_thumb_url', $thumbName);
            }
        }


        // Téléchargement du fichier attaché
        $destname = '';
        $res3 = oledrion_utils::uploadFile($indiceAttached, OLEDRION_ATTACHED_FILES_PATH);
        if ($res3 === true) {
            $item->setVar('product_attachment', basename($destname));
        } else {
            if ($res3 !== false) {
                echo $res3;
            }
        }

        $res = $h_oledrion_products->insert($item);
        if ($res) {
            if (oledrion_utils::getModuleOption('use_tags')) {
                $tag_handler = xoops_getmodulehandler('tag', 'tag');
                $tag_handler->updateByItem($_POST['item_tag'], $item->getVar('product_id'), $xoopsModule->getVar('dirname'), 0);
            }

            $id = $item->getVar('product_id');
            // Notifications ******************************************************
            if ($add == true) {
                //$plugins = oledrion_plugins::getInstance();
                //$plugins->fireAction(oledrion_plugins::EVENT_ON_PRODUCT_CREATE, new oledrion_parameters(array('product' => $item)));
            }
            // Gestion des fabricants ************************************************
            if ($edit) {
                // Suppression préalable
                $criteria = new CriteriaCompo();
                $criteria->add(new Criteria('pm_product_id', $id, '='));
                $h_oledrion_productsmanu->deleteAll($criteria);
            }
            // Puis sauvegarde des données
            if (isset($_POST['manufacturers'])) {
                foreach ($_POST['manufacturers'] as $id2) {
                    $item2 = $h_oledrion_productsmanu->create(true);
                    $item2->setVar('pm_product_id', $id);
                    $item2->setVar('pm_manu_id', intval($id2));
                    $res = $h_oledrion_productsmanu->insert($item2);
                }
            }

            // Gestion des produits relatifs ****************************************
            if ($edit) {
                // Suppression préalable
                $criteria = new CriteriaCompo();
                $criteria->add(new Criteria('related_product_id', $id, '='));
                $h_oledrion_related->deleteAll($criteria);
            }
            // Puis sauvegarde des données
            if (isset($_POST['relatedproducts'])) {
                foreach ($_POST['relatedproducts'] as $id2) {
                    $item2 = $h_oledrion_related->create(true);
                    $item2->setVar('related_product_id', $id);
                    $item2->setVar('related_product_related', intval($id2));
                    $res = $h_oledrion_related->insert($item2);
                }
            }
            oledrion_utils::updateCache();
            oledrion_utils::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
        } else {
            oledrion_utils::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;


    // ****************************************************************************************************************
    case 'copy': // Copier un produit
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (empty($id)) {
            oledrion_utils::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $opRedirect = 'products';
        $product = null;
        $product = $h_oledrion_products->get($id);
        if (is_object($product)) {
            $newProduct = $h_oledrion_products->cloneProduct($product);
            $newProductId = $newProduct->product_id;
            if ($newProduct !== false) {
                oledrion_utils::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect . "&action=edit&id=" . $newProductId, 2);
            } else {
                oledrion_utils::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
            }
        }
        break;


    // ****************************************************************************************************************
    case 'confdelete': // Confirmation de la suppression d'un produit
        // ****************************************************************************************************************
        xoops_cp_header();

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id == 0) {
            oledrion_utils::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $item = $h_oledrion_products->get($id);
        if (!is_object($item)) {
            oledrion_utils::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
        }
        xoops_confirm(array('op' => 'products', 'action' => 'delete', 'id' => $id), 'index.php', _AM_OLEDRION_CONF_DELITEM . '<br />' . $item->getVar('product_title'));
        break;


    // ****************************************************************************************************************
    case 'delete': // Suppression d'un produit
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id == 0) {
            oledrion_utils::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $opRedirect = 'products';
        $tblTmp = array();
        $tblTmp = $h_oledrion_caddy->getCommandIdFromProduct($id);
        if (count($tblTmp) == 0) {
            $item = null;
            $item = $h_oledrion_products->get($id);
            if (is_object($item)) {
                $res = $oledrion_shelf->deleteProduct($item, true);
                if ($res) {
                    oledrion_utils::updateCache();
                    xoops_notification_deletebyitem($xoopsModule->getVar('mid'), 'new_product', $id);
                    oledrion_utils::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
                } else {
                    oledrion_utils::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
                }
            } else {
                oledrion_utils::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl . '?op=' . $opRedirect, 5);
            }
        } else {
            oledrion_utils::htitle(_AM_OLEDRION_SORRY_NOREMOVE, 4);
            $tblTmp2 = array();
            $tblTmp2 = $h_oledrion_commands->getObjects(new Criteria('cmd_id', '(' . implode(',', $tblTmp) . ')', 'IN'), true);
            echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
            $class = '';
            echo "<tr><th align='center'>" . _AM_OLEDRION_ID . "</th><th align='center'>" . _AM_OLEDRION_DATE . "</th><th align='center'>" . _AM_OLEDRION_CLIENT . "</th><th align='center'>" . _AM_OLEDRION_TOTAL_SHIPP . "</th></tr>";
            foreach ($tblTmp2 as $item) {
                $class = ($class == 'even') ? 'odd' : 'even';
                $date = formatTimestamp(strtotime($item->getVar('cmd_date')), 's');
                echo "<tr class='" . $class . "'>\n";
                echo "<td align='right'>" . $item->getVar('cmd_id') . "</td><td align='center'>" . $date . "</td><td align='center'>" . $item->getVar('cmd_lastname') . ' ' . $item->getVar('cmd_firstname') . "</td><td align='center'>" . $item->getVar('cmd_total') . ' ' . oledrion_utils::getModuleOption('money_short') . ' / ' . $item->getVar('cmd_shipping') . ' ' . oledrion_utils::getModuleOption('money_short') . "</td>\n";
                echo "<tr>\n";
            }
            echo '</table>';
            include_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        }
        break;
}
