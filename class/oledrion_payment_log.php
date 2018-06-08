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
 * @author      Hossein Azizabadi (azizabadi@faragostaresh.com)
 */

require_once __DIR__ . '/classheader.php';

/**
 * Class Oledrion_payment_log
 */
class Oledrion_payment_log extends Oledrion_Object
{
    /**
     * constructor
     *
     * normally, this is called from child classes only
     *
     * @access public
     */
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

/**
 * Class OledrionOledrion_payment_logHandler
 */
class OledrionOledrion_payment_logHandler extends Oledrion_XoopsPersistableObjectHandler
{
    /**
     * OledrionOledrion_payment_logHandler constructor.
     * @param XoopsDatabase|null $db
     */
    public function __construct(XoopsDatabase $db)
    { //                                       Table                         Classe                  Id
        parent::__construct($db, 'oledrion_payment_log', 'oledrion_payment_log', 'log_id');
    }
}
