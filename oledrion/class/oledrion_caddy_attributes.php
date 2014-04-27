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
 * @version     $Id: oledrion_caddy_attributes.php 12290 2014-02-07 11:05:17Z beckmi $
 */

/**
 * Gestion des options (attributs) produits dans les commandes
 */
require 'classheader.php';

class oledrion_caddy_attributes extends Oledrion_Object
{
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
        if (xoops_trim($this->getVar($valueToGet, $format)) != '') {
            $names = explode(OLEDRION_ATTRIBUTE_SEPARATOR, $this->getVar($valueToGet, $format));
        }

        return $names;
    }


    /**
     * Ajout d'une option à l'attribut (soit une option vide soit une option valorisée)
     *
     * @param  string  $name
     * @param  string  $value
     * @param  string  $price
     * @return boolean
     * @since 2.3.2009.03.16
     */
    private function appendOption($name, $value, $price = '')
    {
        $names = $values = $prices = array();
        $format = 'e';
        $names = $this->getOption('ca_attribute_names', $format);
        $values = $this->getOption('ca_attribute_values', $format);
        if (oledrion_utils::getModuleOption('use_price')) {
            $prices = $this->getOption('ca_attribute_prices', $format);
        }
        $names[] = $name;
        $values[] = $value;
        if (oledrion_utils::getModuleOption('use_price')) {
            $prices[] = $price;
        }
        $this->setVar('ca_attribute_names', implode(OLEDRION_ATTRIBUTE_SEPARATOR, $names));
        $this->setVar('ca_attribute_values', implode(OLEDRION_ATTRIBUTE_SEPARATOR, $values));
        if (oledrion_utils::getModuleOption('use_price')) {
            $this->setVar('ca_attribute_prices', implode(OLEDRION_ATTRIBUTE_SEPARATOR, $prices));
        }

        return true;
    }


    /**
     * Ajoute une nouvelle option à l'attribut
     *
     * @param  string  $name
     * @param  string  $value
     * @param  string  $price
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
     * @return array
     * @since 2.3.2009.03.23
     */
    public function renderForInvoice(oledrion_products $product, $format = 's')
    {
        $names = $prices = $ret = array();
        $names = $this->getOption('ca_attribute_names', $format);
        if (oledrion_utils::getModuleOption('use_price')) {
            $prices = $this->getOption('ca_attribute_prices', $format);
        }

        $oledrion_Currency = oledrion_Currency::getInstance();
        $counter = 0;
        foreach ($names as $name) {
            $price = 0;
            if (oledrion_utils::getModuleOption('use_price')) {
                if (isset($prices[$counter])) {
                    $price = oledrion_utils::getAmountWithVat(floatval($prices[$counter]), $product->getVar('product_vat_id'));
                    $price = $oledrion_Currency->amountForDisplay($price);
                }
            }
            $ret[] = array('ca_attribute_name' => $name, 'ca_attribute_price_formated' => $price);
            $counter++;
        }

        return $ret;
    }
}

class OledrionOledrion_caddy_attributesHandler extends Oledrion_XoopsPersistableObjectHandler
{
    public function __construct($db)
    { //							    Table			        Classe		    	        Id
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
     * @param  integer $ca_caddy_id L'identifiant de caddy
     * @param  object  $product     Le produit concerné par le caddy
     * @return array
     * @since 2.3.2009.03.23
     */
    public function getFormatedAttributesForCaddy($ca_caddy_id, oledrion_products $product)
    {
        $handlers = oledrion_handler::getInstance();
        $attributes = $ret = array();
        $attributes = $this->getObjects(new Criteria('ca_caddy_id', $ca_caddy_id, '='));
        if (count($attributes) == 0) {
            return $ret;
        }
        foreach ($attributes as $caddyAttribute) {
            $data = array();
            $attribute = null;
            $attribute = $handlers->h_oledrion_attributes->get($caddyAttribute->getVar('ca_attribute_id'));
            if (is_object($attribute)) {
                $data = $attribute->toArray();
            }
            $data['attribute_options'] = $caddyAttribute->renderForInvoice($product);
            $ret[] = $data;
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
        $ret = $ordersIds = array();
        $criteria = new Criteria('ca_attribute_id', $ca_attribute_id, '=');
        $ordersIds = $this->getObjects($criteria, false, true, 'ca_cmd_id', false);
        foreach ($ordersIds as $order) {
            $ret[] = $order->ca_cmd_id;
        }

        return $ret;
    }

    /**
     * Supprime les caddies associés à une commande
     *
     * @param  integer $caddy_cmd_id
     * @return boolean
     */
    public function removeCartsFromOrderId($ca_cmd_id)
    {
        $ca_cmd_id = intval($ca_cmd_id);

        return $this->deleteAll(new criteria('ca_cmd_id', $ca_cmd_id, '='));
    }
}
