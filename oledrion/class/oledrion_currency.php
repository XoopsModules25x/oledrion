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
 * @version     $Id: oledrion_currency.php 12290 2014-02-07 11:05:17Z beckmi $
 */

/**
 * Gestion de la monnaie
 */

defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

class oledrion_Currency
{
    protected $_decimalsCount;
    protected $_thousandsSep;
    protected $_decimalSep;
    protected $_moneyFull;
    protected $_moneyShort;
    protected $_monnaiePlace;

    public function __construct()
    {
        // Get the module's preferences
        $this->_decimalsCount = oledrion_utils::getModuleOption('decimals_count');
        $this->_thousandsSep = oledrion_utils::getModuleOption('thousands_sep');
        $this->_decimalSep = oledrion_utils::getModuleOption('decimal_sep');
        $this->_moneyFull = oledrion_utils::getModuleOption('money_full');
        $this->_moneyShort = oledrion_utils::getModuleOption('money_short');
        $this->_monnaiePlace = oledrion_utils::getModuleOption('monnaie_place');
        $this->_thousandsSep = str_replace('[space]', ' ', $this->_thousandsSep);
        $this->_decimalSep = str_replace('[space]', ' ', $this->_decimalSep);
    }

    /**
     * Access the only instance of this class
     *
     * @return object
     *
     * @static
     * @staticvar   object
     */

    public static function getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new oledrion_Currency();
        }

        return $instance;
    }

    /**
     * Returns an amount according to the currency's preferences (defined in the module's options)
     *
     * @param  float  $amount The amount to work on
     * @return string The amount formated according to the currency
     */
    public function amountInCurrency($amount = 0)
    {
        return number_format($amount, $this->_decimalsCount, $this->_decimalSep, $this->_thousandsSep);
    }


    /**
     * Format an amount for display according to module's preferences
     *
     * @param  float  $originalAmount The amount to format
     * @param  string $format         Format to use, 's' for Short and 'l' for Long
     * @return string The amount formated
     */
    public function amountForDisplay($originalAmount, $format = 's')
    {
        $amount = $this->amountInCurrency($originalAmount);

        $monnaieLeft = $monnaieRight = $monnaieSleft = $monnaieSright = '';
        if ($this->_monnaiePlace == 1) { // To the right
            $monnaieRight = '' . $this->_moneyFull; // Long version
            $monnaieSright = '' . $this->_moneyShort; // Short version
        } else { // To the left
            $monnaieLeft = $this->_moneyFull . ''; // Long version
            $monnaieSleft = $this->_moneyShort . ''; // Short version
        }
        if ($format != 's') {
            return $monnaieLeft . $amount . $monnaieRight;
        } else {
            return $monnaieSleft . $amount . $monnaieSright;
        }
    }
}
