<?php namespace XoopsModules\Oledrion;

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
 * Gestion des listes utilisateurs
 *
 * @since 2.3.2009.06.13
 */

use XoopsModules\Oledrion;
use XoopsModules\Oledrion\Constants;

require_once __DIR__ . '/classheader.php';


/**
 * Class ListsHandler
 */
class ListsHandler extends OledrionPersistableObjectHandler
{
    /**
     * ListsHandler constructor.
     * @param \XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db)
    { //                            Table               Classe           Id       Identifiant
        parent::__construct($db, 'oledrion_lists', Lists::class, 'list_id', 'list_title');
    }

    /**
     * Incrémente le compteur de vues d'une liste
     *
     * @param  Lists $list
     * @return boolean
     */
    public function incrementListViews(Lists $list)
    {
        $res = true;
        if (Oledrion\Utility::getCurrentUserID() != $list->getVar('list_uid')) {
            $sql = 'UPDATE ' . $this->table . ' SET list_views = list_views + 1 WHERE list_id = ' . $list->getVar('list_id');
            $res = $this->db->queryF($sql);
            $this->forceCacheClean();
        }

        return $res;
    }

    /**
     * Incrémente le nombre de produits dans une liste
     *
     * @param  Lists $list
     * @return boolean
     */
    public function incrementListProductsCount(Lists $list)
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
     * @param  Lists $list
     * @param  int            $value
     * @return bool
     */
    public function decrementListProductsCount(Lists $list, $value = 1)
    {
        $value = (int)$value;
        $res   = true;
        $sql   = 'UPDATE ' . $this->table . ' SET list_productscount = list_productscount - $value WHERE list_id = ' . $list->getVar('list_id');
        $res   = $this->db->queryF($sql);
        $this->forceCacheClean();

        return $res;
    }

