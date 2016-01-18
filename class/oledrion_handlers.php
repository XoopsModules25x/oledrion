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
 * @version     $Id: oledrion_handlers.php 12290 2014-02-07 11:05:17Z beckmi $
 */

/**
 * Chargement des handlers utilisés par le module
 */

defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

class oledrion_handler
{
    /**
     * Contient la liste des handlers disponibles
     *
     * @var array
     */
    private $handlersNames = array('oledrion_manufacturer', 'oledrion_products', 'oledrion_productsmanu', 'oledrion_caddy', 'oledrion_cat', 'oledrion_commands', 'oledrion_related', 'oledrion_vat', 'oledrion_votedata', 'oledrion_discounts', 'oledrion_vendors', 'oledrion_files', 'oledrion_persistent_cart', 'oledrion_gateways_options', 'oledrion_attributes', 'oledrion_caddy_attributes', 'oledrion_products_list', 'oledrion_lists', 'oledrion_delivery', 'oledrion_location', 'oledrion_packing', 'oledrion_payment', 'oledrion_location_delivery', 'oledrion_delivery_payment', 'oledrion_payment_log');

    /**
     * Contient l'unique instance de l'objet
     * @var object
     */
    private static $instance = false;

    /**
     * Réceptacle des handlers
     *
     * @var array
     */
    public static $handlers = null;

    /**
     * Méthode chargée de renvoyer les handlers de données en les chargeant à la volée
     *
     * @param  string $name
     * @return mixed  Null si on échoue, sinon l'objet demandé
     */
    public function __get($name)
    {
        if (substr($name, 0, 2) != 'h_') {
            return null;
        }
        if (!in_array(substr($name, 2), $this->handlersNames)) {
            return null;
        }
        if (!isset($this->handlersNames[$name])) {
            $this->handlers[$name] = xoops_getmodulehandler(substr($name, 2), OLEDRION_DIRNAME);
        }

        return $this->handlers[$name];
    }

    private function __construct()
    {
        $this->handlers = array();
    }

    /**
     * Retourne l'instance unique de la classe
     *
     * @return object
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}
