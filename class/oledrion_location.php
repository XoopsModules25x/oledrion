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
 * @author      Hossein Azizabadi (azizabadi@faragostaresh.com)
 */

require_once __DIR__ . '/classheader.php';

/**
 * Class Oledrion_location
 */
class Oledrion_location extends OledrionObject
{
    /**
     * constructor
     *
     * normally, this is called from child classes only
     *
     * @access public
     */
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
        $ret = [];
        $ret = parent::toArray($format);

        return $ret;
    }
}

/**
 * Class OledrionOledrion_locationHandler
 */
class OledrionOledrion_locationHandler extends OledrionPersistableObjectHandler
{
    /**
     * OledrionOledrion_locationHandler constructor.
     * @param \XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    { //                                        Table                   Classe              Id
        parent::__construct($db, 'oledrion_location', 'Oledrion_location', 'location_id');
    }

    /**
     * @param  Oledrion_parameters $parameters
     * @return array
     */
    public function getAllLocation(Oledrion_parameters $parameters)
    {
        $parameters = $parameters->extend(new Oledrion_parameters([
                                                                      'start' => 0,
                                                                      'limit' => 0,
                                                                      'sort'  => 'location_id',
                                                                      'order' => 'ASC'
                                                                  ]));
        $critere    = new Criteria('location_id', 0, '<>');
        $critere->setLimit($parameters['limit']);
        $critere->setStart($parameters['start']);
        $critere->setSort($parameters['sort']);
        $critere->setOrder($parameters['order']);
        $location = [];
        $location = $this->getObjects($critere);

        return $location;
    }

    /**
     * @param  Oledrion_parameters $parameters
     * @return array
     */
    public function getAllPid(Oledrion_parameters $parameters)
    {
        $parameters = $parameters->extend(new Oledrion_parameters([
                                                                      'start' => 0,
                                                                      'limit' => 0,
                                                                      'sort'  => 'location_id',
                                                                      'order' => 'ASC'
                                                                  ]));
        $critere    = new CriteriaCompo();
        $critere->add(new Criteria('location_type', 'parent'));
        $critere->setLimit($parameters['limit']);
        $critere->setStart($parameters['start']);
        $critere->setSort($parameters['sort']);
        $critere->setOrder($parameters['order']);
        $pid = [];
        $pid = $this->getObjects($critere);

        return $pid;
    }

    /**
     * @param $id
     * @return array
     */
    public function getLocation($id)
    {
        $critere = new CriteriaCompo();
        $critere->add(new Criteria('location_online', 1));
        $critere->add(new Criteria('location_type', 'location'));
        $critere->add(new Criteria('location_pid', $id));
        $location = [];
        $location = $this->getObjects($critere);

        return $location;
    }

    /**
     * @param $id
     * @return int
     */
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
