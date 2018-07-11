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
 * Class DeliveryPaymentHandler
 */
class DeliveryPaymentHandler extends OledrionPersistableObjectHandler
{
    /**
     * DeliveryPaymentHandler constructor.
     * @param \XoopsDatabase|null $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        //                                          Table                           Classe                    Id
        parent::__construct($db, 'oledrion_delivery_payment', DeliveryPayment::class, 'dp_id');
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
        $critere = new \CriteriaCompo();
        $critere->add(new \Criteria('dp_delivery', $parameters['delivery']));
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
