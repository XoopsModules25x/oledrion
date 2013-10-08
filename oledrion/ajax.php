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
 * Script pour tout ce qui est relatif à Ajax et JSON
 *
 * @since 2.3.2009.03.17
 */
require_once 'header.php';
error_reporting(0);
@$xoopsLogger->activated = false;

$op = isset($_POST['op']) ? $_POST['op'] : '';
if ($op == '') {
    $op = isset($_GET['op']) ? $_GET['op'] : '';
}
$return = '';
$uid = oledrion_utils::getCurrentUserID();
$isAdmin = oledrion_utils::isAdmin();


switch ($op) {
    // ****************************************************************************************************************
    case 'updatePrice': // Mise à jour du prix du produit en fonction des attributs sélectionnés
        // ****************************************************************************************************************
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        if (isset($_POST['formcontent']) && $product_id > 0) {
            $data = $data = $attributesIds = $attributes = $templateProduct = array();
            $handlers = oledrion_handler::getInstance();
            $product = null;
            $product = $handlers->h_oledrion_products->get($product_id);
            if (!is_object($product)) {
                return _OLEDRION_NA;
            }
            if (!$product->isProductVisible()) {
                return _OLEDRION_NA;
            }
            $vat_id = $product->getVar('product_vat_id');

            if (intval($product->getVar('product_discount_price', '')) != 0) {
                $productPrice = floatval($product->getVar('product_discount_price', 'e'));
            } else {
                $productPrice = floatval($product->getVar('product_price', 'e'));
            }

            parse_str(urldecode($_POST['formcontent']), $data);
            /*
                        require_once 'FirePHPCore/FirePHP.class.php';
                        $firephp = FirePHP::getInstance(true);
                        $firephp->log($data, 'Iterators');
            */
            // On récupère les ID des attributs valorisés
            foreach ($data as $key => $value) {
                $attributesIds[] = oledrion_utils::getId($key);
            }
            if (count($attributesIds) == 0) {
                return _OLEDRION_NA;
            }
            // Puis les attributs
            $attributes = $handlers->h_oledrion_attributes->getItemsFromIds($attributesIds);
            if (count($attributes) == 0) {
                return _OLEDRION_NA;
            }

            // Et on recalcule le prix
            foreach ($attributes as $attribute) {
                $attributeNameInForm = xoops_trim($attribute->getVar('attribute_name') . '_' . $attribute->getVar('attribute_id'));
                if (isset($data[$attributeNameInForm])) {
                    $attributeValues = $data[$attributeNameInForm];
                    if (is_array($attributeValues)) {
                        foreach ($attributeValues as $attributeValue) {
                            $optionName = oledrion_utils::getName($attributeValue);
                            $optionPrice = $attribute->getOptionPriceFromValue($optionName);
                            $productPrice += $optionPrice;
                        }
                    } else {
                        $optionPrice = $attribute->getOptionPriceFromValue(oledrion_utils::getName($attributeValues));
                        $productPrice += $optionPrice;
                    }
                }
            }
            // Mise en template
            include_once XOOPS_ROOT_PATH . '/class/template.php';
            $template = new XoopsTpl();
            $vat = null;
            $vat = $handlers->h_oledrion_vat->get($vat_id);
            $productPriceTTC = oledrion_utils::getAmountWithVat($productPrice, $vat_id);

            $oledrion_Currency = oledrion_Currency::getInstance();

            $templateProduct = $product->toArray();
            $templateProduct['product_final_price_ht_formated_long'] = $oledrion_Currency->amountForDisplay($productPrice, 'l');
            $templateProduct['product_final_price_ttc_formated_long'] = $oledrion_Currency->amountForDisplay($productPriceTTC, 'l');
            if (is_object($vat)) {
                $templateProduct['product_vat_rate'] = $vat->toArray();
            }
            $templateProduct['product_vat_amount_formated_long'] = $oledrion_Currency->amountForDisplay($productPriceTTC - $productPrice, 'l');
            $template->assign('product', $templateProduct);
            $return = $template->fetch('db:oledrion_product_price.html');
        }
        break;
    // ajax search
    case 'search': // ajax search
        $key = $_GET['part'];
        if (isset($key) && $key != '') {
            // Set captul
            $i = 1;
            // Query 1
            $query = "SELECT `product_id` AS `id` , `product_cid` AS `cid`, `product_title` AS `title`, `product_thumb_url` AS `image`, `product_price` AS `price` FROM `" . $xoopsDB->prefix('oledrion_products') . "` WHERE (`product_online` = 1) AND (`product_title` LIKE '%" . $key . "%' OR `product_title` LIKE '%" . ucfirst($key) . "%') LIMIT 0, 10";
            $result = $xoopsDB->query($query);
            while ($row = $xoopsDB->fetchArray($result)) {
                $items[$i]['title'] = $row['title'];
                $items[$i]['type'] = 'product';
                $items[$i]['link'] = XOOPS_URL . '/modules/oledrion/product.php?product_id=' . $row['id'];
                $items[$i]['image'] = OLEDRION_PICTURES_URL . '/' . $row['image'];
                //$items[$i]['price'] = oledrion_utils::getTTC($row['price']);
                $category = $h_oledrion_cat->get($row['cid']);
                $items[$i]['cat_cid'] = $category->getVar('cat_cid');
                $items[$i]['cat_title'] = $category->getVar('cat_title');
                $i++;
            }
            // Query 2
            $query = "SELECT `cat_cid` AS `id` , `cat_title` AS `title`, `cat_imgurl` AS `image`  FROM `" . $xoopsDB->prefix('oledrion_cat') . "` WHERE (`cat_title` LIKE '%" . $key . "%') OR (`cat_title` LIKE '%" . ucfirst($key) . "%') LIMIT 0, 5";
            $result = $xoopsDB->query($query);
            while ($row = $xoopsDB->fetchArray($result)) {
                $items[$i]['title'] = $row['title'];
                $items[$i]['type'] = 'cat';
                $items[$i]['link'] = XOOPS_URL . '/modules/oledrion/category.php?cat_cid=' . $row['id'];
                $items[$i]['image'] = OLEDRION_PICTURES_URL . '/' . $row['image'];
                $items[$i]['price'] = '';
                $i++;
            }
            // Set array
            $results = array();
            // search colors
            foreach ($items as $item) {
                // if it starts with 'part' add to results
                //if( strpos($item['title'], $key) === 0 || strpos($item['title'], ucfirst($key)) === 0 ){
                if ($item['type'] == 'product') {
                    $results[] = '<div class="searchbox">
                         <div class="searchboxright"><a href="' . $item['link'] . '"><img src="' . $item['image'] . '" alt="" /></a></div>
                         <div class="searchboxleft">
                             <div class="searchboxitem"><a href="' . $item['link'] . '">' . $item['title'] . '</a></div>
                             <div class="searchboxcat"><a href="' . XOOPS_URL . '/modules/oledrion/category.php?cat_cid=' . $item['cat_cid'] . '">' . $item['cat_title'] . '</a></div>
                         </div>
                         <div class="clear"></div>
                     </div>';
                } else {
                    $results[] = '<div class="searchbox">
                         <div class="searchboxright"><a href="' . $item['link'] . '"><img src="' . $item['image'] . '" alt="" /></a></div>
                         <div class="searchboxleft">
                             <div class="searchboxitem"><a href="' . $item['link'] . '">' . $item['title'] . '</a></div>
                         </div>
                         <div class="clear"></div>
                     </div>';
                }
                //}
            }
            $return = json_encode($results);
        }
        break;
    // Product output as json
    case 'product':
        $start = intval($_GET['start']);
        $limit = intval($_GET['limit']);
        if (isset($start) && $start != '') {
            $ret = array();
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('product_id', $start, '>='));
            $criteria->add(new Criteria('product_online', 1));
            $criteria->setSort('product_id');
            $criteria->setOrder('ASC');
            $criteria->setLimit($limit);
            $obj = $h_oledrion_products->getObjects($criteria, false);
            if ($obj) {
                foreach ($obj as $root) {
                    $tab = array();
                    $tab = $root->toArray();
                    $json['product_id'] = $tab['product_id'];
                    $json['product_cid'] = $tab['product_cid'];
                    $json['product_title'] = preg_replace('/,/', ';', $tab['product_title']);
                    $json['product_description'] = preg_replace('/,/', ';', $tab['product_description']);
                    $json['product_image_url'] = $tab['product_image_url'];
                    $json['product_thumb_url'] = $tab['product_thumb_url'];
                    $json['product_property1'] = $tab['product_property1'];
                    $json['product_property2'] = $tab['product_property2'];
                    $json['product_property3'] = $tab['product_property3'];
                    $json['product_property4'] = $tab['product_property4'];
                    $json['product_submitted'] = $tab['product_submitted'];
                    unset($tab);
                    $ret[] = $json;
                }
            }
            $return = json_encode($ret);
        }
        break;
    // Product output as json
    case 'category':
        $start = intval($_GET['start']);
        if (isset($start) && $start != '') {
            $ret = array();
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('cat_cid', $start, '>='));
            $criteria->setSort('cat_cid');
            $criteria->setOrder('DESC');
            $obj = $h_oledrion_cat->getObjects($criteria, false);
            if ($obj) {
                foreach ($obj as $root) {
                    $tab = array();
                    $tab = $root->toArray();
                    $json['cat_cid'] = $tab['cat_cid'];
                    $json['cat_pid'] = $tab['cat_pid'];
                    $json['cat_title'] = preg_replace('/,/', ';', $tab['cat_title']);
                    $json['cat_imgurl'] = $tab['cat_imgurl'];
                    unset($tab);
                    $ret[] = $json;
                }
            }
            $return = json_encode($ret);
        }
        break;

    // Product output as json
    case 'price':
        $product_id = intval($_GET['product_id']);
        $product = $h_oledrion_products->get($product_id);
        if (is_object($product)) {
            if ($product->getVar('product_online') && $product->getVar('product_stock') > 0) {
                $product_price = $product->getVar('product_price');
                if ($h_oledrion_attributes->getProductAttributesCount($product->getVar('product_id')) > 0) {
                    $criteria = new CriteriaCompo ();
                    $criteria->add(new Criteria('attribute_product_id', $product->getVar('product_id')));
                    $attribute = $h_oledrion_attributes->getObjects($criteria, false);
                    foreach ($attribute as $root) {
                        $product_price = $root->getVar('attribute_default_value');
                    }
                }
                $ret = array(
                    'product_id' => $product->getVar('product_id'),
                    'product_price' => $product_price,
                ); 
            } else {
                $ret = array(
                    'product_id' => $product->getVar('product_id'),
                    'product_price' => 0,
                ); 
            }
        } else {
            $ret = array(
                'product_id' => 0,
                'product_price' => 0,
            );  
        }
        $return = json_encode($ret);
        break;

    // Ajax rate
    case 'rate':
        if (isset($_POST['product_id'])) {
            $product_id = intval($_POST['product_id']);
            $product = null;
            $product = $h_oledrion_products->get($product_id);
            if (is_object($product) && $product->getVar('product_online') && !oledrion_utils::getModuleOption('show_unpublished') && $product->getVar('product_submitted') < time() && oledrion_utils::getModuleOption('nostock_display') && $product->getVar('product_stock')) {
                $GLOBALS['current_category'] = -1;
                $ratinguser = oledrion_utils::getCurrentUserID();
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
                        oledrion_utils::redirect(_OLEDRION_NORATING, OLEDRION_URL . 'product.php?product_id=' . $product->getVar('product_id'), 4);
                    }
                    $rating = intval($_POST['rating']);
                    if ($rating < 1 || $rating > 10) {
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
                    $ratemessage = _OLEDRION_VOTEAPPRE . '<br />' . sprintf(_OLEDRION_THANKYOU, $xoopsConfig['sitename']);
                    oledrion_utils::redirect($ratemessage, OLEDRION_URL . 'product.php?product_id=' . $product->getVar('product_id'), 2);
                }
            }
        }
        break;
}
echo $return;

