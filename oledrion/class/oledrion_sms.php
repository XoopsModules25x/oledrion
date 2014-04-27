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
 * @copyright   The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author      Hossein Azizabadi (azizabadi@faragostaresh.com)
 * @version     $Id: oledrion_sms.php 12290 2014-02-07 11:05:17Z beckmi $
 */

defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

/*
$information = array();
$information['to'] = '9365965795';
$information['text'] = 'ewrewr we sdf sdfdsf sdfasda sd asd asd';
$sms = oledrion_sms::sendSms($information);
*/

class oledrion_sms
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
        return OLEDRION_CLASS_PATH . 'sms' . DIRECTORY_SEPARATOR . $smsGatewayName;
    }

    /**
     * @param  string $smsGatewayName
     * @return string
     */
    public static function getSmsGatewayFullClassPath($smsGatewayName)
    {
        $smsGatewayPath = self::getSmsGatewayPath($smsGatewayName);

        return $smsGatewayPath . DIRECTORY_SEPARATOR . 'sms.php';
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
        $option = array();
        $smsGatewayPath = self::getSmsGatewayPath($smsGatewayName);
        require_once $smsGatewayPath . DIRECTORY_SEPARATOR . 'option.php';

        return $option;
    }

    /**
     * @param string $smsGatewayName
     */
    public static function sendSms($information = array())
    {
        $smsGatewayName = self::getSmsGateway();
        $option = self::getSmsGatewayOption($smsGatewayName);
        self::includeSmsGatewayClass($smsGatewayName);

        return sms::sendSms($information, $option);
    }
}
