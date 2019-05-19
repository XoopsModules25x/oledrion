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
 * Gestion des caddy
 */

/**
 * Class CaddyHandler
 */
class CaddyHandler extends OledrionPersistableObjectHandler
{
    const CADDY_NAME = 'oledrion_caddie'; // Nom du panier en session

    /**
     * Oledrion\CaddyHandler constructor.
     * @param \XoopsDatabase|null $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        //                          Table             Classe          Id
        parent::__construct($db, 'oledrion_caddy', Caddy::class, 'caddy_id');
    }

    /**
     * Renvoie, si on en trouve un, un produit qui s'est bien vendu avec un produit particulier
     *
     * @param  int $caddy_product_id Identifiant du produit dont on recherche le jumeau
     * @return int Le n° du produit le plus vendu avec le produit en question
     */
    public function getBestWith($caddy_product_id)
    {
        $sql    = 'SELECT caddy_product_id, sum(caddy_qte) mv FROM ' . $this->table . ' WHERE caddy_cmd_id IN (SELECT caddy_cmd_id FROM ' . $this->table . ' WHERE caddy_product_id=' . (int)$caddy_product_id . ') GROUP BY caddy_product_id ORDER BY mv DESC';
        $result = $this->db->query($sql, 1);
        if (!$result) {
            return 0;
        }
        $myrow = $this->db->fetchArray($result);
        $id    = $myrow['caddy_product_id'];
        if ($id != $caddy_product_id) {
            return $id;
        }

        return 0;
    }

    /**
     * Renvoie la liste des produits les plus vendus toutes catégories confondues
     *
     * @param int       $start Début de la recherche
     * @param int       $limit Nombre maximum d'enregistrements à retourner
     * @param int|array $product_cid
     * @param bool      $withQuantity
     * @return array   Les identifiants des X produits les plus vendus dans cette catégorie
     */
    public function getMostSoldProducts($start = 0, $limit = 0, $product_cid = 0, $withQuantity = false)
    {
        //require_once __DIR__ . '/lite.php';
        $ret = [];
        if ($product_cid && is_array($product_cid)) {
            $sql = 'SELECT c.caddy_product_id, sum( c.caddy_qte ) AS mv FROM ' . $this->table . ' c, ' . $this->db->prefix('oledrion_products') . ' b WHERE (c.caddy_product_id = b.product_id) AND b.product_cid IN (' . implode(',', $product_cid) . ') GROUP BY c.caddy_product_id ORDER BY mv DESC';
        } elseif ($product_cid > 0) {
            $sql = 'SELECT c.caddy_product_id, sum( c.caddy_qte ) AS mv FROM ' . $this->table . ' c, ' . $this->db->prefix('oledrion_products') . ' b WHERE (c.caddy_product_id = b.product_id) AND b.product_cid = ' . (int)$product_cid . ' GROUP BY c.caddy_product_id ORDER BY mv DESC';
        } else {
            $sql = 'SELECT caddy_product_id, sum( caddy_qte ) AS mv FROM ' . $this->table . ' GROUP BY caddy_product_id ORDER BY mv DESC';
        }
        //$Cache_Lite = new Oledrion_Cache_Lite($this->cacheOptions);
        $id = $this->_getIdForCache($sql, $start, $limit);
        //$cacheData = $Cache_Lite->get($id);
        //if ($cacheData === false) {
        $result = $this->db->query($sql, $limit, $start);
        if ($result) {
            while (false !== ($myrow = $this->db->fetchArray($result))) {
                if (!$withQuantity) {
                    $ret[$myrow['caddy_product_id']] = $myrow['caddy_product_id'];
                } else {
                    $ret[$myrow['caddy_product_id']] = $myrow['mv'];
                }
            }
        }

        //$Cache_Lite->save($ret);
        return $ret;
        //} else {
        //return $cacheData;
        //}
    }

