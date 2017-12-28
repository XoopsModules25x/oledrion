<?php namespace Xoopsmodules\oledrion;

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

use Xoopsmodules\oledrion;

/**
 * Gestion des options (attributs) produits dans les commandes
 */
require_once __DIR__ . '/classheader.php';

/**
 * Class Caddy_attributes
 */
class CaddyAttributes extends OledrionObject
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
        $names = [];
        if ('' !== xoops_trim($this->getVar($valueToGet, $format))) {
            $names = explode(Constants::OLEDRION_ATTRIBUTE_SEPARATOR, $this->getVar($valueToGet, $format));
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
        $names  = $values = $prices = [];
        $format = 'e';
        $names  = $this->getOption('ca_attribute_names', $format);
        $values = $this->getOption('ca_attribute_values', $format);
        if (oledrion\Utility::getModuleOption('use_price')) {
            $prices = $this->getOption('ca_attribute_prices', $format);
        }
        $names[]  = $name;
        $values[] = $value;
        if (oledrion\Utility::getModuleOption('use_price')) {
            $prices[] = $price;
        }
        $this->setVar('ca_attribute_names', implode(Constants::OLEDRION_ATTRIBUTE_SEPARATOR, $names));
        $this->setVar('ca_attribute_values', implode(Constants::OLEDRION_ATTRIBUTE_SEPARATOR, $values));
        if (oledrion\Utility::getModuleOption('use_price')) {
            $this->setVar('ca_attribute_prices', implode(Constants::OLEDRION_ATTRIBUTE_SEPARATOR, $prices));
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
     * @param  Products $product Le produit concerné par l'attribut
     * @param  string            $format
     * @return array
     * @since 2.3.2009.03.23
     */
    public function renderForInvoice(Products $product, $format = 's')
    {
        $names = $prices = $ret = [];
        $names = $this->getOption('ca_attribute_names', $format);
        if (oledrion\Utility::getModuleOption('use_price')) {
            $prices = $this->getOption('ca_attribute_prices', $format);
        }

        $oledrion_Currency = oledrion\Currency::getInstance();
        $counter           = 0;
        foreach ($names as $name) {
            $price = 0;
            if (oledrion\Utility::getModuleOption('use_price')) {
                if (isset($prices[$counter])) {
                    $price = oledrion\Utility::getAmountWithVat((float)$prices[$counter], $product->getVar('product_vat_id'));
                    $price = $oledrion_Currency->amountForDisplay($price);
                }
            }
            $ret[] = ['ca_attribute_name' => $name, 'ca_attribute_price_formated' => $price];
            ++$counter;
        }

        return $ret;
    }
}
