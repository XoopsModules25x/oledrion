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
 * Gestion des catégories de produits
 */



/**
 * Class Category
 */
class Category extends OledrionObject
{
    /**
     * constructor
     *
     * normally, this is called from child classes only
     */
    public function __construct()
    {
        $this->initVar('cat_cid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cat_pid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cat_title', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cat_imgurl', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cat_description', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('cat_advertisement', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('cat_metakeywords', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cat_metadescription', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cat_metatitle', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cat_footer', XOBJ_DTYPE_TXTAREA, null, false);
        // Pour autoriser le html
        $this->initVar('dohtml', XOBJ_DTYPE_INT, 1, false);
    }

    /**
     * Retourne l'URL de l'image de la catégorie courante
     * @return string L'URL
     */
    public function getPictureUrl()
    {
        return OLEDRION_PICTURES_URL . '/' . $this->getVar('cat_imgurl');
    }

    /**
     * Retourne le chemin de l'image de la catégorie courante
     * @return string Le chemin
     */
    public function getPicturePath()
    {
        return OLEDRION_PICTURES_PATH . '/' . $this->getVar('cat_imgurl');
    }

    /**
     * Indique si l'image de la catégorie existe
     *
     * @return bool Vrai si l'image existe sinon faux
     */
    public function pictureExists()
    {
        $return = false;
        if ('' !== xoops_trim($this->getVar('cat_imgurl')) && file_exists(OLEDRION_PICTURES_PATH . '/' . $this->getVar('cat_imgurl'))) {
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
            if (false === @unlink(OLEDRION_PICTURES_PATH . '/' . $this->getVar('cat_imgurl'))){
                throw new \RuntimeException('The picture '.OLEDRION_PICTURES_PATH . '/' . $this->getVar('cat_imgurl').' could not be deleted.');
            }
        }
        $this->setVar('cat_imgurl', '');
    }

    /**
     * Retourne l'url à utiliser pour accéder à la catégorie en tenant compte des préférences du module
     *
     * @return string L'url à utiliser
     */
    public function getLink()
    {
        require_once XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
        $url = '';
        if (1 == Oledrion\Utility::getModuleOption('urlrewriting')) {
            // On utilise l'url rewriting
            $url = OLEDRION_URL . 'category-' . $this->getVar('cat_cid') . Oledrion\Utility::makeSeoUrl($this->getVar('cat_title', 'n')) . '.html';
        } else {
            // Pas d'utilisation de l'url rewriting
            $url = OLEDRION_URL . 'category.php?cat_cid=' . $this->getVar('cat_cid');
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
        return Oledrion\Utility::makeHrefTitle($this->getVar('cat_title'));
    }

    /**
     * Retourne les éléments du produits formatés pour affichage
     *
     * @param  string $format
     * @return array
     */
    public function toArray($format = 's')
    {
        $ret                     = [];
        $ret                     = parent::toArray($format);
        $ret['cat_full_imgurl']  = $this->getPictureUrl();
        $ret['cat_href_title']   = $this->getHrefTitle();
        $ret['cat_url_rewrited'] = $this->getLink();

        return $ret;
    }
}
