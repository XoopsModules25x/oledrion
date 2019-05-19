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
 * Gestion des fabricants
 */

/**
 * Class ManufacturerHandler
 */
class ManufacturerHandler extends OledrionPersistableObjectHandler
{
    /**
     * ManufacturerHandler constructor.
     * @param \XoopsDatabase|null $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        //                            Table                   Classe               Id            Identifiant
        parent::__construct($db, 'oledrion_manufacturer', Manufacturer::class, 'manu_id', 'manu_commercialname');
    }

    /**
     * Renvoie l'alphabet à partir de la première lettre du nom des fabricants
     *
     * @return array l'alphabet des lettres utilisées !
     */
    public function getAlphabet()
    {
        global $myts;
        $ret    = [];
        $sql    = 'SELECT DISTINCT (UPPER(SUBSTRING(manu_name, 1, 1))) AS oneletter FROM ' . $this->table;
        $result = $this->db->query($sql);
        if (!$result) {
            return $ret;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $ret[] = $myts->htmlSpecialChars($myrow['oneletter']);
        }

        return $ret;
    }

    /**
     * Supprime un fabricant et tout ce qui est relatif
     *
     * @param  Manufacturer $manufacturer
     * @return bool               Le résultat de la suppression
     */
    public function deleteManufacturer(Manufacturer $manufacturer)
    {
        $manufacturer->deletePictures();

        return $this->delete($manufacturer, true);
    }

    /**
     * Retourne le nombre de produits associés à un fabricant
     *
     * @param int $manu_id L'identifiant du fabricant
     * @return int Le nombre de produis associés à un fabricant
     */
    public function getManufacturerProductsCount($manu_id)
    {
        global $productsmanuHandler;

        return $productsmanuHandler->getManufacturerProductsCount($manu_id);
    }

    /**
     * Retourne des fabricants en fonction de leur IDs
     *
     * @param  array $ids Les identifiants des produits
     * @return array Tableau d'objets de type Productsmanu
     */
    public function getManufacturersFromIds($ids)
    {
        $ret = [];
        if ($ids && is_array($ids)) {
            $criteria = new \Criteria('manu_id', '(' . implode(',', $ids) . ')', 'IN');
            $ret      = $this->getObjects($criteria, true, true, '*', false);
        }

        return $ret;
    }

    /**
     * Retourne les produits d'un fabricant (note, ce code serait mieux dans une facade)
     *
     * @param int $manu_id Le fabricant dont on veut récupérer les produits
     * @param int $start   Position de départ
     * @param int $limit   Nombre maximum d'enregistrements à renvoyer
     * @return array   Objects de type Products
     */
    public function getManufacturerProducts($manu_id, $start = 0, $limit = 0)
    {
        $ret = $productsIds = [];
        global $productsmanuHandler, $productsHandler;
        // On commence par récupérer les ID des produits
        $productsIds = $productsmanuHandler->getProductsIdsFromManufacturer($manu_id, $start, $limit);
        // Puis les produits eux même
        $ret = $productsHandler->getProductsFromIDs($productsIds);

        return $ret;
    }
}
