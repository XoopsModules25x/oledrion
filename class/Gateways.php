<?php

namespace XoopsModules\Oledrion;

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

use XoopsModules\Oledrion;

/**
 * Classe chargée de la manipulation des passerelles de paiement
 *
 * Normalement la classe est utilisable de manière statique
 */
// defined('XOOPS_ROOT_PATH') || die('Restricted access');

require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';

/**
 * Class Gateways
 */
class Gateways
{
    /**
     * Retourne la passerelle de paiement en cours d'utilisation
     *
     * @param  null $gateway
     * @return string The name of the payment gateway (en fait le nom de son répertoire)
     */
    public static function getCurrentGateway($gateway = null)
    {
        if ($gateway) {
            $return = $gateway;
        } else {
            $return = xoops_trim(Oledrion\Utility::getModuleOption('used_gateway'));
        }

        if (null === $return) {
            $return = 'Paypal'; // Valeur par défaut
        }

        return $return;
    }

    /**
     * Returns the payment gateway in use
     *
     * @return string The name of the payment gateway (actually the name of its directory)
     */
    public static function getDefaultGateway()
    {
        $return = xoops_trim(Oledrion\Utility::getModuleOption('used_gateway'));
        if (null === $return) {
            $return = 'Paypal'; // Valeur par défaut
        }

        return $return;
    }

    /**
     * Cleans the name of the payment gateway
     *
     * @param  string $gatewayName The name of the payment gateway
     * @return string
     */
    public static function purifyGatewayName($gatewayName)
    {
        return str_replace('..', '', $gatewayName);
    }

    /**
     * Returns the list of installed payment gateways
     *
     * @return array
     */
    public static function getInstalledGatewaysList()
    {
        return \XoopsLists::getDirListAsArray(OLEDRION_GATEWAY_PATH);
    }

    /**
     * Returns the path to a payment gateway
     *
     * @param  string $gatewayName The name of the payment gateway (its directory)
     * @return string
     */
    public static function getGatewayPath($gatewayName)
    {
        return OLEDRION_CLASS_PATH . 'Gateways' . '/' . $gatewayName; // Par exemple c:/inetpub/wwwroot/xoops/modules/oledrion/class/Gateways/Paypal
    }

    /**
     * Returns the full path to the gateway language file
     *
     * @param  mixed $gatewayName
     * @return mixed
     */
    public static function getGatewayLanguageFilename($gatewayName)
    {
        global $xoopsConfig;
        $gatewayPath = self::getGatewayPath($gatewayName);

        return $gatewayPath . '/language/' . $xoopsConfig['language'] . '/main.php';
    }

    /**
     * Load the translation file of a payment gateway
     *
     * @param  string $gatewayName      The name of the payment gateway (its directory)
     * @param  string $languageFilename Used to return the name of the included language file
     * @param  bool   $includeIt
     * @return bool   True if the loading was successful otherwise False
     */
    public static function loadGatewaysLanguageDefines($gatewayName, &$languageFilename = null, $includeIt = true)
    {
        $gatewayPath          = self::getGatewayPath($gatewayName);
        $languageFileIncluded = false;
        $languageFile         = self::getGatewayLanguageFilename($gatewayName);
        $defaultLanguageFile  = $gatewayPath . '/language/english/main.php';
        if (file_exists($languageFile)) {
            if ($includeIt) {
                require_once $languageFile;
            }
            $languageFileIncluded = true;
            $languageFilename     = $languageFile;
        } elseif (file_exists($defaultLanguageFile)) {
            $languageFileIncluded = true;
            if ($includeIt) {
                require_once $defaultLanguageFile;
            }
            $languageFilename = $defaultLanguageFile;
        }

        return $languageFileIncluded;
    }

    /**
     * Returns the list of installed payment gateways
     *
     * @param  string $gatewayName The name of the payment gateway (its directory)
     * @return string
     */
    public static function getGatewayFullClassPath($gatewayName)
    {
        $gatewayPath = self::getGatewayPath($gatewayName);

        return $gatewayPath . '/' . $gatewayName . '.php';
    }

    /**
     * Indicates whether the file containing the class of a payment gateway exists
     *
     * @param  string $gatewayName The name of the payment gateway (its directory)
     * @return bool True if the class file exists otherwise False
     */
    public static function gatewayClassFileExists($gatewayName)
    {
        $gatewayClassPath = self::getGatewayFullClassPath($gatewayName);
        if (file_exists($gatewayClassPath)) {
            return true;
        }

        return false;
    }

    /**
     * Loading (inclusion) of the payment gateway class file
     *
     * @param string $gatewayName
     */
    public static function includeGatewayClass($gatewayName)
    {
        $gatewayClassPath = self::getGatewayFullClassPath($gatewayName);
        require_once $gatewayClassPath;
    }

    /**
     * Returns the name of the expected class for a payment gateway
     *
     * @param  string $gatewayName The name of the payment gateway (its directory)
     * @return string
     */
    public static function gatewayClassName($gatewayName)
    {
        //        return 'oledrion_' . $gatewayName;
        return $gatewayName;
    }

    /**
     * Indicates whether the payment gateway class exists
     *
     * @param  string $gatewayName The name of the payment gateway (its directory)
     * @return bool
     */
    public static function gatewayClassExists($gatewayName)
    {
        $gatewayClassName = self::gatewayClassName($gatewayName);
        if (class_exists($gatewayClassName)) {
            return true;
        }

        return false;
    }

    /**
     * Indicates whether a gateway object extends the abstract class
     *
     * @param  Gateways $gateway The object to check
     * @return bool
     */
    public static function asGoodAncestor($gateway)
    {
        return 'XoopsModules\Oledrion\Gateways\Gateway' === get_parent_class($gateway);
    }

    /**
     * Indicates whether the name of the payment gateway is on the site
     *
     * @param  string $gatewayName The name of the payment gateway
     * @return bool
     */
    public static function isInstalledGatewayName($gatewayName)
    {
        $installedGateways = self::getInstalledGatewaysList();
        if (!in_array($gatewayName, $installedGateways, true)) {
            return false;
        }

        return true;
    }

    /**
     * Shortcuts to retrieve the current gateway object
     *
     * @param  null $gateway
     * @return mixed Either gateway object or null
     */
    public static function getGatewayObject($gateway = null)
    {
        $gateway = self::getCurrentGateway($gateway);
        if (self::isInstalledGatewayName($gateway)) {
            if (self::gatewayClassFileExists($gateway)) {
                if (self::loadGatewaysLanguageDefines($gateway)) {
                    self::includeGatewayClass($gateway);
                    if (self::gatewayClassExists($gateway)) {
                        $gatewayClassName = self::gatewayClassName($gateway);
                        $temporaryGateway = new $gatewayClassName();
                        if (self::asGoodAncestor($temporaryGateway)) {
                            return $temporaryGateway;
                        }
                    }
                }
            }
        }

        return null;
    }
}
