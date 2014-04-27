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
 * @version     $Id$
 */

class sms
{
    public static function sendSms($information = array(), $option = array())
    {
        $parameters = array();
        $parameters['username'] = $option['username'];
        $parameters['password'] = $option['password'];
        $parameters['from'] = $option['number'];
        $parameters['to'] = $information['to'];
        $parameters['text'] =  $information['text'];
        $parameters['isflash'] =false;

        //ini_set("soap.wsdl_cache_enabled", "0");
        //$sms_client = new SoapClient('http://sms.payamakyab.com/post/send.asmx?wsdl', array('encoding'=>'UTF-8'));
        //return $sms_client->SendSimpleSMS2($parameters)->SendSimpleSMS2Result;
        return '';
    }
}
