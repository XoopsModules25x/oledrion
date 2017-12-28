<?php namespace Xoopsmodules\oledrion;

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

use Xoopsmodules\oledrion;

/**
 * Gestion des réductions
 */
require_once __DIR__ . '/classheader.php';


/**
 * Class Discounts
 */
class Discounts extends OledrionObject
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
        $this->initVar('disc_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('disc_title', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('disc_group', XOBJ_DTYPE_INT, null, false); // Groupe Xoops concerné par la remise (0=tous les groupes)
        $this->initVar('disc_cat_cid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('disc_vendor_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('disc_product_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('disc_price_type', XOBJ_DTYPE_INT, null, false); // Type de réduction (dégressive, montant/pourcentage)
        $this->initVar('disc_price_degress_l1qty1', XOBJ_DTYPE_INT, null, false);
        $this->initVar('disc_price_degress_l1qty2', XOBJ_DTYPE_INT, null, false);
        $this->initVar('disc_price_degress_l1total', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('disc_price_degress_l2qty1', XOBJ_DTYPE_INT, null, false);
        $this->initVar('disc_price_degress_l2qty2', XOBJ_DTYPE_INT, null, false);
        $this->initVar('disc_price_degress_l2total', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('disc_price_degress_l3qty1', XOBJ_DTYPE_INT, null, false);
        $this->initVar('disc_price_degress_l3qty2', XOBJ_DTYPE_INT, null, false);
        $this->initVar('disc_price_degress_l3total', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('disc_price_degress_l4qty1', XOBJ_DTYPE_INT, null, false);
        $this->initVar('disc_price_degress_l4qty2', XOBJ_DTYPE_INT, null, false);
        $this->initVar('disc_price_degress_l4total', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('disc_price_degress_l5qty1', XOBJ_DTYPE_INT, null, false);
        $this->initVar('disc_price_degress_l5qty2', XOBJ_DTYPE_INT, null, false);
        $this->initVar('disc_price_degress_l5total', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('disc_price_amount_amount', XOBJ_DTYPE_TXTBOX, null, false); // Montant ou pourcentage de réduction sur le prix
        $this->initVar('disc_price_amount_type', XOBJ_DTYPE_INT, null, false); // Pourcent ou Euros ?
        $this->initVar('disc_price_amount_on', XOBJ_DTYPE_INT, null, false); // Produit ou panier ?
        $this->initVar('disc_price_case', XOBJ_DTYPE_INT, null, false); // Dans quel cas ? (tous les cas, si c''est le premier achat, si le produit n''a jamais été acheté etc)
        $this->initVar('disc_price_case_qty_cond', XOBJ_DTYPE_INT, null, false); // Supérieur, inférieur, égal
        $this->initVar('disc_price_case_qty_value', XOBJ_DTYPE_INT, null, false); // Quantité de produit à tester
        $this->initVar('disc_shipping_type', XOBJ_DTYPE_INT, null, false);
        $this->initVar('disc_shipping_free_morethan', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('disc_shipping_reduce_amount', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('disc_shipping_reduce_cartamount', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('disc_shipping_degress_l1qty1', XOBJ_DTYPE_INT, null, false);
        $this->initVar('disc_shipping_degress_l1qty2', XOBJ_DTYPE_INT, null, false);
        $this->initVar('disc_shipping_degress_l1total', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('disc_shipping_degress_l2qty1', XOBJ_DTYPE_INT, null, false);
        $this->initVar('disc_shipping_degress_l2qty2', XOBJ_DTYPE_INT, null, false);
        $this->initVar('disc_shipping_degress_l2total', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('disc_shipping_degress_l3qty1', XOBJ_DTYPE_INT, null, false);
        $this->initVar('disc_shipping_degress_l3qty2', XOBJ_DTYPE_INT, null, false);
        $this->initVar('disc_shipping_degress_l3total', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('disc_shipping_degress_l4qty1', XOBJ_DTYPE_INT, null, false);
        $this->initVar('disc_shipping_degress_l4qty2', XOBJ_DTYPE_INT, null, false);
        $this->initVar('disc_shipping_degress_l4total', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('disc_shipping_degress_l5qty1', XOBJ_DTYPE_INT, null, false);
        $this->initVar('disc_shipping_degress_l5qty2', XOBJ_DTYPE_INT, null, false);
        $this->initVar('disc_shipping_degress_l5total', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('disc_date_from', XOBJ_DTYPE_INT, null, false); // Date de début de la promo
        $this->initVar('disc_date_to', XOBJ_DTYPE_INT, null, false); // Date de fin de la promo
        $this->initVar('disc_description', XOBJ_DTYPE_TXTAREA, null, false);

        // Pour autoriser le html
        $this->initVar('dohtml', XOBJ_DTYPE_INT, 1, false);
    }
}
