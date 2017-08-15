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
 * Class Oledrion_delivery_payment
 */
class Oledrion_delivery_payment extends Oledrion_Object
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
        $ret = [];
        $ret = parent::toArray($format);

        return $ret;
    }
}

/**
 * Class OledrionOledrion_delivery_paymentHandler
 */
class OledrionOledrion_delivery_paymentHandler extends Oledrion_XoopsPersistableObjectHandler
{
    /**
     * OledrionOledrion_delivery_paymentHandler constructor.
     * @param XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    { //                                          Table                           Classe                    Id
        parent::__construct($db, 'oledrion_delivery_payment', 'oledrion_delivery_payment', 'dp_id');
    }

    /**
     * @param $parameters
     * @return array
     */
    public function getDeliveryPaymantId($parameters)
    {
        $ret = [];
        if (!$parameters['delivery']) {
            return $ret;
        }
        $critere = new CriteriaCompo();
        $critere->add(new Criteria('dp_delivery', $parameters['delivery']));
        $obj = $this->getObjects($critere);
        if ($obj) {
            foreach ($obj as $root) {
                $tab                              = [];
                $tab                              = $root->toArray();
                $ret[$root->getVar('dp_payment')] = $tab;
            }
        }

        return $ret;
    }
}
