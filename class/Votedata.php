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
 * Gestion des votes sur les produits
 */
require_once __DIR__ . '/classheader.php';

/**
 * Class Votedata
 */
class Votedata extends OledrionObject
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
        $this->initVar('vote_ratingid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('vote_product_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('vote_uid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('vote_rating', XOBJ_DTYPE_INT, null, false);
        $this->initVar('vote_ratinghostname', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('vote_ratingtimestamp', XOBJ_DTYPE_INT, null, false);
    }
}

