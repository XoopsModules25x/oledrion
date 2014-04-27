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
 * @version     $Id: oledrion_location.php 12290 2014-02-07 11:05:17Z beckmi $
 */

require 'classheader.php';

class oledrion_location extends Oledrion_Object
{
    public function __construct()
    {
        $this->initVar('location_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('location_pid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('location_title', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('location_online', XOBJ_DTYPE_INT, null, false);
        $this->initVar('location_type', XOBJ_DTYPE_TXTBOX, null, false);
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

class OledrionOledrion_locationHandler extends Oledrion_XoopsPersistableObjectHandler
{
    public function __construct($db)
    { //							            Table					Classe				Id
        parent::__construct($db, 'oledrion_location', 'oledrion_location', 'location_id');
    }

    public function getAllLocation(oledrion_parameters $parameters)
    {
        $parameters = $parameters->extend(new oledrion_parameters(array('start' => 0, 'limit' => 0, 'sort' => 'location_id', 'order' => 'ASC')));
        $critere = new Criteria('location_id', 0, '<>');
        $critere->setLimit($parameters['limit']);
        $critere->setStart($parameters['start']);
        $critere->setSort($parameters['sort']);
        $critere->setOrder($parameters['order']);
        $location = array();
        $location = $this->getObjects($critere);

        return $location;
    }

    public function getAllPid(oledrion_parameters $parameters)
    {
        $parameters = $parameters->extend(new oledrion_parameters(array('start' => 0, 'limit' => 0, 'sort' => 'location_id', 'order' => 'ASC')));
        $critere = new CriteriaCompo();
        $critere->add(new Criteria('location_type', 'parent'));
        $critere->setLimit($parameters['limit']);
        $critere->setStart($parameters['start']);
        $critere->setSort($parameters['sort']);
        $critere->setOrder($parameters['order']);
        $pid = array();
        $pid = $this->getObjects($critere);

        return $pid;
    }

    public function getLocation($id)
    {
        $critere = new CriteriaCompo();
        $critere->add(new Criteria('location_online', 1));
        $critere->add(new Criteria('location_type', 'location'));
        $critere->add(new Criteria('location_pid', $id));
        $location = array();
        $location = $this->getObjects($critere);

        return $location;
    }

    public function haveChild($id)
    {
        $critere = new CriteriaCompo();
        $critere->add(new Criteria('location_online', 1));
        $critere->add(new Criteria('location_type', 'location'));
        $critere->add(new Criteria('location_pid', $id));
        $location = $this->getCount($critere);

        return $location;
    }
}
