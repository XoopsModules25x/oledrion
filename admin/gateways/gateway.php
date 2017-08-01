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
 * Every gateway script must extends this class
 */
// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

abstract class Oledrion_gateway
{
    protected $handlers;
    protected $gatewayInformation;
    public    $languageFilename;

    /**
     * Oledrion_gateway constructor.
     */
    public function __construct()
    {
        $this->getHandlers();
        $this->setGatewayInformation();
    }

    /**
     * Set the information about the current gateway
     * $gateway['name'] = "Paypal";
     * $gateway['foldername'] = 'paypal';
     * $gateway['version'] = 1.0;
     * $gateway['description'] = "PayPal is the safer, easier way to pay and get paid online";
     * $gateway['author'] = "Instant Zero (http://www.herve-thouzard.com/)";
     * $gateway['credits'] = "Hervé Thouzard";
     * $gateway['releaseDate'] = 20081215;    // YYYYMMDD
     */
    abstract public function setGatewayInformation();

    /**
     * Loads the module's handler
     *
     */
    private function getHandlers()
    {
        $this->handlers = OledrionHandler::getInstance();
    }

    /**
     * Returns some informations about the gateway on the form of an array :
     *
     * @return array $gateway
     */
    public function getGatewayInformation()
    {
        return $this->gatewayInformation;
    }

    /**
     * Verifies if the gateway log exists and create it if it does not
     *
     * @param  string $gatewaysLogPath The full path (and name) to the gateway's log file
     * @return void
     */
    public function verifyIfGatewayLogExists($gatewaysLogPath)
    {
        if (!file_exists($gatewaysLogPath)) {
            file_put_contents($gatewaysLogPath, '<?php exit(); ?>');
        }
    }

    /**
     * Ecriture d'un texte dans le fichier log des passerelles
     *
     * @param  string $gatewaysLogPath Le chemin d'accès complet (et le nom) au fichier log
     * @param  string $text            Le texte à écrire
     * @return void
     */
    public function appendToLog($gatewaysLogPath, $text)
    {
        $this->verifyIfGatewayLogExists($gatewaysLogPath);
        $fp = fopen($gatewaysLogPath, 'a');
        if ($fp) {
            fwrite($fp, str_repeat('-', 120) . "\n");
            fwrite($fp, date('d/m/Y H:i:s') . "\n");
            fwrite($fp, $text . "\n");
            fclose($fp);
        }
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
    abstract public function getParametersForm($postUrl);

    /**
     * This method is called by the module to save the gateway's parameters
     * It's up to you to verify data and eventually to complain about uncomplete or missing data
     *
     * @param  array $data Receives $_POST
     * @return boolean True if you succeed to save data else false
     */
    abstract public function saveParametersForm($data);

    /**
     * Returns the URL to redirect user to (for paying)
     * @param $cmd_total
     * @param $cmd_id
     * @return string
     */
    abstract public function getRedirectURL($cmd_total, $cmd_id);

    /**
     * Returns the form to use before to redirect user to the gateway
     *
     * @param  Oledrion_commands $order Objects of type Oledrion_commands
     * @return array  Key = element's name, Value = Element's value
     */
    abstract public function getCheckoutFormContent($order);

    /**
     * Returns the list of countries codes used by the gateways
     *
     */
    abstract public function getCountriesList();

    /**
     * This method is in charge to dialog with the gateway to verify the payment's statuts
     *
     * @param  string $gatewaysLogPath The full path (and name) to the log file
     * @return void
     */
    abstract public function gatewayNotify($gatewaysLogPath);

    /**
     * Returne the gateway's language file
     *
     * @return the filename to use
     */
    public function getGatewayLanguageFile()
    {
        global $xoopsConfig;
        $gatewayName  = $this->gatewayInformation['foldername'];
        $fullFilePath = OLEDRION_GATEWAY_PATH . $gatewayName; // c:/inetpub/wwwroot/xoops3/modules/oledrion/admin/gateways/passerelle
        if (file_exists($fullFilePath . '/language/' . $xoopsConfig['language'] . '/main.php')) {
            return $fullFilePath . '/language/' . $xoopsConfig['language'] . '/main.php';
        } else {
            return $fullFilePath . '/language/english/main.php';
        }
    }
}
