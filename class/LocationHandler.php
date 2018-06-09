<?php namespace XoopsModules\Oledrion;

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
 * Class LocationHandler
 */
class LocationHandler extends OledrionPersistableObjectHandler
{
    /**
     * LocationHandler constructor.
     * @param \XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db)
    { //                                        Table                   Classe              Id
        parent::__construct($db, 'oledrion_location', Location::class, 'location_id');
    }

    /**
     * @param  Parameters $parameters
     * @return array
     */
    public function getAllLocation(Parameters $parameters)
    {
        $parameters = $parameters->extend(new Oledrion\Parameters([
                                                                      'start' => 0,
                                                                      'limit' => 0,
                                                                      'sort'  => 'location_id',
                                                                      'order' => 'ASC'
                                                                  ]));
        $critere    = new \Criteria('location_id', 0, '<>');
        $critere->setLimit($parameters['limit']);
        $critere->setStart($parameters['start']);
        $critere->setSort($parameters['sort']);
        $critere->setOrder($parameters['order']);
        $location = [];
        $location = $this->getObjects($critere);

        return $location;
    }

    /**
     * @param  Parameters $parameters
     * @return array
     */
    public function getAllPid(Parameters $parameters)
    {
        $parameters = $parameters->extend(new Oledrion\Parameters([
                                                                      'start' => 0,
                                                                      'limit' => 0,
                                                                      'sort'  => 'location_id',
                                                                      'order' => 'ASC'
                                                                  ]));
        $critere    = new \CriteriaCompo();
        $critere->add(new \Criteria('location_type', 'parent'));
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
        $critere = new \CriteriaCompo();
        $critere->add(new \Criteria('location_online', 1));
        $critere->add(new \Criteria('location_type', 'location'));
        $critere->add(new \Criteria('location_pid', $id));
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
        $critere = new \CriteriaCompo();
        $critere->add(new \Criteria('location_online', 1));
        $critere->add(new \Criteria('location_type', 'location'));
        $critere->add(new \Criteria('location_pid', $id));
        $location = $this->getCount($critere);

        return $location;
    }
}
