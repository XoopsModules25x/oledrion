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
 * @copyright   {@link http://xoops.org/ XOOPS Project}
 * @license     {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author      Hervé Thouzard (http://www.herve-thouzard.com/)
 */

/**
 * Relation entre listes utilisateurs et produits
 *
 * @since 2.3.2009.06.13
 */
require __DIR__ . '/classheader.php';

/**
 * Class Oledrion_products_list
 */
class Oledrion_products_list extends Oledrion_Object
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
        $this->initVar('productlist_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('productlist_list_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('productlist_product_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('productlist_date', XOBJ_DTYPE_TXTBOX, null, false);
    }
}

/**
 * Class OledrionOledrion_products_listHandler
 */
class OledrionOledrion_products_listHandler extends Oledrion_XoopsPersistableObjectHandler
{
    /**
     * OledrionOledrion_products_listHandler constructor.
     * @param XoopsDatabase|null $db
     */
    public function __construct(XoopsDatabase $db)
    { //                            Table                       Classe                  Id
        parent::__construct($db, 'oledrion_products_list', 'oledrion_products_list', 'productlist_id');
    }

    /**
     * Supprime les produits liés à une liste
     *
     * @param  oledrion_lists $list
     * @return boolean
     */
    public function deleteListProducts(Oledrion_lists $list)
    {
        return $this->deleteAll(new Criteria('productlist_list_id', $list->list_id, '='));
    }

    /**
     * Supprime un produit de toutes les listes
     *
     * @param  integer $productlist_product_id
     * @return booelan
     */
    public function deleteProductFromLists($productlist_product_id)
    {
        return $this->deleteAll(new Criteria('productlist_product_id', $productlist_product_id, '='));
    }

    /**
     * Retourne la liste des produits appartenants à une liste
     *
     * @param  oledrion_lists $list
     * @return array
     */
    public function getProductsFromList(Oledrion_lists $list)
    {
        return $this->getObjects(new criteria('productlist_list_id', $list->getVar('list_id'), '='));
    }

    /**
     * Supprime un produit d'une liste
     *
     * @param  integer $productlist_list_id
     * @param  integer $productlist_product_id
     * @return bool
     */
    public function deleteProductFromList($productlist_list_id, $productlist_product_id)
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('productlist_list_id', $productlist_list_id, '='));
        $criteria->add(new Criteria('productlist_product_id', $productlist_product_id, '='));

        return $this->deleteAll($criteria);
    }

    /**
     * Ajoute un produit à une liste utilisateur
     *
     * @param          $productlist_list_id
     * @param  integer $productlist_product_id Id du produit
     * @return bool
     * @internal param int $productlist_id Id de la liste
     */
    public function addProductToUserList($productlist_list_id, $productlist_product_id)
    {
        $product_list = $this->create(true);
        $product_list->setVar('productlist_list_id', (int)$productlist_list_id);
        $product_list->setVar('productlist_product_id', (int)$productlist_product_id);
        $product_list->setVar('productlist_date', Oledrion_utils::getCurrentSQLDate());

        return $this->insert($product_list, true);
    }

    /**
     * Indique si un produit se trouve déjà dans une liste
     *
     * @param  integer $productlist_list_id
     * @param  integer $productlist_product_id
     * @return bool
     */
    public function isProductAlreadyInList($productlist_list_id, $productlist_product_id)
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('productlist_list_id', $productlist_list_id, '='));
        $criteria->add(new Criteria('productlist_product_id', $productlist_product_id, '='));
        if ($this->getCount($criteria) > 0) {
            return true;
        } else {
            return false;
        }
    }
}
