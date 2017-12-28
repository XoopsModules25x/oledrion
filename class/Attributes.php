<?php namespace Xoopsmodules\oledrion;
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
 * @package         oledrion
 * @author          Hervé Thouzard (http://www.herve-thouzard.com/)
 *
 * Version :
 * ****************************************************************************
 */
use Xoopsmodules\oledrion;
use Xoopsmodules\oledrion\Constants;

/**
 * Gestion des options (attributs) de produits
 *
 * @since 2.3.2009.03.10
 */
require_once __DIR__ . '/classheader.php';

//// Les types d'option
//define('OLEDRION_ATTRIBUTE_RADIO', 1);
//define('OLEDRION_ATTRIBUTE_CHECKBOX', 2);
//define('OLEDRION_ATTRIBUTE_SELECT', 3);
//
//// Le séparateur de données utilisé en interne
//define('OLEDRION_ATTRIBUTE_SEPARATOR', '|');
//define('OLEDRION_EMPTY_OPTION', '');
//
//// Le séparateur de ligne lorsque l'option est un bouton radio ou des cases à cocher
//define('OLEDRION_ATTRIBUTE_CHECKBOX_WHITE_SPACE', 1);     // Séparateur de ligne = espace blanc
//define('OLEDRION_ATTRIBUTE_CHECKBOX_NEW_LINE', 2);        // Séparateur de ligne = retour à la ligne
//
//// Les options par défaut lorsque l'option est une liste déroulante
//define('OLEDRION_ATTRIBUTE_SELECT_VISIBLE_OPTIONS', 1);    // Valeur par défaut, nombre d'options visibles
//define('OLEDRION_ATTRIBUTE_SELECT_MULTIPLE', false);       // Valeur par défaut, sélecteur multiple ?

/**
 * Class Oledrion_attributes
 */
class Attributes extends OledrionObject
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
        $this->initVar('attribute_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('attribute_weight', XOBJ_DTYPE_INT, null, false);
        $this->initVar('attribute_title', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('attribute_name', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('attribute_type', XOBJ_DTYPE_INT, null, false);
        $this->initVar('attribute_mandatory', XOBJ_DTYPE_INT, null, false);
        $this->initVar('attribute_names', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('attribute_values', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('attribute_prices', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('attribute_stocks', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('attribute_product_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('attribute_default_value', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('attribute_option1', XOBJ_DTYPE_INT, null, false);
        $this->initVar('attribute_option2', XOBJ_DTYPE_INT, null, false);
    }

    /**
     * Indique si l'attribut courant a une valeur par défaut
     *
     * @return boolean
     * @since 2.3.2009.03.20
     */
    public function hasDefaultValue()
    {
        if ('' !== xoops_trim($this->getVar('attribute_default_value'))) {
            return true;
        }

        return false;
    }

    /**
     * Retourne le nom du champs tel qu'il est construit dans le formulaire sur la fiche produit
     *
     * @return string
     */
    public function getAttributeNameInForm()
    {
        return $this->getVar('attribute_name') . '_' . $this->getVar('attribute_id');
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
     * Retourne le nombre d'options de l'attribut courant
     *
     * @return integer
     * @since 2.3.2009.03.12
     */
    public function getOptionsCount()
    {
        $names = explode(Constants::OLEDRION_ATTRIBUTE_SEPARATOR, $this->getVar('attribute_names', 's'));

        return count($names);
    }

    /**
     * Ajout d'une option à l'attribut (soit une option vide soit une option valorisée)
     *
     * @param  string $name
     * @param  string $value
     * @param  string $price
     * @param  string $stock
     * @return boolean
     * @since 2.3.2009.03.16
     */
    private function appendOption($name, $value, $price = '', $stock = '')
    {
        $names  = $values = $prices = $stocks = [];
        $format = 'e';
        $names  = $this->getOption('attribute_names', $format);
        $values = $this->getOption('attribute_values', $format);
        if (oledrion\Utility::getModuleOption('use_price')) {
            $prices = $this->getOption('attribute_prices', $format);
        }
        if (oledrion\Utility::getModuleOption('attributes_stocks')) {
            $stocks = $this->getOption('attribute_stocks', $format);
        }
        $names[]  = $name;
        $values[] = $value;
        if (oledrion\Utility::getModuleOption('use_price')) {
            $prices[] = $price;
        }
        if (oledrion\Utility::getModuleOption('attributes_stocks')) {
            $stocks[] = $stock;
        }
        $this->setVar('attribute_names', implode(Constants::OLEDRION_ATTRIBUTE_SEPARATOR, $names));
        $this->setVar('attribute_values', implode(Constants::OLEDRION_ATTRIBUTE_SEPARATOR, $values));
        if (oledrion\Utility::getModuleOption('use_price')) {
            $this->setVar('attribute_prices', implode(Constants::OLEDRION_ATTRIBUTE_SEPARATOR, $prices));
        }
        if (oledrion\Utility::getModuleOption('attributes_stocks')) {
            $this->setVar('attribute_stocks', implode(Constants::OLEDRION_ATTRIBUTE_SEPARATOR, $stocks));
        }

        return true;
    }

    /**
     * Ajoute une option vide à la fin (avec des valeurs par défaut)
     *
     * @return boolean
     * @since 2.3.2009.03.12
     */
    public function addEmptyOption()
    {
        return $this->appendOption(_AM_OLEDRION_ATTRIBUTE_DEF_VALUE, _AM_OLEDRION_ATTRIBUTE_DEF_VALUE, _AM_OLEDRION_ATTRIBUTE_DEF_AMOUNT, _AM_OLEDRION_ATTRIBUTE_DEF_AMOUNT);
    }

    /**
     * Ajoute une nouvelle option à l'attribut
     *
     * @param  string $name
     * @param  string $value
     * @param  string $price
     * @param  string $stock
     * @return boolean
     * @since 2.3.2009.03.16
     */
    public function addOption($name, $value, $price = '', $stock = '')
    {
        return $this->appendOption($name, $value, $price, $stock);
    }

    /**
     * Réinitialisation des options de l'attribut
     *
     * @return boolean True
     * @since 2.3.2009.03.10
     */
    public function resetOptions()
    {
        $this->setVar('attribute_names', Constants::OLEDRION_EMPTY_OPTION);
        $this->setVar('attribute_values', Constants::OLEDRION_EMPTY_OPTION);
        if (oledrion\Utility::getModuleOption('use_price')) {
            $this->setVar('attribute_prices', Constants::OLEDRION_EMPTY_OPTION);
        }
        if (oledrion\Utility::getModuleOption('attributes_stocks')) {
            $this->setVar('attribute_stocks', Constants::OLEDRION_EMPTY_OPTION);
        }

        return true;
    }

    /**
     * Renseigne une option
     *
     * @param  integer $optionNumber (de 0 à N)
     * @param  string  $name         Valeur pour name
     * @param  string  $value        Valeur pour value
     * @param  string  $price        Valeur pour prix
     * @param  string  $stock        Valeur pour stock
     * @return boolean True si la mise à jour s'est faite sinon false
     * @since 2.3.2009.03.10
     */
    public function setOptionValue($optionNumber, $name, $value, $price = '', $stock = '')
    {
        $optionNumber = (int)$optionNumber;
        if ($optionNumber < 0 || $optionNumber > $this->getOptionsCount()) {
            return false;
        }
        $names  = $values = $prices = $stocks = [];
        $format = 'e';
        $names  = $this->getOption('attribute_names', $format);
        $values = $this->getOption('attribute_values', $format);
        if (oledrion\Utility::getModuleOption('use_price')) {
            $prices = $this->getOption('attribute_prices', $format);
        }
        if (oledrion\Utility::getModuleOption('attributes_stocks')) {
            $stocks = $this->getOption('attribute_stocks', $format);
        }
        if (isset($names[$optionNumber])) {
            $names[$optionNumber] = $name;
        }
        if (isset($values[$optionNumber])) {
            $values[$optionNumber] = $value;
        }
        if (oledrion\Utility::getModuleOption('use_price')) {
            if (isset($prices[$optionNumber])) {
                $prices[$optionNumber] = $price;
            }
        }
        if (oledrion\Utility::getModuleOption('attributes_stocks')) {
            if (isset($stocks[$optionNumber])) {
                $stocks[$optionNumber] = $stock;
            }
        }
        $this->setVar('attribute_names', implode(Constants::OLEDRION_ATTRIBUTE_SEPARATOR, $names));
        $this->setVar('attribute_values', implode(Constants::OLEDRION_ATTRIBUTE_SEPARATOR, $values));
        if (oledrion\Utility::getModuleOption('use_price')) {
            $this->setVar('attribute_prices', implode(Constants::OLEDRION_ATTRIBUTE_SEPARATOR, $prices));
        }
        if (oledrion\Utility::getModuleOption('attributes_stocks')) {
            $this->setVar('attribute_stocks', implode(Constants::OLEDRION_ATTRIBUTE_SEPARATOR, $stocks));
        }

        return true;
    }

    /**
     * Echange deux contenus dans un tableau
     *
     * @param  array   $array
     * @param  integer $from
     * @param  integer $to
     * @return void
     * @since 2.3.2009.03.10
     */
    private function swapValues(&$array, $from, $to)
    {
        $tempValue    = $array[$to];
        $array[$to]   = $array[$from];
        $array[$from] = $tempValue;
    }

    /**
     * Fonction interne chargée du déplacement d'une option soit vers le haut soit vers le bas
     *
     * @param  integer $optionNumber
     * @param  integer $upDown 1=Up, 2=Down
     * @return boolean
     * @since 2.3.2009.03.10
     */
    private function moveOption($optionNumber, $upDown)
    {
        $optionNumber = (int)$optionNumber;
        if (1 == $upDown) {    // Up
            $newPosition = $optionNumber - 1;
        } else {    // Down
            $newPosition = $optionNumber + 1;
        }
        if ($optionNumber < 0 || $optionNumber > $this->getOptionsCount()) {
            return false;
        }
        $format = 'e';
        $names  = $this->getOption('attribute_names', $format);
        $values = $this->getOption('attribute_values', $format);
        if (oledrion\Utility::getModuleOption('use_price')) {
            $prices = $this->getOption('attribute_prices', $format);
        }
        if (oledrion\Utility::getModuleOption('attributes_stocks')) {
            $stocks = $this->getOption('attribute_stocks', $format);
        }
        if (isset($names[$optionNumber])) {
            $this->swapValues($names, $optionNumber, $newPosition);
        }
        if (isset($values[$optionNumber])) {
            $this->swapValues($values, $optionNumber, $newPosition);
        }
        if (oledrion\Utility::getModuleOption('use_price')) {
            if (isset($prices[$optionNumber])) {
                $this->swapValues($prices, $optionNumber, $newPosition);
            }
        }
        if (oledrion\Utility::getModuleOption('attributes_stocks')) {
            if (isset($stocks[$optionNumber])) {
                $this->swapValues($stocks, $optionNumber, $newPosition);
            }
        }
        $this->setVar('attribute_names', implode(Constants::OLEDRION_ATTRIBUTE_SEPARATOR, $names));
        $this->setVar('attribute_values', implode(Constants::OLEDRION_ATTRIBUTE_SEPARATOR, $values));
        if (oledrion\Utility::getModuleOption('use_price')) {
            $this->setVar('attribute_prices', implode(Constants::OLEDRION_ATTRIBUTE_SEPARATOR, $prices));
        }
        if (oledrion\Utility::getModuleOption('attributes_stocks')) {
            $this->setVar('attribute_stocks', implode(Constants::OLEDRION_ATTRIBUTE_SEPARATOR, $stocks));
        }

        return true;
    }

    /**
     * Déplace une option vers le haut
     *
     * @param  integer $optionNumber
     * @return boolean
     * @since 2.3.2009.03.10
     */
    public function moveOptionUp($optionNumber)
    {
        return $this->moveOption($optionNumber, 1);
    }

    /**
     * Déplace une option vers le bas
     *
     * @param  integer $optionNumber
     * @return boolean
     * @since 2.3.2009.03.10
     */
    public function moveOptionDown($optionNumber)
    {
        return $this->moveOption($optionNumber, 2);
    }

    /**
     * Supprime une option de l'attribut
     *
     * @param  integer $optionNumber (de 0 à n)
     * @return boolean false si l'indice est hors borne sinon true
     * @since 2.3.2009.03.12
     */
    public function deleteOption($optionNumber)
    {
        $optionNumber = (int)$optionNumber;
        if ($optionNumber < 0 || $optionNumber > $this->getOptionsCount()) {
            return false;
        }
        $format = 'e';
        $names  = $this->getOption('attribute_names', $format);
        $values = $this->getOption('attribute_values', $format);
        if (oledrion\Utility::getModuleOption('use_price')) {
            $prices = $this->getOption('attribute_prices', $format);
        }
        if (oledrion\Utility::getModuleOption('attributes_stocks')) {
            $stocks = $this->getOption('attribute_stocks', $format);
        }
        if (isset($names[$optionNumber])) {
            unset($names[$optionNumber]);
        }
        if (isset($values[$optionNumber])) {
            unset($values[$optionNumber]);
        }
        if (oledrion\Utility::getModuleOption('use_price')) {
            if (isset($prices[$optionNumber])) {
                unset($prices[$optionNumber]);
            }
        }
        if (oledrion\Utility::getModuleOption('attributes_stocks')) {
            if (isset($stocks[$optionNumber])) {
                unset($stocks[$optionNumber]);
            }
        }
        $this->setVar('attribute_names', implode(Constants::OLEDRION_ATTRIBUTE_SEPARATOR, $names));
        $this->setVar('attribute_values', implode(Constants::OLEDRION_ATTRIBUTE_SEPARATOR, $values));
        if (oledrion\Utility::getModuleOption('use_price')) {
            $this->setVar('attribute_prices', implode(Constants::OLEDRION_ATTRIBUTE_SEPARATOR, $prices));
        }
        if (oledrion\Utility::getModuleOption('attributes_stocks')) {
            $this->setVar('attribute_stocks', implode(Constants::OLEDRION_ATTRIBUTE_SEPARATOR, $stocks));
        }

        return true;
    }

    /**
     * Retourne le prix de l'attribut par défaut
     *
     * @param  string $format
     * @return float
     * @since 2.3.2009.03.19
     */
    public function getDefaultAttributePrice($format = 'e')
    {
        $defaultValue = xoops_trim($this->getVar('attribute_default_value', 'e'));
        if ('' !== $defaultValue) {    // Il y a une option par défaut donc un prix
            $values  = $this->getOption('attribute_values', $format);
            $prices  = $this->getOption('attribute_prices', $format);
            $counter = 0;
            if (count($values) > 0) {
                foreach ($values as $value) {
                    if (xoops_trim($value) == $defaultValue) {
                        if (isset($prices[$counter])) {
                            return (float)$prices[$counter];
                        } else {
                            return 0;
                        }
                    }
                    ++$counter;
                }
            }
        }

        return 0;
    }

    /**
     * Retourne la valeur par défaut de l'attribut courant
     *
     * @param  string $format
     * @return string
     * @since 2.3.2009.03.20
     */
    public function getAttributeDefaultValue($format = 'e')
    {
        return xoops_trim($this->getVar('attribute_default_value', $format));
    }

    /**
     * Retourne une liste combinée des options de l'attribut
     *
     * @param  string            $format             Format dans lequel renvoyer les données
     * @param  boolean           $withFormatedPrices Faut il retourner les prix formatés ?
     * @param  Products $product            Le produit de travail
     * @return array
     * @since 2.3.2009.03.11
     */
    public function getAttributeOptions($format = 's', $withFormatedPrices = false, Products $product = null)
    {
        $ret     = [];
        $counter = 0;
        if (null !== $product) {
            $vat_id = $product->getVar('product_vat_id');
        }
        $names  = $this->getOption('attribute_names', $format);
        $values = $this->getOption('attribute_values', $format);
        if (oledrion\Utility::getModuleOption('use_price')) {
            $prices = $this->getOption('attribute_prices', $format);
        }
        if (oledrion\Utility::getModuleOption('attributes_stocks')) {
            $stocks = $this->getOption('attribute_stocks', $format);
        }

        if ($withFormatedPrices) {
            $oledrion_Currency = oledrion\Currency::getInstance();
        }
        if (count($names) > 0) {
            foreach ($names as $key => $name) {
                $price = $stock = 0;
                if (oledrion\Utility::getModuleOption('use_price')) {
                    $price = $prices[$key];
                    if ($withFormatedPrices) {
                        $priceFormated    = $oledrion_Currency->amountForDisplay($price);
                        $priceTtc         = oledrion\Utility::getAmountWithVat($price, $vat_id);
                        $priceTtcFormated = $oledrion_Currency->amountForDisplay($priceTtc);
                        $vat              = $priceTtc - $price;
                        $vatFormated      = $oledrion_Currency->amountForDisplay($vat);
                    }
                }
                if (oledrion\Utility::getModuleOption('attributes_stocks')) {
                    $stock = $stocks[$key];
                }
                if (!$withFormatedPrices) {
                    $ret[] = ['name' => $name, 'value' => $values[$key], 'price' => $price, 'stock' => $stock];
                } else {
                    $ret[] = [
                        'name'             => $name,
                        'value'            => $values[$key],
                        'price'            => $price,
                        'priceFormated'    => $priceFormated,
                        'priceTTC'         => $priceTtc,
                        'priceTTCFormated' => $priceTtcFormated,
                        'vat'              => $vat,
                        'vatFormated'      => $vatFormated,
                        'counter'          => $counter,
                        'stock'            => $stock
                    ];
                }
                ++$counter;
            }
        }

        return $ret;
    }

    /**
     * Retourne la liste des types d'attributs
     *
     * @return array
     * @since 2.3.2009.03.10
     */
    public function getTypesList()
    {
        $attributeTypeName = [
            Constants::OLEDRION_ATTRIBUTE_RADIO    => _AM_OLEDRION_TYPE_RADIO,
            Constants::OLEDRION_ATTRIBUTE_CHECKBOX => _AM_OLEDRION_TYPE_CHECKBOX,
            Constants::OLEDRION_ATTRIBUTE_SELECT   => _AM_OLEDRION_TYPE_LIST
        ];

        return $attributeTypeName;
    }

    /**
     * Retourne le type de l'attribut courant (son libellé)
     *
     * @return mixed Soit le type de l'attribut soit null;
     * @since 2.3.2009.03.10
     */
    public function getTypeName()
    {
        $attributeTypeName = $this->getTypesList();
        if (isset($attributeTypeName[$this->getVar('attribute_type')])) {
            return $attributeTypeName[$this->getVar('attribute_type')];
        } else {
            return null;
        }
    }

    /**
     * Retourne le prix d'une option en fonction de son nom
     *
     * @param  string $optionName
     * @return float
     */
    public function getOptionPriceFromValue($optionName)
    {
        $ret     = 0;
        $format  = 's';
        $counter = 0;
        $values  = $this->getOption('attribute_values', $format);
        $prices  = $this->getOption('attribute_prices', $format);
        foreach ($values as $value) {
            if (xoops_trim($value) == $optionName) {
                if (isset($prices[$counter])) {
                    return (float)$prices[$counter];
                }
            }
            ++$counter;
        }

        return $ret;
    }

    /**
     * Retourne le libellé d'une option en fonction de son nom
     *
     * @param  string $optionName
     * @return string
     */
    public function getOptionNameFromValue($optionName)
    {
        $ret     = '';
        $format  = 's';
        $counter = 0;
        $values  = $this->getOption('attribute_values', $format);
        $names   = $this->getOption('attribute_names', $format);
        foreach ($values as $value) {
            if (xoops_trim($value) == $optionName) {
                if (isset($names[$counter])) {
                    return $names[$counter];
                }
            }
            ++$counter;
        }

        return $ret;
    }

    /**
     * Création du code html de l'attribut
     *
     * On utilise le contenu de templates html (réalisés en Smarty) pour créer le contenu de l'attribut
     * Templates utilisés (selon le type d'attribut) :
     *      oledrion_attribute_checkbox.html
     *      oledrion_attribute_radio.html
     *      oledrion_attribute_select.html
     *
     * @param  Products $product Le produit de "travail"
     * @return string                   Le contenu html
     * @since 2.3.2009.03.16
     */
    public function render(Products $product)
    {
        require_once XOOPS_ROOT_PATH . '/class/template.php';
        $template = new \XoopsTpl();
        $db                = \XoopsDatabaseFactory::getDatabaseConnection();
        $caddyHandler = new oledrion\CaddyHandler($db);

        $options      = [];
        $ret          = $templateName = '';
        $elementName  = $this->getVar('attribute_name', 'e');
        $elementTitle = $this->getVar('attribute_title');
        $option1      = $this->getVar('attribute_option1');
        $option2      = $this->getVar('attribute_option2');

//        $handlers = HandlerManager::getInstance();
        $isInCart =$caddyHandler->isInCart($product->getVar('product_id'));
        if (false === $isInCart) {    // Le produit n'est pas dans le panier, on prend la valeur par défaut
            $defaultValue = [$this->getVar('attribute_default_value')];
        } else {    // Le produit est dans le panier, on va chercher les options qui sont sélectionnées
            $Productattributes = $caddyHandler->getProductAttributesFromCart($product->getVar('product_id'));
            if (isset($Productattributes[$this->getVar('attribute_id')])) {
                $defaultValue = $Productattributes[$this->getVar('attribute_id')];
            } else {    // On prend la valeur par défaut
                $defaultValue = [$this->getVar('attribute_default_value')];
                if (Constants::OLEDRION_ATTRIBUTE_RADIO == $this->attribute_type) {    // Pour les boutons radio, il ne peut y avoir qu'un élément de sélectionné
                    $defaultValue = $this->getVar('attribute_default_value');
                }
            }
            if (!is_array($defaultValue)) {
                $defaultValue = [$defaultValue];
            }
            $newDefaultValue = [];
            foreach ($defaultValue as $oneValue) {
                $newDefaultValue[] = oledrion\Utility::getName($oneValue);
            }
            $defaultValue = $newDefaultValue;
        }
        $options = $this->getAttributeOptions('s', true, $product);

        // Les valeurs communes
        $template->assign('options', $options);
        $template->assign('attributeTitle', $elementTitle);
        $template->assign('defaultValue', $defaultValue);
        $template->assign('attributeName', $this->getVar('attribute_title'));
        $template->assign('name', $elementName);
        $template->assign('attribute_id', $this->getVar('attribute_id'));
        $template->assign('attribute_mandatory', (bool)$this->getVar('attribute_mandatory'));

        switch ($this->getVar('attribute_type')) {
            case Constants::OLEDRION_ATTRIBUTE_SELECT:        // Liste déroulante
                $templateName = 'oledrion_attribute_select.tpl';
                $multiple     = '';
                if (1 == $option2) {    // La sélection multiple est autorisée
                    $multiple = "multiple='multiple' ";
                }
                $template->assign('multiple', $multiple);
                $template->assign('size', $option1);

                break;

            case Constants::OLEDRION_ATTRIBUTE_CHECKBOX:      // Cases à cocher
                $templateName = 'oledrion_attribute_checkbox.tpl';
                $delimiter    = '';
                $delimiter = '<br>';
                if (Constants::OLEDRION_ATTRIBUTE_CHECKBOX_WHITE_SPACE == $option1) {
                    $delimiter = ' ';
                }
                $template->assign('delimiter', $delimiter);
                break;

            case Constants::OLEDRION_ATTRIBUTE_RADIO:         // Boutons radio
                $templateName = 'oledrion_attribute_radio.tpl';
                $delimiter    = '';
                $delimiter = '<br>';
                if (Constants::OLEDRION_ATTRIBUTE_CHECKBOX_WHITE_SPACE == $option1) {
                    $delimiter = ' ';
                }
                $template->assign('delimiter', $delimiter);
                break;
        }
        if ('' !== $templateName) {
            $ret = $template->fetch('db:' . $templateName);
        }

        return $ret;
    }
}