    /**
     * Retourne la liste des ID de produits vendus récemment
     *
     * @param  int $start
     * @param  int $limit
     * @return array
     * @since 2.3.2009.04.08
     */
    public function getRecentlySoldProducts($start = 0, $limit = 0)
    {
        //require_once __DIR__ . '/lite.php';
        $ret = [];
        $sql = 'SELECT c.caddy_product_id FROM ' . $this->table . ' c, ' . $this->db->prefix('oledrion_commands') . ' o WHERE (c.caddy_cmd_id = o.cmd_id) AND (o.cmd_state = ' . Constants::OLEDRION_STATE_VALIDATED . ') ORDER BY cmd_date DESC';
        //$Cache_Lite = new Oledrion_Cache_Lite($this->cacheOptions);
        $id = $this->_getIdForCache($sql, $start, $limit);
        //$cacheData = $Cache_Lite->get($id);
        //if ($cacheData === false) {
        $result = $this->db->query($sql, $limit, $start);
        if ($result) {
            while (false !== ($row = $this->db->fetchArray($result))) {
                $ret[$row['caddy_product_id']] = $row['caddy_product_id'];
            }
        }

        //$Cache_Lite->save($ret);
        return $ret;
        //} else {
        //return $cacheData;
        //}
    }

    /**
     * Indique si le caddy est vide ou pas
     *
     * @return bool vide, ou pas...
     */
    public function isCartEmpty()
    {
        if (isset($_SESSION[self::CADDY_NAME])) {
            return false;
        }

        return true;
    }

    /**
     * Vidage du caddy, s'il existe
     */
    public function emptyCart()
    {
        global $xoopsUser, $persistentCartHandler;
        if (isset($_SESSION[self::CADDY_NAME])) {
            unset($_SESSION[self::CADDY_NAME]);
            if (is_object($xoopsUser)) {
                $persistentCartHandler->deleteAllUserProducts();
            }
        }
    }

    /**
     * Recharge le dernier panier de l'utilisateur
     *
     * @return bool
     */
    public function reloadPersistentCart()
    {
        global $xoopsUser, $persistentCartHandler;
        if (0 == Oledrion\Utility::getModuleOption('persistent_cart')) {
            return false;
        }
        if (is_object($xoopsUser)) {
            $persistent_carts = [];
            $persistent_carts = $persistentCartHandler->getUserProducts();
            if (count($persistent_carts) > 0) {
                foreach ($persistent_carts as $persistent_cart) {
                    $this->addProduct($persistent_cart->getVar('persistent_product_id'), $persistent_cart->getVar('persistent_qty'), null);
                }
            }
        }

        return true;
    }

    /**
     * Ajout d'un produit au caddy
     *
     * @param  int   $product_id   Identifiant du produit
     * @param  int   $quantity     Quantité à ajouter
     * @param  array $attributes   Les attributs du produit
     * @note : Structure du panier (tableau en session) :
     *                             [clé] = numéro de 1 à N
     *                             [valeur] = array (
     *                             'number' => numéro de 1 à N
     *                             'id' => ID du produit
     *                             'qty' => Quantité de produit
     *                             'attributes' => array(
     *                             'attr_id' => id attribut (son numéro dans la base)
     *                             'values' => array(valueId1, valueId2 ...)
     *                             )
     *                             )
     */
    public function addProduct($product_id, $quantity, $attributes = null)
    {
        global $xoopsUser, $persistentCartHandler;
        $tbl_caddie = $tbl_caddie2 = [];
        if (isset($_SESSION[self::CADDY_NAME])) {
            $tbl_caddie = $_SESSION[self::CADDY_NAME];
        }
        $exists = false;
        foreach ($tbl_caddie as $produit) {
            if ($produit['id'] == $product_id) {
                $exists                = true;
                $produit['qty']        += $quantity;
                $produit['attributes'] = $attributes;
                $newQuantity           = $produit['qty'];
            }
            $tbl_caddie2[] = $produit;
        }
        if (!$exists) {
            if (is_object($xoopsUser)) {
                $persistentCartHandler->addUserProduct($product_id, $quantity);
            }
            $datas                      = [];
            $datas['number']            = count($tbl_caddie) + 1;
            $datas['id']                = $product_id;
            $datas['qty']               = $quantity;
            $datas['attributes']        = $attributes;
            $tbl_caddie[]               = $datas;
            $_SESSION[self::CADDY_NAME] = $tbl_caddie;
        } else {
            $_SESSION[self::CADDY_NAME] = $tbl_caddie2;
            if (is_object($xoopsUser)) {
                // Le produit était déjà dans le panier, on va mettre à jour la quantité
                $persistentCartHandler->updateUserProductQuantity($product_id, $newQuantity);
            }
        }
    }

