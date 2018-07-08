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
 *
 * /**
 * Impression du catalogue au format PDF
 */

use XoopsModules\Oledrion;

//use tecnickcom\TCPDF;
require_once dirname(dirname(__DIR__)) . '/mainfile.php';
require_once XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';

if (!is_file(XOOPS_ROOT_PATH . '/class/libraries/vendor/tecnickcom/tcpdf/tcpdf.php')) {
    redirect_header(XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/viewtopic.php?topic_id=' . $topic_id, 3, 'TCPDF for Xoops not installed');
}

error_reporting(0);
@$xoopsLogger->activated = false;

if (1 != Oledrion\Utility::getModuleOption('pdf_catalog')) {
    die();
}

require_once XOOPS_ROOT_PATH . '/class/template.php';
$details        = \Xmf\Request::getInt('catalogFormat', 0, 'POST');
$Tpl            = new \XoopsTpl();
$vatArray       = $tbl_categories = [];
$vatArray       = $vatHandler->getAllVats(new Oledrion\Parameters());
$tbl_categories = $categoryHandler->getAllCategories(new Oledrion\Parameters());
$Tpl->assign('mod_pref', $mod_pref);    // module preferences

$cat_cid  = 0;
$tbl_tmp  = [];
$products = [];
$products = $productsHandler->getRecentProducts(new Oledrion\Parameters(['start' => 0, 'limit' => 0, 'category' => $cat_cid]));

if (count($products) > 0) {
    $helper->loadLanguage('modinfo');
    $Tpl->assign('details', $details);
    $tblAuthors = $tbl_tmp = $tblManufacturersPerProduct = [];
    $tblAuthors = $productsmanuHandler->getObjects(new \Criteria('pm_product_id', '(' . implode(',', array_keys($products)) . ')', 'IN'), true);
    foreach ($tblAuthors as $item) {
        $tbl_tmp[]                                                    = $item->getVar('pm_manu_id');
        $tblManufacturersPerProduct[$item->getVar('pm_product_id')][] = $item;
    }
    $tbl_tmp    = array_unique($tbl_tmp);
    $tblAuthors = $manufacturerHandler->getObjects(new \Criteria('manu_id', '(' . implode(',', $tbl_tmp) . ')', 'IN'), true);
    foreach ($products as $item) {
        $tbl_tmp                               = [];
        $tbl_tmp                               = $item->toArray();
        $tbl_tmp['product_category']           = isset($tbl_categories[$item->getVar('product_cid')]) ? $tbl_categories[$item->getVar('product_cid')]->toArray() : null;
        $tbl_tmp['product_price_ttc']          = Oledrion\Utility::getTTC($item->getVar('product_price'), $vatArray[$item->getVar('product_vat_id')]->getVar('vat_rate'), false, 's');
        $tbl_tmp['product_discount_price_ttc'] = Oledrion\Utility::getTTC($item->getVar('product_discount_price'), $vatArray[$item->getVar('product_vat_id')]->getVar('vat_rate'), false, 's');
        $tbl_join                              = [];
        foreach ($tblManufacturersPerProduct[$item->getVar('product_id')] as $author) {
            $auteur     = $tblAuthors[$author->getVar('pm_manu_id')];
            $tbl_join[] = $auteur->getVar('manu_commercialname') . ' ' . $auteur->getVar('manu_name');
        }
        if (count($tbl_join) > 0) {
            $tbl_tmp['product_joined_manufacturers'] = implode(', ', $tbl_join);
        }
        $Tpl->append('products', $tbl_tmp);
    }
}

$content1 = utf8_encode($Tpl->fetch('db:oledrion_pdf_catalog.tpl'));
$content2 = '';
if (Oledrion\Utility::getModuleOption('use_price')) {
    $content2 = utf8_encode($Tpl->fetch('db:oledrion_purchaseorder.tpl'));
}

// ****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
// ****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
// ****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************

$doc_title    = _OLEDRION_CATALOG;
$doc_subject  = _OLEDRION_CATALOG;
$doc_keywords = 'Instant Zero';

//require_once OLEDRION_PATH.'pdf/config/lang/'._LANGCODE.'.php';
//require_once OLEDRION_PATH.'pdf/tcpdf.php';
require_once XOOPS_ROOT_PATH . '/class/libraries/vendor/tecnickcom/tcpdf/tcpdf.php';

//create new PDF document (document units are set by default to millimeters)
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$firstLine  = utf8_encode(Oledrion\Utility::getModuleName() . ' - ' . $xoopsConfig['sitename']);
$secondLine = OLEDRION_URL . ' - ' . formatTimestamp(time(), 'm');
$pdf->setHeaderData('', '', $firstLine, $secondLine);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
//set auto page breaks
$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
$pdf->setFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

$pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
$pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);

$pdf->setLanguageArray($l); //set language items

//initialize document
//$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->writeHTML($content1, true, 0);
if (Oledrion\Utility::getModuleOption('use_price')) {
    $pdf->AddPage();
    $pdf->writeHTML($content2, true, 0);
}
$pdf->Output();
