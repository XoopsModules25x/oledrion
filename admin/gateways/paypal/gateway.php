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
 * @author      Hervé Thouzard (http://www.herve-thouzard.com/)
 */

/**
 * Paypal Gateway
 */
// defined('XOOPS_ROOT_PATH') || exit('Restricted access.');

class Oledrion_paypal extends Oledrion_gateway
{
    /**
     * Oledrion_paypal constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Retourne des informations sur la passerelle de paiement
     *
     * @return array
     */
    public function setGatewayInformation()
    {
        $gateway                  = [];
        $gateway['name']          = 'Paypal';
        $gateway['foldername']    = 'paypal';
        $gateway['version']       = '1.1';
        $gateway['description']   = 'PayPal is the safer, easier way to pay and get paid online';
        $gateway['author']        = 'Instant Zero (http://www.herve-thouzard.com/)';
        $gateway['credits']       = 'Hervé Thouzard';
        $gateway['releaseDate']   = 20081215;
        $this->gatewayInformation = $gateway;
    }

    /**
     * Retourne le formulaire utilisé pour paramétrer la passerelle de paiement
     *
     * @param $postUrl
     * @return XoopsThemeForm
     */
    public function getParametersForm($postUrl)
    {
        require $this->getGatewayLanguageFile();

        $sform = new XoopsThemeForm(_OLEDRION_PAYPAL_PARAMETERS . ' - ' . $this->gatewayInformation['name'], 'frmPaypal', $postUrl);
        // You must specify the gateway folder's name
        $sform->addElement(new XoopsFormHidden('gateway', $this->gatewayInformation['foldername']));

        // Adresse email Paypal du compte marchand
        $paypal_email = new XoopsFormText(_OLEDRION_PAYPAL_EMAIL, 'paypal_email', 50, 255, $this->handlers->h_oledrion_gateways_options->getGatewayOptionValue($this->gatewayInformation['foldername'], 'paypal_email'));
        $paypal_email->setDescription(_OLEDRION_PAYPAL_EMAILDSC);
        $sform->addElement($paypal_email, true);

        // Libellé de la monnaie pour Paypal
        $paypal_money = new XoopsFormSelect(_OLEDRION_PAYPAL_MONEY_P, 'paypal_money', $this->handlers->h_oledrion_gateways_options->getGatewayOptionValue($this->gatewayInformation['foldername'], 'paypal_money'));
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
                                          'USD' => 'U.S. Dollar'
                                      ]);
        $sform->addElement($paypal_money, true);

        // Paypal en mode test ?
        $paypal_test = new XoopsFormRadioYN(_OLEDRION_PAYPAL_TEST, 'paypal_test', $this->handlers->h_oledrion_gateways_options->getGatewayOptionValue($this->gatewayInformation['foldername'], 'paypal_test'));
        $sform->addElement($paypal_test, true);

        // Forcé à vrai ...
        $sform->addElement(new XoopsFormHidden('use_ipn', 1));

        $button_tray = new XoopsFormElementTray('', '');
        $submit_btn  = new XoopsFormButton('', 'post', _AM_OLEDRION_GATEWAYS_UPDATE, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);

        return $sform;
    }

    /**
     * Sauvegarde des paramètres de la passerelle de paiement
     *
     * @param  array $data Les données du formulaire
     * @return boolean Le résultat de l'enregistrement des données
     */
    public function saveParametersForm($data)
    {
        $parameters = ['paypal_email', 'paypal_money', 'paypal_test', 'use_ipn'];
        // On commence par supprimer les valeurs actuelles
        $gatewayName = $this->gatewayInformation['foldername'];
        $this->handlers->h_oledrion_gateways_options->deleteGatewayOptions($gatewayName);
        foreach ($parameters as $parameter) {
            if (!$this->handlers->h_oledrion_gateways_options->setGatewayOptionValue($gatewayName, $parameter, $data[$parameter])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Formate le montant au format Paypal
     * @param $amount
     * @return string
     */
    private function formatAmount($amount)
    {
        return number_format($amount, 2, '.', '');
    }

    /**
     * Retourne l'url vers laquelle rediriger l'utilisateur pour le paiement en ligne
     *
     * @param $cmd_total
     * @param $cmd_id
     * @return string
     */
    public function getRedirectURL($cmd_total, $cmd_id)
    {
        $test_mode = (int)$this->handlers->h_oledrion_gateways_options->getGatewayOptionValue($this->gatewayInformation['foldername'], 'paypal_test');
        if (1 == $test_mode) {
            return 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        } else {
            return 'https://www.paypal.com/cgi-bin/webscr';
        }
    }

    /**
     * Retourne les éléments à ajouter au formulaire en tant que zones cachées
     *
     * @param array $order La commande client
     * @param       array
     * @return array
     */
    public function getCheckoutFormContent($order)
    {
        global $xoopsConfig;
        $gatewayName  = $this->gatewayInformation['foldername'];
        $paypal_money = $this->handlers->h_oledrion_gateways_options->getGatewayOptionValue($gatewayName, 'paypal_money');
        $paypal_email = $this->handlers->h_oledrion_gateways_options->getGatewayOptionValue($gatewayName, 'paypal_email');
        $use_ipn      = (int)$this->handlers->h_oledrion_gateways_options->getGatewayOptionValue($gatewayName, 'use_ipn');

        $ret                     = [];
        $ret['cmd']              = '_xclick';
        $ret['upload']           = '1';
        $ret['currency_code']    = $paypal_money;
        $ret['business']         = $paypal_email;
        $ret['return']           = OLEDRION_URL . 'thankyou.php'; // Page (générique) de remerciement après paiement
        $ret['image_url']        = XOOPS_URL . '/images/logo.gif';
        $ret['cpp_header_image'] = XOOPS_URL . '/images/logo.gif';
        $ret['invoice']          = $order->getVar('cmd_id');
        $ret['item_name']        = _OLEDRION_COMMAND . $order->getVar('cmd_id') . ' - ' . OledrionUtility::makeHrefTitle($xoopsConfig['sitename']);
        $ret['item_number']      = $order->getVar('cmd_id');
        $ret['tax']              = 0; // ajout 25/03/2008
        $ret['amount']           = $this->formatAmount((float)$order->getVar('cmd_total', 'n'));
        $ret['custom']           = $order->getVar('cmd_id');
        //$ret['rm'] = 2;   // Renvoyer les données par POST (normalement)
        $ret['email'] = $order->getVar('cmd_email');
        if ('' !== xoops_trim($order->getVar('cmd_cancel'))) { // URL à laquelle le navigateur du client est ramené si le paiement est annulé
            $ret['cancel_return'] = OLEDRION_URL . 'cancel-payment.php?id=' . $order->getVar('cmd_cancel');
        }
        if (1 == $use_ipn) {
            $ret['notify_url'] = OLEDRION_URL . 'gateway-notify.php'; // paypal-notify.php
        }

        return $ret;
    }

    /**
     * Retourne la liste des pays à utiliser dans le formulaire de saisie des informations client (checkout.php)
     *
     * @return array
     */
    public function getCountriesList()
    {
        require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';

        return XoopsLists::getCountryList();
    }

    /**
     * Utilisée lors du dialog avec Paypal dans le cas de l'utilisation de l'IPN
     * Note : Spécifique Paypal
     *
     * @return string L'URL chez Paypal à appeler pour obtenir des informations
     */
    private function getdialogURL()
    {
        $test_mode = (int)$this->handlers->h_oledrion_gateways_options->getGatewayOptionValue($this->gatewayInformation['foldername'], 'paypal_test');
        if (1 == $test_mode) {
            return 'www.sandbox.paypal.com';
        } else {
            return 'www.paypal.com';
        }
    }

    /**
     * Dialogue avec la passerelle de paiement pour indiquer l'état de la commande
     * L'appellant se charge de vérifier que le fichier log existe
     *
     * @param  string $gatewaysLogPath Le chemin d'accès complet au fichier log
     * @return void
     */
    public function gatewayNotify($gatewaysLogPath)
    {
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
        $url          = $this->getdialogURL();
        $gatewayName  = $this->gatewayInformation['foldername'];
        $paypal_email = $this->handlers->h_oledrion_gateways_options->getGatewayOptionValue($gatewayName, 'paypal_email');
        $paypal_money = $this->handlers->h_oledrion_gateways_options->getGatewayOptionValue($gatewayName, 'paypal_money');
        $header       = '';
        $header       .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
        $header       .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header       .= 'Content-Length: ' . strlen($req) . "\r\n\r\n";
        $errno        = 0;
        $errstr       = '';
        $fp           = fsockopen($url, 80, $errno, $errstr, 30);
        if ($fp) {
            fwrite($fp, "$header$req");
            while (!feof($fp)) {
                $res = fgets($fp, 1024);
                if (0 == strcmp($res, 'VERIFIED')) {
                    $log      .= "VERIFIED\t";
                    $paypalok = true;
                    if ('COMPLETED' !== strtoupper($_POST['payment_status'])) {
                        $paypalok = false;
                    }
                    if (strtoupper($_POST['receiver_email']) != strtoupper($paypal_email)) {
                        $paypalok = false;
                    }
                    if (strtoupper($_POST['mc_currency']) != strtoupper($paypal_money)) {
                        $paypalok = false;
                    }
                    if (!$_POST['custom']) {
                        $paypalok = false;
                    }
                    $montant = $_POST['mc_gross'];
                    if ($paypalok) {
                        $ref      = (int)$_POST['custom']; // Numéro de la commande
                        $commande = null;
                        $commande = $this->handlers->h_oledrion_commands->get($ref);
                        if (is_object($commande)) {
                            if ($montant == $commande->getVar('cmd_total')) { // Commande vérifiée
                                $this->handlers->h_oledrion_commands->validateOrder($commande); // Validation de la commande et mise à jour des stocks
                            } else {
                                $this->handlers->h_oledrion_commands->setFraudulentOrder($commande);
                            }
                        }
                    } else {
                        if (isset($_POST['custom'])) {
                            $ref      = (int)$_POST['custom'];
                            $commande = null;
                            $commande = $this->handlers->h_oledrion_commands->get($ref);
                            if (is_object($commande)) {
                                switch (strtoupper($_POST['payment_status'])) {
                                    case 'PENDING':
                                        $this->handlers->h_oledrion_commands->setOrderPending($commande);
                                        break;
                                    case 'FAILED':
                                        $this->handlers->h_oledrion_commands->setOrderFailed($commande);
                                        break;
                                }
                            }
                        }
                    }
                } else {
                    $log .= "$res\n";
                }
            }
            fclose($fp);
        } else {
            $log .= "Error with the fsockopen function, unable to open communication ' : ($errno) $errstr\n";
        }

        // Ecriture dans le fichier log
        $fp = fopen($gatewaysLogPath, 'a');
        if ($fp) {
            fwrite($fp, str_repeat('-', 120) . "\n");
            fwrite($fp, date('d/m/Y H:i:s') . "\n");
            if (isset($_POST['txn_id'])) {
                fwrite($fp, 'Transaction : ' . $_POST['txn_id'] . "\n");
            }
            fwrite($fp, 'Result : ' . $log . "\n");
            fclose($fp);
        }
    }
}
