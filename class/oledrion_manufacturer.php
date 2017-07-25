<?php
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
require_once __DIR__ . '/classheader.php';

/**
 * Class Oledrion_manufacturer
 */
class Oledrion_manufacturer extends Oledrion_Object
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
     * @param  integer $pictureNumber Le numéro (de 1 à 5) de l'image que l'on souhaite récupérer
     * @return mixed   L'URL    Soit l'url de l'image soit False si l'indice passé en paramètre n'est pas correct
     */
    public function getPictureUrl($pictureNumber)
    {
        $pictureNumber = (int)$pictureNumber;
        if ($pictureNumber > 0 && $pictureNumber < 6) {
            return OLEDRION_PICTURES_URL . '/' . $this->getVar('manu_photo' . $pictureNumber);
        } else {
            return false;
        }
    }

    /**
     * Retourne le chemin de l'une des 5 images du fabricant courant
     *
     * @param  integer $pictureNumber Le numéro (de 1 à 5) de l'image que l'on souhaite récupérer
     * @return string  Le chemin
     */
    public function getPicturePath($pictureNumber)
    {
        $pictureNumber = (int)$pictureNumber;
        if ($pictureNumber > 0 && $pictureNumber < 6) {
            return OLEDRION_PICTURES_PATH . '/' . $this->getVar('manu_photo' . $pictureNumber);
        } else {
            return false;
        }
    }

    /**
     * Indique si une des 5 images du fabricant existe
     *
     * @param  integer $pictureNumber Le numéro (de 1 à 5) de l'image que l'on souhaite récupérer
     * @return boolean Vrai si l'image existe sinon faux
     */
    public function pictureExists($pictureNumber)
    {
        $pictureNumber = (int)$pictureNumber;
        $return        = false;
        if ($pictureNumber > 0 && $pictureNumber < 6) {
            if (xoops_trim($this->getVar('manu_photo' . $pictureNumber)) != ''
                && file_exists(OLEDRION_PICTURES_PATH . '/' . $this->getVar('manu_photo' . $pictureNumber))) {
                $return = true;
            }
        }

        return $return;
    }

    /**
     * Supprime une des 5 images du fabricant
     *
     * @param  integer $pictureNumber Le numéro (de 1 à 5) de l'image que l'on souhaite récupérer
     * @return void
     */
    public function deletePicture($pictureNumber)
    {
        $pictureNumber = (int)$pictureNumber;
        if ($pictureNumber > 0 && $pictureNumber < 6) {
            if ($this->pictureExists($pictureNumber)) {
                @unlink(OLEDRION_PICTURES_PATH . '/' . $this->getVar('manu_photo' . $pictureNumber));
            }
            $this->setVar('manu_photo' . $pictureNumber, '');
        }
    }

    /**
     * Supprime toutes les images du fabricant (raccourcis)
     * @return void
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
        if (OledrionUtility::getModuleOption('urlrewriting') == 1) { // On utilise l'url rewriting
            $url = OLEDRION_URL . 'manufacturer-' . $this->getVar('manu_id') . OledrionUtility::makeSeoUrl($this->getVar('manu_commercialname', 'n') . ' ' . $this->getVar('manu_name')) . '.html';
        } else { // Pas d'utilisation de l'url rewriting
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
        return OledrionUtility::makeHrefTitle($this->getVar('manu_commercialname') . ' ' . $this->getVar('manu_name'));
    }

    /**
     * Retourne l'initiale du fabricant (à modifier selon le sens de l'écriture !)
     * @return string L'initiale
     */
    public function getInitial()
    {
        return strtoupper(substr($this->getVar('manu_name'), 0, 1));
    }

    /**
     * Retourne les éléments du fabricant formatés pour affichage
     *
     * @param  string $format Le format à utiliser
     * @return array  Les informations formatées
     */
    public function toArray($format = 's')
    {
        $ret = array();
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

/**
 * Class OledrionOledrion_manufacturerHandler
 */
class OledrionOledrion_manufacturerHandler extends Oledrion_XoopsPersistableObjectHandler
{
    /**
     * OledrionOledrion_manufacturerHandler constructor.
     * @param object $db
     */
    public function __construct($db)
    { //                            Table                   Classe               Id            Identifiant
        parent::__construct($db, 'oledrion_manufacturer', 'oledrion_manufacturer', 'manu_id', 'manu_commercialname');
    }

    /**
     * Renvoie l'alphabet à partir de la première lettre du nom des fabricants
     *
     * @return array l'alphabet des lettres utilisées !
     */
    public function getAlphabet()
    {
        global $myts;
        $ret    = array();
        $sql    = 'SELECT DISTINCT (UPPER(SUBSTRING(manu_name, 1, 1))) AS oneletter FROM ' . $this->table;
        $result = $this->db->query($sql);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $ret[] = $myts->htmlSpecialChars($myrow['oneletter']);
        }

        return $ret;
    }

    /**
     * Supprime un fabricant et tout ce qui est relatif
     *
     * @param  oledrion_manufacturer $manufacturer
     * @return boolean               Le résultat de la suppression
     */
    public function deleteManufacturer(oledrion_manufacturer $manufacturer)
    {
        $manufacturer->deletePictures();

        return $this->delete($manufacturer, true);
    }

    /**
     * Retourne le nombre de produits associés à un fabricant
     *
     * @param  integer $manu_id L'identifiant du fabricant
     * @return integer Le nombre de produis associés à un fabricant
     */
    public function getManufacturerProductsCount($manu_id)
    {
        global $h_oledrion_productsmanu;

        return $h_oledrion_productsmanu->getManufacturerProductsCount($manu_id);
    }

    /**
     * Retourne des fabricants en fonction de leur IDs
     *
     * @param  array $ids Les identifiants des produits
     * @return array Tableau d'objets de type oledrion_productsmanu
     */
    public function getManufacturersFromIds($ids)
    {
        $ret = array();
        if (is_array($ids) && count($ids) > 0) {
            $criteria = new Criteria('manu_id', '(' . implode(',', $ids) . ')', 'IN');
            $ret      = $this->getObjects($criteria, true, true, '*', false);
        }

        return $ret;
    }

    /**
     * Retourne les produits d'un fabricant (note, ce code serait mieux dans une facade)
     *
     * @param  integer $manu_id Le fabricant dont on veut récupérer les produits
     * @param  integer $start   Position de départ
     * @param  integer $limit   Nombre maximum d'enregistrements à renvoyer
     * @return array   Objects de type oledrion_products
     */
    public function getManufacturerProducts($manu_id, $start = 0, $limit = 0)
    {
        $ret = $productsIds = array();
        global $h_oledrion_productsmanu, $h_oledrion_products;
        // On commence par récupérer les ID des produits
        $productsIds = $h_oledrion_productsmanu->getProductsIdsFromManufacturer($manu_id, $start, $limit);
        // Puis les produits eux même
        $ret = $h_oledrion_products->getProductsFromIDs($productsIds);

        return $ret;
    }
}
