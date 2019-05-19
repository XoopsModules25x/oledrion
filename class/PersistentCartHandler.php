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
 * Panier persistant
 *
 * Lorque l'option correspondante dans le module est activée, tout produit rajouté dans le panier est
 * enregistré en base de données (à condition que l'utilisateur soit connecté).
 * Si l'utilisateur quitte le site et revient plus tard, cela permet de recharger son panier.
 */

/**
 * Class PersistentCartHandler
 */
class PersistentCartHandler extends OledrionPersistableObjectHandler
{
    /**
     * PersistentCartHandler constructor.
     * @param \XoopsDatabase|null $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        //                          Table                     Classe                        Id
        parent::__construct($db, 'oledrion_persistent_cart', PersistentCart::class, 'persistent_id');
    }

    /**
     * Supprime un produit des paniers enregistrés
     *
     * @param  mixed $persistent_product_id L'ID du produit à supprimer ou un tableau d'identifiants à supprimer
     * @return bool
     */
    public function deleteProductForAllCarts($persistent_product_id)
    {
        if (0 == Oledrion\Utility::getModuleOption('persistent_cart')) {
            return true;
        }
        if (is_array($persistent_product_id)) {
            $criteria = new \Criteria('persistent_product_id', '(' . implode(',', $persistent_product_id) . ')', 'IN');
        } else {
            $criteria = new \Criteria('persistent_product_id', $persistent_product_id, '=');
        }

        return $this->deleteAll($criteria);
    }

    /**
     * Purge des produits d'un utilisateur
     *
     * @param int $persistent_uid L'identifiant de l'utilisateur
     * @return bool Le résultat de la suppression
     */
    public function deleteAllUserProducts($persistent_uid = 0)
    {
        if (0 == Oledrion\Utility::getModuleOption('persistent_cart')) {
            return true;
        }
        $persistent_uid = 0 == $persistent_uid ? Oledrion\Utility::getCurrentUserID() : $persistent_uid;

        $criteria = new \Criteria('persistent_uid', $persistent_uid, '=');

        return $this->deleteAll($criteria);
    }

    /**
     * Supprime UN produit d'un utilisateur
     *
     * @param int $persistent_product_id L'identifiant du produit
     * @param int $persistent_uid        L'identifiant de l'utilisateur
     * @return bool Le résultat de la suppression
     */
    public function deleteUserProduct($persistent_product_id, $persistent_uid = 0)
    {
        if (0 == Oledrion\Utility::getModuleOption('persistent_cart')) {
            return true;
        }
        $persistent_uid = 0 == $persistent_uid ? Oledrion\Utility::getCurrentUserID() : $persistent_uid;
        $criteria       = new \CriteriaCompo();
        $criteria->add(new \Criteria('persistent_uid', $persistent_uid, '='));
        $criteria->add(new \Criteria('persistent_product_id', $persistent_product_id, '='));

        return $this->deleteAll($criteria);
    }

    /**
     * Ajoute un produit au panier d'un utilisateur
     *
     * @param int $persistent_product_id L'ID du produit
     * @param int $persistent_qty        La quantité de produits
     * @param int $persistent_uid        L'ID de l'utilisateur
     * @return bool Le résultat de l'ajout du produit
     */
    public function addUserProduct($persistent_product_id, $persistent_qty, $persistent_uid = 0)
    {
        if (0 == Oledrion\Utility::getModuleOption('persistent_cart')) {
            return true;
        }
        $persistent_uid  = 0 == $persistent_uid ? Oledrion\Utility::getCurrentUserID() : $persistent_uid;
        $persistent_cart = $this->create(true);
        $persistent_cart->setVar('persistent_product_id', $persistent_product_id);
        $persistent_cart->setVar('persistent_uid', $persistent_uid);
        $persistent_cart->setVar('persistent_date', time());
        $persistent_cart->setVar('persistent_qty', $persistent_qty);

        return $this->insert($persistent_cart, true);
    }

    /**
     * Mise à jour de la quantité de produit d'un utilisateur
     *
     * @param int $persistent_product_id L'identifiant du produit
     * @param int $persistent_qty        La quantité de produit
     * @param int $persistent_uid        L'ID de l'utilisateur
     * @return bool Le résultat de la mise à jour
     */
    public function updateUserProductQuantity($persistent_product_id, $persistent_qty, $persistent_uid = 0)
    {
        if (0 == Oledrion\Utility::getModuleOption('persistent_cart')) {
            return true;
        }
        $persistent_uid = 0 == $persistent_uid ? Oledrion\Utility::getCurrentUserID() : $persistent_uid;
        $criteria       = new \CriteriaCompo();
        $criteria->add(new \Criteria('persistent_uid', $persistent_uid, '='));
        $criteria->add(new \Criteria('persistent_product_id', $persistent_product_id, '='));

        return $this->updateAll('persistent_qty', $persistent_qty, $criteria, true);
    }

    /**
     * Indique s'il existe un panier pour un utilisateur
     *
     * @param int $persistent_uid L'id de l'utilisateur
     * @return bool
     */
    public function isCartExists($persistent_uid = 0)
    {
        if (0 == Oledrion\Utility::getModuleOption('persistent_cart')) {
            return false;
        }
        $persistent_uid = 0 == $persistent_uid ? Oledrion\Utility::getCurrentUserID() : $persistent_uid;
        $criteria       = new \Criteria('persistent_uid', $persistent_uid, '=');

        return (bool)$this->getCount($criteria);
    }

    /**
     * Retourne les produits d'un utilisateur
     *
     * @param int $persistent_uid L'ID de l'utilisateur
     * @return array|bool   Tableaux d'objets de type PersistentCart
     */
    public function getUserProducts($persistent_uid = 0)
    {
        if (0 == Oledrion\Utility::getModuleOption('persistent_cart')) {
            return false;
        }
        $persistent_uid = 0 == $persistent_uid ? Oledrion\Utility::getCurrentUserID() : $persistent_uid;
        $criteria       = new \Criteria('persistent_uid', $persistent_uid, '=');

        return $this->getObjects($criteria);
    }
}
