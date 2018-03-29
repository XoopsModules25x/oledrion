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

require_once __DIR__ . '/classheader.php';


/**
 * Class PackingHandler
 */
class PackingHandler extends OledrionPersistableObjectHandler
{
    /**
     * PackingHandler constructor.
     * @param \XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db)
    { //                                       Table                    Classe              Id
        parent::__construct($db, 'oledrion_packing', Packing::class, 'packing_id');
    }

    /**
     * @param  Parameters $parameters
     * @return array
     */
    public function getAllPacking(Parameters $parameters)
    {
        $parameters = $parameters->extend(new Oledrion\Parameters([
                                                                      'start' => 0,
                                                                      'limit' => 0,
                                                                      'sort'  => 'packing_id',
                                                                      'order' => 'ASC'
                                                                  ]));
        $critere    = new \Criteria('packing_id', 0, '<>');
        $critere->setLimit($parameters['limit']);
        $critere->setStart($parameters['start']);
        $critere->setSort($parameters['sort']);
        $critere->setOrder($parameters['order']);
        $packings = [];
        $packings =& $this->getObjects($critere);

        return $packings;
    }

    /**
     * @return array
     */
    public function getPacking()
    {
        $ret     = [];
        $critere = new \CriteriaCompo();
        $critere->add(new \Criteria('packing_online', '1'));
        $packings =& $this->getObjects($critere);
        foreach ($packings as $root) {
            $tab   = [];
            $tab   = $root->toArray();
            $ret[] = $tab;
        }

        return $ret;
    }
}
