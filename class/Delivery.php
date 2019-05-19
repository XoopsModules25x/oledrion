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
 * Class Delivery
 */
class Delivery extends OledrionObject
{
    /**
     * constructor
     *
     * normally, this is called from child classes only
     */
    public function __construct()
    {
        $this->initVar('delivery_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('delivery_title', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('delivery_description', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('delivery_online', XOBJ_DTYPE_INT, null, false);
        $this->initVar('delivery_image', XOBJ_DTYPE_TXTBOX, null, false);

        $this->initVar('dohtml', XOBJ_DTYPE_INT, 1, false);
    }

    /**
     * Retourne l'URL de l'image de la catégorie courante
     * @return string L'URL
     */
    public function getPictureUrl()
    {
        if ('' !== xoops_trim($this->getVar('product_image_url'))) {
            return OLEDRION_PICTURES_URL . '/' . $this->getVar('delivery_image');
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
        if ('' !== xoops_trim($this->getVar('delivery_image')) && file_exists(OLEDRION_PICTURES_PATH . '/' . $this->getVar('delivery_image'))) {
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
            if (false === @unlink(OLEDRION_PICTURES_PATH . '/' . $this->getVar('delivery_image'))) {
                throw new \RuntimeException('The picture ' . OLEDRION_PICTURES_PATH . '/' . $this->getVar('delivery_image') . ' could not be deleted.');
            }
        }
        $this->setVar('delivery_image', '');
    }

    /**
     * Retourne les éléments du produits formatés pour affichage
     *
     * @param  string $format
     * @return array
     */
    public function toArray($format = 's')
    {
        global $locationDeliveryHandler;
        $ret                       = [];
        $ret                       = parent::toArray($format);
        $ret['delivery_image_url'] = $this->getPictureUrl();

        return $ret;
    }
}
