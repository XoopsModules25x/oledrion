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
 * @author      Hossein Azizabadi (azizabadi@faragostaresh.com)
 */

use Xoopsmodules\oledrion;

require_once __DIR__ . '/classheader.php';

/**
 * Class Payment
 */
class Payment extends OledrionObject
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
        $this->initVar('payment_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('payment_title', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('payment_description', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('payment_online', XOBJ_DTYPE_INT, null, false);
        $this->initVar('payment_type', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('payment_gateway', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('payment_image', XOBJ_DTYPE_TXTBOX, null, false);
    }

    /**
     * Retourne l'URL de l'image de la catégorie courante
     * @return string L'URL
     */
    public function getPictureUrl()
    {
        if ('' !== xoops_trim($this->getVar('product_image_url'))) {
            return OLEDRION_PICTURES_URL . '/' . $this->getVar('payment_image');
        } else {
            return '';
        }
    }

    /**
     * Indique si l'image de la catégorie existe
     *
     * @return boolean Vrai si l'image existe sinon faux
     */
    public function pictureExists()
    {
        $return = false;
        if ('' !== xoops_trim($this->getVar('payment_image'))
            && file_exists(OLEDRION_PICTURES_PATH . '/' . $this->getVar('payment_image'))) {
            $return = true;
        }

        return $return;
    }

    /**
     * Supprime l'image associée à une catégorie
     * @return void
     */
    public function deletePicture()
    {
        if ($this->pictureExists()) {
            @unlink(OLEDRION_PICTURES_PATH . '/' . $this->getVar('payment_image'));
        }
        $this->setVar('payment_image', '');
    }

    /**
     * Retourne les éléments du produits formatés pour affichage
     *
     * @param  string $format
     * @return array
     */
    public function toArray($format = 's')
    {
        $ret                      = [];
        $ret                      = parent::toArray($format);
        $ret['payment_image_url'] = $this->getPictureUrl();

        return $ret;
    }
}
