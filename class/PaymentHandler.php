<?php namespace Xoopsmodules\oledrion;
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

use Xoopsmodules\oledrion;

require_once __DIR__ . '/classheader.php';


/**
 * Class PaymentHandler
 */
class PaymentHandler extends OledrionPersistableObjectHandler
{
    /**
     * PaymentHandler constructor.
     * @param \XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db)
    { //                                       Table                    Classe              Id
        parent::__construct($db, 'oledrion_payment', Payment::class, 'payment_id');
    }

    /**
     * @param  Parameters $parameters
     * @return array
     */
    public function getAllPayment(Parameters $parameters)
    {
        $parameters = $parameters->extend(new oledrion\Parameters([
                                                                      'start' => 0,
                                                                      'limit' => 0,
                                                                      'sort'  => 'payment_id',
                                                                      'order' => 'ASC'
                                                                  ]));
        $critere    = new \Criteria('payment_id', 0, '<>');
        $critere->setLimit($parameters['limit']);
        $critere->setStart($parameters['start']);
        $critere->setSort($parameters['sort']);
        $critere->setOrder($parameters['order']);
        $categories = [];
        $categories = $this->getObjects($critere);

        return $categories;
    }

    /**
     * @param $delivery_id
     * @return array
     */
    public function getThisDeliveryPayment($delivery_id)
    {
        global $deliveryPaymentHandler;
        $ret              = [];
        $parameters       = ['delivery' => $delivery_id];
        $delivery_payment = $deliveryPaymentHandler->getDeliveryPaymantId($parameters);
        foreach ($delivery_payment as $payment) {
            $id[] = $payment['dp_payment'];
        }

        $critere = new \CriteriaCompo();
        $critere->add(new \Criteria('payment_id', '(' . implode(',', $id) . ')', 'IN'));
        $critere->add(new \Criteria('payment_online', 1));
        $obj = $this->getObjects($critere);
        if ($obj) {
            foreach ($obj as $root) {
                $tab   = [];
                $tab   = $root->toArray();
                $ret[] = $tab;
            }
        }

        return $ret;
    }
}
