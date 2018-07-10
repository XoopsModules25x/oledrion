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
 * Classe chargée de faire la liaison entre les produits et les fabricants
 */

use XoopsModules\Oledrion;



/**
 * Class ProductsmanuHandler
 */
class ProductsmanuHandler extends OledrionPersistableObjectHandler
{
    /**
     * ProductsmanuHandler constructor.
     * @param \XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        //                            Table                   Classe              Id
        parent::__construct($db, 'oledrion_productsmanu', Productsmanu::class, 'pm_id');
    }

    /**
     * Retourne le nombre de produits associé à un fabricant
     *
     * @param int $pm_manu_id L'identifiant du fabricant
     * @return int Le nombre de fabricants
     */
    public function getManufacturerProductsCount($pm_manu_id)
    {
        $criteria = new \Criteria('pm_manu_id', $pm_manu_id, '=');

        return $this->getCount($criteria);
    }

    /**
     * Retourne des fabricants de produits en fonction de leur IDs
     *
     * @param  array $ids Les identifiants des produits
     * @return array Tableau d'objets de type Productsmanu
     */
    public function getFromProductsIds($ids)
    {
        $ret = [];
        if (is_array($ids)) {
            $criteria = new \Criteria('pm_product_id', '(' . implode(',', $ids) . ')', 'IN');
            $ret      = $this->getObjects($criteria, true, true, '*', false);
        }

        return $ret;
    }

    /**
     * Retourne les identifiants des produits d'un fabricant
     *
     * @param int  $pm_manu_id L'identifiant du fabricant
     * @param  int $start
     * @param  int $limit
     * @return array  Les ID des produits
     */
    public function getProductsIdsFromManufacturer($pm_manu_id, $start = 0, $limit = 0)
    {
        $ret      = [];
        $criteria = new \Criteria('pm_manu_id', $pm_manu_id, '=');
        $criteria->setStart($start);
        $criteria->setLimit($limit);
        $items = $this->getObjects($criteria, false, false, 'pm_product_id', false);
        if (count($items) > 0) {
            foreach ($items as $item) {
                $ret[] = $item['pm_product_id'];
            }
        }

        return $ret;
    }

    /**
     * Supprime un produit d'un fabricant
     *
     * @param int $pm_product_id
     * @return bool
     */
    public function removeManufacturerProduct($pm_product_id)
    {
        $pm_product_id = (int)$pm_product_id;

        return $this->deleteAll(new \Criteria('pm_product_id', $pm_product_id, '='));
    }
}
