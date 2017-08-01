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
 * Gestion des options (attributs) produits dans les commandes
 */
require_once __DIR__ . '/classheader.php';

/**
 * Class Oledrion_caddy_attributes
 */
class Oledrion_caddy_attributes extends Oledrion_Object
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
        $this->initVar('ca_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('ca_cmd_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('ca_caddy_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('ca_attribute_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('ca_attribute_values', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('ca_attribute_names', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('ca_attribute_prices', XOBJ_DTYPE_TXTAREA, null, false);
    }

    /**
     * Retourne une option de l'attribut
     *
     * @param  string $valueToGet
     * @param  string $format
     * @return array
     * @since 2.3.2009.03.11
     */
    public function getOption($valueToGet, $format = 'e')
    {
        $names = array();
        if (xoops_trim($this->getVar($valueToGet, $format)) !== '') {
            $names = explode(OLEDRION_ATTRIBUTE_SEPARATOR, $this->getVar($valueToGet, $format));
        }

        return $names;
    }

    /**
     * Ajout d'une option à l'attribut (soit une option vide soit une option valorisée)
     *
     * @param  string $name
     * @param  string $value
     * @param  string $price
     * @return boolean
     * @since 2.3.2009.03.16
     */
    private function appendOption($name, $value, $price = '')
    {
        $names  = $values = $prices = array();
        $format = 'e';
        $names  = $this->getOption('ca_attribute_names', $format);
        $values = $this->getOption('ca_attribute_values', $format);
        if (OledrionUtility::getModuleOption('use_price')) {
            $prices = $this->getOption('ca_attribute_prices', $format);
        }
        $names[]  = $name;
        $values[] = $value;
        if (OledrionUtility::getModuleOption('use_price')) {
            $prices[] = $price;
        }
        $this->setVar('ca_attribute_names', implode(OLEDRION_ATTRIBUTE_SEPARATOR, $names));
        $this->setVar('ca_attribute_values', implode(OLEDRION_ATTRIBUTE_SEPARATOR, $values));
        if (OledrionUtility::getModuleOption('use_price')) {
            $this->setVar('ca_attribute_prices', implode(OLEDRION_ATTRIBUTE_SEPARATOR, $prices));
        }

        return true;
    }

    /**
     * Ajoute une nouvelle option à l'attribut
     *
     * @param  string $name
     * @param  string $value
     * @param  string $price
     * @return boolean
     * @since 2.3.2009.03.16
     */
    public function addOption($name, $value, $price = '')
    {
        return $this->appendOption($name, $value, $price);
    }

    /**
     * Retourne les informations formatées de l'attribut pour affichage dans la facture
     *
     * @param  oledrion_products $product Le produit concerné par l'attribut
     * @param  string            $format
     * @return array
     * @since 2.3.2009.03.23
     */
    public function renderForInvoice(oledrion_products $product, $format = 's')
    {
        $names = $prices = $ret = array();
        $names = $this->getOption('ca_attribute_names', $format);
        if (OledrionUtility::getModuleOption('use_price')) {
            $prices = $this->getOption('ca_attribute_prices', $format);
        }

        $oledrion_Currency = Oledrion_Currency::getInstance();
        $counter           = 0;
        foreach ($names as $name) {
            $price = 0;
            if (OledrionUtility::getModuleOption('use_price')) {
                if (isset($prices[$counter])) {
                    $price = OledrionUtility::getAmountWithVat((float)$prices[$counter], $product->getVar('product_vat_id'));
                    $price = $oledrion_Currency->amountForDisplay($price);
                }
            }
            $ret[] = array('ca_attribute_name' => $name, 'ca_attribute_price_formated' => $price);
            ++$counter;
        }

        return $ret;
    }
}

/**
 * Class OledrionOledrion_caddy_attributesHandler
 */
class OledrionOledrion_caddy_attributesHandler extends Oledrion_XoopsPersistableObjectHandler
{
    /**
     * OledrionOledrion_caddy_attributesHandler constructor.
     * @param XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    { //                                Table                   Classe                      Id
        parent::__construct($db, 'oledrion_caddy_attributes', 'oledrion_caddy_attributes', 'ca_id');
    }

    /**
     * Retourne le nombre d'attributs liés à un caddy
     *
     * @param  integer $ca_caddy_id L'ID du caddy concerné
     * @return integer
     * @since 2.3.2009.03.23
     */
    public function getAttributesCountForCaddy($ca_caddy_id)
    {
        return $this->getCount(new Criteria('ca_caddy_id', $ca_caddy_id, '='));
    }

    /**
     * Retourne la liste formatée des attributs liés à un caddy
     *
     * @param  integer           $ca_caddy_id L'identifiant de caddy
     * @param  Oledrion_products $product     Le produit concerné par le caddy
     * @return array
     * @since 2.3.2009.03.23
     */
    public function getFormatedAttributesForCaddy($ca_caddy_id, oledrion_products $product)
    {
        $handlers   = OledrionHandler::getInstance();
        $attributes = $ret = array();
        $attributes = $this->getObjects(new Criteria('ca_caddy_id', $ca_caddy_id, '='));
        if (count($attributes) == 0) {
            return $ret;
        }
        foreach ($attributes as $caddyAttribute) {
            $data      = array();
            $attribute = null;
            $attribute = $handlers->h_oledrion_attributes->get($caddyAttribute->getVar('ca_attribute_id'));
            if (is_object($attribute)) {
                $data = $attribute->toArray();
            }
            $data['attribute_options'] = $caddyAttribute->renderForInvoice($product);
            $ret[]                     = $data;
        }

        return $ret;
    }

    /**
     * Retourne le nombre de caddy attributs liés à un attribut
     *
     * @param  integer $ca_attribute_id L'Identifiant de l'attribut concerné
     * @return integer
     * @since 2.3.2009.03.23
     */
    public function getCaddyCountFromAttributeId($ca_attribute_id)
    {
        return $this->getCount(new Criteria('ca_attribute_id', $ca_attribute_id, '='));
    }

    /**
     * Retourne la liste des numéros de commandes "liés" à un attribut
     *
     * @param  integer $ca_attribute_id
     * @return array
     */
    public function getCommandIdFromAttribute($ca_attribute_id)
    {
        $ret       = $ordersIds = array();
        $criteria  = new Criteria('ca_attribute_id', $ca_attribute_id, '=');
        $ordersIds = $this->getObjects($criteria, false, true, 'ca_cmd_id', false);
        foreach ($ordersIds as $order) {
            $ret[] = $order->ca_cmd_id;
        }

        return $ret;
    }

    /**
     * Supprime les caddies associés à une commande
     *
     * @param $ca_cmd_id
     * @return bool
     * @internal param int $caddy_cmd_id
     */
    public function removeCartsFromOrderId($ca_cmd_id)
    {
        $ca_cmd_id = (int)$ca_cmd_id;

        return $this->deleteAll(new criteria('ca_cmd_id', $ca_cmd_id, '='));
    }
}
