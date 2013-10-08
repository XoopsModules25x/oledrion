<?php
if (!defined('XOOPS_ROOT_PATH')) {
    die("XOOPS root path not defined");
}
//require_once('nusoap.php');

class oledrion_pec24 extends oledrion_gateway
{
    function __construct()
    {
        parent::__construct();
    }

    function setGatewayInformation()
    {
        $gateway = array();
        $gateway['name'] = 'Parsian';
        $gateway['foldername'] = 'pec24';
        $gateway['version'] = '1.0';
        $gateway['description'] = "سيستم پرداخت الکترونيک بانک پارسیان";
        $gateway['author'] = "Hossein Azizabadi";
        $gateway['credits'] = "جسین عزیزآبادی";
        $gateway['releaseDate'] = 20121020;
        $this->gatewayInformation = $gateway;
    }

    function getParametersForm($postUrl)
    {
        $sform = new XoopsThemeForm(_OLEDRION_PARSIAN_PARAMETERS . ' - ' . $this->gatewayInformation['name'], 'frmParsian', $postUrl);
        $sform->addElement(new XoopsFormHidden('gateway', $this->gatewayInformation['foldername']));
        $pin = new XoopsFormText(_OLEDRION_PARSIAN_MID, 'parsian_mid', 50, 255, $this->handlers->h_oledrion_gateways_options->getGatewayOptionValue($this->gatewayInformation['foldername'], 'parsian_mid'));
        $pin->setDescription(_OLEDRION_PARSIAN_MIDDSC);
        $sform->addElement($pin, true);
        $button_tray = new XoopsFormElementTray('', '');
        $submit_btn = new XoopsFormButton('', 'post', _AM_OLEDRION_GATEWAYS_UPDATE, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        return $sform;
    }

    function saveParametersForm($data)
    {
        if (xoops_trim($this->languageFilename) != '' && file_exists($this->languageFilename)) {
            require $this->languageFilename;
        }
        $gatewayName = $this->gatewayInformation['foldername'];
        $this->handlers->h_oledrion_gateways_options->deleteGatewayOptions($gatewayName);
        if (!$this->handlers->h_oledrion_gateways_options->setGatewayOptionValue($gatewayName, 'parsian_mid', $data['parsian_mid'])) return false;
        return true;
    }

    private function formatAmount($amount)
    {
        return number_format($amount, 2, '.', '');
    }

    function getAuthority($cmd_total, $cmd_id)
    {
        $url = $this->getdialogURL();
        if (extension_loaded('soap')) {
            $soapclient = new SoapClient($url);
        } else {
            require_once('nusoap.php');
            $soapclient = new soapclient($url, 'wsdl');
        }
        $params = array(
            'pin' => $this->getParsianMid(),
            'amount' => intval($this->formatAmount($cmd_total)),
            'orderId' => intval($cmd_id),
            'callbackUrl' => OLEDRION_URL . 'gateway-notify.php?cmd_id=' . intval($cmd_id) . '&cmd_total=' . intval($this->formatAmount($cmd_total)),
            'authority' => 0,
            'status' => 1
        );
        $sendParams = array($params);
        //$res = $soapclient->call('PinPaymentRequest', $sendParams);
        //return $res['authority'];
    }

    function getParsianMid()
    {
        global $xoopsConfig;
        $gatewayName = $this->gatewayInformation['foldername'];
        $parsian_mid = $this->handlers->h_oledrion_gateways_options->getGatewayOptionValue($gatewayName, 'parsian_mid');
        return $parsian_mid;
    }

    function getRedirectURL($cmd_total, $cmd_id)
    {
        $authority = $this->getAuthority($cmd_total, $cmd_id);
        return "https://www.pecco24.com:27635/pecpaymentgateway/?au=" . $authority;
    }

    function getCheckoutFormContent($order)
    {
        $ret = array();
        $ret['pin'] = $this->getParsianMid();
        $ret['amount'] = intval($this->formatAmount($order->getVar('cmd_total')));
        $ret['orderId'] = $order->getVar('cmd_id');
        $ret['callbackUrl'] = OLEDRION_URL . 'gateway-notify.php?cmd_id=' . $order->getVar('cmd_id') . '&cmd_total=' . intval($this->formatAmount($order->getVar('cmd_total')));
        $ret['authority'] = 0;
        $ret['status'] = 1;
        return $ret;
    }

    function getCountriesList()
    {
        require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
        return XoopsLists::getCountryList();
    }

    private function getdialogURL()
    {
        return 'https://www.pecco24.com:27635/pecpaymentgateway/eshopservice.asmx?wsdl';
    }

    function gatewayNotify($gatewaysLogPath)
    {
        // Get from bank
        $authority = $_GET['au'];
        $status = $_GET['rs'];
        $cmd_id = intval($_GET['cmd_id']);
        $cmd_total = intval($_GET['cmd_total']);
        // Set soap
        $url = $this->getdialogURL();
        if (extension_loaded('soap')) {
            $soapclient = new SoapClient($url);
        } else {
            require_once('nusoap.php');
            $soapclient = new soapclient($url, 'wsdl');
        }
        // here we update our database
        $save_ok = 0;
        if ($authority) {
            $save_ok = 1;
        }
        // doing
        if (($status == 0) && $save_ok) {
            if ((!$soapclient) || ($err = $soapclient->getError())) {
                // this is unsucccessfull connection
                $commande = null;
                $commande = $this->handlers->h_oledrion_commands->get($cmd_id);
                if (is_object($commande)) {
                    $this->handlers->h_oledrion_commands->setOrderFailed($commande);
                    $user_log = 'خطا در پرداخت - خطا در ارتباط با بانک';
                } else {
                    $this->handlers->h_oledrion_commands->setFraudulentOrder($commande);
                    $user_log = 'خطا در ارتباط با بانک - اطلاعات پرداخت شما نا معتبر است';
                }
            } else {
                //$status = 1;
                $params = array(
                    'pin' => $this->getParsianMid(),
                    'authority' => $authority,
                    'status' => $status,
                );
                $sendParams = array($params);
                $res = $soapclient->call('PinPaymentEnquiry', $sendParams);
                $status = $res['status'];
                if ($status == 0) {
                    // this is a succcessfull payment
                    // we update our DataBase
                    $commande = null;
                    $commande = $this->handlers->h_oledrion_commands->get($cmd_id);
                    if (is_object($commande)) {
                        if ($cmd_total == intval($commande->getVar('cmd_total'))) {
                            $this->handlers->h_oledrion_commands->validateOrder($commande);
                            $user_log = 'پرداخت شما با موفقیت انجام شد. محصول برای شما ارسال می شود';
                        } else {
                            $this->handlers->h_oledrion_commands->setFraudulentOrder($commande);
                            $user_log = 'اطلاعات پرداخت شما نا معتبر است';
                        }
                    }
                    $log .= "VERIFIED\t";
                } else {
                    // this is a UNsucccessfull payment
                    // we update our DataBase
                    $commande = null;
                    $commande = $this->handlers->h_oledrion_commands->get($cmd_id);
                    if (is_object($commande)) {
                        $this->handlers->h_oledrion_commands->setOrderFailed($commande);
                        $user_log = 'خطا در پرداخت - وضعیت این پرداخت صحیح نیست';
                    } else {
                        $this->handlers->h_oledrion_commands->setFraudulentOrder($commande);
                        $user_log = 'وضعیت این پرداخت صحیح نیست - اطلاعات پرداخت شما نا معتبر است';
                    }
                    $log .= "$status\n";
                }
            }
        } else {
            // this is a UNsucccessfull payment
            $commande = null;
            $commande = $this->handlers->h_oledrion_commands->get($cmd_id);
            if (is_object($commande)) {
                $this->handlers->h_oledrion_commands->setOrderFailed($commande);
                $user_log = 'خطا در پرداخت - این پرداخت نا معتبر است';
            } else {
                $this->handlers->h_oledrion_commands->setFraudulentOrder($commande);
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
                fwrite($fp, "Transaction : " . $status . "\n");
            }
            fwrite($fp, "Result : " . $log . "\n");
            fwrite($fp, "Peyment note : " . $user_log . "\n");
            fclose($fp);
        }
        return $user_log;
    }
}
