<?php

namespace XoopsModules\Oledrion;

/**
 * ****************************************************************************
 * oledrion - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard (http://www.herve-thouzard.com/)
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Hervé Thouzard (http://www.herve-thouzard.com/)
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author          Hervé Thouzard (http://www.herve-thouzard.com/)
 *
 * Version :
 * ****************************************************************************
 */

use XoopsModules\Oledrion;

/**
 * Gestion des options (attributs) de produits
 *
 * @since 2.3.2009.03.10
 */


/*
// Les types d'option
define('OLEDRION_ATTRIBUTE_RADIO', 1);
define('OLEDRION_ATTRIBUTE_CHECKBOX', 2);
define('OLEDRION_ATTRIBUTE_SELECT', 3);

// Le séparateur de données utilisé en interne
define('OLEDRION_ATTRIBUTE_SEPARATOR', '|');
define('OLEDRION_EMPTY_OPTION', '');

// Le séparateur de ligne lorsque l'option est un bouton radio ou des cases à cocher
define('OLEDRION_ATTRIBUTE_CHECKBOX_WHITE_SPACE', 1);     // Séparateur de ligne = espace blanc
define('OLEDRION_ATTRIBUTE_CHECKBOX_NEW_LINE', 2);        // Séparateur de ligne = retour à la ligne

// Les options par défaut lorsque l'option est une liste déroulante
define('OLEDRION_ATTRIBUTE_SELECT_VISIBLE_OPTIONS', 1);    // Valeur par défaut, nombre d'options visibles
define('OLEDRION_ATTRIBUTE_SELECT_MULTIPLE', false);       // Valeur par défaut, sélecteur multiple ?
*/

/**
 * Class OledrionOledrion_attributesHandler
 */
class AttributesHandler extends OledrionPersistableObjectHandler
{
    /**
     * OledrionOledrion_attributesHandler constructor.
     * @param \XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        //                             Table               Classe                  Id
        parent::__construct($db, 'oledrion_attributes', Attributes::class, 'attribute_id');
    }

    /**
     * Supprime tous les attributs d'un produit
     *
     * @param int $attribute_product_id
     * @return bool Le résultat de la suppression
     * @since 2.3.2009.03.16
     */
    public function deleteProductAttributes($attribute_product_id)
    {
        $attribute_product_id = (int)$attribute_product_id;

        return $this->deleteAll(new \Criteria('attribute_product_id', $attribute_product_id, '='));
    }

    /**
     * Retourne le nombre total d'attributs d'un produit (qu'ils soient obligatoires ou pas)
     *
     * @param int $attribute_product_id
     * @return int
     * @since 2.3.2009.03.16
     */
    public function getProductAttributesCount($attribute_product_id)
    {
        return $this->getCount(new \Criteria('attribute_product_id', $attribute_product_id, '='));
    }

    /**
     * Retourne la liste des attributs d'un produit
     *
     * @param  int|array $product_id Le produit concerné
     * @param  null      $attributesIds
     * @return array
     */
    public function getProductsAttributesList($product_id, $attributesIds = null)
    {
        $ret      = [];
        $criteria = new \CriteriaCompo();
        if (is_array($product_id)) {
            $criteria->add(new \Criteria('attribute_product_id', '(' . implode(',', $product_id) . ')', 'IN'));
        } else {
            $criteria->add(new \Criteria('attribute_product_id', $product_id, '='));
        }
        if (is_array($attributesIds) && count($attributesIds) > 0) {
            $criteria->add(new \Criteria('attribute_id', '(' . implode(',', array_keys($attributesIds)) . ')', 'IN'));
        }
        $criteria->setSort('attribute_weight, attribute_title');    // L'ajout du titre dans le tri permet de trier même lorsque le poids n'est pas valorisé
        $ret = $this->getObjects($criteria);

        return $ret;
    }

