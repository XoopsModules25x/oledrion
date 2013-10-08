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

require 'classheader.php';

class oledrion_payment_log extends Oledrion_Object
{
    public function __construct()
    {
        $this->initVar('log_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('log_create', XOBJ_DTYPE_INT, null, false);
        $this->initVar('log_status', XOBJ_DTYPE_INT, null, false);
        $this->initVar('log_ip', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('log_type', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('log_payment', XOBJ_DTYPE_INT, null, false);
        $this->initVar('log_gatewa', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('log_uid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('log_command', XOBJ_DTYPE_INT, null, false);
        $this->initVar('log_amount', XOBJ_DTYPE_INT, null, false);
        $this->initVar('log_authority', XOBJ_DTYPE_TXTBOX, null, false);
    }
}


class OledrionOledrion_payment_logHandler extends Oledrion_XoopsPersistableObjectHandler
{
    public function __construct($db)
    { //							           Table					     Classe				     Id
        parent::__construct($db, 'oledrion_payment_log', 'oledrion_payment_log', 'log_id');
    }
}
