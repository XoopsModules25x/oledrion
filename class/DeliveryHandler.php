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
 * @author      Hossein Azizabadi (azizabadi@faragostaresh.com)
 */

use XoopsModules\Oledrion;

/**
 * Class DeliveryHandler
 */
class DeliveryHandler extends OledrionPersistableObjectHandler
{
    /**
     * DeliveryHandler constructor.
     * @param \XoopsDatabase|null $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        //                            Table                   Classe              Id
        parent::__construct($db, 'oledrion_delivery', Delivery::class, 'delivery_id');
    }

    /**
     * @param  Parameters $parameters
     * @return array
     */
    public function getAllDelivery(Parameters $parameters)
    {
        $parameters = $parameters->extend(new Oledrion\Parameters([
                                                                      'start' => 0,
                                                                      'limit' => 0,
                                                                      'sort'  => 'delivery_id',
                                                                      'order' => 'ASC',
                                                                  ]));
        $critere    = new \Criteria('delivery_id', 0, '<>');
        $critere->setLimit($parameters['limit']);
        $critere->setStart($parameters['start']);
        $critere->setSort($parameters['sort']);
        $critere->setOrder($parameters['order']);
        $categories = [];
        $categories = $this->getObjects($critere);

        return $categories;
    }

    /**
     * @param  Parameters $parameters
     * @return array
     */
    public function getLocationDelivery(Parameters $parameters)
    {
        global $locationDeliveryHandler;
        $ret               = [];
        $parameters        = $parameters->extend(new Oledrion\Parameters([
                                                                             'start'    => 0,
                                                                             'limit'    => 0,
                                                                             'sort'     => 'delivery_id',
                                                                             'order'    => 'ASC',
                                                                             'location' => '',
                                                                         ]));
        $location_delivery = $locationDeliveryHandler->getLocationDeliveryId($parameters);

        $critere = new \CriteriaCompo();
        $critere->setLimit($parameters['limit']);
        $critere->setStart($parameters['start']);
        $critere->setSort($parameters['sort']);
        $critere->setOrder($parameters['order']);
        $obj = $this->getObjects($critere);
        if ($obj) {
            foreach ($obj as $root) {
                //                $tab = [];
                $tab = $root->toArray();
                if (isset($location_delivery[$root->getVar('delivery_id')]['ld_delivery']) && $location_delivery[$root->getVar('delivery_id')]['ld_delivery'] == $root->getVar('delivery_id')) {
                    $tab['ld_id']['delivery_select']  = 1;
                    $tab['ld_id']['ld_id']            = $location_delivery[$root->getVar('delivery_id')]['ld_id'];
                    $tab['ld_id']['ld_location']      = $location_delivery[$root->getVar('delivery_id')]['ld_location'];
                    $tab['ld_id']['ld_delivery']      = $location_delivery[$root->getVar('delivery_id')]['ld_delivery'];
                    $tab['ld_id']['ld_price']         = $location_delivery[$root->getVar('delivery_id')]['ld_price'];
                    $tab['ld_id']['ld_delivery_time'] = $location_delivery[$root->getVar('delivery_id')]['ld_delivery_time'];
                }
                $ret[] = $tab;
            }
        }

        return $ret;
    }

    /**
     * @param $location_id
     * @return array
     */
    public function getThisLocationDelivery($location_id)
    {
        global $locationDeliveryHandler;
        $oledrionCurrency  = Oledrion\Currency::getInstance();
        $ret               = [];
        $parameters        = ['location' => $location_id];
        $location_delivery = $locationDeliveryHandler->getLocationDeliveryId($parameters);
        foreach ($location_delivery as $location) {
            $id[] = $location['ld_delivery'];
        }

        $critere = new \CriteriaCompo();
        $critere->add(new \Criteria('delivery_id', '(' . implode(',', $id) . ')', 'IN'));
        $critere->add(new \Criteria('delivery_online', 1));
        $obj = $this->getObjects($critere);
        if ($obj) {
            foreach ($obj as $root) {
                $tab                              = [];
                $tab                              = $root->toArray();
                $tab['delivery_price']            = $location_delivery[$root->getVar('delivery_id')]['ld_price'];
                $tab['delivery_price_fordisplay'] = $oledrionCurrency->amountForDisplay($tab['delivery_price']);
                $tab['delivery_time']             = $location_delivery[$root->getVar('delivery_id')]['ld_delivery_time'];
                $ret[]                            = $tab;
            }
        }

        return $ret;
    }

    /**
     * @param $location_id
     * @param $delivery_id
     * @return array
     */
    public function getThisLocationThisDelivery($location_id, $delivery_id)
    {
        global $locationDeliveryHandler;
        $location_delivery     = $locationDeliveryHandler->getDelivery($location_id, $delivery_id);
        $ret                   = [];
        $obj                   = $this->get($location_id);
        $ret                   = $obj->toArray();
        $ret['delivery_price'] = $location_delivery['ld_price'];
        $ret['delivery_time']  = $location_delivery['ld_delivery_time'];

        return $ret;
    }
}
