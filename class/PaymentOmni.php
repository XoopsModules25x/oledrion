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
 * @author      Ahmed Khan (azizabadi@faragostaresh.com)
 *              based on: https://www.startutorial.com/articles/view/a-quick-guide-on-integration-omnipay-on-php-projects
 */

use Omnipay\Common\CreditCard;
use Omnipay\Omnipay;

/**
 * Class Payment
 */
class PaymentOmni
{
    private $pay;
    private $card;

    /**
     * @param $value
     * @return bool|string
     */
    public function setcard($value)
    {
        $card = [
            'number'      => $value['card'],
            'expiryMonth' => $value['expiremonth'],
            'expiryYear'  => $value['expireyear'],
            'cvv'         => $value['cvv'],
        ];

        try {
            $ccard = new CreditCard($card);
            $ccard->validate();
            $this->card = $card;

            return true;
        }
        catch (\Throwable $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * @param $value
     * @return string
     */
    public function makepayment($value)
    {
        try {
            // Setup payment Gateway
            $pay = Omnipay::create('Stripe');
            $pay->setApiKey('YOUR API KEY');
            // Send purchase request
            $response = $pay->purchase([
                                           'amount'   => $value['amount'],
                                           'currency' => $value['currency'],
                                           'card'     => $this->card,
                                       ])->send();

            // Process response
            if ($response->isSuccessful()) {
                return 'Thankyou for your payment';
            }

            if ($response->isRedirect()) {
                // Redirect to offsite payment gateway
                return $response->getMessage();
            }
            // Payment failed
            return $response->getMessage();
        }
        catch (\Throwable $ex) {
            return $ex->getMessage();
        }
    }
}
