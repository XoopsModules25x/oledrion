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
 * Class Packing
 */
class Packing extends OledrionObject
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
        $this->initVar('packing_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('packing_title', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('packing_width', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('packing_length', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('packing_weight', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('packing_image', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('packing_description', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('packing_price', XOBJ_DTYPE_INT, null, false);
        $this->initVar('packing_online', XOBJ_DTYPE_INT, null, false);
    }

    /**
     * Retourne l'URL de l'image de la catégorie courante
     * @return string L'URL
     */
    public function getPictureUrl()
    {
        if ('' !== xoops_trim($this->getVar('product_image_url'))) {
            return OLEDRION_PICTURES_URL . '/' . $this->getVar('packing_image');
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
        if ('' !== xoops_trim($this->getVar('packing_image'))
            && file_exists(OLEDRION_PICTURES_PATH . '/' . $this->getVar('packing_image'))) {
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
            @unlink(OLEDRION_PICTURES_PATH . '/' . $this->getVar('packing_image'));
        }
        $this->setVar('packing_image', '');
    }

    /**
     * Retourne les éléments du produits formatés pour affichage
     *
     * @param  string $format
     * @return array
     */
    public function toArray($format = 's')
    {
        $oledrion_Currency               = oledrion\Currency::getInstance();
        $ret                             = [];
        $ret                             = parent::toArray($format);
        $ret['packing_price_fordisplay'] = $oledrion_Currency->amountForDisplay($this->getVar('packing_price'));
        $ret['packing_image_url']        = $this->getPictureUrl();

        return $ret;
    }
}
