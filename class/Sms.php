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
 * @author      Hossein Azizabadi (azizabadi@faragostaresh.com)
 */

use XoopsModules\Oledrion;

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

/*
$information = array();
$information['to'] = '9365965795';
$information['text'] = 'ewrewr we sdf sdfdsf sdfasda sd asd asd';
$sms = Sms::sendSms($information);
*/

/**
 * Class Sms
 * @package XoopsModules\Oledrion
 */
class Sms
{
    /**
     *
     * @return string
     */
    public static function getSmsGateway()
    {
        return OLEDRION_SMS_GATEWAY;
    }

    /**
     * @param  string $smsGatewayName
     * @return string
     */
    public static function getSmsGatewayPath($smsGatewayName)
    {
        return OLEDRION_CLASS_PATH . 'sms' . '/' . $smsGatewayName;
    }

    /**
     * @param  string $smsGatewayName
     * @return string
     */
    public static function getSmsGatewayFullClassPath($smsGatewayName)
    {
        $smsGatewayPath = self::getSmsGatewayPath($smsGatewayName);

        return $smsGatewayPath . '/' . 'sms.php';
    }

    /**
     * @param string $smsGatewayName
     */
    public static function includeSmsGatewayClass($smsGatewayName)
    {
        $smsGatewayClassPath = self::getSmsGatewayFullClassPath($smsGatewayName);
        require_once $smsGatewayClassPath;
    }

    /**
     * @param  string $smsGatewayName
     * @return array
     */
    public static function getSmsGatewayOption($smsGatewayName)
    {
        $option         = [];
        $smsGatewayPath = self::getSmsGatewayPath($smsGatewayName);
        require_once $smsGatewayPath . '/' . 'option.php';

        return $option;
    }

    /**
     * @param  array $information
     * @return string
     * @internal param string $smsGatewayName
     */
    public static function sendSms($information = [])
    {
        $smsGatewayName = self::getSmsGateway();
        $option         = self::getSmsGatewayOption($smsGatewayName);
        self::includeSmsGatewayClass($smsGatewayName);

        return self::sendSms($information, $option);
    }
}
