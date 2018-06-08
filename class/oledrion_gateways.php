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
 * Classe chargée de la manipulation des passerelles de paiement
 *
 * Normalement la classe est utilisable de manière statique
 *
 */
// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';

/**
 * Class Oledrion_gateways
 */
class Oledrion_gateways
{
    /**
     * Retourne la passerelle de paiement en cours d'utilisation
     *
     * @param  null $gateway
     * @return string Le nom de la  passerelle de paiement (en fait le nom de son répertoire)
     */
    public static function getCurrentGateway($gateway = null)
    {
        if ($gateway) {
            $return = $gateway;
        } else {
            $return = xoops_trim(OledrionUtility::getModuleOption('used_gateway'));
        }

        if ($return == '') {
            $return = 'paypal'; // Valeur par défaut
        }

        return $return;
    }

    /**
     * Retourne la passerelle de paiement en cours d'utilisation
     *
     * @return string Le nom de la  passerelle de paiement (en fait le nom de son répertoire)
     */
    public static function getDefaultGateway()
    {
        $return = xoops_trim(OledrionUtility::getModuleOption('used_gateway'));
        if ($return == '') {
            $return = 'paypal'; // Valeur par défaut
        }

        return $return;
    }

    /**
     * Nettoie le nom de la passerelle de paiement
     *
     * @param  string $gatewayName Le nom de la  passerelle de paiement
     * @return string
     */
    public static function purifyGatewayName($gatewayName)
    {
        return str_replace('..', '', $gatewayName);
    }

    /**
     * Retourne la liste des passerelles de paiement installées
     *
     * @return array
     */
    public static function getInstalledGatewaysList()
    {
        return XoopsLists::getDirListAsArray(OLEDRION_ADMIN_PATH . 'gateways/');
    }

    /**
     * Retourne le chemin d'accès à une passerelle de paiement
     *
     * @param  string $gatewayName Le nom de la  passerelle de paiement (son répertoire)
     * @return string
     */
    public static function getGatewayPath($gatewayName)
    {
        return OLEDRION_ADMIN_PATH . 'gateways' . '/' . $gatewayName; // Par exemple c:/inetpub/wwwroot/xoops/modules/oledrion/admin/gateways/paypal
    }

    /**
     * Retourne le chemin complet vers le fichier de langue de la passerelle
     *
     * @param  unknown_type $gatewayName
     * @return mixed
     */
    public static function getGatewayLanguageFilename($gatewayName)
    {
        global $xoopsConfig;
        $gatewayPath = self::getGatewayPath($gatewayName);

        return $gatewayPath . '/language/' . $xoopsConfig['language'] . '/main.php';
    }

    /**
     * Charge le fichier de traductions d'une passerelle de paiement
     *
     * @param  string $gatewayName      Le nom de la  passerelle de paiement (son répertoire)
     * @param  string $languageFilename Utilisé pour retourner le nom du fichier de langue inclu
     * @param  bool   $includeIt
     * @return bool   True si le chargement a réussi sinon Faux
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
     * Retourne le chemin d'accès complet à une passerelle de paiement
     *
     * @param  string $gatewayName Le nom de la  passerelle de paiement (son répertoire)
     * @return string
     */
    public static function getGatewayFullClassPath($gatewayName)
    {
        $gatewayPath = self::getGatewayPath($gatewayName);

        return $gatewayPath . '/gateway.php';
    }

    /**
     * Indique si le fichier contenant la classe d'une passerelle de paiement existe
     *
     * @param  string $gatewayName Le nom de la  passerelle de paiement (son répertoire)
     * @return boolean True si le fichier de la classe existe sinon Faux
     */
    public static function gatewayClassFileExists($gatewayName)
    {
        $gatewayClassPath = self::getGatewayFullClassPath($gatewayName);
        if (file_exists($gatewayClassPath)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Chargement (inclusion) du fichier de la classe de la passerelle de paiement
     *
     * @param string $gatewayName
     */
    public static function includeGatewayClass($gatewayName)
    {
        $gatewayClassPath = self::getGatewayFullClassPath($gatewayName);
        require_once $gatewayClassPath;
    }

    /**
     * Retourne le nom de la classe attendu pour une passerelle de paiement
     *
     * @param  string $gatewayName Le nom de la  passerelle de paiement (son répertoire)
     * @return string
     */
    public static function gatewayClassName($gatewayName)
    {
        return 'Oledrion_' . $gatewayName;
    }

    /**
     * Indique si la classe de la passerelle de paiement existe
     *
     * @param  string $gatewayName Le nom de la  passerelle de paiement (son répertoire)
     * @return boolean
     */
    public static function gatewayClassExists($gatewayName)
    {
        $gatewayClassName = self::gatewayClassName($gatewayName);
        if (class_exists($gatewayClassName)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Indique si un objet de type gateway étend bien la classe abstraite
     *
     * @param  object $gateway L'objet à vérifier
     * @return boolean
     */
    public static function asGoodAncestor($gateway)
    {
        if (get_parent_class($gateway) === 'Oledrion_gateway') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Indique si Le nom de la  passerelle de paiement se trouve sur le site
     *
     * @param  string $gatewayName Le nom de la  passerelle de paiement
     * @return boolean
     */
    public static function isInstalledGatewayName($gatewayName)
    {
        $installedGateways = self::getInstalledGatewaysList();
        if (!in_array($gatewayName, $installedGateways)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Raccourcis pour récupérer l'objet gateway courant
     *
     * @param  null $gateway
     * @return mixed Soit l'objet gateway soit null
     *
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
