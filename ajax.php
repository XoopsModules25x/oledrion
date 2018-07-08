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
 * Script pour tout ce qui est relatif à Ajax et JSON
 *
 * @since 2.3.2009.03.17
 */

use XoopsModules\Oledrion;
use XoopsModules\Oledrion\Constants;

require_once __DIR__ . '/header.php';
error_reporting(0);
@$xoopsLogger->activated = false;
$db         = \XoopsDatabaseFactory::getDatabaseConnection();
$vatHandler = new Oledrion\VatHandler($db);

$op = \Xmf\Request::getString('op', '', 'POST');
if ('' === $op) {
    $op = \Xmf\Request::getString('op', '', 'GET');
}
$return  = '';
$uid     = Oledrion\Utility::getCurrentUserID();
$isAdmin = Oledrion\Utility::isAdmin();

switch ($op) {
    // ****************************************************************************************************************
    case 'updatePrice': // Mise à jour du prix du produit en fonction des attributs sélectionnés

        // ****************************************************************************************************************
        $product_id = \Xmf\Request::getInt('product_id', 0, 'POST');
        $product    = null;
        if ($product_id > 0 && \Xmf\Request::hasVar('formcontent', 'POST')) {
            $data = $data = $attributesIds = $attributes = $templateProduct = [];
            //            $handlers = HandlerManager::getInstance();
            /** @var \XoopsModules\Oledrion\Products $product */
            $product = $productsHandler->get($product_id);
            if (!is_object($product)) {
                return _OLEDRION_NA;
            }
            if (!$product->isProductVisible()) {
                return _OLEDRION_NA;
            }
            $vat_id = $product->getVar('product_vat_id');

            if (0 !== (int)$product->getVar('product_discount_price', '')) {
                $productPrice = (float)$product->getVar('product_discount_price', 'e');
            } else {
                $productPrice = (float)$product->getVar('product_price', 'e');
            }

            parse_str(urldecode($_POST['formcontent']), $data);
            /*
                        require_once __DIR__ . '/FirePHPCore/FirePHP.class.php';
                        $firephp = FirePHP::getInstance(true);
                        $firephp->log($data, 'Iterators');
            */
            // On récupère les ID des attributs valorisés
            foreach ($data as $key => $value) {
                $attributesIds[] = Oledrion\Utility::getId($key);
            }
            if (0 === count($attributesIds)) {
                return _OLEDRION_NA;
            }
            // Puis les attributs
            $attributes = $attributesHandler->getItemsFromIds($attributesIds);
            if (0 === count($attributes)) {
                return _OLEDRION_NA;
            }

            // Et on recalcule le prix
            foreach ($attributes as $attribute) {
                $attributeNameInForm = xoops_trim($attribute->getVar('attribute_name') . '_' . $attribute->getVar('attribute_id'));
                if (isset($data[$attributeNameInForm])) {
                    $attributeValues = $data[$attributeNameInForm];
                    if (is_array($attributeValues)) {
                        foreach ($attributeValues as $attributeValue) {
                            $optionName   = Oledrion\Utility::getName($attributeValue);
                            $optionPrice  = $attribute->getOptionPriceFromValue($optionName);
                            $productPrice += $optionPrice;
                        }
                    } else {
                        $optionPrice  = $attribute->getOptionPriceFromValue(Oledrion\Utility::getName($attributeValues));
                        $productPrice += $optionPrice;
                    }
                }
            }
            // Mise en template
            require_once XOOPS_ROOT_PATH . '/class/template.php';
            $template        = new \XoopsTpl();
            $vat             = null;
            $vat             = $vatHandler->get($vat_id);
            $productPriceTTC = Oledrion\Utility::getAmountWithVat($productPrice, $vat_id);

            $oledrionCurrency = Oledrion\Currency::getInstance();

            $templateProduct                                          = $product->toArray();
            $templateProduct['product_final_price_ht_formated_long']  = $oledrionCurrency->amountForDisplay($productPrice, 'l');
            $templateProduct['product_final_price_ttc_formated_long'] = $oledrionCurrency->amountForDisplay($productPriceTTC, 'l');
            if (is_object($vat)) {
                $templateProduct['product_vat_rate'] = $vat->toArray();
            }
            $templateProduct['product_vat_amount_formated_long'] = $oledrionCurrency->amountForDisplay($productPriceTTC - $productPrice, 'l');
            $template->assign('product', $templateProduct);
            $return = $template->fetch('db:oledrion_product_price.tpl');
        }

        break;
    // ajax search
    case 'search': // ajax search

        $key = $_GET['part'];
        if (isset($key) && '' !== $key) {
            // Set captul
            $i = 1;
            // Query 1
            $query  = 'SELECT `product_id` AS `id` , `product_cid` AS `cid`, `product_title` AS `title`, `product_thumb_url` AS `image`, `product_price` AS `price` FROM `'
                      . $xoopsDB->prefix('oledrion_products')
                      . "` WHERE (`product_online` = 1) AND (`product_title` LIKE '%"
                      . $key
                      . "%' OR `product_title` LIKE '%"
                      . ucfirst($key)
                      . "%') LIMIT 0, 10";
            $result = $xoopsDB->query($query);
            while (false !== ($row = $xoopsDB->fetchArray($result))) {
                $items[$i]['title'] = $row['title'];
                $items[$i]['type']  = 'product';
                $items[$i]['link']  = XOOPS_URL . '/modules/oledrion/product.php?product_id=' . $row['id'];
                $items[$i]['image'] = OLEDRION_PICTURES_URL . '/' . $row['image'];
                //$items[$i]['price'] = Oledrion\Utility::getTTC($row['price']);
                $category               = $categoryHandler->get($row['cid']);
                $items[$i]['cat_cid']   = $category->getVar('cat_cid');
                $items[$i]['cat_title'] = $category->getVar('cat_title');
                ++$i;
            }
            // Query 2
            $query  = 'SELECT `cat_cid` AS `id` , `cat_title` AS `title`, `cat_imgurl` AS `image`  FROM `' . $xoopsDB->prefix('oledrion_cat') . "` WHERE (`cat_title` LIKE '%" . $key . "%') OR (`cat_title` LIKE '%" . ucfirst($key) . "%') LIMIT 0, 5";
            $result = $xoopsDB->query($query);
            while (false !== ($row = $xoopsDB->fetchArray($result))) {
                $items[$i]['title'] = $row['title'];
                $items[$i]['type']  = 'cat';
                $items[$i]['link']  = XOOPS_URL . '/modules/oledrion/category.php?cat_cid=' . $row['id'];
                $items[$i]['image'] = OLEDRION_PICTURES_URL . '/' . $row['image'];
                $items[$i]['price'] = '';
                ++$i;
            }
            // Set array
            $results = [];
            // search colors
            foreach ($items as $item) {
                // if it starts with 'part' add to results
                //if ( strpos($item['title'], $key) === 0 || strpos($item['title'], ucfirst($key)) === 0 ) {
                if ('product' === $item['type']) {
                    $results[] = '<div class="searchbox">
                         <div class="searchboxright"><a href="' . $item['link'] . '"><img src="' . $item['image'] . '" alt=""></a></div>
                         <div class="searchboxleft">
                             <div class="searchboxitem"><a href="' . $item['link'] . '">' . $item['title'] . '</a></div>
                             <div class="searchboxcat"><a href="' . XOOPS_URL . '/modules/oledrion/category.php?cat_cid=' . $item['cat_cid'] . '">' . $item['cat_title'] . '</a></div>
                         </div>
                         <div class="clear"></div>
                     </div>';
                } else {
                    $results[] = '<div class="searchbox">
                         <div class="searchboxright"><a href="' . $item['link'] . '"><img src="' . $item['image'] . '" alt=""></a></div>
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

        $start = \Xmf\Request::getInt('start', 0, 'GET');
        $limit = \Xmf\Request::getInt('limit', 0, 'GET');
        if (isset($start) && '' !== $start) {
            $ret      = [];
            $criteria = new \CriteriaCompo();
            $criteria->add(new \Criteria('product_id', $start, '>='));
            $criteria->add(new \Criteria('product_online', 1));
            $criteria->setSort('product_id');
            $criteria->setOrder('ASC');
            $criteria->setLimit($limit);
            $obj = $productsHandler->getObjects($criteria, false);
            if ($obj) {
                foreach ($obj as $root) {
                    $tab                         = [];
                    $tab                         = $root->toArray();
                    $json['product_id']          = $tab['product_id'];
                    $json['product_cid']         = $tab['product_cid'];
                    $json['product_title']       = preg_replace('/,/', ';', $tab['product_title']);
                    $json['product_description'] = preg_replace('/,/', ';', $tab['product_description']);
                    $json['product_image_url']   = $tab['product_image_url'];
                    $json['product_thumb_url']   = $tab['product_thumb_url'];
                    $json['product_property1']   = $tab['product_property1'];
                    $json['product_property2']   = $tab['product_property2'];
                    $json['product_property3']   = $tab['product_property3'];
                    $json['product_property4']   = $tab['product_property4'];
                    $json['product_submitted']   = $tab['product_submitted'];
                    unset($tab);
                    $ret[] = $json;
                }
            }
            $return = json_encode($ret);
        }

        break;
    // Product output as json
    case 'category':

        $start = \Xmf\Request::getInt('start', 0, 'GET');
        if (isset($start) && '' !== $start) {
            $ret      = [];
            $criteria = new \CriteriaCompo();
            $criteria->add(new \Criteria('cat_cid', $start, '>='));
            $criteria->setSort('cat_cid');
            $criteria->setOrder('DESC');
            $obj = $categoryHandler->getObjects($criteria, false);
            if ($obj) {
                foreach ($obj as $root) {
                    $tab                = [];
                    $tab                = $root->toArray();
                    $json['cat_cid']    = $tab['cat_cid'];
                    $json['cat_pid']    = $tab['cat_pid'];
                    $json['cat_title']  = preg_replace('/,/', ';', $tab['cat_title']);
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

        $product_id = \Xmf\Request::getInt('product_id', 0, 'GET');
        $product    = $productsHandler->get($product_id);
        if (is_object($product)) {
            if ($product->getVar('product_online') && $product->getVar('product_stock') > 0) {
                $product_price = $product->getVar('product_price');
                if ($attributesHandler->getProductAttributesCount($product->getVar('product_id')) > 0) {
                    $criteria = new \CriteriaCompo();
                    $criteria->add(new \Criteria('attribute_product_id', $product->getVar('product_id')));
                    $attribute = $attributesHandler->getObjects($criteria, false);
                    foreach ($attribute as $root) {
                        $product_price = $root->getVar('attribute_default_value');
                    }
                }
                $ret = [
                    'product_id'    => $product->getVar('product_id'),
                    'product_price' => $product_price,
                ];
            } else {
                $ret = [
                    'product_id'    => $product->getVar('product_id'),
                    'product_price' => 0,
                ];
            }
        } else {
            $ret = [
                'product_id'    => 0,
                'product_price' => 0,
            ];
        }
        $return = json_encode($ret);

        break;
    // Ajax rate
    case 'rate':

        if (\Xmf\Request::hasVar('product_id', 'POST')) {
            $product_id = \Xmf\Request::getInt('product_id', 0, 'POST');
            $product    = null;
            $product    = $productsHandler->get($product_id);
            if (is_object($product) && $product->getVar('product_online') && !Oledrion\Utility::getModuleOption('show_unpublished') && Oledrion\Utility::getModuleOption('nostock_display') && $product->getVar('product_submitted') < time() && $product->getVar('product_stock')) {
                $GLOBALS['current_category'] = -1;
                $ratinguser                  = Oledrion\Utility::getCurrentUserID();
                $canRate                     = true;
                if (0 !== $ratinguser) {
                    if ($votedataHandler->hasUserAlreadyVoted($ratinguser, $product->getVar('product_id'))) {
                        $canRate = false;
                    }
                } else {
                    if ($votedataHandler->hasAnonymousAlreadyVoted('', $product->getVar('product_id'))) {
                        $canRate = false;
                    }
                }
                if ($canRate) {
                    /* if ($_POST['rating'] == '--') {
                                            Oledrion\Utility::redirect(_OLEDRION_NORATING, OLEDRION_URL . 'product.php?product_id=' . $product->getVar('product_id'), 4);
                                        } */
                    $rating = \Xmf\Request::getInt('rating', 0, 'POST');
                    /* if ($rating < 1 || $rating > 10) {
                        exit(_ERRORS);
                    } */
                    if (1 === $rating || -1 === $rating) {
                        $result = $votedataHandler->createRating($product->getVar('product_id'), $ratinguser, $rating);

                        $totalVotes = 0;
                        $sumRating  = 0;
                        $ret        = 0;
                        $ret        = $votedataHandler->getCountRecordSumRating($product->getVar('product_id'), $totalVotes, $sumRating);

                        //$finalrating = $sumRating / $totalVotes;
                        //$finalrating = number_format($finalrating, 4);

                        $productsHandler->updateRating($product_id, $sumRating, $totalVotes);
                        //$ratemessage = _OLEDRION_VOTEAPPRE . '<br>' . sprintf(_OLEDRION_THANKYOU, $xoopsConfig['sitename']);
                        //Oledrion\Utility::redirect($ratemessage, OLEDRION_URL . 'product.php?product_id=' . $product->getVar('product_id'), 2);
                    } else {
                        $return = false;
                    }
                } else {
                    $return = false;
                }
            }
        }

        break;
    case 'order':

        $ret            = [];
        $ret['status']  = 0;
        $ret['message'] = 'error';
        if (\Xmf\Request::hasVar('product_id', 'POST') && is_numeric($_POST['product_id'])) {
            // Set from post
            $product_id    = \Xmf\Request::getString('product_id', '', 'POST');
            $cmd_lastname  = \Xmf\Request::getString('cmd_lastname', '', 'POST');
            $cmd_firstname = \Xmf\Request::getString('cmd_firstname', '', 'POST');
            $cmd_adress    = \Xmf\Request::getString('cmd_adress', '', 'POST');
            $cmd_zip       = \Xmf\Request::getString('cmd_zip', '', 'POST');
            $cmd_town      = \Xmf\Request::getString('cmd_town', '', 'POST');
            $cmd_country   = \Xmf\Request::getString('cmd_country', '', 'POST');
            $cmd_telephone = \Xmf\Request::getString('cmd_telephone', '', 'POST');
            $cmd_mobile    = \Xmf\Request::getString('cmd_mobile', '', 'POST');
            $cmd_email     = \Xmf\Request::getString('cmd_email', '', 'POST');
            //$cmd_total = isset($_POST['cmd_total']) ? $_POST['cmd_total'] : '';
            //$cmd_shipping = isset($_POST['cmd_shipping']) ? $_POST['cmd_shipping'] : '';
            $cmd_packing_price = \Xmf\Request::getString('cmd_packing_price', '', 'POST');
            $cmd_bill          = \Xmf\Request::getString('cmd_bill', '', 'POST');
            $cmd_text          = \Xmf\Request::getString('cmd_text', '', 'POST');
            $cmd_comment       = \Xmf\Request::getString('cmd_comment', '', 'POST');
            $cmd_vat_number    = \Xmf\Request::getString('cmd_vat_number', '', 'POST');
            $cmd_packing       = \Xmf\Request::getString('cmd_packing', '', 'POST');
            $cmd_packing_id    = \Xmf\Request::getString('cmd_packing_id', '', 'POST');
            $cmd_location      = \Xmf\Request::getString('cmd_location', '', 'POST');
            $cmd_location_id   = \Xmf\Request::getString('cmd_location_id', '', 'POST');
            $cmd_delivery      = \Xmf\Request::getString('cmd_delivery', '', 'POST');
            $cmd_delivery_id   = \Xmf\Request::getString('cmd_delivery_id', '', 'POST');
            $cmd_payment       = \Xmf\Request::getString('cmd_payment', '', 'POST');
            $cmd_payment_id    = \Xmf\Request::getString('cmd_payment_id', '', 'POST');
            $cmd_track         = \Xmf\Request::getString('cmd_track', '', 'POST');
            $cmd_gift          = \Xmf\Request::getString('cmd_gift', '', 'POST');
            $attributes        = \Xmf\Request::getString('attributes', '', 'POST');
            // Get product
            $product       = $productsHandler->get($product_id);
            $product_price = $product->getVar('product_price');
            if ($attributesHandler->getProductAttributesCount($product->getVar('product_id')) > 0) {
                $criteria = new \CriteriaCompo();
                $criteria->add(new \Criteria('attribute_product_id', $product->getVar('product_id')));
                $attribute = $attributesHandler->getObjects($criteria, false);
                foreach ($attribute as $root) {
                    $product_price = $root->getVar('attribute_default_value');
                }
            }
            if ($product->getVar('product_online') && $product->getVar('product_stock') > 0) {
                // Set parameter
                $password       = md5(xoops_makepass());
                $passwordCancel = md5(xoops_makepass());
                $uid            = Oledrion\Utility::getCurrentUserID();
                $cmd_total      = $product_price;
                $cmd_shipping   = 0;
                // Save command
                $commande = $commandsHandler->create(true);
                $commande->setVar('cmd_uid', $uid);
                $commande->setVar('cmd_date', date('Y-m-d'));
                $commande->setVar('cmd_create', time());
                $commande->setVar('cmd_state', Constants::OLEDRION_STATE_NOINFORMATION);
                $commande->setVar('cmd_ip', Oledrion\Utility::IP());
                $commande->setVar('cmd_lastname', $cmd_lastname);
                $commande->setVar('cmd_firstname', $cmd_firstname);
                $commande->setVar('cmd_adress', $cmd_adress);
                $commande->setVar('cmd_zip', $cmd_zip);
                $commande->setVar('cmd_town', $cmd_town);
                $commande->setVar('cmd_country', $cmd_country);
                $commande->setVar('cmd_telephone', $cmd_telephone);
                $commande->setVar('cmd_mobile', $cmd_mobile);
                $commande->setVar('cmd_email', $cmd_email);
                $commande->setVar('cmd_articles_count', 1);
                $commande->setVar('cmd_total', Oledrion\Utility::formatFloatForDB($cmd_total));
                $commande->setVar('cmd_shipping', Oledrion\Utility::formatFloatForDB($cmd_shipping));
                $commande->setVar('cmd_packing_price', $cmd_packing_price);
                $commande->setVar('cmd_bill', $cmd_bill);
                $commande->setVar('cmd_password', $password);
                $commande->setVar('cmd_text', $cmd_text);
                $commande->setVar('cmd_cancel', $passwordCancel);
                $commande->setVar('cmd_comment', $cmd_comment);
                $commande->setVar('cmd_vat_number', $cmd_vat_number);
                $commande->setVar('cmd_packing', $cmd_packing);
                $commande->setVar('cmd_packing_id', $cmd_packing_id);
                $commande->setVar('cmd_location', $cmd_location);
                $commande->setVar('cmd_location_id', $cmd_location_id);
                $commande->setVar('cmd_delivery', $cmd_delivery);
                $commande->setVar('cmd_delivery_id', $cmd_delivery_id);
                $commande->setVar('cmd_payment', $cmd_payment);
                $commande->setVar('cmd_payment_id', $cmd_payment_id);
                $commande->setVar('cmd_status', 2);
                $commande->setVar('cmd_track', $cmd_track);
                $commande->setVar('cmd_gift', $cmd_gift);
                $res1 = $commandsHandler->insert($commande, true);
                // Save caddy
                $caddy = $caddyHandler->create(true);
                $caddy->setVar('caddy_product_id', $product_id);
                $caddy->setVar('caddy_qte', $product->getVar('product_qty'));
                $caddy->setVar('caddy_price', Oledrion\Utility::formatFloatForDB($cmd_total));
                $caddy->setVar('caddy_cmd_id', $commande->getVar('cmd_id'));
                $caddy->setVar('caddy_shipping', Oledrion\Utility::formatFloatForDB($cmd_shipping));
                $caddy->setVar('caddy_pass', md5(xoops_makepass()));
                $res2 = $caddyHandler->insert($caddy, true);
                // Attributs
                /* if ($res2 && is_array($attributes) && count($attributes) > 0) {
                    foreach ($attributes as $attributeId => $attributeInformation) {
                        $caddyAttribute = $handlers->h_oledrion_caddy_attributes->create(true);
                        $caddyAttribute->setVar('ca_cmd_id', $commande->getVar('cmd_id'));
                        $caddyAttribute->setVar('ca_caddy_id', $caddy->getVar('caddy_id'));
                        $caddyAttribute->setVar('ca_attribute_id', $attributeId);
                        $selectedOptions = $attributeInformation['attribute_options'];
                        $msgCommande .= '- ' . $attributeInformation['attribute_title'] . "\n";
                        foreach ($selectedOptions as $selectedOption) {
                            $caddyAttribute ->addOption($selectedOption['option_name'], $selectedOption['option_value'], $selectedOption['option_price']);
                            $msgCommande .= '    ' . $selectedOption['option_name'] . ' : ' . $selectedOption['option_ttc_formated'] . "\n";
                        }
                        $handlers->h_oledrion_caddy_attributes->insert($caddyAttribute, true);
                    }
                } */
                if (!$res1) {
                    $ret['status']  = 0;
                    $ret['message'] = _OLEDRION_ERROR10;
                } else {
                    $ret['status']  = 1;
                    $ret['message'] = 'ok';
                    // Send mail
                    /* $msgCommande = '';
                    $msgCommande .= str_pad($product_id, 5, ' ') . ' ';
                    $msgCommande .= str_pad('', 10, ' ', STR_PAD_LEFT) . ' ';
                    $msgCommande .= str_pad($product->getVar('product_title'), 19, ' ', STR_PAD_LEFT) . ' ';
                    $msgCommande .= str_pad($product->getVar('product_qty'), 8, ' ', STR_PAD_LEFT) . ' ';
                    $msgCommande .= str_pad($oledrionCurrency->amountForDisplay($product_price), 15, ' ', STR_PAD_LEFT) . ' ';+
                    $msgCommande .= "\n";
                    $msgCommande .= "\n\n" . _OLEDRION_TOTAL . " " . $oledrionCurrency->mountForDisplay($cmd_total) . "\n";
                    $msg = array();
                    $msg['COMMANDE'] = $msgCommande;
                    $msg['NUM_COMMANDE'] = $commande->getVar('cmd_id');
                    $msg['NOM'] = $commande->getVar('cmd_lastname');
                    $msg['PRENOM'] = $commande->getVar('cmd_firstname');
                    $msg['ADRESSE'] = $commande->getVar('cmd_adress', 'n');
                    $msg['CP'] = $commande->getVar('cmd_zip');
                    $msg['VILLE'] = $commande->getVar('cmd_town');
                    $msg['PAYS'] = $countries[$commande->getVar('cmd_country')];
                    $msg['TELEPHONE'] = $commande->getVar('cmd_telephone');
                    $msg['EMAIL'] = $commande->getVar('cmd_email');
                    $msg['URL_BILL'] = OLEDRION_URL . 'invoice.php?id=' . $commande->getVar('cmd_id') . '&pass=' . $commande->getVar('cmd_password');
                    $msg['IP'] = Oledrion\Utility::IP();
                    if ($commande->getVar('cmd_bill') == 1) {
                        $msg['FACTURE'] = _YES;
                    } else {
                        $msg['FACTURE'] = _NO;
                    }
                    // Send mail to client
                    Oledrion\Utility::sendEmailFromTpl('command_client.tpl', $commande -> getVar('cmd_email'), sprintf(_OLEDRION_THANKYOU_CMD, $xoopsConfig['sitename']), $msg);
                    // Send mail to admin
                    Oledrion\Utility::sendEmailFromTpl('command_shop.tpl', Oledrion\Utility::getEmailsFromGroup(Oledrion\Utility::getModuleOption('grp_sold')), _OLEDRION_NEW_COMMAND, $msg);
                    */
                    // Send SMS
                    if (Oledrion\Utility::getModuleOption('sms_checkout')) {
                        $information['to']   = ltrim($commande->getVar('cmd_mobile'), 0);
                        $information['text'] = Oledrion\Utility::getModuleOption('sms_checkout_text');
                        $sms                 = \XoopsModules\Oledrion\Sms::sendSms($information);
                    }
                }
            } else {
                $ret['status']  = 0;
                $ret['message'] = _OLEDRION_ERROR10;
            }
        }
        $return = json_encode($ret);

        break;
}
echo $return;
