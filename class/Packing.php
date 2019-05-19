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
 * @author      Hossein Azizabadi (azizabadi@faragostaresh.com)
 */

use XoopsModules\Oledrion;

/**
 * Class Packing
 */
class Packing extends OledrionObject
{
    /**
     * constructor
     *
     * normally, this is called from child classes only
     */
    public function __construct()
    {
        $this->initVar('packing_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('packing_title', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('packing_width', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('packing_length', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('packing_weight', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('packing_image', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('packing_description', XOBJ_DTYPE_OTHER, null, false);
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
        }

        return '';
    }

    /**
     * Indique si l'image de la catégorie existe
     *
     * @return bool Vrai si l'image existe sinon faux
     */
    public function pictureExists()
    {
        $return = false;
        if ('' !== xoops_trim($this->getVar('packing_image')) && file_exists(OLEDRION_PICTURES_PATH . '/' . $this->getVar('packing_image'))) {
            $return = true;
        }

        return $return;
    }

    /**
     * Supprime l'image associée à une catégorie
     */
    public function deletePicture()
    {
        if ($this->pictureExists()) {
            if (false === @unlink(OLEDRION_PICTURES_PATH . '/' . $this->getVar('packing_image'))) {
                throw new \RuntimeException('The picture ' . OLEDRION_PICTURES_PATH . '/' . $this->getVar('packing_image') . ' could not be deleted.');
            }
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
        $oledrionCurrency                = Oledrion\Currency::getInstance();
        $ret                             = [];
        $ret                             = parent::toArray($format);
        $ret['packing_price_fordisplay'] = $oledrionCurrency->amountForDisplay($this->getVar('packing_price'));
        $ret['packing_image_url']        = $this->getPictureUrl();

        return $ret;
    }
}
