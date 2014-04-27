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
 * @author      Hossein Azizabadi (azizabadi@faragostaresh.com)
 * @version     $Id: oledrion_location_delivery.php 12290 2014-02-07 11:05:17Z beckmi $
 */

require 'classheader.php';

class oledrion_location_delivery extends Oledrion_Object
{
    public function __construct()
    {
        $this->initVar('ld_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('ld_location', XOBJ_DTYPE_INT, null, false);
        $this->initVar('ld_delivery', XOBJ_DTYPE_INT, null, false);
        $this->initVar('ld_price', XOBJ_DTYPE_INT, null, false);
        $this->initVar('ld_delivery_time', XOBJ_DTYPE_INT, null, false);
    }

    /**
     * Retourne les éléments du produits formatés pour affichage
     *
     * @param  string $format
     * @return array
     */
    public function toArray($format = 's')
    {
        $ret = array();
        $ret = parent::toArray($format);

        return $ret;
    }
}

class OledrionOledrion_location_deliveryHandler extends Oledrion_XoopsPersistableObjectHandler
{
    public function __construct($db)
    { //							              Table					           Classe				       Id
        parent::__construct($db, 'oledrion_location_delivery', 'oledrion_location_delivery', 'ld_id');
    }

    public function getLocationDeliveryId($parameters)
    {
        $ret = array();
        if (!$parameters['location']) {
            return $ret;
        }
        $critere = new CriteriaCompo ();
        $critere->add(new Criteria('ld_location', $parameters['location']));
        $obj = $this->getObjects($critere);
        if ($obj) {
            foreach ($obj as $root) {
                $tab = array();
                $tab = $root->toArray();
                $ret[$root->getVar('ld_delivery')] = $tab;
            }
        }

        return $ret;
    }

    public function getDelivery($ld_delivery, $ld_location)
    {
        $ret = array();
        $critere = new CriteriaCompo ();
        $critere->add(new Criteria('ld_delivery', $ld_delivery));
        $critere->add(new Criteria('ld_location', $ld_location));
        $critere->setLimit(1);
        $obj = $this->getObjects($critere);
        if ($obj) {
            foreach ($obj as $root) {
                $tab = array();
                $ret = $root->toArray();
            }
        }

        return $ret;
    }
}
