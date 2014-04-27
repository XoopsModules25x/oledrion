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
 * @version     $Id: oledrion_products.php 12290 2014-02-07 11:05:17Z beckmi $
 */

/**
 * Gestion des produits mis en vente
 */
require 'classheader.php';

class oledrion_products extends Oledrion_Object
{
    public function __construct()
    {
        $this->initVar('product_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('product_cid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('product_title', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_vendor_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('product_sku', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_extraid', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_width', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_length', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_unitmeasure1', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_url', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_url2', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_url3', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_image_url', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_thumb_url', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_submitter', XOBJ_DTYPE_INT, null, false);
        $this->initVar('product_online', XOBJ_DTYPE_INT, null, false);
        $this->initVar('product_date', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_submitted', XOBJ_DTYPE_INT, null, false);
        $this->initVar('product_hits', XOBJ_DTYPE_INT, null, false);
        $this->initVar('product_rating', XOBJ_DTYPE_INT, null, false);
        $this->initVar('product_votes', XOBJ_DTYPE_INT, null, false);
        $this->initVar('product_comments', XOBJ_DTYPE_INT, null, false);
        $this->initVar('product_price', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_shipping_price', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_discount_price', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_stock', XOBJ_DTYPE_INT, null, false);
        $this->initVar('product_alert_stock', XOBJ_DTYPE_INT, null, false);
        $this->initVar('product_summary', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('product_description', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('product_attachment', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_weight', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_unitmeasure2', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_vat_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('product_download_url', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_recommended', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_metakeywords', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_metadescription', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_metatitle', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_delivery_time', XOBJ_DTYPE_INT, null, false);
        $this->initVar('product_ecotaxe', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_property1', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_property2', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_property3', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_property4', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_property5', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_property6', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_property7', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_property8', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_property9', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('product_property10', XOBJ_DTYPE_TXTBOX, null, false);

        // Pour autoriser le html
        $this->initVar('dohtml', XOBJ_DTYPE_INT, 1, false);
    }

    /**
     * Indique si le produit courant est visible (périmé, encore en stock, en ligne etc)
     *
     * @return boolean
     * @since 2.3.2009.03.17
     */
    public function isProductVisible()
    {
        $isAdmin = oledrion_utils::isAdmin();
        if ($this->getVar('product_online') == 0) {
            if (!$isAdmin) {
                return false;
            }
        }
        if (oledrion_utils::getModuleOption('show_unpublished') == 0 && $this->getVar('product_submitted') > time()) {
            if (!$isAdmin) {
                return false;
            }
        }
        if (oledrion_utils::getModuleOption('nostock_display') == 0 && $this->getVar('product_stock') == 0) {
            if (!$isAdmin) {
                return false;
            }
        }

        return true;
    }

    /**
     * Retourne l'URL de l'image du produit courant
     *
     * @return string L'URL
     */
    public function getPictureUrl()
    {
        if (xoops_trim($this->getVar('product_image_url')) != '') {
            return OLEDRION_PICTURES_URL . '/' . $this->getVar('product_image_url');
        } else {
            return '';
        }
    }

    /**
     * Retourne le chemin de l'image du produit courant
     *
     * @return string Le chemin
     */
    public function getPicturePath()
    {
        if (xoops_trim($this->getVar('product_image_url')) != '') {
            return OLEDRION_PICTURES_PATH . DIRECTORY_SEPARATOR . $this->getVar('product_image_url');
        } else {
            return '';
        }
    }

    /**
     * Retourne l'URL de la vignette du produit courant
     *
     * @return string L'URL
     */
    public function getThumbUrl()
    {
        if (xoops_trim($this->getVar('product_thumb_url')) != '') {
            return OLEDRION_PICTURES_URL . '/' . $this->getVar('product_thumb_url');
        } else {
            return '';
        }
    }

    /**
     * Retourne l'URL de la vignette du produit courant
     *
     * @return string L'URL
     */
    public function getThumbPath()
    {
        if (xoops_trim($this->getVar('product_thumb_url')) != '') {
            return OLEDRION_PICTURES_PATH . DIRECTORY_SEPARATOR . $this->getVar('product_thumb_url');
        } else {
            return '';
        }
    }

    /**
     * Indique si l'image du produit existe
     *
     * @return boolean Vrai si l'image existe sinon faux
     */
    public function pictureExists()
    {
        $return = false;
        if (xoops_trim($this->getVar('product_image_url')) != '' && file_exists(OLEDRION_PICTURES_PATH . DIRECTORY_SEPARATOR . $this->getVar('product_image_url'))) {
            $return = true;
        }

        return $return;
    }

    /**
     * Indique si la vignette du produit existe
     *
     * @return boolean Vrai si l'image existe sinon faux
     */
    public function thumbExists()
    {
        $return = false;
        if (xoops_trim($this->getVar('product_thumb_url')) != '' && file_exists(OLEDRION_PICTURES_PATH . DIRECTORY_SEPARATOR . $this->getVar('product_thumb_url'))) {
            $return = true;
        }

        return $return;
    }

    /**
     * Supprime l'image associée à un produit
     *
     * @return void
     */
    public function deletePicture()
    {
        if ($this->pictureExists()) {
            @unlink(OLEDRION_PICTURES_PATH . DIRECTORY_SEPARATOR . $this->getVar('product_image_url'));
        }
        $this->setVar('product_image_url', '');
    }

    /**
     * Indique si le fichier attaché à un produit existe
     *
     * @return boolean
     */
    public function attachmentExists()
    {
        $return = false;
        if (xoops_trim($this->getVar('product_attachment')) != '' && file_exists(OLEDRION_ATTACHED_FILES_PATH . DIRECTORY_SEPARATOR . $this->getVar('product_attachment'))) {
            $return = true;
        }

        return $return;
    }

    /**
     * Supprime le fichier attaché
     *
     * @return void
     */
    public function deleteAttachment()
    {
        if ($this->attachmentExists()) {
            @unlink(OLEDRION_ATTACHED_FILES_PATH . DIRECTORY_SEPARATOR . $this->getVar('product_attachment'));
        }
        $this->setVar('product_attachment', '');
    }

    /**
     * Supprime la miniature associée à un produit
     *
     * @return void
     */
    public function deleteThumb()
    {
        if ($this->thumbExists()) {
            @unlink(OLEDRION_PICTURES_PATH . DIRECTORY_SEPARATOR . $this->getVar('product_thumb_url'));
        }
        $this->setVar('product_thumb_url', '');
    }

    /**
     * Supprime les 2 images (raccourcis)
     *
     * @return void
     */
    public function deletePictures()
    {
        $this->deletePicture();
        $this->deleteThumb();
    }

    /**
     * Retourne le prix TTC du prix réduit du produit courant
     *
     * @return floatval Le montant TTC du prix réduit
     */
    public function getDiscountTTC()
    {
        return oledrion_utils::getAmountWithVat($this->getVar('product_discount_price', 'e'), $this->getVar('product_vat_id'));
    }

    /**
     * Retourne le montant TTC du prix normal du produit
     *
     * @return floatval
     */
    public function getTTC()
    {
        return oledrion_utils::getAmountWithVat($this->getVar('product_price', 'e'), $this->getVar('product_vat_id'));
    }

    /**
     * Indique si le produit courant est recommandé.
     *
     * @return boolean Vrai si le produit est recommandé sinon faux
     */
    public function isRecommended($withDescription = false)
    {
        if ($this->getVar('product_recommended') != '0000-00-00') {
            return $withDescription ? _YES : true;
        } else {
            return $withDescription ? _NO : false;
        }
    }

    /**
     * Place le produit courant dans l'état "recommandé"
     *
     * @return void
     */
    public function setRecommended()
    {
        $this->setVar('product_recommended', date("Y-m-d"));
    }

    /**
     * Enlève "l'attribut" recommandé d'un produit
     *
     * @return void
     */
    public function unsetRecommended()
    {
        $this->setVar('product_recommended', '0000-00-00');
    }

    /**
     * Retourne l'image qui indique si le produit est recommandé ou pas
     *
     * @return string La chaine à utiliser pour voir l'image
     */
    public function recommendedPicture()
    {
        if ($this->isRecommended()) {
            return "<img src=\"" . OLEDRION_IMAGES_URL . "heart.png\" alt=\"" . _OLEDRION_IS_RECOMMENDED . "\" />&nbsp;";
        } else {
            return "<img src=\"" . OLEDRION_IMAGES_URL . "blank.gif\" alt=\"\" />";
        }
    }

    /**
     * Retourne le lien du produit courant en tenant compte de l'URL Rewriting
     *
     * @param  integer $product_id    L'identifiant du produit
     * @param  string  $product_title Le titre du produit
     * @param  boolean $shortVersion  Indique si on veut la version avec l'url complpète ou la version avec juste la page et le paramètre
     * @return string
     */
    public function getLink($product_id = 0, $product_title = '', $shortVersion = false)
    {
        $url = '';
        if ($product_id == 0 && $product_title == '') {
            $product_id = $this->getVar('product_id');
            $product_title = $this->getVar('product_title', 'n');
        }
        if (oledrion_utils::getModuleOption('urlrewriting') == 1) { // On utilise l'url rewriting
            if (!$shortVersion) {
                $url = OLEDRION_URL . 'product-' . $product_id . oledrion_utils::makeSeoUrl($product_title) . '.html';
            } else {
                $url = 'product-' . $product_id . oledrion_utils::makeSeoUrl($product_title) . '.html';
            }
        } else { // Pas d'utilisation de l'url rewriting
            if (!$shortVersion) {
                $url = OLEDRION_URL . 'product.php?product_id=' . $product_id;
            } else {
                $url = 'product.php?product_id=' . $product_id;
            }
        }

        return $url;
    }

    /**
     * Retourne le nombre d'attributs du produit courant
     *
     * @return integer
     * @since 2.3.2009.03.19
     */
    public function productAttributesCount()
    {
        return oledrion_handler::getInstance()->h_oledrion_attributes->getProductAttributesCount($this->getVar('product_id'));
    }

    /**
     * Retourne le nombre d'attributs obligatoires d'un produit
     *
     * @note: La fonction est "doublée", elle se trouve içi et dans la classe des attributs pour des raisons de facilité (et de logique)
     *
     * @return integer
     * @since 2.3.2009.03.20
     */
    public function getProductMandatoryAttributesCount()
    {
        return oledrion_handler::getInstance()->h_oledrion_attributes->getProductMandatoryAttributesCount($this);
    }

    /**
     * Retourne la liste des attributs obligatoires du produit
     *
     * @return array
     * @since 2.3.2009.03.20
     */
    public function getProductMandatoryFieldsList()
    {
        return oledrion_handler::getInstance()->h_oledrion_attributes->getProductMandatoryFieldsList($this);
    }

    /**
     * Retourne la liste des attributs du produit courant
     *
     * @return array Objets de type oledrion_attributes
     * @since 2.3.2009.03.20
     */
    public function getProductsAttributesList($attributesIds = null)
    {
        return oledrion_handler::getInstance()->h_oledrion_attributes->getProductsAttributesList($this->getVar('product_id'), $attributesIds);
    }

    /**
     * Retourne le montant HT initial des options
     *
     * @return float
     */
    public function getInitialOptionsPrice()
    {
        return oledrion_handler::getInstance()->h_oledrion_attributes->getInitialOptionsPrice($this);
    }

    /**
     *
     */
    public function isNewProduct()
    {
        $time = time() - (60 * 60 * 24 * 10);
        if ($this->getVar('product_submitted') > $time) {
            return 1;
        } else {
            return 0;
        }
    }


    /**
     * Retourne les éléments du produits formatés pour affichage
     *
     * @param  string $format Le format à utiliser
     * @return array  Les informations formatées
     */
    public function toArray($format = 's')
    {
        $ret = array();
        $ret = parent::toArray($format);
        $oledrion_Currency = oledrion_Currency::getInstance();
        $ttc = $finalPriceTTC = $this->getTTC();
        $finalPriceHT = floatval($this->getVar('product_price'));

        $ret['product_ecotaxe_formated'] = $oledrion_Currency->amountForDisplay($this->getVar('product_ecotaxe'));

        $ret['product_price_formated'] = $oledrion_Currency->amountForDisplay($this->getVar('product_price', 'e'));
        $ret['product_shipping_price_formated'] = $oledrion_Currency->amountForDisplay($this->getVar('product_shipping_price', 'e'));
        $ret['product_discount_price_formated'] = $oledrion_Currency->amountForDisplay($this->getVar('product_discount_price', 'e'));
        $ret['product_price_ttc'] = $oledrion_Currency->amountForDisplay($ttc);
        $ret['product_price_ttc_long'] = $oledrion_Currency->amountForDisplay($ttc, 'l');

        if (intval($this->getVar('product_discount_price')) > 0) { //geeker
            $finalPriceTTC = $this->getDiscountTTC();
            $finalPriceHT = floatval($this->getVar('product_discount_price', 'e'));
            $ret['product_discount_price_ttc'] = $oledrion_Currency->amountForDisplay($this->getDiscountTTC());
            $ret['product_discount_price_ttc_long'] = $oledrion_Currency->amountForDisplay($this->getDiscountTTC(), 'l');
        } else {
            $ret['product_discount_price_ttc'] = '';
            $ret['product_discount_price_ttc_long'] = '';
        }
        // Les informations sur les attributs
        $attributesCount = $this->productAttributesCount();
        $ret['product_attributes_count'] = $attributesCount;
        if ($attributesCount > 0) {
            $optionsPrice = $this->getInitialOptionsPrice();
            $ret['product_price_formated'] = $oledrion_Currency->amountForDisplay(floatval($this->getVar('product_price', 'e')) + $optionsPrice);
            $ret['product_discount_price_formated'] = $oledrion_Currency->amountForDisplay(floatval($this->getVar('product_discount_price', 'e')) + $optionsPrice);
            $ret['product_price_ttc'] = $oledrion_Currency->amountForDisplay($ttc + $optionsPrice);
            $ret['product_price_ttc_long'] = $oledrion_Currency->amountForDisplay($ttc + $optionsPrice, 'l');
            if (intval($this->getVar('product_discount_price')) != 0) {
                $finalPriceTTC = $this->getDiscountTTC() + $optionsPrice;
                $finalPriceHT = floatval($this->getVar('product_discount_price', 'e')) + $optionsPrice;
                $ret['product_discount_price_ttc'] = $oledrion_Currency->amountForDisplay(floatval($this->getDiscountTTC()) + $optionsPrice);
                $ret['product_discount_price_ttc_long'] = $oledrion_Currency->amountForDisplay(floatval($this->getDiscountTTC()) + $optionsPrice, 'l');
            }
        }

        $ret['product_final_price_ht_formated_long'] = $oledrion_Currency->amountForDisplay($finalPriceHT, 'l');
        $ret['product_final_price_ttc'] = $finalPriceTTC;
        $ret['product_final_price_ttc_javascript'] = oledrion_utils::formatFloatForDB($finalPriceTTC);
        $ret['product_final_price_ttc_formated'] = $oledrion_Currency->amountForDisplay($finalPriceTTC);
        $ret['product_final_price_ttc_formated_long'] = $oledrion_Currency->amountForDisplay($finalPriceTTC, 'l');
        $ret['product_vat_amount_formated_long'] = $oledrion_Currency->amountForDisplay($finalPriceHT - $finalPriceTTC);

        $ret['product_tooltip'] = oledrion_utils::makeInfotips($this->getVar('product_description'));
        $ret['product_url_rewrited'] = $this->getLink();
        $ret['product_href_title'] = oledrion_utils::makeHrefTitle($this->getVar('product_title'));
        $ret['product_recommended'] = $this->isRecommended();
        $ret['product_recommended_picture'] = $this->recommendedPicture();

        $ret['product_image_full_url'] = $this->getPictureUrl();
        $ret['product_thumb_full_url'] = $this->getThumbUrl();
        $ret['product_image_full_path'] = $this->getPicturePath();
        $ret['product_thumb_full_path'] = $this->getThumbPath();

        $ret['product_shorten_summary'] = oledrion_utils::truncate_tagsafe($this->getVar('product_summary'), OLEDRION_SUMMARY_MAXLENGTH);
        $ret['product_shorten_description'] = oledrion_utils::truncate_tagsafe($this->getVar('product_description'), OLEDRION_SUMMARY_MAXLENGTH);
        $ret['product_new'] = $this->isNewProduct();

        return $ret;
    }
}


class OledrionOledrion_productsHandler extends Oledrion_XoopsPersistableObjectHandler
{
    public function __construct($db)
    { //							Table				Classe			 	Id			Libellé
        parent::__construct($db, 'oledrion_products', 'oledrion_products', 'product_id', 'product_title');
    }


    /**
     * Retourne la liste des x produits les plus vus par les visiteurs
     *
     * @param  integer $start    Début des données
     * @param  integer $limit    Nombre maximum d'enregistrements à renvoyer
     * @param  integer $category Identifiant de la catégorie (évenutellement)
     * @param  string  $sort     Champ sur lequel trier
     * @param  string  $order    Sens du tri
     * @return array   Tableau de produits (sous la forme d'objets)
     */
    public function getMostViewedProducts(oledrion_parameters $parameters)
    {
        $parameters = $parameters->extend(new oledrion_parameters(array('start' => 0, 'limit' => 0, 'category' => 0, 'sort' => 'product_hits', 'order' => 'DESC')));
        $data = array();
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('product_online', 1, '='));
        if (oledrion_utils::getModuleOption('show_unpublished') == 0) { // Ne pas afficher les produits qui ne sont pas publiés
            $criteria->add(new Criteria('product_submitted', time(), '<='));
        }
        if (oledrion_utils::getModuleOption('nostock_display') == 0) { // Se limiter aux seuls produits encore en stock
            $criteria->add(new Criteria('product_stock', 0, '>'));
        }
        if (is_array($parameters['category']) && count($parameters['category']) > 0) {
            $criteria->add(new Criteria('product_cid', '(' . implode(',', $parameters['category']) . ')', 'IN'));
        } elseif ($parameters['category'] != 0) {
            $criteria->add(new Criteria('product_cid', intval($parameters['category']), '='));
        }
        $criteria->add(new Criteria('product_hits', 0, '>'));

        $criteria->setLimit($parameters['limit']);
        $criteria->setStart($parameters['start']);
        $criteria->setSort($parameters['sort']);
        $criteria->setOrder($parameters['order']);
        $data = $this->getObjects($criteria, true);

        return $data;
    }

    /**
     * Retourne la liste des x produits les mieux notés par les visiteurs
     *
     * @param  integer $start    Début des données
     * @param  integer $limit    Nombre maximum d'enregistrements à renvoyer
     * @param  integer $category Identifiant de la catégorie (évenutellement)
     * @return array   Tableau de produits (sous la forme d'objets)
     */
    public function getBestRatedProducts(oledrion_parameters $parameters)
    {
        $parameters = $parameters->extend(new oledrion_parameters(array('start' => 0, 'limit' => 0, 'category' => 0, 'sort' => 'product_rating', 'order' => 'DESC')));
        $data = array();
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('product_online', 1, '='));
        $criteria->add(new Criteria('product_rating', 0, '>')); // Se limiter aux seuls produits qui ont été vraiment notés
        if (oledrion_utils::getModuleOption('show_unpublished') == 0) { // Ne pas afficher les produits qui ne sont pas publiés
            $criteria->add(new Criteria('product_submitted', time(), '<='));
        }
        if (oledrion_utils::getModuleOption('nostock_display') == 0) { // Se limiter aux seuls produits encore en stock
            $criteria->add(new Criteria('product_stock', 0, '>'));
        }
        if (is_array($parameters['category']) && count($parameters['category']) > 0) {
            $criteria->add(new Criteria('product_cid', '(' . implode(',', $parameters['category']) . ')', 'IN'));
        } elseif ($parameters['category'] != 0) {
            $criteria->add(new Criteria('product_cid', intval($parameters['category']), '='));
        }
        $criteria->setLimit($parameters['limit']);
        $criteria->setStart($parameters['start']);
        $criteria->setSort($parameters['sort']);
        $criteria->setOrder($parameters['order']);
        $data = $this->getObjects($criteria, true);

        return $data;
    }

    /**
     * Retourne la liste des x derniers produits recommandés
     *
     * @param  integer $start    Indice de départ
     * @param  integer $limit    Nombre maximum d'enregistrements à renvoyer
     * @param  integer $category Identifiant de la catégorie (évenutellement)
     * @return array   Tableau de produits (sous la forme d'objets)
     */
    public function getRecentRecommended(oledrion_parameters $parameters)
    {
        $parameters = $parameters->extend(new oledrion_parameters(array('start' => 0, 'limit' => 0, 'category' => 0, 'sort' => 'product_recommended', 'order' => 'DESC')));
        $data = array();
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('product_online', 1, '='));
        $criteria->add(new Criteria('product_recommended', '0000-00-00', '<>'));
        if (oledrion_utils::getModuleOption('show_unpublished') == 0) { // Ne pas afficher les produits qui ne sont pas publiés
            $criteria->add(new Criteria('product_submitted', time(), '<='));
        }
        if (oledrion_utils::getModuleOption('nostock_display') == 0) { // Se limiter aux seuls produits encore en stock
            $criteria->add(new Criteria('product_stock', 0, '>'));
        }
        if (is_array($parameters['category'])) {
            $criteria->add(new Criteria('product_cid', '(' . implode(',', $parameters['category']) . ')', 'IN'));
        } elseif ($parameters['category'] != 0) {
            $criteria->add(new Criteria('product_cid', intval($parameters['category']), '='));
        }
        $criteria->setLimit($parameters['limit']);
        $criteria->setStart($parameters['start']);
        $criteria->setSort($parameters['sort']);
        $criteria->setOrder($parameters['order']);
        $data = $this->getObjects($criteria, true);

        return $data;
    }

    /**
     * Retourne le nombre total de produits recommandés
     *
     * @return integer Le nombre total de produits recommandés
     */
    public function getRecommendedCount()
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('product_online', 1, '='));
        $criteria->add(new Criteria('product_recommended', '0000-00-00', '<>'));
        if (oledrion_utils::getModuleOption('show_unpublished') == 0) { // Ne pas afficher les produits qui ne sont pas publiés
            $criteria->add(new Criteria('product_submitted', time(), '<='));
        }
        if (oledrion_utils::getModuleOption('nostock_display') == 0) { // Se limiter aux seuls produits encore en stock
            $criteria->add(new Criteria('product_stock', 0, '>'));
        }

        return $this->getCount($criteria);
    }


    /**
     * Retourne la liste des x derniers produits parus toutes catégories confondues ou dans une catégorie spécifique
     *
     * @param  integer $start         Début des données
     * @param  integer $limit         Nombre maximum d'enregistrements à renvoyer
     * @param  mixed   $category      Identifiant de la catégorie (évenutellement) ou tableau d'ID ou rien du tout
     * @param  string  $sort          Champ(s) à utiliser pour le tri
     * @param  string  $order         Ordre de tri
     * @param  integer $excluded      Produit à exclure de la liste (éventuellement)
     * @param  boolean $thisMonthOnly Indique s'il ne faut prendre que les produits du mois
     * @return array   Tableau de produits (sous la forme d'objets)
     */
    public function getRecentProducts(oledrion_parameters $parameters)
    {
        $parameters = $parameters->extend(new oledrion_parameters(array('start' => 0, 'limit' => 0, 'category' => 0, 'sort' => 'product_submitted DESC, product_title', 'order' => '', 'excluded' => 0, 'thisMonthOnly' => false)));
        $data = array();
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('product_online', 1, '='));
        if (oledrion_utils::getModuleOption('show_unpublished') == 0) { // Ne pas afficher les produits qui ne sont pas publiés
            $criteria->add(new Criteria('product_submitted', time(), '<='));
        }
        if (oledrion_utils::getModuleOption('nostock_display') == 0) { // Se limiter aux seuls produits encore en stock
            $criteria->add(new Criteria('product_stock', 0, '>'));
        }
        if (is_array($parameters['category']) && count($parameters['category']) > 0) {
            $criteria->add(new Criteria('product_cid', '(' . implode(',', $parameters['category']) . ')', 'IN'));
        } elseif ($parameters['category'] > 0) {
            $criteria->add(new Criteria('product_cid', intval($parameters['category']), '='));
        }
        if ($parameters['excluded'] > 0) {
            $criteria->add(new Criteria('product_id', $parameters['excluded'], '<>'));
        }

        if ($parameters['thisMonthOnly']) {
            $criteria->add(oledrion_utils::getThisMonthCriteria());
        }

        $criteria->setLimit($parameters['limit']);
        $criteria->setStart($parameters['start']);
        $criteria->setSort($parameters['sort']);
        if (xoops_trim($parameters['order']) != '') {
            $criteria->setOrder($parameters['order']);
        }
        $data = $this->getObjects($criteria, true);

        return $data;
    }

    /**
     * Retourne le nombre total de produits récents (éventuellement dans une catégorie)
     *
     * @param  mixed   $category        Array ou Integer
     * @param  integer $excludedProduct ID d'un produit à exclure
     * @return integer
     */
    public function getRecentProductsCount($category = 0, $excludedProduct = 0)
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('product_online', 1, '='));
        if (oledrion_utils::getModuleOption('show_unpublished') == 0) { // Ne pas afficher les produits qui ne sont pas publiés
            $criteria->add(new Criteria('product_submitted', time(), '<='));
        }
        if (oledrion_utils::getModuleOption('nostock_display') == 0) { // Se limiter aux seuls produits encore en stock
            $criteria->add(new Criteria('product_stock', 0, '>'));
        }
        if (is_array($category)) {
            $criteria->add(new Criteria('product_cid', '(' . implode(',', $category) . ')', 'IN'));
        } elseif ($category > 0) {
            $criteria->add(new Criteria('product_cid', intval($category), '='));
        }
        if ($excludedProduct > 0) {
            $criteria->add(new Criteria('product_id', $excludedProduct, '<>'));
        }

        return $this->getCount($criteria);
    }

    /**
     * Retourne la liste des produits qui correspondent à des "critères" de manière à les utiliser pour la newsletter
     *
     * @param  integer $startingDate Date de soumission du produit à prendre comme borne inférieure
     * @param  integer $endingDate   Date de soumission du produit à prendre comme borne supérieure
     * @param  mixed   $category     Soit un tableau d'ID de catégories soit un ID unique de catégorie
     * @param  integer $start        Position de départ
     * @param  integer $limit        Nombre d'enregistrements à retourner
     * @return array   Des objects de type produits
     */
    public function getProductsForNewsletter(oledrion_parameters $parameters)
    {
        $parameters = $parameters->extend(new oledrion_parameters(array('startingDate' => 0, 'endingDate' => 0, 'category' => 0, 'start' => 0, 'limit' => 0)));
        $data = array();
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('product_online', 1, '='));
        $criteria->add(new Criteria('product_submitted', $parameters['startingDate'], '>='));
        $criteria->add(new Criteria('product_submitted', $parameters['endingDate'], '<='));
        if (oledrion_utils::getModuleOption('show_unpublished') == 0) { // Ne pas afficher les produits qui ne sont pas publiés
            $criteria->add(new Criteria('product_submitted', time(), '<='));
        }
        if (oledrion_utils::getModuleOption('nostock_display') == 0) { // Se limiter aux seuls produits encore en stock
            $criteria->add(new Criteria('product_stock', 0, '>'));
        }
        if (is_array($parameters['category'])) {
            $criteria->add(new Criteria('product_cid', '(' . implode(',', $parameters['category']) . ')', 'IN'));
        } elseif ($parameters['category'] > 0) {
            $criteria->add(new Criteria('product_cid', intval($parameters['category']), '='));
        }
        $criteria->setLimit($parameters['limit']);
        $criteria->setStart($parameters['start']);
        $criteria->setSort('product_title');
        $data = $this->getObjects($criteria, true);

        return $data;
    }

