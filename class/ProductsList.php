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
 * @author      Hervé Thouzard (http://www.herve-thouzard.com/)
 */

use XoopsModules\Oledrion;

/**
 * Relation entre listes utilisateurs et produits
 *
 * @since 2.3.2009.06.13
 */
require_once __DIR__ . '/classheader.php';

/**
 * Class ProductsList
 */
class ProductsList extends OledrionObject
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
        $this->initVar('productlist_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('productlist_list_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('productlist_product_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('productlist_date', XOBJ_DTYPE_TXTBOX, null, false);
    }
}
