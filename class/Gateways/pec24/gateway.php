<?php namespace XoopsModules\Oledrion\Gateways\pec24;

// defined('XOOPS_ROOT_PATH') || die('Restricted access');
//require_once('nusoap.php');

use XoopsModules\Oledrion;
use XoopsModules\Oledrion\Constants;
use XoopsModules\Oledrion\Gateways\Gateway;

/**
 * Class Pec24
 */
class Pec24 extends Gateway
{
    /**
     * Pec24 constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function setGatewayInformation()
    {
        $gateway                  = [];
        $gateway['name']          = 'Parsian';
        $gateway['foldername']    = 'pec24';
        $gateway['version']       = '1.0';
        $gateway['description']   = 'سيستم پرداخت الکترونيک بانک پارسیان';
        $gateway['author']        = 'Hossein Azizabadi';
        $gateway['credits']       = 'جسین عزیزآبادی';
        $gateway['releaseDate']   = 20121020;
        $this->gatewayInformation = $gateway;
    }

    /**
     * This method is called to display a form containing the gateways parameters.
     * You must return a XoopsThemeForm and this form MUST use the post method.
     * The module is in charge to load your defines before to call this method and
     * it loads xoopsformloader.php
     *
     * If your gateway does not requires parameters, then you must return false
     *
     * @param $postUrl
     * @return mixed
     * @internal param string $posstUrl The url to use to post data to
     */
    public function getParametersForm($postUrl)
    {
        $db = \XoopsDatabaseFactory::getDatabaseConnection();
        $gatewaysOptionsHandler = new Oledrion\GatewaysOptionsHandler($db);
        $sform = new \XoopsThemeForm(_OLEDRION_PARSIAN_PARAMETERS . ' - ' . $this->gatewayInformation['name'], 'frmParsian', $postUrl);
        $sform->addElement(new \XoopsFormHidden('gateway', $this->gatewayInformation['foldername']));
        $pin = new \XoopsFormText(_OLEDRION_PARSIAN_MID, 'parsian_mid', 50, 255, $gatewaysOptionsHandler->getGatewayOptionValue($this->gatewayInformation['foldername'], 'parsian_mid'));
        $pin->setDescription(_OLEDRION_PARSIAN_MIDDSC);
        $sform->addElement($pin, true);
        $button_tray = new \XoopsFormElementTray('', '');
        $submit_btn  = new \XoopsFormButton('', 'post', _AM_OLEDRION_GATEWAYS_UPDATE, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);

        return $sform;
    }

    /**
     * This method is called by the module to save the gateway's parameters
     * It's up to you to verify data and eventually to complain about uncomplete or missing data
     *
     * @param  array $data Receives $_POST
     * @return boolean True if you succeed to save data else false
     */
    public function saveParametersForm($data)
    {
        if ('' !== xoops_trim($this->languageFilename) && file_exists($this->languageFilename)) {
            require $this->languageFilename;
        }
        $db = \XoopsDatabaseFactory::getDatabaseConnection();
        $gatewaysOptionsHandler = new Oledrion\GatewaysOptionsHandler($db);
        $gatewayName = $this->gatewayInformation['foldername'];
        $gatewaysOptionsHandler->deleteGatewayOptions($gatewayName);
        if (!$gatewaysOptionsHandler->setGatewayOptionValue($gatewayName, 'parsian_mid', $data['parsian_mid'])) {
            return false;
        }

        return true;
    }

    /**
     * @param $amount
     * @return string
     */
    private function formatAmount($amount)
    {
        return number_format($amount, 2, '.', '');
    }

    /**
     * @param $cmd_total
     * @param $cmd_id
     */
    public function getAuthority($cmd_total, $cmd_id)
    {
        $url = $this->getdialogURL();
        if (extension_loaded('soap')) {
            $soapclient = new Soapclient($url);
        } else {
            require_once __DIR__ . '/nusoap.php';
            $soapclient = new Soapclient($url, 'wsdl');
        }
        $params     = [
            'pin'         => $this->getParsianMid(),
            'amount'      => (int)$this->formatAmount($cmd_total),
            'orderId'     => (int)$cmd_id,
            'callbackUrl' => OLEDRION_URL . 'gateway-notify.php?cmd_id=' . (int)$cmd_id . '&cmd_total=' . (int)$this->formatAmount($cmd_total),
            'authority'   => 0,
            'status'      => 1
        ];
        $sendParams = [$params];
        //$res = $soapclient->call('PinPaymentRequest', $sendParams);
        //return $res['authority'];
    }

    /**
     * @return mixed
     */
    public function getParsianMid()
    {
        $db = \XoopsDatabaseFactory::getDatabaseConnection();
        $gatewaysOptionsHandler = new Oledrion\GatewaysOptionsHandler($db);
        global $xoopsConfig;
        $gatewayName = $this->gatewayInformation['foldername'];
        $parsian_mid = $gatewaysOptionsHandler->getGatewayOptionValue($gatewayName, 'parsian_mid');

        return $parsian_mid;
    }

    /**
     * Returns the URL to redirect user to (for paying)
     * @param $cmd_total
     * @param $cmd_id
     * @return string
     */
    public function getRedirectURL($cmd_total, $cmd_id)
    {
        $authority = $this->getAuthority($cmd_total, $cmd_id);

        return 'https://www.pecco24.com:27635/pecpaymentgateway/?au=' . $authority;
    }

