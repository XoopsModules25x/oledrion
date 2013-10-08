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
 * Gestion des listes utilisateurs
 *
 * @since 2.3.2009.06.13
 */
require 'classheader.php';

/**
 * Définition des types de listes
 */
define("OLEDRION_LISTS_ALL_PUBLIC", -2); // Que les publiques
define("OLEDRION_LISTS_ALL", -1); // Toutes sans distinction
define("OLEDRION_LISTS_PRIVATE", 0);
define("OLEDRION_LISTS_WISH", 1);
define("OLEDRION_LISTS_RECOMMEND", 2);

class oledrion_lists extends Oledrion_Object
{
    public function __construct()
    {
        $this->initVar('list_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('list_uid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('list_title', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('list_date', XOBJ_DTYPE_INT, null, false);
        $this->initVar('list_productscount', XOBJ_DTYPE_INT, null, false);
        $this->initVar('list_views', XOBJ_DTYPE_INT, null, false);
        $this->initVar('list_password', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('list_type', XOBJ_DTYPE_INT, null, false);
        $this->initVar('list_description', XOBJ_DTYPE_TXTAREA, null, false);
    }

    /**
     * Indique si la liste courante est accessible de l'utilisateur courant
     *
     * @return boolean
     */
    public function isSuitableForCurrentUser()
    {
        $uid = oledrion_utils::getCurrentUserID();
        if ($this->getVar('list_type') == OLEDRION_LISTS_PRIVATE) {
            if ($uid == 0 || $uid != $this->getVar('list_uid')) {
                return false;
            }
        }
        return true;
    }

    /**
     * Retourne un tableau associatif qui pour chaque type de liste indique son type sous forme de texte
     *
     * @return array
     */
    static function getTypesArray()
    {
        return array(OLEDRION_LISTS_PRIVATE => _OLEDRION_LIST_PRIVATE, OLEDRION_LISTS_WISH => _OLEDRION_LIST_PUBLIC_WISH_LIST, OLEDRION_LISTS_RECOMMEND => _OLEDRION_LIST_PUBLIC_RECOMMENDED_LIST);
    }

    /**
     * Retourne la description de la liste courante
     *
     * @return string
     */
    public function getListTypeDescription()
    {
        $description = $this->getTypesArray();
        return $description[$this->list_type];
    }

    /**
     * Retourne l'url à utiliser pour accéder à la liste en tenant compte des préférences du module
     *
     * @return string    L'url à utiliser
     */
    public function getLink()
    {
        $url = '';
        if (oledrion_utils::getModuleOption('urlrewriting') == 1) { // On utilise l'url rewriting
            $url = OLEDRION_URL . 'list-' . $this->getVar('list_id') . oledrion_utils::makeSeoUrl($this->getVar('list_title', 'n')) . '.html';
        } else { // Pas d'utilisation de l'url rewriting
            $url = OLEDRION_URL . 'list.php?list_id=' . $this->getVar('list_id');
        }
        return $url;
    }

    /**
     * Retourne la date de création de la liste formatée
     *
     * @param string $format
     * @return string
     */
    public function getFormatedDate($format = 's')
    {
        return formatTimestamp($this->list_date, $format);
    }

    /**
     * Rentourne la chaine à utiliser dans une balise <a> pour l'attribut href
     *
     * @return string
     */
    public function getHrefTitle()
    {
        return oledrion_utils::makeHrefTitle($this->getVar('list_title'));
    }

    /**
     * Retourne le nom de l'auteur de la liste courante
     *
     * @return string
     */
    public function getListAuthorName()
    {
        return XoopsUser::getUnameFromId($this->getVar('list_uid', true));
    }

    /**
     * Retourne les éléments formatés pour affichage (en général)
     *
     * @param string $format
     * @return array
     */
    public function toArray($format = 's')
    {
        $ret = array();
        $ret = parent::toArray($format);
        $ret['list_type_description'] = $this->getListTypeDescription();
        $ret['list_href_title'] = $this->getHrefTitle();
        $ret['list_url_rewrited'] = $this->getLink();
        $ret['list_formated_date'] = $this->getFormatedDate();
        $ret['list_username'] = $this->getListAuthorName();
        $ret['list_formated_count'] = sprintf(_OLEDRION_PRODUCTS_COUNT, $this->getVar('list_productscount'));
        return $ret;
    }

}


class OledrionOledrion_listsHandler extends Oledrion_XoopsPersistableObjectHandler
{
    public function __construct($db)
    { //							Table				Classe			 Id       Identifiant
        parent::__construct($db, 'oledrion_lists', 'oledrion_lists', 'list_id', 'list_title');
    }

    /**
     * Incrémente le compteur de vues d'une liste
     *
     * @param oledrion_lists $list
     * @return boolean
     */
    public function incrementListViews(oledrion_lists $list)
    {
        $res = true;
        if (oledrion_utils::getCurrentUserID() != $list->getVar('list_uid')) {
            $sql = 'UPDATE ' . $this->table . ' SET list_views = list_views + 1 WHERE list_id = ' . $list->getVar('list_id');
            $res = $this->db->queryF($sql);
            $this->forceCacheClean();
        }
        return $res;
    }


    /**
     * Incrémente le nombre de produits dans une liste
     *
     * @param oledrion_lists $list
     * @return boolean
     */
    public function incrementListProductsCount(oledrion_lists $list)
    {
        $res = true;
        $sql = 'UPDATE ' . $this->table . ' SET list_productscount = list_productscount + 1 WHERE list_id = ' . $list->getVar('list_id');
        $res = $this->db->queryF($sql);
        $this->forceCacheClean();
        return $res;
    }

    /**
     * Décrémente le nombre de produits dans une liste
     *
     * @param oledrion_lists $list
     * @return boolean
     */
    public function decrementListProductsCount(oledrion_lists $list, $value = 1)
    {
        $value = intval($value);
        $res = true;
        $sql = 'UPDATE ' . $this->table . ' SET list_productscount = list_productscount - $value WHERE list_id = ' . $list->getVar('list_id');
        $res = $this->db->queryF($sql);
        $this->forceCacheClean();
        return $res;
    }


    /**
     * Retourne la liste des listes récentes
     *
     * @param integer $start
     * @param integer $limit
     * @param string $sort
     * @param string $order
     * @param boolean $idAsKey
     * @param integer $listType
     * @param integer $list_uid
     * @return array    Tableau d'objets de type oledrion_lists [clé] = id liste
     */
    public function getRecentLists(oledrion_parameters $parameters)
    {
        $parameters = $parameters->extend(new oledrion_parameters(array('start' => 0, 'limit' => 0, 'sort' => 'list_date', 'order' => 'DESC', 'idAsKey' => true, 'listType' => OLEDRION_LISTS_ALL, 'list_uid' => 0)));
        $criteria = new CriteriaCompo();
        switch ($parameters['listType']) {
            case OLEDRION_LISTS_ALL:
                $criteria->add(new Criteria('list_id', 0, '<>'));
                break;
            case OLEDRION_LISTS_ALL_PUBLIC:
                $criteria->add(new Criteria('list_type', OLEDRION_LISTS_WISH, '='));
                $criteria->add(new Criteria('list_type', OLEDRION_LISTS_RECOMMEND, '='), 'OR');
                break;
            default:
                $criteria->add(new Criteria('list_type', $parameters['listType'], '='));
                break;
        }
        if ($parameters['list_uid'] > 0) {
            $criteria->add(new Criteria('list_uid', $parameters['list_uid'], '='));
        }
        $criteria->setSort($parameters['sort']);
        $criteria->setOrder($parameters['order']);
        $criteria->setStart($parameters['start']);
        $criteria->setLimit($parameters['limit']);
        return $this->getObjects($criteria, $parameters['idAsKey']);
    }

    /**
     * Retourne le nombre de listes d'un certain type
     *
     * @param integer $listType
     * @param integer $list_uid
     * @return integer
     */
    public function getRecentListsCount($listType = OLEDRION_LISTS_ALL, $list_uid = 0)
    {
        $criteria = new CriteriaCompo();
        switch ($listType) {
            case OLEDRION_LISTS_ALL:
                $criteria->add(new Criteria('list_id', 0, '<>'));
                break;
            case OLEDRION_LISTS_ALL_PUBLIC:
                $criteria->add(new Criteria('list_type', OLEDRION_LISTS_WISH, '='));
                $criteria->add(new Criteria('list_type', OLEDRION_LISTS_RECOMMEND, '='), 'OR');
                break;
            default:
                $criteria->add(new Criteria('list_type', $listType, '='));
                break;
        }
        if ($list_uid > 0) {
            $criteria->add(new Criteria('list_uid', $list_uid, '='));
        }
        return $this->getCount($criteria);
    }

    /**
     * Retourne une liste d'utilisateurs Xoops en fonction d'une liste de listes
     *
     * @param array $oledrion_lists
     * @return array [clé] = id utilisateur
     */
    public function getUsersFromLists($oledrion_lists)
    {
        $usersList = array();
        foreach ($oledrion_lists as $list) {
            $usersList[] = $list->list_uid;
        }
        if (count($usersList) > 0) {
            return oledrion_utils::getUsersFromIds($usersList);
        } else {
            return array();
        }
    }

    /**
     * Suppression d'une liste (et des produits qui lui sont rattachés)
     *
     * @param oledrion_lists $list
     * @return boolean
     */
    public function deleteList(oledrion_lists $list)
    {
        $handlers = oledrion_handler::getInstance();
        $handlers->h_oledrion_products_list->deleteListProducts($list);
        return $this->delete($list, true);
    }

    /**
     * Retourne les produits d'une liste
     *
     * @param oledrion_lists $list
     * @return array    Objets de type oledrion_products
     */
    public function getListProducts(oledrion_lists $list)
    {
        $productsInList = $ret = $productsIds = array();
        $handlers = oledrion_handler::getInstance();
        $productsInList = $handlers->h_oledrion_products_list->getProductsFromList($list);
        if (count($productsInList) == 0) {
            return $ret;
        }
        foreach ($productsInList as $product) {
            $productsIds[] = $product->getVar('productlist_product_id');
        }
        if (count($productsIds) > 0) {
            $ret = $handlers->h_oledrion_products->getProductsFromIDs($productsIds);
        }
        return $ret;
    }

    /**
     * Indique si une liste appartient bien à un utilisateur
     *
     * @param integer $list_id
     * @param integer $list_uid
     * @return boolean
     */
    public function isThisMyList($list_id, $list_uid = 0)
    {
        if ($list_uid == 0) {
            $list_uid = oledrion_utils::getCurrentUserID();
        }
        $list = null;
        $list = $this->get(intval($list_id));
        if (!is_object($list)) {
            return false;
        }
        if ($list->getVar('list_uid') == $list_uid) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Indique si un produit est dans une liste d'un utilisateur
     *
     * @param integer $productlist_product_id
     * @param integer $list_uid
     * @return boolean
     */
    public function isProductInUserList($productlist_product_id, $list_uid = 0)
    {
        //require_once 'lite.php';
        if ($list_uid == 0) {
            $list_uid = oledrion_utils::getCurrentUserID();
        }
        if ($list_uid == 0) {
            return true;
        }
        $ret = false;
        $start = $limit = 0;
        $list_uid = intval($list_uid);
        $productlist_product_id = intval($productlist_product_id);
        $sql = 'SELECT Count(*) FROM ' . $this->table . ' l, ' . $this->db->prefix('oledrion_products_list') . ' p WHERE (p.productlist_list_id = l.list_id) AND (l.list_uid = ' . $list_uid . ') AND (p.productlist_product_id =' . $productlist_product_id . ')';
        //$Cache_Lite = new oledrion_Cache_Lite($this->cacheOptions);
        $id = $this->_getIdForCache($sql, $start, $limit);
        //$cacheData = $Cache_Lite->get($id);
        //if ($cacheData === false) {
        $result = $this->db->query($sql, $limit, $start);
        if ($result) {
            list($count) = $this->db->fetchRow($result);
            if ($count > 0) {
                $ret = true;
            }
        }
        //$Cache_Lite->save($ret);
        return $ret;
        //} else {
        //return $cacheData;
        //}
    }

    /**
     * Retourne les x dernières listes qui contiennent des produits dans une certaine catégorie
     *
     * @param integer $cateGoryId    L'identifiant de la catégorie
     * @param integer $list_type    Le type de liste
     * @param integer $limit        Le nombre maximum de listes à retourner
     * @return array                Objets de type oledrion_lists, [clé] = id liste
     */
    public function listsFromCurrentCategory($categoryId, $list_type, $limit)
    {
        //require_once 'lite.php';
        $ret = array();
        $start = 0;
        $categoryId = intval($categoryId);
        $list_type = intval($list_type);
        $limit = intval($limit);
        $sql = 'SELECT distinct(z.productlist_list_id) FROM ' . $this->db->prefix('oledrion_products_list') . ' z, ' . $this->db->prefix('oledrion_products') . ' p, ' . $this->db->prefix('oledrion_lists') . ' l WHERE (l.list_type = ' . $list_type . ') AND (p.product_cid = ' . $categoryId . ') AND (l.list_id = z.productlist_list_id) AND (z.productlist_product_id = p.product_id) AND (p.product_online = 1) ORDER BY l.list_date DESC';
        //$Cache_Lite = new oledrion_Cache_Lite($this->cacheOptions);
        $id = $this->_getIdForCache($sql, $start, $limit);
        //$cacheData = $Cache_Lite->get($id);
        //if ($cacheData === false) {
        $result = $this->db->query($sql, $limit, $start);
        if ($result) {
            while ($row = $this->db->fetchArray($result)) {
                $ret[] = $row['productlist_list_id'];
            }
            $ret = $this->getItemsFromIds($ret);
        }
        //$Cache_Lite->save($ret);
        return $ret;
        //} else {
        //	return $cacheData;
        //}
    }
}