    /**
     * Inidique si un produit est dans le caddy
     *
     * @param  int $caddy_product_id Le numéro interne du produit dans la table Produits
     * @return mixed   False si le produit n'est pas dans le caddy sinon son indice dans le caddy
     * @since 2.3.2009.03.15
     */
    public function isInCart($caddy_product_id)
    {
        $cart = [];
        if (isset($_SESSION[self::CADDY_NAME])) {
            $cart = $_SESSION[self::CADDY_NAME];
        } else {
            return false;
        }
        $counter = 0;
        foreach ($cart as $produit) {
            if ($produit['id'] == $caddy_product_id) {
                return $counter;
            }
            ++$counter;
        }

        return false;
    }

    /**
     * Retourne les attributs d'un produit depuis le panier
     *
     * @param  int $caddy_product_id Le numéro interne du produit dans la table Produits
     * @return mixed   False si le produit n'est pas dans le caddy sinon ses attributs sous la forme d'un tableau
     * @since 2.3.2009.03.15
     */
    public function getProductAttributesFromCart($caddy_product_id)
    {
        $cart = [];
        if (isset($_SESSION[self::CADDY_NAME])) {
            $cart = $_SESSION[self::CADDY_NAME];
        } else {
            return false;
        }
        foreach ($cart as $produit) {
            if ($produit['id'] == $caddy_product_id) {
                return $produit['attributes'];
            }
        }

        return false;
    }

    /**
     * Renumérotage des produits dans le caddy après une suppression
     *
     * @param  array $caddy Le caddy actuel
     * @return array Le caddy avec 'number' renuméroté
     */
    private function renumberCart($caddy)
    {
        $newCaddy = [];
        $counter  = 1;
        foreach ($caddy as $values) {
            $temporary               = [];
            $temporary['number']     = $counter;
            $temporary['id']         = $values['id'];
            $temporary['qty']        = $values['qty'];
            $temporary['attributes'] = $values['attributes'];
            $newCaddy[]              = $temporary;
            ++$counter;
        }

        return $newCaddy;
    }

    /**
     * Suppression d'un produit du caddy
     *
     * @param int $indice Indice de l'élément à supprimer
     */
    public function deleteProduct($indice)
    {
        global $xoopsUser, $persistentCartHandler;
        $tbl_caddie = [];
        if (isset($_SESSION[self::CADDY_NAME])) {
            $tbl_caddie = $_SESSION[self::CADDY_NAME];
            if (isset($tbl_caddie[$indice])) {
                if (is_object($xoopsUser)) {
                    $datas = [];
                    $datas = $tbl_caddie[$indice];
                    $persistentCartHandler->deleteUserProduct($datas['id']);
                }
                unset($tbl_caddie[$indice]);
                if (count($tbl_caddie) > 0) {
                    $tbl_caddie                 = $this->renumberCart($tbl_caddie);
                    $_SESSION[self::CADDY_NAME] = $tbl_caddie;
                } else {
                    unset($_SESSION[self::CADDY_NAME]);
                }
            }
        }
    }