    /**
     * Retourne le nombre total de produits publiés dans la base en tenant compte des préférences du module
     *
     * @param  intefer $product_cid Catégorie du produit
     * @return integer Le nombre de produits publiés
     */
    public function getTotalPublishedProductsCount($product_cid = 0)
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('product_online', 1, '='));
        if (oledrion_utils::getModuleOption('show_unpublished') == 0) { // Ne pas afficher les produits qui ne sont pas publiés
            $criteria->add(new Criteria('product_submitted', time(), '<='));
        }
        if (oledrion_utils::getModuleOption('nostock_display') == 0) { // Se limiter aux seuls produits encore en stock
            $criteria->add(new Criteria('product_stock', 0, '>'));
        }
        if ($product_cid > 0) {
            $criteria->add(new Criteria('product_cid', intval($product_cid), '='));
        }

        return $this->getCount($criteria);
    }

    /**
     * Récupération de l'ID et du titre d'une série de produits répondants à un critère
     *
     * @param  object $criteria critère de sélection
     * @return array  Tableau dont la clé = ID produit et la valeur le titre du produit
     */
    public function getIdTitle($criteria)
    {
        global $myts;
        $ret = array();
        $sql = 'SELECT product_id, product_title FROM ' . $this->table;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ($criteria->getSort() != '') {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $ret[$myrow['product_id']] = $myts->htmlSpecialChars($myrow['product_title']);
        }

        return $ret;
    }

    /**
     * Mise à jour du compteur de lectures du produit
     *
     * @param  integer $product_id L'identifiant du produit dont il faut mettre à jour le compteur de lectures
     * @return boolean Le résultat de la mise à jour
     */
    public function addCounter($product_id)
    {
        $sql = 'UPDATE ' . $this->table . ' SET product_hits = product_hits + 1 WHERE product_id= ' . intval($product_id);
        // Note, pas de mise à jour du cache !
        return $this->db->queryF($sql);
    }


    /**
     * Mise à jour de la notation d'un produit
     *
     * @param  integer $product_id Identifiant du produit
     * @param  float   $rating     la notation
     * @param  integer $votes      Le nombre de votes du produit
     * @return boolean Le résultat de la mise à jour
     */
    public function updateRating($product_id, $rating, $votes)
    {
        $sql = 'UPDATE ' . $this->table . ' SET product_rating = ' . intval($rating) . ', product_votes = ' . intval($votes) . ' WHERE product_id = ' . intval($product_id);

        return $this->db->queryF($sql);
    }

    /**
     * Mise à jour du nombre de commentaires d'un produit
     *
     * @param integer $product_id    Identifiant du produit
     * @param integer $commentsCount Nombre total de commentaires
     */
    public function updateCommentsCount($product_id, $commentsCount)
    {
        $product = null;
        $product = $this->get($product_id);
        if (is_object($product)) {
            $criteria = new Criteria('product_id', $product_id, '=');
            $this->updateAll('product_comments', $commentsCount, $criteria, true);
        }
    }

    /**
     * Retourne x produits au hasard
     *
     * @param  integer $start         Début des données
     * @param  integer $limit         Nombre maximum d'enregistrements à renvoyer
     * @param  integer $category      Identifiant de la catégorie (évenutellement)
     * @param  string  $sort          Zone sur laquelle faire le tri
     * @param  string  $order         Ordre de tri
     * @param  boolean $thisMonthOnly Uniquement les produits du mois en cours ?
     * @return array   Tableau de produits (sous la forme d'objets)
     */
    public function getRandomProducts(oledrion_parameters $parameters)
    {
        $parameters = $parameters->extend(new oledrion_parameters(array('start' => 0, 'limit' => 0, 'category' => 0, 'sort' => 'RAND()', 'order' => 'ASC', 'thisMonthOnly' => false)));
        $data = array();
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('product_online', 1, '='));
        if (oledrion_utils::getModuleOption('show_unpublished') == 0) { // Ne pas afficher les produits qui ne sont pas publiés
            $criteria->add(new Criteria('product_submitted', time(), '<='));
        }
        if (oledrion_utils::getModuleOption('nostock_display') == 0) { // Se limiter aux seuls produits encore en stock
            $criteria->add(new Criteria('product_stock', 0, '>'));
        }
        if (is_array($parameters['category'])) {
            $criteria->add(new Criteria('product_cid', '(' . implode(',', $parameters['category']) . ')', 'IN'));
        } elseif ($parameters['category'] != 0) {
            $criteria->add(new Criteria('product_cid', intval($parameters['category']), '='));
        }

        if ($parameters['thisMonthOnly']) {
            $criteria->add(oledrion_utils::getThisMonthCriteria());
        }

        $criteria->setLimit($parameters['limit']);
        $criteria->setStart($parameters['start']);
        $criteria->setSort($parameters['sort']);
        $criteria->setOrder($parameters['order']);
        $data = $this->getObjects($criteria, true);

        return $data;
    }


    /**
     * Retourne x produits en promo
     *
     * @param  integer $start    Début des données
     * @param  integer $limit    Nombre maximum d'enregistrements à renvoyer
     * @param  integer $category Identifiant de la catégorie (évenutellement)
     * @return array   Tableau de produits (sous la forme d'objets)
     */
    public function getPromotionalProducts(oledrion_parameters $parameters)
    {
        $parameters = $parameters->extend(new oledrion_parameters(array('start' => 0, 'limit' => 0, 'category' => 0, 'sort' => 'product_title', 'order' => 'DESC')));
        $data = array();
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('product_online', 1, '='));
        if (oledrion_utils::getModuleOption('show_unpublished') == 0) { // Ne pas afficher les produits qui ne sont pas publiés
            $criteria->add(new Criteria('product_submitted', time(), '<='));
        }
        if (oledrion_utils::getModuleOption('nostock_display') == 0) { // Se limiter aux seuls produits encore en stock
            $criteria->add(new Criteria('product_stock', 0, '>'));
        }
        if (is_array($parameters['category'])) {
            $criteria->add(new Criteria('product_cid', '(' . implode(',', $parameters['category']) . ')', 'IN'));
        } elseif ($parameters['category'] != 0) {
            $criteria->add(new Criteria('product_cid', intval($parameters['category']), '='));
        }
        $criteria->add(new Criteria('product_discount_price', 0, '>'));
        $criteria->setLimit($parameters['limit']);
        $criteria->setStart($parameters['start']);
        $criteria->setSort($parameters['sort']);
        $criteria->setOrder($parameters['order']);
        $data = $this->getObjects($criteria, true);

        return $data;
    }


    /**
     * Retourne les produits dont les stocks sont bas
     *
     * @param  integer $start Début des données
     * @param  integer $limit Nombre maximum d'enregistrements à renvoyer
     * @return array   Tableau de produits (sous la forme d'objets)
     */
    public function getLowStocks($start = 0, $limit = 0)
    {
        $ret = array();
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE product_online = 1';
        if (oledrion_utils::getModuleOption('show_unpublished') == 0) { // Ne pas afficher les produits qui ne sont pas publiés
            $sql .= ' AND product_submitted <= ' . time();
        }
        $sql .= ' AND product_stock <= product_alert_stock ';
        $sql .= ' AND product_alert_stock > 0';
        $sql .= ' ORDER BY product_stock';
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }

        $ret = $this->convertResultSet($result, true, true);

        return $ret;
    }

    /**
     * Retourne le nombre de produits dont la quantité en stock est inférieure ou égale à la quantité d'alerte
     *
     * @return integer Le nombre de produits concernés
     */
    public function getLowStocksCount()
    {
        $ret = array();
        $sql = 'SELECT Count(*) as cpt FROM ' . $this->table . ' WHERE product_online = 1';
        if (oledrion_utils::getModuleOption('show_unpublished') == 0) { // Ne pas afficher les produits qui ne sont pas publiés
            $sql .= ' AND product_submitted <= ' . time();
        }
        $sql .= ' AND product_stock <= product_alert_stock ';
        $sql .= ' AND product_alert_stock > 0';
        $result = $this->db->query($sql);
        if (!$result) {
            return $ret;
        }
        $count = 0;
        list($count) = $this->db->fetchRow($result);

        return $count;
    }

    /**
     * Augmente les quantités en stock d'un produit
     *
     * @param object $product Objet produit
     * @param $quantity $quantity Quantité à rajouter
     */
    public function increaseStock($product, $quantity = 1)
    {
        $product->setVar('product_stock', $product->getVar('product_stock') + $quantity);
        $this->insert($product, true);

        return true;
    }

    /**
     * Diminue les quantités en stock d'un produit
     *
     * @param object $product Objet produit
     * @param $quantity $quantity Quantité à soustraire
     */
    public function decreaseStock(&$product, $quantity = 1)
    {
        if ($product->getVar('product_stock') - $quantity > 0) {
            $product->setVar('product_stock', $product->getVar('product_stock') - $quantity);
            $this->insert($product, true);
        } else {
            $product->setVar('product_stock', 0);
        }

        return true;
    }

    /**
     * Indique si la quantité d'alerte d'un produit est atteinte
     *
     * @param  object  $products L'objet produit concerné
     * @return boolean Vrai si la quantité d'alerte est atteinte, sinon faux
     */
    public function isAlertStockReached(&$product)
    {
        if ($product->getVar('product_stock') < $product->getVar('product_alert_stock')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Méthode chargée de vérifier si le stock d'alerte est atteint et si c'est le cas, d'envoyer une alerte
     *
     * @param  object  $product Produit dont il faut faire la vérification
     * @return boolean vrai si l'alerte à du être générée sinon faux
     */
    public function verifyLowStock(&$product)
    {
        if ($this->isAlertStockReached($product)) {
            $msg = array();
            $msg['PRODUCT_NAME'] = $product->getVar('product_title');
            $msg['ACTUAL_QUANTITY'] = $product->getVar('product_stock');
            $msg['ALERT_QUANTITY'] = $product->getVar('product_alert_stock');
            $msg['PUBLIC_URL'] = $product->getLink();
            $msg['ADMIN_URL'] = OLEDRION_URL . 'admin/index.php?op=editproduct&id=' . $product->getVar('product_id');
            oledrion_utils::sendEmailFromTpl('shop_lowstock.tpl', oledrion_utils::getEmailsFromGroup(oledrion_utils::getModuleOption('stock_alert_email')), _OLEDRION_STOCK_ALERT, $msg);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Retourne la plus petite date de création d'un produit ainsi que la "plus grande" date de création d'un produit
     *
     * @param  integer $minDate Date mini (parmètre de sortie)
     * @param  integer $maxDate Date maxi (paramètre de sortie)
     * @return boolean Vrai si on a pu récupérer ces valeurs, faux sinon
     */
    public function getMinMaxPublishedDate(&$minDate, &$maxDate)
    {
        $sql = 'SELECT Min(product_submitted) AS minDate, Max(product_submitted) as maxDate FROM ' . $this->table . ' WHERE product_online = 1 ';
        $result = $this->db->query($sql);
        if (!$result) {
            return false;
        }
        $myrow = $this->db->fetchArray($result);
        $minDate = $myrow['minDate'];
        $maxDate = $myrow['maxDate'];

        return true;
    }

    /**
     * Retourne des produits en fonction de leur IDs tout en tenant compte du fait qu'ils sont en ligne et payés !
     *
     * @param  array   $ids     Les identifiants des produits
     * @param  boolean $showAll Afficher les produits même s'ils ne sont plus en stock ?
     * @return array   Tableau d'objets de type oledrion_products, Clé = Id Produit
     */
    public function getProductsFromIDs($ids, $showAll = false)
    {
        $ret = array();
        if (is_array($ids)) {
            $criteria = new CriteriaCompo();
            if (oledrion_utils::getModuleOption('show_unpublished') == 0) { // Ne pas afficher les produits qui ne sont pas publiés
                $criteria->add(new Criteria('product_submitted', time(), '<='));
            }
            if (oledrion_utils::getModuleOption('nostock_display') == 0 && !$showAll) { // Se limiter aux seuls produits encore en stock
                $criteria->add(new Criteria('product_stock', 0, '>'));
            }
            $criteria->add(new Criteria('product_id', '(' . implode(',', $ids) . ')', 'IN'));
            $ret = $this->getObjects($criteria, true, true, '*', false);
        }

        return $ret;
    }


    /**
     * Retourne le nombre de produits d'une ou de plusieurs catégories
     *
     * @param  mixed   $cat_cid Soit un ID de catégorie unique soit un tableau d'ID de catégories
     * @return integer Le nombre de produits associés à cette catégorie
     */
    public function getCategoryProductsCount($cat_cid)
    {
        if (is_array($cat_cid)) {
            $lst_ids = implode(',', $cat_cid);
            $criteria = new Criteria('product_cid', '(' . $lst_ids . ')', 'IN');
        } else {
            $criteria = new Criteria('product_cid', $cat_cid, '=');
        }

        return $this->getCount($criteria);
    }

    /**
     * Retourne le nombre de produits associés à un vendeur
     *
     * @param  integer $product_vendor_id L'ID du vendeur
     * @return integer Le nombre de produits
     */
    public function getVendorProductsCount($product_vendor_id)
    {
        $criteria = new Criteria('product_vendor_id', $product_vendor_id, '=');

        return $this->getCount($criteria);
    }

    /**
     * Retourne le nombre de produits associés à une TVA
     *
     * @param  integer $product_vat_id L'identifiant de la TVA
     * @return integer Le nombre de produits
     */
    public function getVatProductsCount($product_vat_id)
    {
        $criteria = new Criteria('product_vat_id', $product_vat_id, '=');

        return $this->getCount($criteria);
    }

    /**
     * Clone d'un produit
     *
     * @param  object $originalProduct Le produit à cloner
     * @return mixed  Soit l'objet représentant le nouveau produit soit false
     */
    public function cloneProduct(oledrion_products $originalProduct)
    {
        global $h_oledrion_productsmanu, $h_oledrion_files, $h_oledrion_productsmanu, $h_oledrion_related, $oledrion_handlers;
        $newProduct = $originalProduct->xoopsClone();
        if (OLEDRION_DUPLICATED_PLACE == 'right') {
            $newProduct->setVar('product_title', $originalProduct->getvar('product_title') . ' ' . _AM_OLEDRION_DUPLICATED);
        } else {
            $newProduct->setVar('product_title', _AM_OLEDRION_DUPLICATED . ' ' . $originalProduct->getvar('product_title'));
        }
        $newProduct->setVar('product_id', 0);
        $newProduct->setNew();

        // Copie des 2 images
        if (xoops_trim($originalProduct->getVar('product_image_url')) != '') {
            $resCopy = oledrion_utils::duplicateFile(OLEDRION_PICTURES_PATH, $originalProduct->getVar('product_image_url'));
            if ($resCopy !== false) {
                $newProduct->setVar('product_image_url', $resCopy);
            }
        }
        if (xoops_trim($originalProduct->getVar('product_thumb_url')) != '') {
            $resCopy = oledrion_utils::duplicateFile(OLEDRION_PICTURES_PATH, $originalProduct->getVar('product_thumb_url'));
            if ($resCopy !== false) {
                $newProduct->setVar('product_thumb_url', $resCopy);
            }
        }

        // Copie du fichier attaché
        if (xoops_trim($originalProduct->getVar('product_attachment')) != '') {
            $resCopy = oledrion_utils::duplicateFile(OLEDRION_ATTACHED_FILES_PATH, $originalProduct->getVar('product_attachment'));
            if ($resCopy !== false) {
                $newProduct->setVar('product_attachment', $resCopy);
            }
        }

        $res = $this->insert($newProduct, true);
        if ($res) {
            $newProductId = $newProduct->getVar('product_id');
            // Copie des fichiers liés
            if ($h_oledrion_files->getProductFilesCount($originalProduct->product_id) > 0) {
                $attachedFiles = array();
                $attachedFiles = $h_oledrion_files->getProductFiles($originalProduct->product_id);
                if (count($attachedFiles) > 0) {
                    foreach ($attachedFiles as $oneFile) {
                        $newAttachedFile = $oneFile->xoopsClone();
                        $newAttachedFile->setVar('file_product_id', $newProductId);
                        $resCopy = oledrion_utils::duplicateFile(OLEDRION_ATTACHED_FILES_PATH, $oneFile->getVar('file_filename'));
                        if ($resCopy !== false) {
                            $newAttachedFile->setVar('file_filename', $resCopy);
                        }
                        $newAttachedFile->setNew();
                        $h_oledrion_files->insert($newAttachedFile, true);
                    }
                }
            }

            // Copie des fabricants
            $tblTmp = array();
            $criteria = new Criteria('pm_product_id', $originalProduct->getVar('product_id'), '=');
            $tblTmp = $h_oledrion_productsmanu->getObjects($criteria);
            foreach ($tblTmp as $productAuthor) {
                $newProductAuthor = $productAuthor->xoopsClone();
                $newProductAuthor->setVar('pm_product_id', $newProductId);
                $newProductAuthor->setVar('pm_id', 0);
                $newProductAuthor->setNew();
                $h_oledrion_productsmanu->insert($newProductAuthor, true);
            }

            // Copie des produits relatifs
            $tblTmp = array();
            $criteria = new Criteria('related_product_id', $originalProduct->getVar('product_id'), '=');
            $tblTmp = $h_oledrion_related->getObjects($criteria);
            foreach ($tblTmp as $related) {
                $newRelated = $related->xoopsClone();
                $newRelated->setVar('related_product_id', $newProductId);
                $newRelated->setVar('related_id', 0);
                $newRelated->setNew();
                $h_oledrion_related->insert($newRelated, true);
            }

            // Copie des attributs
            if ($oledrion_handlers->h_oledrion_attributes->getProductAttributesCount($originalProduct->product_id) > 0) {
                $criteria = new Criteria('attribute_product_id', $originalProduct->product_id, '=');
                $tblTmp = $oledrion_handlers->h_oledrion_attributes->getObjects($criteria);
                foreach ($tblTmp as $attribute) {
                    $newAttribute = $attribute->xoopsClone();
                    $newAttribute->setVar('attribute_product_id', $newProductId);
                    $newAttribute->setVar('attribute_id', 0);
                    $newAttribute->setNew();
                    $oledrion_handlers->h_oledrion_attributes->insert($newAttribute, true);
                }
            }

            return $newProduct;
        } else {
            return false;
        }
    }

    /**
     * Construit un sélecteur de produit(s) en fonction des paramètres et en tenant compte du nombre total de produits dans la base
     *
     * @todo: Remplacer les paramètres par un objet paramètre et/ou un tableau
     * @param  string  $caption     Le titre du sélecteur
     * @param  string  $name        Le nom du champ qui receuille les produits
     * @param  mixed   $value       La valeur sélectionnées
     * @param  integer $size        Le nombre d'éléments visibles dans le sélecteur
     * @param  boolean $multiple    Indique si c'est un sélecteur multiple ou pas
     * @param  array   $values      Les valeurs sélectionnées ou les valeurs qui font le sélecteur
     * @param  boolean $showAll     Indique s'il faut voir tous les produits ou pas (pas publiés et en stock)
     * @param  string  $sort        Zone de tri
     * @param  string  $order       Ordre de tri
     * @param  string  $formName    Nom du formulaire
     * @param  string  $description Description à rajouter à la zone
     * @param  mixed   $withNull    Option à rajouter en premier
     * @return object  Retourne soit un objet de type tray {@link XoopsFormElementTray} soit un select {@link XoopsFormSelect}
     */
    public function productSelector(oledrion_parameters $parameters)
    {
        $parameters = $parameters->extend(new oledrion_parameters(array('caption' => '', 'name' => '', 'value' => null, 'size' => 1, 'multiple' => false, 'values' => null, 'showAll' => true, 'sort' => 'product_title', 'order' => 'ASC', 'formName' => '', 'description' => '', 'withNull' => null)));
        static $jqueryIncluded = null;
        require_once XOOPS_ROOT_PATH . "/class/xoopsformloader.php";
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('product_id', 0, '<>'));
        if (!$parameters['showAll']) {
            if (oledrion_utils::getModuleOption('show_unpublished') == 0) { // Ne pas afficher les produits qui ne sont pas publiés
                $criteria->add(new Criteria('product_submitted', time(), '<='));
            }
            if (oledrion_utils::getModuleOption('nostock_display') == 0) { // Se limiter aux seuls produits encore en stock
                $criteria->add(new Criteria('product_stock', 0, '>'));
            }
        }
        $criteria->setSort($parameters['sort']);
        $criteria->setOrder($parameters['order']);
        $itemsCount = $this->getCount($criteria);
        if ($itemsCount > oledrion_utils::getModuleOption('max_products')) { // Il faut créer notre propre sélecteur
            if ($parameters['multiple']) {
                if ($jqueryIncluded == null) {
                    $jqueryIncluded = true;
                    global $xoTheme;
                    $xoTheme->addScript("browse.php?Frameworks/jquery/jquery.js");
                }
                oledrion_utils::callJavascriptFile('select/select.js', false, true);
                $productTray = new XoopsFormElementTray($parameters['caption'], '');
                $productsList = new XoopsFormSelect('', $parameters['name'], $parameters['values'], $parameters['size'], $parameters['multiple']);
                // Recherche des produits
                $selectedProducts = $this->getList(new Criteria('product_id', '(' . implode(',', $parameters['values']) . ')', 'IN'));
                $productsList->addOptionArray($selectedProducts); // Les valeurs sélectionnées
                $productTray->addElement($productsList);
                $removeButton = new XoopsFormButton('', 'removeProduct', _AM_OLEDRION_REMOVE_SELECTED, 'button');
                if (oledrion_utils::isX20()) {
                    $removeButton->setExtra(" onclick=\"removeOptionSelected('" . $parameters['name'] . "[]');\"");
                } else {
                    $removeButton->setExtra(" onclick=\"removeOptionSelected('" . $parameters['name'] . "');\"");
                }
                $productTray->addElement($removeButton);
                if (oledrion_utils::isX20()) {
                    $link = "<a href=\"javascript:openWithSelfMain('" . OLEDRION_ADMIN_URL . "productsselector.php?mutipleSelect=1&amp;callerName=" . $parameters['name'] . "[]', '', " . OLEDRION_MAX_PRODUCTS_POPUP_WIDTH . ", " . OLEDRION_MAX_PRODUCTS_POPUP_HEIGHT . ");\">" . _AM_OLEDRION_SELECT_OTHER_P . "</a>";
                } else {
                    $link = "<a href=\"javascript:openWithSelfMain('" . OLEDRION_ADMIN_URL . "productsselector.php?mutipleSelect=1&amp;callerName=" . $parameters['name'] . "', '', " . OLEDRION_MAX_PRODUCTS_POPUP_WIDTH . ", " . OLEDRION_MAX_PRODUCTS_POPUP_HEIGHT . ");\">" . _AM_OLEDRION_SELECT_OTHER_P . "</a>";
                }
                $linkLabel = new xoopsFormLabel('', '<br />' . $link);
                $productTray->addElement($linkLabel);
                echo "<script type=\"text/javascript\">\n";
                echo "jQuery().ready(function($) {\n";
                echo "$(\"#" . $parameters['formName'] . "\").submit( function() {\n";
                echo "	selectAll('" . $parameters['name'] . "', true);\n";
                echo "} );\n";
                echo "});\n";
                echo "</script>\n";
            } else { // Pas de sélection multiple
                $productTray = new XoopsFormElementTray($parameters['caption'], '');
                $productsList = new XoopsFormSelect('', $parameters['name'], $parameters['value'], $parameters['size'], $parameters['multiple']);
                // Recherche des produits
                if ($parameters['value'] > 0) {
                    $selectedProducts = $this->getList(new Criteria('product_id', $parameters['value'], '='));
                    if (!is_null($parameters['withNull'])) {
                        $selectedProducts[0] = $parameters['withNull'];
                        ksort($selectedProducts);
                    }
                    $productsList->addOptionArray($selectedProducts); // Les valeurs sélectionnées
                }
                $productTray->addElement($productsList);
                $link = "<a href=\"javascript:openWithSelfMain('" . OLEDRION_ADMIN_URL . "productsselector.php?mutipleSelect=0&amp;callerName=" . $parameters['name'] . "', '', " . OLEDRION_MAX_PRODUCTS_POPUP_WIDTH . ", " . OLEDRION_MAX_PRODUCTS_POPUP_HEIGHT . ");\">" . _AM_OLEDRION_SELECT_PRODUCT . "</a>";
                $linkLabel = new xoopsFormLabel('', '<br />' . $link);
                $productTray->addElement($linkLabel);
            }
        } else {
            $productTray = new XoopsFormSelect($parameters['caption'], $parameters['name'], $parameters['value'], $parameters['size'], $parameters['multiple']);
            $products = $this->getList($criteria);
            $productTray->addOptionArray($products);
        }

        if (trim($parameters['description']) != '') {
            $productTray->setDescription($parameters['description']);
        }

        return $productTray;
    }
}
