<?php namespace XoopsModules\Oledrion;

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
 * Gestion des produits mis en vente
 */

use XoopsModules\Oledrion;

// require_once __DIR__ . '/classheader.php';

/**
 * Class Products
 */
class Products extends OledrionObject
{
    /**
     * constructor
     *
     * normally, this is called from child classes only
     *
     * @access public
     */
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
        // B.R. Start
        $this->initVar('skip_packing', XOBJ_DTYPE_INT, null, false);
        $this->initVar('skip_location', XOBJ_DTYPE_INT, null, false);
        $this->initVar('skip_delivery', XOBJ_DTYPE_INT, null, false);
        // B.R. End
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
        $isAdmin = Oledrion\Utility::isAdmin();
        if (0 == $this->getVar('product_online')) {
            if (!$isAdmin) {
                return false;
            }
        }
        if (0 == Oledrion\Utility::getModuleOption('show_unpublished') && $this->getVar('product_submitted') > time()) {
            if (!$isAdmin) {
                return false;
            }
        }
        if (0 == Oledrion\Utility::getModuleOption('nostock_display') && 0 == $this->getVar('product_stock')) {
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
        if ('' !== xoops_trim($this->getVar('product_image_url'))) {
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
        if ('' !== xoops_trim($this->getVar('product_image_url'))) {
            return OLEDRION_PICTURES_PATH . '/' . $this->getVar('product_image_url');
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
        if ('' !== xoops_trim($this->getVar('product_thumb_url'))) {
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
        if ('' !== xoops_trim($this->getVar('product_thumb_url'))) {
            return OLEDRION_PICTURES_PATH . '/' . $this->getVar('product_thumb_url');
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
        if ('' !== xoops_trim($this->getVar('product_image_url'))
            && file_exists(OLEDRION_PICTURES_PATH . '/' . $this->getVar('product_image_url'))) {
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
        if ('' !== xoops_trim($this->getVar('product_thumb_url'))
            && file_exists(OLEDRION_PICTURES_PATH . '/' . $this->getVar('product_thumb_url'))) {
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
            @unlink(OLEDRION_PICTURES_PATH . '/' . $this->getVar('product_image_url'));
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
        if ('' !== xoops_trim($this->getVar('product_attachment'))
            && file_exists(OLEDRION_ATTACHED_FILES_PATH . '/' . $this->getVar('product_attachment'))) {
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
            @unlink(OLEDRION_ATTACHED_FILES_PATH . '/' . $this->getVar('product_attachment'));
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
            @unlink(OLEDRION_PICTURES_PATH . '/' . $this->getVar('product_thumb_url'));
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
     * @return float Le montant TTC du prix réduit
     */
    public function getDiscountTTC()
    {
        return Oledrion\Utility::getAmountWithVat($this->getVar('product_discount_price', 'e'), $this->getVar('product_vat_id'));
    }

    /**
     * Retourne le montant TTC du prix normal du produit
     *
     * @return float
     */
    public function getTTC()
    {
        return Oledrion\Utility::getAmountWithVat($this->getVar('product_price', 'e'), $this->getVar('product_vat_id'));
    }

    /**
     * Indique si le produit courant est recommandé.
     *
     * @param  bool $withDescription
     * @return bool Vrai si le produit est recommandé sinon faux
     */
    public function isRecommended($withDescription = false)
    {
        if ('0000-00-00' != $this->getVar('product_recommended')) {
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
        $this->setVar('product_recommended', date('Y-m-d'));
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
            return '<img src="' . OLEDRION_IMAGES_URL . 'heart.png" alt="' . _OLEDRION_IS_RECOMMENDED . '">&nbsp;';
        } else {
            return '<img src="' . OLEDRION_IMAGES_URL . 'blank.gif" alt="">';
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
        if (0 == $product_id && '' === $product_title) {
            $product_id    = $this->getVar('product_id');
            $product_title = $this->getVar('product_title', 'n');
            // B.R. New
            $product_url = $this->getVar('product_url3', 'n');
            // End New
        }
        // B.R. New
        if (empty($product_url)) {
            // End New
        if (1 == Oledrion\Utility::getModuleOption('urlrewriting')) { // On utilise l'url rewriting
            if (!$shortVersion) {
                $url = OLEDRION_URL . 'product-' . $product_id . Oledrion\Utility::makeSeoUrl($product_title) . '.html';
            } else {
                $url = 'product-' . $product_id . Oledrion\Utility::makeSeoUrl($product_title) . '.html';
            }
        } else { // Pas d'utilisation de l'url rewriting
            if (!$shortVersion) {
                $url = OLEDRION_URL . 'product.php?product_id=' . $product_id;
            } else {
                $url = 'product.php?product_id=' . $product_id;
            }
        }
            // B.R. New
        } else {
            $url = '../' . $product_url;
        }
        // B.R. End New
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
        $db                = \XoopsDatabaseFactory::getDatabaseConnection();
        $attributesHandler = new Oledrion\AttributesHandler($db);
        return $attributesHandler->getProductAttributesCount($this->getVar('product_id'));
    }

    /**
     * Retourne le nombre d'attributs obligatoires d'un produit
     *
     * @note  : La fonction est "doublée", elle se trouve içi et dans la classe des attributs pour des raisons de facilité (et de logique)
     *
     * @return integer
     * @since 2.3.2009.03.20
     */
    public function getProductMandatoryAttributesCount()
    {
        $db                = \XoopsDatabaseFactory::getDatabaseConnection();
        $attributesHandler = new Oledrion\AttributesHandler($db);
        return $attributesHandler->getProductMandatoryAttributesCount($this);
    }

    /**
     * Retourne la liste des attributs obligatoires du produit
     *
     * @return array
     * @since 2.3.2009.03.20
     */
    public function getProductMandatoryFieldsList()
    {
        $db                = \XoopsDatabaseFactory::getDatabaseConnection();
        $attributesHandler = new Oledrion\AttributesHandler($db);
        return $attributesHandler->getProductMandatoryFieldsList($this);
    }

    /**
     * Retourne la liste des attributs du produit courant
     *
     * @param  null $attributesIds
     * @return array Objets de type Oledrion_attributes
     * @since 2.3.2009.03.20
     */
    public function getProductsAttributesList($attributesIds = null)
    {
        $db                = \XoopsDatabaseFactory::getDatabaseConnection();
        $attributesHandler = new Oledrion\AttributesHandler($db);
        return $attributesHandler->getProductsAttributesList($this->getVar('product_id'), $attributesIds);
    }

    /**
     * Retourne le montant HT initial des options
     *
     * @return float
     */
    public function getInitialOptionsPrice()
    {
        $db                = \XoopsDatabaseFactory::getDatabaseConnection();
        $attributesHandler = new Oledrion\AttributesHandler($db);
        return $attributesHandler->getInitialOptionsPrice($this);
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
        $ret               = [];
        $ret               = parent::toArray($format);
        $oledrionCurrency = Oledrion\Currency::getInstance();
        $ttc               = $finalPriceTTC = $this->getTTC();
        $finalPriceHT      = (float)$this->getVar('product_price');

        $ret['product_ecotaxe_formated'] = $oledrionCurrency->amountForDisplay($this->getVar('product_ecotaxe'));

        $ret['product_price_formated']          = $oledrionCurrency->amountForDisplay($this->getVar('product_price', 'e'));
        $ret['product_shipping_price_formated'] = $oledrionCurrency->amountForDisplay($this->getVar('product_shipping_price', 'e'));
        $ret['product_discount_price_formated'] = $oledrionCurrency->amountForDisplay($this->getVar('product_discount_price', 'e'));
        $ret['product_price_ttc']               = $oledrionCurrency->amountForDisplay($ttc);
        $ret['product_price_ttc_long']          = $oledrionCurrency->amountForDisplay($ttc, 'l');

        if ((int)$this->getVar('product_discount_price') > 0) { //geeker
            $finalPriceTTC                          = $this->getDiscountTTC();
            $finalPriceHT                           = (float)$this->getVar('product_discount_price', 'e');
            $ret['product_discount_price_ttc']      = $oledrionCurrency->amountForDisplay($this->getDiscountTTC());
            $ret['product_discount_price_ttc_long'] = $oledrionCurrency->amountForDisplay($this->getDiscountTTC(), 'l');
        } else {
            $ret['product_discount_price_ttc']      = '';
            $ret['product_discount_price_ttc_long'] = '';
        }
        // Les informations sur les attributs
        $attributesCount                 = $this->productAttributesCount();
        $ret['product_attributes_count'] = $attributesCount;
        if ($attributesCount > 0) {
            $optionsPrice                           = $this->getInitialOptionsPrice();
            $ret['product_price_formated']          = $oledrionCurrency->amountForDisplay((float)$this->getVar('product_price', 'e') + $optionsPrice);
            $ret['product_discount_price_formated'] = $oledrionCurrency->amountForDisplay((float)$this->getVar('product_discount_price', 'e') + $optionsPrice);
            $ret['product_price_ttc']               = $oledrionCurrency->amountForDisplay($ttc + $optionsPrice);
            $ret['product_price_ttc_long']          = $oledrionCurrency->amountForDisplay($ttc + $optionsPrice, 'l');
            if (0 != (int)$this->getVar('product_discount_price')) {
                $finalPriceTTC                          = $this->getDiscountTTC() + $optionsPrice;
                $finalPriceHT                           = (float)$this->getVar('product_discount_price', 'e') + $optionsPrice;
                $ret['product_discount_price_ttc']      = $oledrionCurrency->amountForDisplay((float)$this->getDiscountTTC() + $optionsPrice);
                $ret['product_discount_price_ttc_long'] = $oledrionCurrency->amountForDisplay((float)$this->getDiscountTTC() + $optionsPrice, 'l');
            }
        }

        $ret['product_final_price_ht_formated_long']  = $oledrionCurrency->amountForDisplay($finalPriceHT, 'l');
        $ret['product_final_price_ttc']               = $finalPriceTTC;
        $ret['product_final_price_ttc_javascript']    = Oledrion\Utility::formatFloatForDB($finalPriceTTC);
        $ret['product_final_price_ttc_formated']      = $oledrionCurrency->amountForDisplay($finalPriceTTC);
        $ret['product_final_price_ttc_formated_long'] = $oledrionCurrency->amountForDisplay($finalPriceTTC, 'l');
        $ret['product_vat_amount_formated_long']      = $oledrionCurrency->amountForDisplay($finalPriceHT - $finalPriceTTC);

        $ret['product_tooltip']             = Oledrion\Utility::makeInfotips($this->getVar('product_description'));
        $ret['product_url_rewrited']        = $this->getLink();
        $ret['product_href_title']          = Oledrion\Utility::makeHrefTitle($this->getVar('product_title'));
        $ret['product_recommended']         = $this->isRecommended();
        $ret['product_recommended_picture'] = $this->recommendedPicture();

        $ret['product_image_full_url']  = $this->getPictureUrl();
        $ret['product_thumb_full_url']  = $this->getThumbUrl();
        $ret['product_image_full_path'] = $this->getPicturePath();
        $ret['product_thumb_full_path'] = $this->getThumbPath();

        $ret['product_shorten_summary']     = Oledrion\Utility::truncate_tagsafe($this->getVar('product_summary'), OLEDRION_SUMMARY_MAXLENGTH);
        $ret['product_shorten_description'] = Oledrion\Utility::truncate_tagsafe($this->getVar('product_description'), OLEDRION_SUMMARY_MAXLENGTH);
        $ret['product_new']                 = $this->isNewProduct();

        return $ret;
    }
}
