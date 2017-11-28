<?php
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

/**
 * Gestion des options (attributs) de produits
 *
 * @since 2.3.2009.03.10
 */
require_once __DIR__ . '/classheader.php';

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

/**
 * Class Oledrion_attributes
 */
class Oledrion_attributes extends OledrionObject
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
            $names = explode(OLEDRION_ATTRIBUTE_SEPARATOR, $this->getVar($valueToGet, $format));
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
        $names = explode(OLEDRION_ATTRIBUTE_SEPARATOR, $this->getVar('attribute_names', 's'));

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
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('use_price')) {
            $prices = $this->getOption('attribute_prices', $format);
        }
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('attributes_stocks')) {
            $stocks = $this->getOption('attribute_stocks', $format);
        }
        $names[]  = $name;
        $values[] = $value;
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('use_price')) {
            $prices[] = $price;
        }
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('attributes_stocks')) {
            $stocks[] = $stock;
        }
        $this->setVar('attribute_names', implode(OLEDRION_ATTRIBUTE_SEPARATOR, $names));
        $this->setVar('attribute_values', implode(OLEDRION_ATTRIBUTE_SEPARATOR, $values));
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('use_price')) {
            $this->setVar('attribute_prices', implode(OLEDRION_ATTRIBUTE_SEPARATOR, $prices));
        }
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('attributes_stocks')) {
            $this->setVar('attribute_stocks', implode(OLEDRION_ATTRIBUTE_SEPARATOR, $stocks));
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
        $this->setVar('attribute_names', OLEDRION_EMPTY_OPTION);
        $this->setVar('attribute_values', OLEDRION_EMPTY_OPTION);
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('use_price')) {
            $this->setVar('attribute_prices', OLEDRION_EMPTY_OPTION);
        }
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('attributes_stocks')) {
            $this->setVar('attribute_stocks', OLEDRION_EMPTY_OPTION);
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
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('use_price')) {
            $prices = $this->getOption('attribute_prices', $format);
        }
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('attributes_stocks')) {
            $stocks = $this->getOption('attribute_stocks', $format);
        }
        if (isset($names[$optionNumber])) {
            $names[$optionNumber] = $name;
        }
        if (isset($values[$optionNumber])) {
            $values[$optionNumber] = $value;
        }
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('use_price')) {
            if (isset($prices[$optionNumber])) {
                $prices[$optionNumber] = $price;
            }
        }
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('attributes_stocks')) {
            if (isset($stocks[$optionNumber])) {
                $stocks[$optionNumber] = $stock;
            }
        }
        $this->setVar('attribute_names', implode(OLEDRION_ATTRIBUTE_SEPARATOR, $names));
        $this->setVar('attribute_values', implode(OLEDRION_ATTRIBUTE_SEPARATOR, $values));
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('use_price')) {
            $this->setVar('attribute_prices', implode(OLEDRION_ATTRIBUTE_SEPARATOR, $prices));
        }
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('attributes_stocks')) {
            $this->setVar('attribute_stocks', implode(OLEDRION_ATTRIBUTE_SEPARATOR, $stocks));
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
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('use_price')) {
            $prices = $this->getOption('attribute_prices', $format);
        }
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('attributes_stocks')) {
            $stocks = $this->getOption('attribute_stocks', $format);
        }
        if (isset($names[$optionNumber])) {
            $this->swapValues($names, $optionNumber, $newPosition);
        }
        if (isset($values[$optionNumber])) {
            $this->swapValues($values, $optionNumber, $newPosition);
        }
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('use_price')) {
            if (isset($prices[$optionNumber])) {
                $this->swapValues($prices, $optionNumber, $newPosition);
            }
        }
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('attributes_stocks')) {
            if (isset($stocks[$optionNumber])) {
                $this->swapValues($stocks, $optionNumber, $newPosition);
            }
        }
        $this->setVar('attribute_names', implode(OLEDRION_ATTRIBUTE_SEPARATOR, $names));
        $this->setVar('attribute_values', implode(OLEDRION_ATTRIBUTE_SEPARATOR, $values));
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('use_price')) {
            $this->setVar('attribute_prices', implode(OLEDRION_ATTRIBUTE_SEPARATOR, $prices));
        }
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('attributes_stocks')) {
            $this->setVar('attribute_stocks', implode(OLEDRION_ATTRIBUTE_SEPARATOR, $stocks));
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
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('use_price')) {
            $prices = $this->getOption('attribute_prices', $format);
        }
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('attributes_stocks')) {
            $stocks = $this->getOption('attribute_stocks', $format);
        }
        if (isset($names[$optionNumber])) {
            unset($names[$optionNumber]);
        }
        if (isset($values[$optionNumber])) {
            unset($values[$optionNumber]);
        }
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('use_price')) {
            if (isset($prices[$optionNumber])) {
                unset($prices[$optionNumber]);
            }
        }
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('attributes_stocks')) {
            if (isset($stocks[$optionNumber])) {
                unset($stocks[$optionNumber]);
            }
        }
        $this->setVar('attribute_names', implode(OLEDRION_ATTRIBUTE_SEPARATOR, $names));
        $this->setVar('attribute_values', implode(OLEDRION_ATTRIBUTE_SEPARATOR, $values));
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('use_price')) {
            $this->setVar('attribute_prices', implode(OLEDRION_ATTRIBUTE_SEPARATOR, $prices));
        }
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('attributes_stocks')) {
            $this->setVar('attribute_stocks', implode(OLEDRION_ATTRIBUTE_SEPARATOR, $stocks));
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
     * @param  Oledrion_products $product            Le produit de travail
     * @return array
     * @since 2.3.2009.03.11
     */
    public function getAttributeOptions($format = 's', $withFormatedPrices = false, Oledrion_products $product = null)
    {
        $ret     = [];
        $counter = 0;
        if (null !== $product) {
            $vat_id = $product->getVar('product_vat_id');
        }
        $names  = $this->getOption('attribute_names', $format);
        $values = $this->getOption('attribute_values', $format);
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('use_price')) {
            $prices = $this->getOption('attribute_prices', $format);
        }
        if (\Xoopsmodules\oledrion\Utility::getModuleOption('attributes_stocks')) {
            $stocks = $this->getOption('attribute_stocks', $format);
        }

        if ($withFormatedPrices) {
            $oledrion_Currency = Oledrion_Currency::getInstance();
        }
        if (count($names) > 0) {
            foreach ($names as $key => $name) {
                $price = $stock = 0;
                if (\Xoopsmodules\oledrion\Utility::getModuleOption('use_price')) {
                    $price = $prices[$key];
                    if ($withFormatedPrices) {
                        $priceFormated    = $oledrion_Currency->amountForDisplay($price);
                        $priceTtc         = \Xoopsmodules\oledrion\Utility::getAmountWithVat($price, $vat_id);
                        $priceTtcFormated = $oledrion_Currency->amountForDisplay($priceTtc);
                        $vat              = $priceTtc - $price;
                        $vatFormated      = $oledrion_Currency->amountForDisplay($vat);
                    }
                }
                if (\Xoopsmodules\oledrion\Utility::getModuleOption('attributes_stocks')) {
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
            OLEDRION_ATTRIBUTE_RADIO    => _AM_OLEDRION_TYPE_RADIO,
            OLEDRION_ATTRIBUTE_CHECKBOX => _AM_OLEDRION_TYPE_CHECKBOX,
            OLEDRION_ATTRIBUTE_SELECT   => _AM_OLEDRION_TYPE_LIST
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
     * @param  Oledrion_products $product Le produit de "travail"
     * @return string                   Le contenu html
     * @since 2.3.2009.03.16
     */
    public function render(Oledrion_products $product)
    {
        require_once XOOPS_ROOT_PATH . '/class/template.php';
        $template = new XoopsTpl();

        $options      = [];
        $ret          = $templateName = '';
        $elementName  = $this->getVar('attribute_name', 'e');
        $elementTitle = $this->getVar('attribute_title');
        $option1      = $this->getVar('attribute_option1');
        $option2      = $this->getVar('attribute_option2');

        $handlers = OledrionHandler::getInstance();
        $isInCart = $handlers->h_oledrion_caddy->isInCart($product->getVar('product_id'));
        if (false === $isInCart) {    // Le produit n'est pas dans le panier, on prend la valeur par défaut
            $defaultValue = [$this->getVar('attribute_default_value')];
        } else {    // Le produit est dans le panier, on va chercher les options qui sont sélectionnées
            $Productattributes = $handlers->h_oledrion_caddy->getProductAttributesFromCart($product->getVar('product_id'));
            if (isset($Productattributes[$this->getVar('attribute_id')])) {
                $defaultValue = $Productattributes[$this->getVar('attribute_id')];
            } else {    // On prend la valeur par défaut
                if (OLEDRION_ATTRIBUTE_RADIO == $this->attribute_type) {    // Pour les boutons radio, il ne peut y avoir qu'un élément de sélectionné
                    $defaultValue = $this->getVar('attribute_default_value');
                } else {
                    $defaultValue = [$this->getVar('attribute_default_value')];
                }
            }
            if (!is_array($defaultValue)) {
                $defaultValue = [$defaultValue];
            }
            $newDefaultValue = [];
            foreach ($defaultValue as $oneValue) {
                $newDefaultValue[] = \Xoopsmodules\oledrion\Utility::getName($oneValue);
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
            case OLEDRION_ATTRIBUTE_SELECT:        // Liste déroulante
                $templateName = 'oledrion_attribute_select.tpl';
                $multiple     = '';
                if (1 == $option2) {    // La sélection multiple est autorisée
                    $multiple = "multiple='multiple' ";
                }
                $template->assign('multiple', $multiple);
                $template->assign('size', $option1);

                break;

            case OLEDRION_ATTRIBUTE_CHECKBOX:      // Cases à cocher
                $templateName = 'oledrion_attribute_checkbox.tpl';
                $delimiter    = '';
                if (OLEDRION_ATTRIBUTE_CHECKBOX_WHITE_SPACE == $option1) {
                    $delimiter = ' ';
                } else {
                    $delimiter = '<br>';
                }
                $template->assign('delimiter', $delimiter);
                break;

            case OLEDRION_ATTRIBUTE_RADIO:         // Boutons radio
                $templateName = 'oledrion_attribute_radio.tpl';
                $delimiter    = '';
                if (OLEDRION_ATTRIBUTE_CHECKBOX_WHITE_SPACE == $option1) {
                    $delimiter = ' ';
                } else {
                    $delimiter = '<br>';
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

/**
 * Class OledrionOledrion_attributesHandler
 */
class OledrionOledrion_attributesHandler extends OledrionPersistableObjectHandler
{
    /**
     * OledrionOledrion_attributesHandler constructor.
     * @param \XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    {    //                             Table               Classe                  Id
        parent::__construct($db, 'oledrion_attributes', 'Oledrion_attributes', 'attribute_id');
    }

    /**
     * Supprime tous les attributs d'un produit
     *
     * @param  integer $attribute_product_id
     * @return boolean Le résultat de la suppression
     * @since 2.3.2009.03.16
     */
    public function deleteProductAttributes($attribute_product_id)
    {
        $attribute_product_id = (int)$attribute_product_id;

        return $this->deleteAll(new Criteria('attribute_product_id', $attribute_product_id, '='));
    }

    /**
     * Retourne le nombre total d'attributs d'un produit (qu'ils soient obligatoires ou pas)
     *
     * @param  integer $attribute_product_id
     * @return integer
     * @since 2.3.2009.03.16
     */
    public function getProductAttributesCount($attribute_product_id)
    {
        return $this->getCount(new Criteria('attribute_product_id', $attribute_product_id, '='));
    }

    /**
     * Retourne la liste des attributs d'un produit
     *
     * @param  integer $product_id Le produit concerné
     * @param  null    $attributesIds
     * @return array
     */
    public function getProductsAttributesList($product_id, $attributesIds = null)
    {
        $ret      = [];
        $criteria = new CriteriaCompo();
        if (is_array($product_id)) {
            $criteria->add(new Criteria('attribute_product_id', '(' . implode(',', $product_id) . ')', 'IN'));
        } else {
            $criteria->add(new Criteria('attribute_product_id', $product_id, '='));
        }
        if (is_array($attributesIds) && count($attributesIds) > 0) {
            $criteria->add(new Criteria('attribute_id', '(' . implode(',', array_keys($attributesIds)) . ')', 'IN'));
        }
        $criteria->setSort('attribute_weight, attribute_title');    // L'ajout du titre dans le tri permet de trier même lorsque le poids n'est pas valorisé
        $ret = $this->getObjects($criteria);

        return $ret;
    }

    /**
     * Construction de la liste des attributs d'un produit
     *
     * @param  Oledrion_products $product              Le produit concerné
     * @param  integer           $mandatoryFieldsCount Retourne le nombre d'options requises
     * @return array                    Les options construites en html
     * @since 2.3.2009.03.16
     */
    public function constructHtmlProductAttributes(Oledrion_products $product, &$mandatoryFieldsCount = 0)
    {
        $attributes = $ret = [];
        $attributes = $this->getProductsAttributesList($product->getVar('product_id'));
        if (0 == count($attributes)) {
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
     * @param  Oledrion_products $product
     * @return float
     */
    public function getInitialOptionsPrice(Oledrion_products $product)
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
     * @param  Oledrion_attributes $originalAttribute
     * @return mixed               Soit le nouvel attribut si tout a bien marché sinon false
     * @internal param Oledrion_attributes $attribute L'attribute à cloner
     * @since    2.3.2009.03.16
     */
    public function cloneAttribute(Oledrion_attributes $originalAttribute)
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
        } else {
            return false;
        }
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
     * @param  Oledrion_attributes $attribute
     * @return boolean
     * @since 2.3.2009.03.17
     */
    public function deleteAttribute(Oledrion_attributes $attribute)
    {
        return $this->delete($attribute, true);
        // TODO: Supprimer dans les attributs paniers
    }

    /**
     * Retourne le nombre d'attributs obligatoires d'un produit
     *
     * @param  Oledrion_products $product
     * @return integer
     * @since 2.3.2009.03.20
     */
    public function getProductMandatoryAttributesCount(Oledrion_products $product)
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('attribute_product_id', $product->getVar('attribute_product_id'), '='));
        $criteria->add(new Criteria('attribute_mandatory', 1, '='));

        return $this->getCount($criteria);
    }

    /**
     * Retourne le nom des champs (représentant les attributs) obligatoires que l'on devrait trouver suite à une sélection de produit
     *
     * @param  Oledrion_products $product
     * @return array             objets des type Oledrion_attributes
     */
    public function getProductMandatoryFieldsList(Oledrion_products $product)
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('attribute_product_id', $product->getVar('attribute_product_id'), '='));
        $criteria->add(new Criteria('attribute_mandatory', 1, '='));

        return $this->getObjects($criteria);
    }

    /**
     * Calcul le prix HT des options sélectionnées pour un produit
     *
     * @param  array   $choosenAttributes [clé] = attribute_id, [value] = array(valueId1, valueId2 ...)
     * @param  integer $product_vat_id    L'ID de TVA du produit
     * @param  array   $descriptions      Tableau valorisé par la méthode [clé] = Id attribut [valeur] = array('attribute_title', array('attribute_names', 'attribute_prices'))
     * @return float
     * @since 2.3.2009.03.21
     */
    public function getProductOptionsPrice($choosenAttributes, $product_vat_id, &$descriptions = null)
    {
        static $vats = [];
        if (is_array($vats) && isset($vats[$product_vat_id])) {
            $vat_rate = $vats[$product_vat_id];
        } else {
            $vat = null;
            $vat = OledrionHandler::getInstance()->h_oledrion_vat->get($product_vat_id);
            if (is_object($vat)) {
                $vats[$product_vat_id] = $vat_rate = $vat->getVar('vat_rate', 'n');
            }
        }
        $ret           = 0;
        $attributesIds = $attributes = [];
        if (!is_array($choosenAttributes) || 0 == count($choosenAttributes)) {
            return $ret;
        }
        $attributesIds = array_keys($choosenAttributes);

        $attributes = $this->getItemsFromIds($attributesIds);
        if (0 == count($attributes)) {
            return $ret;
        }
        $oledrion_Currency = Oledrion_Currency::getInstance();

        foreach ($choosenAttributes as $userAttributeId => $userAttributeValues) {
            if (isset($attributes[$userAttributeId])) {
                $attribute           = $attributes[$userAttributeId];
                $dataForDescriptions = [];
                $optionDescription   = '';
                if (is_array($userAttributeValues) && count($userAttributeValues) > 0) {
                    foreach ($userAttributeValues as $option) {
                        $optionName            = \Xoopsmodules\oledrion\Utility::getName($option);
                        $price                 = $attribute->getOptionPriceFromValue($optionName);
                        $optionDescription     = $attribute->getOptionNameFromValue($optionName);
                        $vatAmount             = \Xoopsmodules\oledrion\Utility::getVAT($price, $vat_rate);
                        $ttc                   = $price + $vatAmount;
                        $vatAmountFormated     = $oledrion_Currency->amountForDisplay($vatAmount);
                        $htFormated            = $oledrion_Currency->amountForDisplay($price);
                        $ttcFormated           = $oledrion_Currency->amountForDisplay($ttc);
                        $dataForDescriptions[] = [
                            'option_name'              => $optionDescription,
                            'option_value'             => $optionName,
                            'option_price'             => $price,
                            'option_vat'               => $vatAmount,
                            'option_ttc'               => $ttc,
                            'option_price_ht_formated' => $htFormated,
                            'option_vat_formated'      => $vatAmountFormated,
                            'option_ttc_formated'      => $ttcFormated
                        ];
                        $ret                   += $price;    // Total de toutes les options
                    }
                } else {
                    $optionName            = \Xoopsmodules\oledrion\Utility::getName($userAttributeValues);
                    $price                 = $attribute->getOptionPriceFromValue($optionName);
                    $optionDescription     = $attribute->getOptionNameFromValue($optionName);
                    $vatAmount             = \Xoopsmodules\oledrion\Utility::getVAT($price, $vat_rate);
                    $ttc                   = $price + $vatAmount;
                    $vatAmountFormated     = $oledrion_Currency->amountForDisplay($vatAmount);
                    $htFormated            = $oledrion_Currency->amountForDisplay($price);
                    $ttcFormated           = $oledrion_Currency->amountForDisplay($ttc);
                    $dataForDescriptions[] = [
                        'option_name'              => $optionDescription,
                        'option_value'             => $optionName,
                        'option_price'             => $price,
                        'option_vat'               => $vatAmount,
                        'option_ttc'               => $ttc,
                        'option_price_ht_formated' => $htFormated,
                        'option_vat_formated'      => $vatAmountFormated,
                        'option_ttc_formated'      => $ttcFormated
                    ];
                    $ret                   += $price;    // Total de toutes les options
                }
                if (is_array($descriptions)) {
                    $descriptions[$attribute->getVar('attribute_id')] = [
                        'attribute_title'   => $attribute->getVar('attribute_title'),
                        'attribute_options' => $dataForDescriptions
                    ];
                }
            }
        }

        return $ret;
    }
}
