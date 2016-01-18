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
 * @version     $Id: oledrion_delivery_payment.php 12290 2014-02-07 11:05:17Z beckmi $
 */

require 'classheader.php';

class oledrion_delivery_payment extends Oledrion_Object
{
    public function __construct()
    {
        $this->initVar('dp_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('dp_delivery', XOBJ_DTYPE_INT, null, false);
        $this->initVar('dp_payment', XOBJ_DTYPE_INT, null, false);
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

class OledrionOledrion_delivery_paymentHandler extends Oledrion_XoopsPersistableObjectHandler
{
    public function __construct($db)
    { //							              Table					          Classe				    Id
        parent::__construct($db, 'oledrion_delivery_payment', 'oledrion_delivery_payment', 'dp_id');
    }

    public function getDeliveryPaymantId($parameters)
    {
        $ret = array();
        if (!$parameters['delivery']) {
            return $ret;
        }
        $critere = new CriteriaCompo ();
        $critere->add(new Criteria('dp_delivery', $parameters['delivery']));
        $obj = $this->getObjects($critere);
        if ($obj) {
            foreach ($obj as $root) {
                $tab = array();
                $tab = $root->toArray();
                $ret[$root->getVar('dp_payment')] = $tab;
            }
        }

        return $ret;
    }
}