    /**
     * Retourne la liste des listes récentes
     *
     * @param  Parameters $parameters
     * @return array               Tableau d'objets de type Lists [clé] = id liste
     * @internal param int $start
     * @internal param int $limit
     * @internal param string $sort
     * @internal param string $order
     * @internal param bool $idAsKey
     * @internal param int $listType
     * @internal param int $list_uid
     */
    public function getRecentLists(Parameters $parameters)
    {
        $parameters = $parameters->extend(new Oledrion\Parameters([
                                                                      'start'    => 0,
                                                                      'limit'    => 0,
                                                                      'sort'     => 'list_date',
                                                                      'order'    => 'DESC',
                                                                      'idAsKey'  => true,
                                                                      'listType' => Constants::OLEDRION_LISTS_ALL,
                                                                      'list_uid' => 0
                                                                  ]));
        $criteria   = new \CriteriaCompo();
        switch ($parameters['listType']) {
            case Constants::OLEDRION_LISTS_ALL:
                $criteria->add(new \Criteria('list_id', 0, '<>'));
                break;
            case Constants::OLEDRION_LISTS_ALL_PUBLIC:
                $criteria->add(new \Criteria('list_type', Constants::OLEDRION_LISTS_WISH, '='));
                $criteria->add(new \Criteria('list_type', Constants::OLEDRION_LISTS_RECOMMEND, '='), 'OR');
                break;
            default:
                $criteria->add(new \Criteria('list_type', $parameters['listType'], '='));
                break;
        }
        if ($parameters['list_uid'] > 0) {
            $criteria->add(new \Criteria('list_uid', $parameters['list_uid'], '='));
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
     * @param  integer $listType
     * @param  integer $list_uid
     * @return integer
     */
    public function getRecentListsCount($listType = Constants::OLEDRION_LISTS_ALL, $list_uid = 0)
    {
        $criteria = new \CriteriaCompo();
        switch ($listType) {
            case Constants::OLEDRION_LISTS_ALL:
                $criteria->add(new \Criteria('list_id', 0, '<>'));
                break;
            case Constants::OLEDRION_LISTS_ALL_PUBLIC:
                $criteria->add(new \Criteria('list_type', Constants::OLEDRION_LISTS_WISH, '='));
                $criteria->add(new \Criteria('list_type', Constants::OLEDRION_LISTS_RECOMMEND, '='), 'OR');
                break;
            default:
                $criteria->add(new \Criteria('list_type', $listType, '='));
                break;
        }
        if ($list_uid > 0) {
            $criteria->add(new \Criteria('list_uid', $list_uid, '='));
        }

        return $this->getCount($criteria);
    }

    /**
     * Retourne une liste d'utilisateurs Xoops en fonction d'une liste de listes
     *
     * @param  array $oledrion_lists
     * @return array [clé] = id utilisateur
     */
    public function getUsersFromLists($oledrion_lists)
    {
        $usersList = [];
        foreach ($oledrion_lists as $list) {
            $usersList[] = $list->list_uid;
        }
        if (count($usersList) > 0) {
            return Oledrion\Utility::getUsersFromIds($usersList);
        } else {
            return [];
        }
    }

    /**
     * Suppression d'une liste (et des produits qui lui sont rattachés)
     *
     * @param  Lists $list
     * @return boolean
     */
    public function deleteList(Lists $list)
    {
//        $handlers = HandlerManager::getInstance();
        $productsListHandler->deleteListProducts($list);

        return $this->delete($list, true);
    }

    /**
     * Retourne les produits d'une liste
     *
     * @param  Lists $list
     * @return array          Objets de type Products
     */
    public function getListProducts(Lists $list)
    {
        $db = \XoopsDatabaseFactory::getDatabaseConnection();
        $productsListHandler = new Oledrion\ProductsListHandler($db);
        $productsHandler = new Oledrion\ProductsHandler($db);
        $productsInList = $ret = $productsIds = [];
//        $handlers       = HandlerManager::getInstance();
        $productsInList = $productsListHandler->getProductsFromList($list);
        if (0 == count($productsInList)) {
            return $ret;
        }
        foreach ($productsInList as $product) {
            $productsIds[] = $product->getVar('productlist_product_id');
        }
        if (count($productsIds) > 0) {
            $ret = $productsHandler->getProductsFromIDs($productsIds);
        }

        return $ret;
    }

    /**
     * Indique si une liste appartient bien à un utilisateur
     *
     * @param  integer $list_id
     * @param  integer $list_uid
     * @return boolean
     */
    public function isThisMyList($list_id, $list_uid = 0)
    {
        if (0 == $list_uid) {
            $list_uid = Oledrion\Utility::getCurrentUserID();
        }
        $list = null;
        $list = $this->get((int)$list_id);
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
     * @param  integer $productlist_product_id
     * @param  integer $list_uid
     * @return boolean
     */
    public function isProductInUserList($productlist_product_id, $list_uid = 0)
    {
        //require_once __DIR__ . '/lite.php';
        if (0 == $list_uid) {
            $list_uid = Oledrion\Utility::getCurrentUserID();
        }
        if (0 == $list_uid) {
            return true;
        }
        $ret                    = false;
        $start                  = $limit = 0;
        $list_uid               = (int)$list_uid;
        $productlist_product_id = (int)$productlist_product_id;
        $sql                    = 'SELECT COUNT(*) FROM ' . $this->table . ' l, ' . $this->db->prefix('oledrion_products_list') . ' p WHERE (p.productlist_list_id = l.list_id) AND (l.list_uid = ' . $list_uid . ') AND (p.productlist_product_id =' . $productlist_product_id . ')';
        //$Cache_Lite = new Oledrion_Cache_Lite($this->cacheOptions);
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
     * @param          $categoryId
     * @param  integer $list_type Le type de liste
     * @param  integer $limit     Le nombre maximum de listes à retourner
     * @return array   Objets de type Lists, [clé] = id liste
     * @internal param int $cateGoryId L'identifiant de la catégorie
     */
    public function listsFromCurrentCategory($categoryId, $list_type, $limit)
    {
        //require_once __DIR__ . '/lite.php';
        $ret        = [];
        $start      = 0;
        $categoryId = (int)$categoryId;
        $list_type  = (int)$list_type;
        $limit      = (int)$limit;
        $sql        = 'SELECT DISTINCT(z.productlist_list_id) FROM '
                      . $this->db->prefix('oledrion_products_list')
                      . ' z, '
                      . $this->db->prefix('oledrion_products')
                      . ' p, '
                      . $this->db->prefix('oledrion_lists')
                      . ' l WHERE (l.list_type = '
                      . $list_type
                      . ') AND (p.product_cid = '
                      . $categoryId
                      . ') AND (l.list_id = z.productlist_list_id) AND (z.productlist_product_id = p.product_id) AND (p.product_online = 1) ORDER BY l.list_date DESC';
        //$Cache_Lite = new Oledrion_Cache_Lite($this->cacheOptions);
        $id = $this->_getIdForCache($sql, $start, $limit);
        //$cacheData = $Cache_Lite->get($id);
        //if ($cacheData === false) {
        $result = $this->db->query($sql, $limit, $start);
        if ($result) {
            while (false !== ($row = $this->db->fetchArray($result))) {
                $ret[] = $row['productlist_list_id'];
            }
            $ret = $this->getItemsFromIds($ret);
        }

        //$Cache_Lite->save($ret);
        return $ret;
        //} else {
        //  return $cacheData;
        //}
    }
}
