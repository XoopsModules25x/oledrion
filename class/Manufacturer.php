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

/**
 * Gestion des fabricants
 */

use XoopsModules\Oledrion;

/**
 * Class Manufacturer
 */
class Manufacturer extends OledrionObject
{
    /**
     * constructor
     *
     * normally, this is called from child classes only
     */
    public function __construct()
    {
        $this->initVar('manu_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('manu_name', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('manu_commercialname', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('manu_email', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('manu_bio', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('manu_url', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('manu_photo1', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('manu_photo2', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('manu_photo3', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('manu_photo4', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('manu_photo5', XOBJ_DTYPE_TXTBOX, null, false);
        // Pour autoriser le html
        $this->initVar('dohtml', XOBJ_DTYPE_INT, 1, false);
    }

    /**
     * Retourne l'URL d'une des 5 images du fabricant courant
     *
     * @param int $pictureNumber Le numéro (de 1 à 5) de l'image que l'on souhaite récupérer
     * @return mixed   L'URL    Soit l'url de l'image soit False si l'indice passé en paramètre n'est pas correct
     */
    public function getPictureUrl($pictureNumber)
    {
        $pictureNumber = (int)$pictureNumber;
        if ($pictureNumber > 0 && $pictureNumber < 6) {
            return OLEDRION_PICTURES_URL . '/' . $this->getVar('manu_photo' . $pictureNumber);
        }

        return false;
    }

    /**
     * Retourne le chemin de l'une des 5 images du fabricant courant
     *
     * @param int $pictureNumber Le numéro (de 1 à 5) de l'image que l'on souhaite récupérer
     * @return string  Le chemin
     */
    public function getPicturePath($pictureNumber)
    {
        $pictureNumber = (int)$pictureNumber;
        if ($pictureNumber > 0 && $pictureNumber < 6) {
            return OLEDRION_PICTURES_PATH . '/' . $this->getVar('manu_photo' . $pictureNumber);
        }

        return false;
    }

    /**
     * Indique si une des 5 images du fabricant existe
     *
     * @param int $pictureNumber Le numéro (de 1 à 5) de l'image que l'on souhaite récupérer
     * @return bool Vrai si l'image existe sinon faux
     */
    public function pictureExists($pictureNumber)
    {
        $pictureNumber = (int)$pictureNumber;
        $return        = false;
        if ($pictureNumber > 0 && $pictureNumber < 6) {
            if ('' !== xoops_trim($this->getVar('manu_photo' . $pictureNumber)) && file_exists(OLEDRION_PICTURES_PATH . '/' . $this->getVar('manu_photo' . $pictureNumber))) {
                $return = true;
            }
        }

        return $return;
    }

    /**
     * Supprime une des 5 images du fabricant
     *
     * @param int $pictureNumber Le numéro (de 1 à 5) de l'image que l'on souhaite récupérer
     */
    public function deletePicture($pictureNumber)
    {
        $pictureNumber = (int)$pictureNumber;
        if ($pictureNumber > 0 && $pictureNumber < 6) {
            if ($this->pictureExists($pictureNumber)) {
                if (false === @unlink(OLEDRION_PICTURES_PATH . '/' . $this->getVar('manu_photo' . $pictureNumber))){
                    throw new \RuntimeException('The picture '.OLEDRION_PICTURES_PATH . '/' . $this->getVar('manu_photo' . $pictureNumber).' could not be deleted.');
                }
            }
            $this->setVar('manu_photo' . $pictureNumber, '');
        }
    }

    /**
     * Supprime toutes les images du fabricant (raccourcis)
     */
    public function deletePictures()
    {
        for ($i = 1; $i <= 5; ++$i) {
            $this->deletePicture($i);
        }
    }

    /**
     * Retourne l'url à utiliser pour accéder à la page d'un fabricant
     *
     * @return string
     */
    public function getLink()
    {
        $url = '';
        if (1 == Oledrion\Utility::getModuleOption('urlrewriting')) {
            // On utilise l'url rewriting
            $url = OLEDRION_URL . 'manufacturer-' . $this->getVar('manu_id') . Oledrion\Utility::makeSeoUrl($this->getVar('manu_commercialname', 'n') . ' ' . $this->getVar('manu_name')) . '.html';
        } else {
            // Pas d'utilisation de l'url rewriting
            $url = OLEDRION_URL . 'manufacturer.php?manu_id=' . $this->getVar('manu_id');
        }

        return $url;
    }

    /**
     * Rentourne la chaine à envoyer dans une balise <a> pour l'attribut href
     *
     * @return string
     */
    public function getHrefTitle()
    {
        return Oledrion\Utility::makeHrefTitle($this->getVar('manu_commercialname') . ' ' . $this->getVar('manu_name'));
    }

    /**
     * Retourne l'initiale du fabricant (à modifier selon le sens de l'écriture !)
     * @return string L'initiale
     */
    public function getInitial()
    {
        return mb_strtoupper($this->getVar('manu_name')[0]);
    }

    /**
     * Retourne les éléments du fabricant formatés pour affichage
     *
     * @param  string $format Le format à utiliser
     * @return array  Les informations formatées
     */
    public function toArray($format = 's')
    {
        $ret = [];
        $ret = parent::toArray($format);
        for ($i = 1; $i <= 5; ++$i) {
            $ret['manu_photo' . $i . '_url'] = $this->getPictureUrl($i);
        }
        $ret['manu_url_rewrited'] = $this->getLink();
        $ret['manu_href_title']   = $this->getHrefTitle();
        $ret['manu_initial']      = $this->getInitial();

        return $ret;
    }
}