    /**
     * Returns the form to use before to redirect user to the gateway
     *
     * @param  Commands $order Objects of type Commands
     * @return array  Key = element's name, Value = Element's value
     */
    public function getCheckoutFormContent($order)
    {
        $ret                = [];
        $ret['pin']         = $this->getParsianMid();
        $ret['amount']      = (int)$this->formatAmount($order->getVar('cmd_total'));
        $ret['orderId']     = $order->getVar('cmd_id');
        $ret['callbackUrl'] = OLEDRION_URL . 'gateway-notify.php?cmd_id=' . $order->getVar('cmd_id') . '&cmd_total=' . (int)$this->formatAmount($order->getVar('cmd_total'));
        $ret['authority']   = 0;
        $ret['status']      = 1;

        return $ret;
    }

    /**
     * Returns the list of countries codes used by the gateways
     *
     */
    public function getCountriesList()
    {
        require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';

        return \XoopsLists::getCountryList();
    }

    /**
     * @return string
     */
    private function getdialogURL()
    {
        return 'https://www.pecco24.com:27635/pecpaymentgateway/eshopservice.asmx?wsdl';
    }

    /**
     * This method is in charge to dialog with the gateway to verify the payment's statuts
     *
     * @param  string $gatewaysLogPath The full path (and name) to the log file
     * @return void
     */
    public function gatewayNotify($gatewaysLogPath)
    {
        $db = \XoopsDatabaseFactory::getDatabaseConnection();
        $commandsHandler = new Oledrion\CommandsHandler($db);
        // Get from bank
        $authority = $_GET['au'];
        $status    = $_GET['rs'];
        $cmd_id    = \Xmf\Request::getInt('cmd_id', 0, 'GET');
        $cmd_total = \Xmf\Request::getInt('cmd_total', 0, 'GET');
        // Set soap
        $url = $this->getdialogURL();
        if (extension_loaded('soap')) {
            $soapclient = new SoapClient($url);
        } else {
            require_once __DIR__ . '/nusoap.php';
            $soapclient = new soapclient($url, 'wsdl');
        }
        // here we update our database
        $save_ok = 0;
        if ($authority) {
            $save_ok = 1;
        }
        // doing
        if ((0 == $status) && $save_ok) {
            if ((!$soapclient) || ($err = $soapclient->getError())) {
                // this is unsucccessfull connection
                $commande = null;
                $commande = $commandsHandler->get($cmd_id);
                if (is_object($commande)) {
                    $commandsHandler->setOrderFailed($commande);
                    $user_log = 'خطا در پرداخت - خطا در ارتباط با بانک';
                } else {
                    $commandsHandler->setFraudulentOrder($commande);
                    $user_log = 'خطا در ارتباط با بانک - اطلاعات پرداخت شما نا معتبر است';
                }
            } else {
                //$status = 1;
                $params     = [
                    'pin'       => $this->getParsianMid(),
                    'authority' => $authority,
                    'status'    => $status
                ];
                $sendParams = [$params];
                $res        = $soapclient->call('PinPaymentEnquiry', $sendParams);
                $status     = $res['status'];
                if (0 == $status) {
                    // this is a succcessfull payment
                    // we update our DataBase
                    $commande = null;
                    $commande = $commandsHandler->get($cmd_id);
                    if (is_object($commande)) {
                        if ($cmd_total == (int)$commande->getVar('cmd_total')) {
                            $commandsHandler->validateOrder($commande);
                            $user_log = 'پرداخت شما با موفقیت انجام شد. محصول برای شما ارسال می شود';
                        } else {
                            $commandsHandler->setFraudulentOrder($commande);
                            $user_log = 'اطلاعات پرداخت شما نا معتبر است';
                        }
                    }
                    $log .= "VERIFIED\t";
                } else {
                    // this is a UNsucccessfull payment
                    // we update our DataBase
                    $commande = null;
                    $commande = $commandsHandler->get($cmd_id);
                    if (is_object($commande)) {
                        $commandsHandler->setOrderFailed($commande);
                        $user_log = 'خطا در پرداخت - وضعیت این پرداخت صحیح نیست';
                    } else {
                        $commandsHandler->setFraudulentOrder($commande);
                        $user_log = 'وضعیت این پرداخت صحیح نیست - اطلاعات پرداخت شما نا معتبر است';
                    }
                    $log .= "$status\n";
                }
            }
        } else {
            // this is a UNsucccessfull payment
            $commande = null;
            $commande = $commandsHandler->get($cmd_id);
            if (is_object($commande)) {
                $commandsHandler->setOrderFailed($commande);
                $user_log = 'خطا در پرداخت - این پرداخت نا معتبر است';
            } else {
                $commandsHandler->setFraudulentOrder($commande);
                $user_log = 'این پرداخت نا معتبر است - اطلاعات پرداخت شما نا معتبر است';
            }
            $log .= "$status\n";
        }

        // Ecriture dans le fichier log
        $fp = fopen($gatewaysLogPath, 'a');
        if ($fp) {
            fwrite($fp, str_repeat('-', 120) . "\n");
            fwrite($fp, date('d/m/Y H:i:s') . "\n");
            if (isset($status)) {
                fwrite($fp, 'Transaction : ' . $status . "\n");
            }
            fwrite($fp, 'Result : ' . $log . "\n");
            fwrite($fp, 'Peyment note : ' . $user_log . "\n");
            fclose($fp);
        }

        return $user_log;
    }
}
