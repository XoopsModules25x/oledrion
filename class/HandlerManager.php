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
 * Chargement des handlers utilisés par le module
 */
// defined('XOOPS_ROOT_PATH') || die('Restricted access');

class HandlerManager
{
    /**
     * Contient la liste des handlers disponibles
     *
     * @var array
     */
    private $handlersNames = [
        'oledrion_manufacturer',
        'oledrion_products',
        'oledrion_productsmanu',
        'oledrion_caddy',
        'oledrion_cat',
        'oledrion_commands',
        'oledrion_related',
        'oledrion_vat',
        'oledrion_votedata',
        'oledrion_discounts',
        'oledrion_vendors',
        'oledrion_files',
        'oledrion_persistent_cart',
        'oledrion_gateways_options',
        'oledrion_attributes',
        'oledrion_caddy_attributes',
        'oledrion_products_list',
        'oledrion_lists',
        'oledrion_delivery',
        'oledrion_location',
        'oledrion_packing',
        'oledrion_payment',
        'oledrion_location_delivery',
        'oledrion_delivery_payment',
        'oledrion_payment_log',
    ];

    /**
     * Contient l'unique instance de l'objet
     * @var HandlerManager
     */
    private static $instance = false;

    /**
     * Réceptacle des handlers
     *
     * @var array
     */
    public $handlers;

    /**
     * Méthode chargée de renvoyer les handlers de données en les chargeant à la volée
     *
     * @param  string $name
     * @return mixed  Null si on échoue, sinon l'objet demandé
     */
    public function __get($name)
    {
        //        if (0 !== strpos($name, 'h_')) {
        //            return null;
        //        }

        if (!in_array(mb_substr($name, 2), $this->handlersNames, true)) {
            return null;
        }
        if (!isset($this->handlersNames[$name])) {
            //            $this->handlers[$name] = xoops_getModuleHandler(substr($name, 2), OLEDRION_DIRNAME);
            $db                    = \XoopsDatabaseFactory::getDatabaseConnection();
            $class                 = 'Oledrion\\' . $name;
            $this->handlers[$name] = new $class($db);
        }

        return $this->handlers[$name];
    }

    /**
     * HandlerManager constructor.
     */
    private function __construct()
    {
        $this->handlers = [];
    }

    /**
     * Retourne l'instance unique de la classe
     *
     * @return HandlerManager
     */
    public static function getInstance()
    {
        static $instance;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }
}