    /**
     * Mise à jour des quantités du caddy suite à la validation du formulaire du caddy
     */
    public function updateQuantites()
    {
        global $productsHandler, $xoopsUser, $persistentCartHandler;
        $tbl_caddie = $tbl_caddie2 = [];
        if (isset($_SESSION[self::CADDY_NAME])) {
            $tbl_caddie = $_SESSION[self::CADDY_NAME];
            foreach ($tbl_caddie as $produit) {
                $number = $produit['number'];
                $name   = 'qty_' . $number;
                if (isset($_POST[$name])) {
                    $valeur = \Xmf\Request::getInt($name, 0, 'POST');
                    if ($valeur > 0) {
                        $product_id = $produit['id'];
                        $product    = null;
                        $product    = $productsHandler->get($product_id);
                        if (is_object($product)) {
                            if ($product->getVar('product_stock') - $valeur > 0) {
                                $produit['qty'] = $valeur;
                                $tbl_caddie2[]  = $produit;
                            } else {
                                $produit['qty'] = $product->getVar('product_stock');
                                $tbl_caddie2[]  = $produit;
                            }
                            if (is_object($xoopsUser)) {
                                $persistentCartHandler->updateUserProductQuantity($product_id, $produit['qty']);
                            }
                        }
                    }
                } else {
                    $tbl_caddie2[] = $produit;
                }
            }
            if (count($tbl_caddie2) > 0) {
                $_SESSION[self::CADDY_NAME] = $tbl_caddie2;
            } else {
                unset($_SESSION[self::CADDY_NAME]);
            }
        }
    }

    /**
     * Renvoie les éléments constituants une commande
     *
     * @param  int $caddy_cmd_id Identifiant de la commande
     * @return array   Tableau d'objets caddy
     */
    public function getCaddyFromCommand($caddy_cmd_id)
    {
        $ret     = [];
        $critere = new \Criteria('caddy_cmd_id', $caddy_cmd_id, '=');
        $ret     = $this->getObjects($critere);

        return $ret;
    }

    /**
     * Retourne tous les produits d'un caddy
     *
     * @param  array $carts Objets de type Caddy
     * @return array Tableau d'objets de type oledrion_products, Clé = Id produit
     * @since 2.31.2009.07.25
     */
    public function getProductsFromCaddy($carts)
    {
        $ret = $productsIds = [];
        foreach ($carts as $cart) {
            $productsIds[] = $cart->getVar('caddy_product_id');
        }
        if (count($productsIds) > 0) {
            //            $handlers = HandlerManager::getInstance();
            $ret = $productsHandler->getProductsFromIDs($productsIds, true);
        }

        return $ret;
    }

    /**
     * Renvoie les ID de commandes pour un produit acheté
     *
     * @param  int $product_id Identifiant du produit
     * @return array   Les ID des commandes dans lesquelles ce produit a été commandé
     */
    public function getCommandIdFromProduct($product_id)
    {
        $ret    = [];
        $sql    = 'SELECT caddy_cmd_id FROM ' . $this->table . ' WHERE caddy_product_id=' . (int)$product_id;
        $result = $this->db->query($sql);
        if (!$result) {
            return $ret;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $ret[] = $myrow['caddy_cmd_id'];
        }

        return $ret;
    }

    /**
     * Retourne un caddy à partir de son mot de passe
     *
     * @param  string $caddy_pass Le mot de passe à utiliser
     * @return mixed  Soit un object de type oledrion_caddy ou null
     */
    public function getCaddyFromPassword($caddy_pass)
    {
        $ret     = null;
        $caddies = [];
        $critere = new \Criteria('caddy_pass', $caddy_pass, '=');
        $caddies = $this->getObjects($critere);
        if (count($caddies) > 0) {
            $ret = $caddies[0];
        }

        return $ret;
    }

    /**
     * Marque un caddy comme ayant été téléchargé
     *
     * @param \XoopsModules\Oledrion\Caddy $caddy
     * @return bool        Le résultat de la mise à jour
     */
    public function markCaddyAsNotDownloadableAnyMore(Caddy $caddy)
    {
        $caddy->setVar('caddy_pass', '');

        return $this->insert($caddy, true);
    }

    /**
     * Supprime les caddies associés à une commande
     *
     * @param  int $caddy_cmd_id
     * @return bool
     */
    public function removeCartsFromOrderId($caddy_cmd_id)
    {
        $caddy_cmd_id = (int)$caddy_cmd_id;

        return $this->deleteAll(new \Criteria('caddy_cmd_id', $caddy_cmd_id, '='));
    }
}
