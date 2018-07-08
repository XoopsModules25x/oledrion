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

// require_once __DIR__ . '/classheader.php';

/**
 * Class LocationDeliveryHandler
 */
class LocationDeliveryHandler extends OledrionPersistableObjectHandler
{
    /**
     * LocationDeliveryHandler constructor.
     * @param \XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        //                                          Table                            Classe                      Id
        parent::__construct($db, 'oledrion_location_delivery', LocationDelivery::class, 'ld_id');
    }

    /**
     * @param $parameters
     * @return array
     */
    public function getLocationDeliveryId($parameters)
    {
        $ret = [];
        if (!$parameters['location']) {
            return $ret;
        }
        $critere = new \CriteriaCompo();
        $critere->add(new \Criteria('ld_location', $parameters['location']));
        $obj = $this->getObjects($critere);
        if ($obj) {
            foreach ($obj as $root) {
                $tab                               = [];
                $tab                               = $root->toArray();
                $ret[$root->getVar('ld_delivery')] = $tab;
            }
        }

        return $ret;
    }

    /**
     * @param $ld_delivery
     * @param $ld_location
     * @return array
     */
    public function getDelivery($ld_delivery, $ld_location)
    {
        $ret     = [];
        $critere = new \CriteriaCompo();
        $critere->add(new \Criteria('ld_delivery', $ld_delivery));
        $critere->add(new \Criteria('ld_location', $ld_location));
        $critere->setLimit(1);
        $obj = $this->getObjects($critere);
        if ($obj) {
            foreach ($obj as $root) {
                $tab = [];
                $ret = $root->toArray();
            }
        }

        return $ret;
    }
}
