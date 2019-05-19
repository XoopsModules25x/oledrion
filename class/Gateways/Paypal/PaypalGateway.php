<?php

namespace XoopsModules\Oledrion\Gateways\Paypal;

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
 * @author      Hervé Thouzard (http://www.herve-thouzard.com/)
 */

use XoopsModules\Oledrion;
use XoopsModules\Oledrion\Gateways\Gateway;

/**
 * Paypal Gateway
 */
// defined('XOOPS_ROOT_PATH') || die('Restricted access');

class PaypalGateway extends Gateway
{
    /**
     * Paypal constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns information about the payment gateway
     */
    public function setGatewayInformation()
    {
        $gateway                  = [];
        $gateway['name']          = 'Paypal';
        $gateway['foldername']    = 'Paypal';
        $gateway['version']       = '1.1';
        $gateway['description']   = 'PayPal is the safer, easier way to pay and get paid online';
        $gateway['author']        = 'Instant Zero (http://www.herve-thouzard.com/)';
        $gateway['credits']       = 'Hervé Thouzard';
        $gateway['releaseDate']   = 20081215;
        $this->gatewayInformation = $gateway;
    }

    /**
     * Returns the form used to set up the payment gateway
     *
     * @param $postUrl
     * @return \XoopsThemeForm
     */
    public function getParametersForm($postUrl)
    {
        require_once $this->getGatewayLanguageFile();
        $db                     = \XoopsDatabaseFactory::getDatabaseConnection();
        $gatewaysOptionsHandler = new Oledrion\GatewaysOptionsHandler($db);

        $sform = new \XoopsThemeForm(_OLEDRION_PAYPAL_PARAMETERS . ' - ' . $this->gatewayInformation['name'], 'frmPaypal', $postUrl);
        // You must specify the gateway folder's name
        $sform->addElement(new \XoopsFormHidden('gateway', $this->gatewayInformation['foldername']));

        // Paypal email address of the merchant account
        $paypal_email = new \XoopsFormText(_OLEDRION_PAYPAL_EMAIL, 'paypal_email', 50, 255, $gatewaysOptionsHandler->getGatewayOptionValue($this->gatewayInformation['foldername'], 'paypal_email'));
        $paypal_email->setDescription(_OLEDRION_PAYPAL_EMAILDSC);
        $sform->addElement($paypal_email, true);

        // Denomination of currency for Paypal
        $paypal_money = new \XoopsFormSelect(_OLEDRION_PAYPAL_MONEY_P, 'paypal_money', $gatewaysOptionsHandler->getGatewayOptionValue($this->gatewayInformation['foldername'], 'paypal_money'));
        $paypal_money->addOptionArray([
                                          'AUD' => 'Australian Dollar',
                                          'CAD' => 'Canadian Dollar',
                                          'CHF' => 'Swiss Franc',
                                          'CZK' => 'Czech Koruna',
                                          'DKK' => 'Danish Krone',
                                          'EUR' => 'Euro',
                                          'GBP' => 'Pound Sterling',
                                          'HKD' => 'Hong Kong Dollar',
                                          'HUF' => 'Hungarian Forint',
                                          'JPY' => 'Japanese Yen',
                                          'NOK' => 'Norwegian Krone',
                                          'NZD' => 'New Zealand Dollar',
                                          'PLN' => 'Polish Zloty',
                                          'SEK' => 'Swedish Krona',
                                          'SGD' => 'Singapore Dollar',
                                          'USD' => 'U.S. Dollar',
                                      ]);
        $sform->addElement($paypal_money, true);

        // Paypal in test mode ?
        $paypal_test = new \XoopsFormRadioYN(_OLEDRION_PAYPAL_TEST, 'paypal_test', $gatewaysOptionsHandler->getGatewayOptionValue($this->gatewayInformation['foldername'], 'paypal_test'));
        $sform->addElement($paypal_test, true);

        // Forced to true ...
        $sform->addElement(new \XoopsFormHidden('use_ipn', 1));

        $buttonTray = new \XoopsFormElementTray('', '');
        $submit_btn = new \XoopsFormButton('', 'post', _AM_OLEDRION_GATEWAYS_UPDATE, 'submit');
        $buttonTray->addElement($submit_btn);
        $sform->addElement($buttonTray);

        return $sform;
    }

    /**
     * Backing up payment gateway settings
     *
     * @param  array $data The data of the form
     * @return bool The result of the data recording
     */
    public function saveParametersForm($data)
    {
        $db                     = \XoopsDatabaseFactory::getDatabaseConnection();
        $gatewaysOptionsHandler = new Oledrion\GatewaysOptionsHandler($db);
        $parameters             = ['paypal_email', 'paypal_money', 'paypal_test', 'use_ipn'];
        // We start by deleting the current values
        $gatewayName = $this->gatewayInformation['foldername'];
        $gatewaysOptionsHandler->deleteGatewayOptions($gatewayName);
        foreach ($parameters as $parameter) {
            if (!$gatewaysOptionsHandler->setGatewayOptionValue($gatewayName, $parameter, $data[$parameter])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Formats the amount in Paypal format
     * @param $amount
     * @return string
     */
    private function formatAmount($amount)
    {
        return number_format($amount, 2, '.', '');
    }

    /**
     * Returns the url to which to redirect the user for online payment
     *
     * @param $cmd_total
     * @param $cmd_id
     * @return string
     */
    public function getRedirectURL($cmd_total, $cmd_id)
    {
        $db                     = \XoopsDatabaseFactory::getDatabaseConnection();
        $gatewaysOptionsHandler = new Oledrion\GatewaysOptionsHandler($db);
        $test_mode              = (int)$gatewaysOptionsHandler->getGatewayOptionValue($this->gatewayInformation['foldername'], 'paypal_test');
        if (1 === $test_mode) {
            return 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        }

        return 'https://www.paypal.com/cgi-bin/webscr';
    }

    /**
     * Returns the elements to add to the form as hidden areas
     *
     * @param array $order The sales order
     * @param       array
     * @return array
     */
    public function getCheckoutFormContent($order)
    {
        //        global $xoopsConfig;
        $db                     = \XoopsDatabaseFactory::getDatabaseConnection();
        $gatewaysOptionsHandler = new Oledrion\GatewaysOptionsHandler($db);
        $gatewayName            = $this->gatewayInformation['foldername'];
        $paypal_money           = $gatewaysOptionsHandler->getGatewayOptionValue($gatewayName, 'paypal_money');
        $paypal_email           = $gatewaysOptionsHandler->getGatewayOptionValue($gatewayName, 'paypal_email');
        $use_ipn                = (int)$gatewaysOptionsHandler->getGatewayOptionValue($gatewayName, 'use_ipn');

        // B.R. Start
        // Need array of product_id's for optional DB update
        $caddyHandler = new Oledrion\CaddyHandler($db);
        $caddy        = $caddyHandler->getCaddyFromCommand($order->getVar('cmd_id'));
        $products     = [];
        foreach ($caddy as $item) {
            $products[] = $item->getVar('caddy_product_id');
        }
        $product_ids = implode(',', $products);
        // B.R. End

        $ret                     = [];
        $ret['cmd']              = '_xclick';
        $ret['upload']           = '1';
        $ret['currency_code']    = $paypal_money;
        $ret['business']         = $paypal_email;
        $ret['return']           = OLEDRION_URL . 'thankyou.php'; // (Generic) thank you page after payment
        $ret['image_url']        = XOOPS_URL . '/images/logo.gif';
        $ret['cpp_header_image'] = XOOPS_URL . '/images/logo.gif';
        $ret['invoice']          = $order->getVar('cmd_id');

        // B.R. $ret['item_name']        = _OLEDRION_COMMAND . $order->getVar('cmd_id') . ' - ' . Oledrion\Utility::makeHrefTitle($xoopsConfig['sitename']);
        // B.R. Start
        $ret['item_name'] = $product_ids;
        // B.R. End

        $ret['item_number'] = $order->getVar('cmd_id');
        $ret['tax']         = 0; // added 25/03/2008
        $ret['amount']      = $this->formatAmount((float)$order->getVar('cmd_total', 'n'));
        $ret['custom']      = $order->getVar('cmd_id');
        //$ret['rm'] = 2;   // Resend data by POST (normally)
        $ret['email'] = $order->getVar('cmd_email');
        if ('' !== xoops_trim($order->getVar('cmd_cancel'))) {
            // URL to which the client's browser is brought back if the payment is canceled
            $ret['cancel_return'] = OLEDRION_URL . 'cancel-payment.php?id=' . $order->getVar('cmd_cancel');
        }
        if (1 === $use_ipn) {
            $ret['notify_url'] = OLEDRION_URL . 'gateway-notify.php'; // paypal-notify.php
        }

        return $ret;
    }

    /**
     * Returns the list of countries to use in the customer information entry form (checkout.php)
     *
     * @return array
     */
    public function getCountriesList()
    {
        require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';

        return \XoopsLists::getCountryList();
    }

    /**
     * Used during the dialog with Paypal in the case of the use of the IPN
     * Note : Specific Paypal
     *
     * @return string The URL at Paypal to call for information
     */
    private function getDialogURL()
    {
        $db                     = \XoopsDatabaseFactory::getDatabaseConnection();
        $gatewaysOptionsHandler = new Oledrion\GatewaysOptionsHandler($db);
        $test_mode              = (int)$gatewaysOptionsHandler->getGatewayOptionValue($this->gatewayInformation['foldername'], 'paypal_test');
        if (1 === $test_mode) {
            return 'www.sandbox.paypal.com';
        }

        return 'www.paypal.com';
    }

    /**
     * Dialogue with the payment gateway to indicate the status of the order
     * The caller is responsible for checking that the log file exists
     *
     * @param  string $gatewaysLogPath The full path to the log file
     */
    public function gatewayNotify($gatewaysLogPath)
    {
        $db                     = \XoopsDatabaseFactory::getDatabaseConnection();
        $gatewaysOptionsHandler = new Oledrion\GatewaysOptionsHandler($db);
        $commandsHandler        = new Oledrion\CommandsHandler($db);
        $executionStartTime     = microtime(true);
        error_reporting(0);
        @$xoopsLogger->activated = false;

        $log     = '';
        $req     = 'cmd=_notify-validate';
        $slashes = get_magic_quotes_gpc();
        foreach ($_POST as $key => $value) {
            if ($slashes) {
                $log   .= "$key=" . stripslashes($value) . "\n";
                $value = urlencode(stripslashes($value));
            } else {
                $log   .= "$key=" . $value . "\n";
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
        }
        $url          = $this->getDialogURL();
        $gatewayName  = $this->gatewayInformation['foldername'];
        $paypal_email = $gatewaysOptionsHandler->getGatewayOptionValue($gatewayName, 'paypal_email');
        $paypal_money = $gatewaysOptionsHandler->getGatewayOptionValue($gatewayName, 'paypal_money');
        $header       = '';
        $header       .= "POST /cgi-bin/webscr HTTP/1.1\r\n";
        $header       .= "Host: $url\r\n";
        $header       .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header       .= 'Content-Length: ' . mb_strlen($req) . "\r\n\r\n";
        $errno        = 0;
        $errstr       = '';
        $fp           = fsockopen("ssl://$url", 443, $errno, $errstr, 30);
        if ($fp) {
            fwrite($fp, "$header$req");
            while (!feof($fp)) {
                $res = fgets($fp, 1024);
                if (0 === strcmp(trim($res), 'VERIFIED')) {
                    $log      .= "PAYPAL VERIFIED\n";
                    $paypalok = true;
                    if ('COMPLETED' !== mb_strtoupper($_POST['payment_status'])) {
                        $paypalok = false;
                    }
                    if (mb_strtoupper($_POST['receiver_email']) != mb_strtoupper($paypal_email)) {
                        $paypalok = false;
                    }
                    if (mb_strtoupper($_POST['mc_currency']) != mb_strtoupper($paypal_money)) {
                        $paypalok = false;
                    }
                    if (!$_POST['custom']) {
                        $paypalok = false;
                    }
                    $montant = $_POST['mc_gross'];

                    //R.B. start
                    $ref      = (int)$_POST['custom']; // Order number
                    $commande = null;
                    $commande = $commandsHandler->get($ref);

                    if (!is_object($commande)) {
                        // TODO: Why is this failing?
                        // TODO: Is there a more appropriate response code?
                        //header("HTTP/1.1 500 Internal Server Error");
                        http_response_code(500);
                        $log .= sprintf("not_object: %d\n", $ref);
                        file_put_contents($gatewaysLogPath, $log, FILE_APPEND | LOCK_EX);

                        return;
                    }
                    //R.B. end
                    $pid = pcntl_fork();
                    switch ($pid) {
                        case -1:
                            die('could not fork');
                            break;
                        case 0:
                            // In the new (child) process

                            // At this point, all PayPal session variables collected, done Paypal session
                            // Rest of transaction can be processed offline to decouple site load from Paypal transaction time
                            // PayPal requires this session to return within 30 seconds, or will retry
                            $PayPalEndTime = microtime(true);
                            if ($paypalok) {
                                /* R.B. start
                                                                $ref      = \Xmf\Request::getInt('custom', 0, 'POST'); // Numéro de la commande
                                                                $commande = null;
                                                                $commande = $commandsHandler->get($ref);
                                                                if (is_object($commande)) {
                                                                 */ //R.B. end

                                if ($montant == $commande->getVar('cmd_total')) {
                                    // Verified order
                                    $email_name = sprintf('%s/%d%s', OLEDRION_UPLOAD_PATH, $commande->getVar('cmd_id'), OLEDRION_CONFIRMATION_EMAIL_FILENAME_SUFFIX);
                                    if (file_exists($email_name)) {
                                        $commandsHandler->validateOrder($commande); // Validation of the order and inventory update
                                        $msg = [];
                                        $msg = unserialize(file_get_contents($email_name));
                                        // Add Transaction ID variable to email variables for templates
                                        $msg['TRANSACTION_ID'] = $_POST['txn_id'];
                                        // Send confirmation email to user
                                        $email_address = $commande->getVar('cmd_email');
                                        Oledrion\Utility::sendEmailFromTpl('command_client.tpl', $email_address, sprintf(_OLEDRION_THANKYOU_CMD, $xoopsConfig['sitename']), $msg);
                                        // Send mail to admin
                                        Oledrion\Utility::sendEmailFromTpl('command_shop.tpl', Oledrion\Utility::getEmailsFromGroup(Oledrion\Utility::getModuleOption('grp_sold')), _OLEDRION_NEW_COMMAND, $msg);

                                        //R.B. start
                                        // TODO: add transaction ID to SMS and online user invoice
                                        // Update user database
                                        if (file_exists(OLEDRION_DB_UPDATE_SCRIPT)) {
                                            include OLEDRION_DB_UPDATE_SCRIPT;
                                            $product_ids = $_POST['item_name'];
                                            $products    = [];
                                            $products    = explode(',', $product_ids);
                                            foreach ($products as $item) {
                                                $product_id = $item;
                                                // updateDB($product_id, $user_id, $transaction_id);
                                                $log .= updateDB($product_id, $_POST['receiver_email'], $_POST['txn_id']);
                                            }
                                        }
                                        //R.B. end

                                        if (false === @unlink($email_name)) {
                                            throw new \RuntimeException('The file ' . $email_name . ' could not be deleted.');
                                        }
                                    } else {
                                        $duplicate_ipn = 1;
                                    }
                                } else {
                                    $commandsHandler->setFraudulentOrder($commande);
                                }
                            } else {
                                //R.B. start
                                // $log .= "not_object\n";
                                //  }
                                // } else {
                                //R.B. end
                                $log .= "paypal not OK\n";
                                if (\Xmf\Request::hasVar('custom', 'POST')) {
                                    // R.B. start
                                    // $ref      = \Xmf\Request::getInt('custom', 0, 'POST');
                                    // $commande = null;
                                    // $commande = $commandsHandler->get($ref);
                                    // if (is_object($commande)) {
                                    //R.B. end
                                    switch (mb_strtoupper($_POST['payment_status'])) {
                                        case 'PENDING':
                                            $commandsHandler->setOrderPending($commande);
                                            break;
                                        case 'FAILED':
                                            $commandsHandler->setOrderFailed($commande);
                                            break;
                                        // R.B. }
                                    }
                                }
                            }
                            // Write to the log file
                            $logfp = fopen($gatewaysLogPath, 'ab');
                            if ($logfp) {
                                if ($duplicate_ipn) {
                                    fwrite($logfp, sprintf("Duplicate paypal IPN, order: %d\n", $commande->getVar('cmd_id')));
                                } else {
                                    fwrite($logfp, str_repeat('-', 120) . "\n");
                                    fwrite($logfp, date('d/m/Y H:i:s') . "\n");
                                    if (\Xmf\Request::hasVar('txn_id', 'POST')) {
                                        fwrite($logfp, 'Transaction : ' . $_POST['txn_id'] . "\n");
                                    }
                                    fwrite($logfp, 'Result : ' . $log . "\n");
                                }
                                $executionEndTime = microtime(true);
                                $PayPalSeconds    = $PayPalEndTime - $executionStartTime;
                                $TotalSeconds     = $executionEndTime - $executionStartTime;
                                fwrite($logfp, "Paypal session took $PayPalSeconds, Total transaction took $TotalSeconds seconds.\n");
                                fclose($logfp);
                            }

                            break;
                        default:
                            // In the main (parent) process in which the script is running

                            // At this point, all PayPal session variables collected, done Paypal session
                            // Rest of transaction can be proccessed offline to decouple Paypal transaction time from site load
                            // PayPal requires this session to return within 30 seconds, or will retry

                            return;
                            break;
                    }
                } else {
                    $log .= "$res\n";
                }
            }
            fclose($fp);
        } else {
            $errtext = "Error with the fsockopen function, unable to open communication ' : ($errno) $errstr\n";
            file_put_contents($gatewaysLogPath, $errtext, FILE_APPEND | LOCK_EX);
        }
    }
}
