<?php

namespace XoopsModules\Oledrion;

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

/**
 * Sales order management
 */


/**
 * Class Commands
 */
class Commands extends OledrionObject
{
    /**
     * constructor
     *
     * normally, this is called from child classes only
     */
    public function __construct()
    {
        $this->initVar('cmd_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cmd_uid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cmd_date', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_create', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cmd_state', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cmd_ip', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_lastname', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_firstname', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_adress', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('cmd_zip', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_town', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_country', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_telephone', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_mobile', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_email', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_articles_count', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cmd_total', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_shipping', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_packing_price', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_bill', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cmd_password', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_text', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('cmd_cancel', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_comment', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('cmd_vat_number', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('cmd_packing', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_packing_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cmd_location', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_location_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cmd_delivery', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_delivery_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cmd_payment', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_payment_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cmd_status', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cmd_track', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_gift', XOBJ_DTYPE_TXTBOX, null, false);
    }

    /**
     * Returns the elements of the products formatted for display
     *
     * @param  string $format The format to use
     * @return array  Formatted information
     */
    public function toArray($format = 's')
    {
        $ret = [];
        $ret = parent::toArray($format);
        require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
        $countries                           = [];
        $countries                           = \XoopsLists::getCountryList();
        $oledrionCurrency                    = Oledrion\Currency::getInstance();
        $ret['cmd_total_fordisplay']         = $oledrionCurrency->amountForDisplay($this->getVar('cmd_total')); // Montant TTC de la commande
        $ret['cmd_shipping_fordisplay']      = $oledrionCurrency->amountForDisplay($this->getVar('cmd_shipping')); // Montant TTC des frais de port
        $ret['cmd_packing_price_fordisplay'] = $oledrionCurrency->amountForDisplay($this->getVar('cmd_packing_price'));
        $ret['cmd_text_fordisplay']          = nl2br($this->getVar('cmd_text')); // Liste des réductions accordées
        if (isset($countries[$this->getVar('cmd_country')])) {
            // Libellé du pays de l'acheteur
            $ret['cmd_country_label'] = $countries[$this->getVar('cmd_country')];
        }
        if ($this->getVar('cmd_uid') > 0) {
            $ret['cmd_uname'] = \XoopsUser::getUnameFromId($this->getVar('cmd_uid'));
        }
        $ret['cmd_create_date'] = formatTimestamp($this->getVar('cmd_create'), _MEDIUMDATESTRING);

        return $ret;
    }
}
