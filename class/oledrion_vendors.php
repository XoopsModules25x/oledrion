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
 * Gestion des vendeurs
 */
require_once __DIR__ . '/classheader.php';

/**
 * Class Oledrion_vendors
 */
class Oledrion_vendors extends OledrionObject
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
        $this->initVar('vendor_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('vendor_name', XOBJ_DTYPE_TXTBOX, null, false);
    }
}

/**
 * Class OledrionOledrion_vendorsHandler
 */
class OledrionOledrion_vendorsHandler extends OledrionPersistableObjectHandler
{
    /**
     * OledrionOledrion_vendorsHandler constructor.
     * @param XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    { //                            Table               Classe          Id          Libellé
        parent::__construct($db, 'oledrion_vendors', 'Oledrion_vendors', 'vendor_id', 'vendor_name');
    }

    /**
     * Renvoie la liste de tous les vendeurs du module
     *
     * @param  Oledrion_parameters $parameters
     * @return array               tableau d'objets de type vendors
     * @internal param int $start Position de départ
     * @internal param int $limit Nombre total d'enregistrements à renvoyer
     * @internal param string $order Champ sur lequel faire le tri
     * @internal param string $order Ordre du tri
     * @internal param bool $idaskey Indique si le tableau renvoyé doit avoir pour clé l'identifiant unique de l'enregistrement
     */
    public function getAllVendors(Oledrion_parameters $parameters)
    {
        $parameters = $parameters->extend(new Oledrion_parameters([
                                                                      'start'   => 0,
                                                                      'limit'   => 0,
                                                                      'sort'    => 'vendor_name',
                                                                      'order'   => 'ASC',
                                                                      'idaskey' => true
                                                                  ]));
        $critere    = new Criteria('vendor_id', 0, '<>');
        $critere->setLimit($parameters['limit']);
        $critere->setStart($parameters['start']);
        $critere->setSort($parameters['sort']);
        $critere->setOrder($parameters['order']);
        $categories = [];
        $categories = $this->getObjects($critere, $parameters['idaskey']);

        return $categories;
    }

    /**
     * Retourne le nombre de produits associés à un vendeur
     *
     * @param  integer $vendor_id L'ID du vendeur
     * @return integer Le nombre de produits du vendeur
     */
    public function getVendorProductsCount($vendor_id)
    {
        global $h_oledrion_products;

        return $h_oledrion_products->getVendorProductsCount($vendor_id);
    }

    /**
     * Supprime un vendeur
     *
     * @param  Oledrion_vendors $vendor
     * @return boolean          Le résultat de la suppression
     */
    public function deleteVendor(Oledrion_vendors $vendor)
    {
        return $this->delete($vendor, true);
    }

    /**
     * Retourne des vendeurs selon leur ID
     *
     * @param  array $ids Les ID des vendeurs à retrouver
     * @return array Objets de type Oledrion_vendors
     */
    public function getVendorsFromIds($ids)
    {
        $ret = [];
        if (is_array($ids) && count($ids) > 0) {
            $criteria = new Criteria('vendor_id', '(' . implode(',', $ids) . ')', 'IN');
            $ret      = $this->getObjects($criteria, true, true, '*', false);
        }

        return $ret;
    }
}