    /**
     * Construction de la liste des attributs d'un produit
     *
     * @param  Products $product              Le produit concerné
     * @param int       $mandatoryFieldsCount Retourne le nombre d'options requises
     * @return array                    Les options construites en html
     * @since 2.3.2009.03.16
     */
    public function constructHtmlProductAttributes(Products $product, &$mandatoryFieldsCount = 0)
    {
        $attributes = $ret = [];
        $attributes = $this->getProductsAttributesList($product->getVar('product_id'));
        if (0 === count($attributes)) {
            return $ret;
        }
        foreach ($attributes as $attribute) {
            if ((bool)$attribute->getVar('attribute_mandatory')) {
                ++$mandatoryFieldsCount;
            }
            $ret[] = $attribute->render($product);
        }

        return $ret;
    }

    /**
     * Retourne le montant initial des options d'un produit
     *
     * @param  Products $product
     * @return float
     */
    public function getInitialOptionsPrice(Products $product)
    {
        $ret        = 0;
        $attributes = [];
        $attributes = $this->getProductsAttributesList($product->getVar('product_id'));
        foreach ($attributes as $attribute) {
            $ret += $attribute->getDefaultAttributePrice();
        }

        return $ret;
    }

    /**
     * Clonage d'un attribut
     *
     * @param  Attributes $originalAttribute
     * @return mixed               Soit le nouvel attribut si tout a bien marché sinon false
     * @internal param Oledrion_attributes $attribute L'attribute à cloner
     * @since    2.3.2009.03.16
     */
    public function cloneAttribute(Attributes $originalAttribute)
    {
        $newAttribute = $originalAttribute->xoopsClone();
        if (OLEDRION_DUPLICATED_PLACE === 'right') {
            $newAttribute->setVar('attribute_title', $originalAttribute->getVar('attribute_title') . ' ' . _AM_OLEDRION_DUPLICATED);
        } else {
            $newAttribute->setVar('attribute_title', _AM_OLEDRION_DUPLICATED . ' ' . $originalAttribute->getVar('attribute_title'));
        }
        $newAttribute->setVar('attribute_id', 0);
        $newAttribute->setNew();

        $res = $this->insert($newAttribute, true);
        if ($res) {
            return $newAttribute;
        }

        return false;
    }

    /**
     * Retourne la liste des produits utilisés dans la table (liste unique)
     *
     * @return array Value = id produits (uniques)
     * @since 2.3.2009.03.16
     */
    public function getDistinctsProductsIds()
    {
        $ret = [];
        $ret = $this->getDistincts('attribute_product_id');
        if (count($ret) > 0) {
            return array_values($ret);
        }

        return $ret;
    }

    /**
     * Suppression d'un attribut (et de ce qui y est rattaché)
     *
     * @param  Attributes $attribute
     * @return bool
     * @since 2.3.2009.03.17
     */
    public function deleteAttribute(Attributes $attribute)
    {
        return $this->delete($attribute, true);
        // TODO: Supprimer dans les attributs paniers
    }

    /**
     * Retourne le nombre d'attributs obligatoires d'un produit
     *
     * @param  Products $product
     * @return int
     * @since 2.3.2009.03.20
     */
    public function getProductMandatoryAttributesCount(Products $product)
    {
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('attribute_product_id', $product->getVar('attribute_product_id'), '='));
        $criteria->add(new \Criteria('attribute_mandatory', 1, '='));

