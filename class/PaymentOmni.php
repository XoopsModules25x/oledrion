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
 * @author      Ahmed Khan (azizabadi@faragostaresh.com)
 *              based on: https://www.startutorial.com/articles/view/a-quick-guide-on-integration-omnipay-on-php-projects
 */

use Omnipay\Omnipay;
use Omnipay\Common\CreditCard;

class Payment
{
    private $pay;
    private $card;
    public function setcard($value)
    {
        try {
            $card = [
                'number' => $value['card'],
                'expiryMonth' => $value['expiremonth'],
                'expiryYear' => $value['expireyear'],
                'cvv' => $value['cvv']
            ];
            $ccard = new CreditCard($card);
            $ccard->validate();
            $this->card = $card;
            return true;
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function makepayment($value)
    {
        try {

            // Setup payment Gateway
            $pay = Omnipay::create('Stripe');
            $pay->setApiKey('YOUR API KEY');
            // Send purchase request
            $response = $pay->purchase(
                [
                    'amount' => $value['amount'],
                    'currency' => $value['currency'],
                    'card' => $this->card
                ]
            )->send();

            // Process response
            if ($response->isSuccessful()) {
                return 'Thankyou for your payment';
            } elseif ($response->isRedirect()) {

                // Redirect to offsite payment gateway
                return $response->getMessage();
            } else {
                // Payment failed
                return $response->getMessage();
            }
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }
}
