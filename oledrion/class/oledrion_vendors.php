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
 * @copyright   The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author      Hervé Thouzard (http://www.herve-thouzard.com/)
 * @version     $Id$
 */

/**
 * Gestion des vendeurs
 */
require 'classheader.php';

class oledrion_vendors extends Oledrion_Object
{
    public function __construct()
    {
        $this->initVar('vendor_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('vendor_name', XOBJ_DTYPE_TXTBOX, null, false);
    }
}


class OledrionOledrion_vendorsHandler extends Oledrion_XoopsPersistableObjectHandler
{
    public function __construct($db)
    { //							Table				Classe		 	Id			Libellé
        parent::__construct($db, 'oledrion_vendors', 'oledrion_vendors', 'vendor_id', 'vendor_name');
    }

    /**
     * Renvoie la liste de tous les vendeurs du module
     *
     * @param integer $start Position de départ
     * @param integer $limit Nombre total d'enregistrements à renvoyer
     * @param string $order Champ sur lequel faire le tri
     * @param string $order Ordre du tri
     * @param boolean $idaskey Indique si le tableau renvoyé doit avoir pour clé l'identifiant unique de l'enregistrement
     * @return array tableau d'objets de type vendors
     */
    public function getAllVendors(oledrion_parameters $parameters)
    {
        $parameters = $parameters->extend(new oledrion_parameters(array('start' => 0, 'limit' => 0, 'sort' => 'vendor_name', 'order' => 'ASC', 'idaskey' => true)));
        $critere = new Criteria('vendor_id', 0, '<>');
        $critere->setLimit($parameters['limit']);
        $critere->setStart($parameters['start']);
        $critere->setSort($parameters['sort']);
        $critere->setOrder($parameters['order']);
        $categories = array();
        $categories = $this->getObjects($critere, $parameters['idaskey']);
        return $categories;
    }

    /**
     * Retourne le nombre de produits associés à un vendeur
     *
     * @param integer    $vendor_id    L'ID du vendeur
     * @return integer    Le nombre de produits du vendeur
     */
    public function getVendorProductsCount($vendor_id)
    {
        global $h_oledrion_products;
        return $h_oledrion_products->getVendorProductsCount($vendor_id);
    }

    /**
     * Supprime un vendeur
     *
     * @param oledrion_vendors $vendor
     * @return boolean    Le résultat de la suppression
     */
    public function deleteVendor(oledrion_vendors $vendor)
    {
        return $this->delete($vendor, true);
    }

    /**
     * Retourne des vendeurs selon leur ID
     *
     * @param array $ids    Les ID des vendeurs à retrouver
     * @return array    Objets de type oledrion_vendors
     */
    public function getVendorsFromIds($ids)
    {
        $ret = array();
        if (is_array($ids) && count($ids) > 0) {
            $criteria = new Criteria('vendor_id', '(' . implode(',', $ids) . ')', 'IN');
            $ret = $this->getObjects($criteria, true, true, '*', false);
        }
        return $ret;
    }
}
