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
 * Managing payment gateway options
 */
// require_once __DIR__ . '/classheader.php';

/**
 * Class GatewaysOptionsHandler
 */
class GatewaysOptionsHandler extends OledrionPersistableObjectHandler
{
    /**
     * GatewaysOptionsHandler constructor.
     * @param \XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        //                                Table                       Classe                      Id
        parent::__construct($db, 'oledrion_gateways_options', GatewaysOptions::class, 'option_id');
    }

    /**
     * Returns all the options of a payment gateway
     *
     * @param  string $option_gateway The name of the payment gateway
     * @return array  Tableau d'objets de type GatewaysOptions
     */
    public function getGatewayOptions($option_gateway)
    {
        $criteria = new \Criteria('option_gateway', $option_gateway, '=');

        return $this->getObjects($criteria);
    }

    /**
     * Removes all options from a payment gateway
     *
     * @param  string $option_gateway
     * @return bool The result of removing options
     */
    public function deleteGatewayOptions($option_gateway)
    {
        $criteria = new \Criteria('option_gateway', $option_gateway, '=');

        return $this->deleteAll($criteria);
    }

    /**
     * Returns an option of a payment gateway
     *
     * @param  string $option_gateway The name of the payment gateway
     * @param  string $option_name    The option you want to recover
     * @return array Objet de type GatewaysOptions
     */
    public function getGatewayOption($option_gateway, $option_name)
    {
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('option_gateway', $option_gateway, '='));
        $criteria->add(new \Criteria('option_name', $option_name, '='));

        return $this->getObjects($criteria);
    }

    /**
     * Returns the VALUE of an option of a payment gateway
     *
     * @param  string $option_gateway he name of a payment gateway
     * @param  string $option_name    The option you want to recover
     * @param  string $format         The format in which we want to recover the value (in relation to getVar())
     * @param  bool   $unserialize    Whether to deserialize the return value
     * @return mixed   The value of the option or null if the option can not be found
     */
    public function getGatewayOptionValue($option_gateway, $option_name, $format = 'N', $unserialize = false)
    {
        $ret      = [];
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('option_gateway', $option_gateway, '='));
        $criteria->add(new \Criteria('option_name', $option_name, '='));
        $ret = $this->getObjects($criteria);
        if (count($ret) > 0) {
            if ($unserialize) {
                return unserialize($ret[0]->getVar('option_value', $format));
            }

            return $ret[0]->getVar('option_value', $format);
        }

        return null;
    }

    /**
     * Positions the value of an option of a payment gateway and saves it
     *
     * @param  string $option_gateway The name of the payment gateway
     * @param  string $option_name    The name of the option
     * @param  mixed  $option_value   The value of the option
     * @param  bool   $serialize      Whether to serialize the value before saving
     * @return bool The result of the update (true if the update was made otherwise false)
     */
    public function setGatewayOptionValue($option_gateway, $option_name, $option_value, $serialize = false)
    {
        $ret      = [];
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('option_gateway', $option_gateway, '='));
        $criteria->add(new \Criteria('option_name', $option_name, '='));
        $ret = $this->getObjects($criteria);
        if (count($ret) > 0) {
            $option = $ret[0];
            if ($serialize) {
                $option->setVar('option_value', serialize($option_value));
            } else {
                $option->setVar('option_value', $option_value);
            }

            return $this->insert($option, true);
        }

        // Option not found, we will create it
        $option = $this->create(true);
        $option->setVar('option_gateway', $option_gateway);
        $option->setVar('option_name', $option_name);
        $option->setVar('option_value', $option_value);

        return $this->insert($option, true);
    }
}
