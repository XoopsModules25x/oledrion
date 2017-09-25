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
 * Création du contenu d'une newsletter (dans l'administration)
 */
if (!defined('OLEDRION_ADMIN')) {
    exit();
}
switch ($action) {
    // ****************************************************************************************************************
    case 'default': // Création de la newsletter
        // ****************************************************************************************************************
        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation('index.php?op=newsletter');
        //        OledrionUtility::htitle(_MI_OLEDRION_ADMENU7, 4);

        require_once OLEDRION_PATH . 'class/tree.php';
        $sform     = new XoopsThemeForm(_MI_OLEDRION_ADMENU7, 'frmnewsletter', $baseurl);
        $datesTray = new XoopsFormElementTray(_AM_OLEDRION_NEWSLETTER_BETWEEN);
        $minDate   = $maxDate = 0;
        $h_oledrion_products->getMinMaxPublishedDate($minDate, $maxDate);
        $date1 = new XoopsFormTextDateSelect('', 'date1', 15, $minDate);
        $date2 = new XoopsFormTextDateSelect(_AM_OLEDRION_EXPORT_AND, 'date2', 15, $maxDate);
        $datesTray->addElement($date1);
        $datesTray->addElement($date2);
        $sform->addElement($datesTray);

        $categories = $h_oledrion_cat->getAllCategories(new Oledrion_parameters());
        $mytree     = new Oledrion_XoopsObjectTree($categories, 'cat_cid', 'cat_pid');

        if (OledrionUtility::checkVerXoops($GLOBALS['xoopsModule'], '2.5.9')) {
            $htmlSelect = $mytree->makeSelectElement('cat_cid', 'cat_title', '-', 0, true, 0, _AM_OLEDRION_ALL, _AM_OLEDRION_IN_CATEGORY);
            $sform->addElement($htmlSelect);
        } else {
            $htmlSelect = $mytree->makeSelBox('cat_cid', 'cat_title', '-', 0, _AM_OLEDRION_ALL);
            $sform->addElement(new XoopsFormLabel(_AM_OLEDRION_IN_CATEGORY, $htmlSelect), true);
        }

        $sform->addElement(new XoopsFormHidden('op', 'newsletter'), false);
        $sform->addElement(new XoopsFormHidden('action', 'launch'), false);
        $sform->addElement(new XoopsFormRadioYN(_AM_OLEDRION_REMOVE_BR, 'removebr', 1), false);
        $sform->addElement(new XoopsFormRadioYN(_AM_OLEDRION_NEWSLETTER_HTML_TAGS, 'removehtml', 0), false);
        $sform->addElement(new XoopsFormTextArea(_AM_OLEDRION_NEWSLETTER_HEADER, 'header', '', 4, 70), false);
        $sform->addElement(new XoopsFormTextArea(_AM_OLEDRION_NEWSLETTER_FOOTER, 'footer', '', 4, 70), false);
        $button_tray = new XoopsFormElementTray('', '');
        $submit_btn  = new XoopsFormButton('', 'post', _SUBMIT, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        $sform = OledrionUtility::formMarkRequiredFields($sform);
        $sform->display();
        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    // ****************************************************************************************************************
    case 'launch': // Création effective de la newsletter
        // ****************************************************************************************************************
        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation('index.php?op=newsletter');
        //        OledrionUtility::htitle(_MI_OLEDRION_ADMENU7, 4);

        $newsletterTemplate = '';
        if (file_exists(OLEDRION_PATH . 'language/' . $xoopsConfig['language'] . '/newsletter.php')) {
            require_once OLEDRION_PATH . 'language/' . $xoopsConfig['language'] . '/newsletter.php';
        } else {
            require_once OLEDRION_PATH . 'language/english/newsletter.php';
        }
        echo '<br>';
        $removeBr   = $removeHtml = false;
        $removeBr   = isset($_POST['removebr']) ? (int)$_POST['removebr'] : 0;
        $removeHtml = isset($_POST['removehtml']) ? (int)$_POST['removehtml'] : 0;
        $header     = isset($_POST['header']) ? $_POST['header'] : '';
        $footer     = isset($_POST['footer']) ? $_POST['footer'] : '';
        $date1      = strtotime($_POST['date1']);
        $date2      = strtotime($_POST['date2']);
        $cat_id     = (int)$_POST['cat_cid'];
        $products   = $categories = [];
        $products   = $h_oledrion_products->getProductsForNewsletter(new Oledrion_parameters([
                                                                                                 'startingDate' => $date1,
                                                                                                 'endingDate'   => $date2,
                                                                                                 'category'     => $cat_id
                                                                                             ]));
        $newsfile   = OLEDRION_NEWSLETTER_PATH;
        $categories = $h_oledrion_cat->getAllCategories(new Oledrion_parameters([
                                                                                    'start'   => 0,
                                                                                    'limit'   => 0,
                                                                                    'sort'    => 'cat_title',
                                                                                    'order'   => 'ASC',
                                                                                    'idaskey' => true
                                                                                ]));
        $vats       = $h_oledrion_vat->getAllVats(new Oledrion_parameters());

        $fp = fopen($newsfile, 'w');
        if (!$fp) {
            OledrionUtility::redirect(_AM_OLEDRION_ERROR_7, $baseurl . '?op=newsletter', 5);
        }
        if ('' !== xoops_trim($header)) {
            fwrite($fp, $header);
        }
        foreach ($products as $item) {
            $content  = $newsletterTemplate;
            $tblTmp   = $tblTmp2 = [];
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('pm_product_id', $item->getVar('product_id'), '='));
            $tblTmp = $h_oledrion_productsmanu->getObjects($criteria);
            foreach ($tblTmp as $productManufacturer) {
                $tblTmp2[] = $productManufacturer->getVar('pm_manu_id');
            }
            $manufacturers = $h_oledrion_manufacturer->getObjects(new Criteria('manu_id', '(' . implode(',', $tblTmp2) . ')', 'IN'), true);
            $tblTmp        = [];
            foreach ($manufacturers as $manufacturer) {
                $tblTmp[] = $manufacturer->getVar('manu_commercialname') . ' ' . $manufacturer->getVar('manu_name');
            }

            $search  = [
                '%title%',
                '%category%',
                '%author%',
                '%published%',
                '%price%',
                '%money%',
                '%hometext%',
                '%fulltext%',
                '%discountprice%',
                '%link%',
                '%product_sku%',
                '%product_extraid%',
                '%product_width%',
                '%product_date%',
                '%product_shipping_price%',
                '%product_stock%',
                '%product_unitmeasure1%',
                '%product_weight%',
                '%product_unitmeasure2%',
                '%product_download_url%',
                '%product_length%'
            ];
            $replace = [
                $item->getVar('product_title'),
                $categories[$item->getVar('product_cid')]->getVar('cat_title'),
                implode(', ', $tblTmp),
                formatTimestamp($item->getVar('product_submitted'), 's'),
                OledrionUtility::getTTC($item->getVar('product_price'), $vats[$item->getVar('product_vat_id')]->getVar('vat_rate')),
                OledrionUtility::getModuleOption('money_full'),
                $item->getVar('product_summary'),
                $item->getVar('product_description'),
                OledrionUtility::getTTC($item->getVar('product_discount_price'), $vats[$item->getVar('product_vat_id')]->getVar('vat_rate')),
                $item->getLink(),
                $item->getVar('product_sku'),
                $item->getVar('product_extraid'),
                $item->getVar('product_width'),
                $item->getVar('product_date'),
                $item->getVar('product_shipping_price'),
                $item->getVar('product_stock'),
                $item->getVar('product_unitmeasure1'),
                $item->getVar('product_weight'),
                $item->getVar('product_unitmeasure2'),
                $item->getVar('product_download_url'),
                $item->getVar('product_length')
            ];
            $content = str_replace($search, $replace, $content);
            if ($removeBr) {
                $content = str_replace('<br>', "\r\n", $content);
            }
            if ($removeHtml) {
                $content = strip_tags($content);
            }
            fwrite($fp, $content);
        }
        if ('' !== xoops_trim($footer)) {
            fwrite($fp, $footer);
        }
        fclose($fp);
        $newsfile = OLEDRION_NEWSLETTER_URL;
        echo "<a href='$newsfile' target='_blank'>" . _AM_OLEDRION_NEWSLETTER_READY . '</a>';
        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;
}