        return $this->getCount($criteria);
    }

    /**
     * Retourne le nom des champs (représentant les attributs) obligatoires que l'on devrait trouver suite à une sélection de produit
     *
     * @param  Products $product
     * @return array             objets des type Oledrion_attributes
     */
    public function getProductMandatoryFieldsList(Products $product)
    {
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('attribute_product_id', $product->getVar('attribute_product_id'), '='));
        $criteria->add(new \Criteria('attribute_mandatory', 1, '='));

        return $this->getObjects($criteria);
    }

    /**
     * Calcul le prix HT des options sélectionnées pour un produit
     *
     * @param  array $choosenAttributes [clé] = attribute_id, [value] = array(valueId1, valueId2 ...)
     * @param int    $product_vat_id    L'ID de TVA du produit
     * @param  array $descriptions      Tableau valorisé par la méthode [clé] = Id attribut [valeur] = array('attribute_title', array('attribute_names', 'attribute_prices'))
     * @return float
     * @since 2.3.2009.03.21
     */
    public function getProductOptionsPrice($choosenAttributes, $product_vat_id, &$descriptions = null)
    {
        $db         = \XoopsDatabaseFactory::getDatabaseConnection();
        $vatHandler = new Oledrion\VatHandler($db);
        $vat_rate   = 0;
        static $vats = [];
        if (is_array($vats) && isset($vats[$product_vat_id])) {
            $vat_rate = $vats[$product_vat_id];
        } else {
            $vat = null;
            $vat = $vatHandler->get($product_vat_id);
            if (is_object($vat)) {
                $vats[$product_vat_id] = $vat_rate = $vat->getVar('vat_rate', 'n');
            }
        }
        $ret           = 0;
        $attributesIds = $attributes = [];
        if (!is_array($choosenAttributes) || 0 === count($choosenAttributes)) {
            return $ret;
        }
        $attributesIds = array_keys($choosenAttributes);

        $attributes = $this->getItemsFromIds($attributesIds);
        if (0 === count($attributes)) {
            return $ret;
        }
        $oledrionCurrency = Oledrion\Currency::getInstance();

        foreach ($choosenAttributes as $userAttributeId => $userAttributeValues) {
            if (isset($attributes[$userAttributeId])) {
                /** @var \XoopsModules\Oledrion\Attributes $attribute */
                $attribute           = $attributes[$userAttributeId];
                $dataForDescriptions = [];
                $optionDescription   = '';
                if (is_array($userAttributeValues) && count($userAttributeValues) > 0) {
                    foreach ($userAttributeValues as $option) {
                        $optionName            = Oledrion\Utility::getName($option);
                        $price                 = $attribute->getOptionPriceFromValue($optionName);
                        $optionDescription     = $attribute->getOptionNameFromValue($optionName);
                        $vatAmount             = Oledrion\Utility::getVAT($price, $vat_rate);
                        $ttc                   = $price + $vatAmount;
                        $vatAmountFormated     = $oledrionCurrency->amountForDisplay($vatAmount);
                        $htFormated            = $oledrionCurrency->amountForDisplay($price);
                        $ttcFormated           = $oledrionCurrency->amountForDisplay($ttc);
                        $dataForDescriptions[] = [
                            'option_name'              => $optionDescription,
                            'option_value'             => $optionName,
                            'option_price'             => $price,
                            'option_vat'               => $vatAmount,
                            'option_ttc'               => $ttc,
                            'option_price_ht_formated' => $htFormated,
                            'option_vat_formated'      => $vatAmountFormated,
                            'option_ttc_formated'      => $ttcFormated,
                        ];
                        $ret                   += $price;    // Total de toutes les options
                    }
                } else {
                    $optionName            = Oledrion\Utility::getName($userAttributeValues);
                    $price                 = $attribute->getOptionPriceFromValue($optionName);
                    $optionDescription     = $attribute->getOptionNameFromValue($optionName);
                    $vatAmount             = Oledrion\Utility::getVAT($price, $vat_rate);
                    $ttc                   = $price + $vatAmount;
                    $vatAmountFormated     = $oledrionCurrency->amountForDisplay($vatAmount);
                    $htFormated            = $oledrionCurrency->amountForDisplay($price);
                    $ttcFormated           = $oledrionCurrency->amountForDisplay($ttc);
                    $dataForDescriptions[] = [
                        'option_name'              => $optionDescription,
                        'option_value'             => $optionName,
                        'option_price'             => $price,
                        'option_vat'               => $vatAmount,
                        'option_ttc'               => $ttc,
                        'option_price_ht_formated' => $htFormated,
                        'option_vat_formated'      => $vatAmountFormated,
                        'option_ttc_formated'      => $ttcFormated,
                    ];
                    $ret                   += $price;    // Total de toutes les options
                }
                if (is_array($descriptions)) {
                    $descriptions[$attribute->getVar('attribute_id')] = [
                        'attribute_title'   => $attribute->getVar('attribute_title'),
                        'attribute_options' => $dataForDescriptions,
                    ];
                }
            }
        }

        return $ret;
    }
}
