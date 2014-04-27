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
 * @version     $Id: oledrion_shelf.php 12290 2014-02-07 11:05:17Z beckmi $
 */

/**
 * Facade pour les produits
 */
defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

class oledrion_shelf
{
    private $handlers;

    public function __construct()
    {
        $this->initHandlers();
    }

    /**
     * Chargement des handlers
     */
    private function initHandlers()
    {
        $this->handlers = oledrion_handler::getInstance();
    }

    /**
     * Retourne le nombre de produits d'un certain type
     *
     * @param string $type Le type de produits dont on veut récupérer le nombre
     */
    public function getProductsCount($type = 'recent', $category = 0, $excluded = 0)
    {
        switch (strtolower($type)) {
            case 'recent':
                return $this->handlers->h_oledrion_products->getRecentProductsCount($category, $excluded);
                break;
        }

        return 0;
    }

    /**
     * Supprime un produit (et tout ce qui lui est relatif)
     * @param oledrion_products $product
     */
    public function deleteProduct(oledrion_products $product)
    {
        global $xoopsModule;
        $id = $product->getVar('product_id');

        // On commence par supprimer les commentaires
        $mid = $xoopsModule->getVar('mid');
        xoops_comment_delete($mid, $id);

        // Puis les votes
        $this->handlers->h_oledrion_votedata->deleteProductRatings($id);

        // Puis les produits relatifs
        $this->handlers->h_oledrion_related->deleteProductRelatedProducts($id);

        // Les images (la grande et la miniature)
        $product->deletePictures();

        // Le fichier attaché
        $product->deleteAttachment();

        // Les fichiers attachés
        $this->handlers->h_oledrion_files->deleteProductFiles($id);

        // Suppression dans les paniers persistants enregistrés
        $this->handlers->h_oledrion_persistent_cart->deleteProductForAllCarts($id);

        // Les attributs qui lui sont rattachés
        $this->handlers->h_oledrion_attributes->deleteProductAttributes($id);

        // Le produit dans les listes
        $this->handlers->h_oledrion_products_list->deleteProductFromLists($id);

        // La relation entre le produit et le fabricant
        $this->handlers->h_oledrion_productsmanu->removeManufacturerProduct($id);

        // Le produit dans les remises
        $this->handlers->h_oledrion_discounts->removeProductFromDiscounts($id);

        // Et le produit en lui même, à la fin
        return $this->handlers->h_oledrion_products->delete($product, true);
    }


    /**
     * Cherche et retourne la liste de produits relatifs à une liste de produits
     *
     * @param  array $productsIds La liste des produits dont on cherche les produits relatifs
     * @return array Clé = ID Produit, valeurs (deuxième dimension) = liste des produits relatifs
     */
    private function getRelatedProductsFromProductsIds($productsIds)
    {
        $relatedProducts = $relatedProductsIds = array();
        if (is_array($productsIds) && count($productsIds) > 0) {
            $relatedProductsIds = $this->handlers->h_oledrion_related->getRelatedProductsFromProductsIds($productsIds);
            if (count($relatedProductsIds) > 0) {
                $tmp = array();
                foreach ($relatedProductsIds as $relatedProductId) {
                    $tmp[] = $relatedProductId->getVar('related_product_related');
                }
                $tmp = array_unique($tmp);
                sort($tmp);
                if (count($tmp) > 0) {
                    $tempRelatedProducts = $this->handlers->h_oledrion_products->getProductsFromIDs($tmp);
                    foreach ($relatedProductsIds as $relatedProductId) {
                        if (isset($tempRelatedProducts[$relatedProductId->getVar('related_product_related')])) {
                            $relatedProducts[$relatedProductId->getVar('related_product_id')][] = $tempRelatedProducts[$relatedProductId->getVar('related_product_related')];
                        }
                    }
                }
            }
        }

        return $relatedProducts;
    }


    /**
     * Retourne une liste de produits selon certains critères
     *
     * @param  oledrion_shelf_parameters $parameters Les paramètres de filtrage
     * @return array                     Tableau prêt à être utilisé dans les templates
     */
    public function getProducts(oledrion_shelf_parameters $parameters)
    {
        global $vatArray;
        $parametersValues = $parameters->getParameters();
        $productType = $parametersValues['productsType'];
        $start = $parametersValues['start'];
        $limit = $parametersValues['limit'];
        $category = $parametersValues['category'];
        $sort = $parametersValues['sort'];
        $order = $parametersValues['order'];
        $excluded = $parametersValues['excluded'];
        $withXoopsUser = $parametersValues['withXoopsUser'];
        $withRelatedProducts = $parametersValues['withRelatedProducts'];
        $withQuantity = $parametersValues['withQuantity'];
        $thisMonthOnly = $parametersValues['thisMonthOnly'];
        $ret = $xoopsUsersIDs = $users = $relatedProducts = $productsManufacturers = $manufacturersPerProduct = $products = $productsIds = $categoriesIds = $vendorsIds = $manufacturersIds = $manufacturers = $categories = $vendors = array();
        // On commence par récupérer la liste des produits
        switch (strtolower($productType)) {
            case 'recent':
                $products = $this->handlers->h_oledrion_products->getRecentProducts(new oledrion_parameters(array('start' => $start, 'limit' => $limit, 'category' => $category, 'sort' => $sort, 'order' => $order, 'excluded' => $excluded, 'thisMonthOnly' => $thisMonthOnly)));
                break;

            case 'mostsold':
                $tempProductsIds = array();
                $tempProductsIds = $this->handlers->h_oledrion_caddy->getMostSoldProducts($start, $limit, $category, $withQuantity);
                if (count($tempProductsIds) > 0) {
                    $products = $this->handlers->h_oledrion_products->getProductsFromIDs(array_keys($tempProductsIds));
                }
                break;

            case 'recentlysold':
                $tempProductsIds = array();
                $tempProductsIds = $this->handlers->h_oledrion_caddy->getRecentlySoldProducts($start, $limit);
                if (count($tempProductsIds) > 0) {
                    $tempProductsIds = array_unique($tempProductsIds);
                }
                if (count($tempProductsIds) > 0) {
                    $products = $this->handlers->h_oledrion_products->getProductsFromIDs(array_keys($tempProductsIds));
                }
                break;

            case 'mostviewed':
                $products = $this->handlers->h_oledrion_products->getMostViewedProducts(new oledrion_parameters(array('start' => $start, 'limit' => $limit, 'category' => $category, 'sort' => $sort, 'order' => $order)));
                break;

            case 'bestrated':
                $products = $this->handlers->h_oledrion_products->getBestRatedProducts(new oledrion_parameters(array('start' => $start, 'limit' => $limit, 'category' => $category, 'sort' => $sort, 'order' => $order)));
                break;

            case 'recommended':
                $products = $this->handlers->h_oledrion_products->getRecentRecommended(new oledrion_parameters(array('start' => $start, 'limit' => $limit, 'category' => $category, 'sort' => $sort, 'order' => $order)));
                break;

            case 'promotional':
                $products = $this->handlers->h_oledrion_products->getPromotionalProducts(new oledrion_parameters(array('start' => $start, 'limit' => $limit, 'category' => $category, 'sort' => $sort, 'order' => $order)));
                break;

            case 'random':
                $products = $this->handlers->h_oledrion_products->getRandomProducts(new oledrion_parameters(array('start' => $start, 'limit' => $limit, 'category' => $category, 'sort' => $sort, 'order' => $order, 'thisMonthOnly' => $thisMonthOnly)));
        }

        if (count($products) > 0) {
            $productsIds = array_keys($products);
        } else {
            return $ret;
        }

        // Recherche des Id des catégories et des vendeurs
        foreach ($products as $product) {
            $categoriesIds[] = $product->getVar('product_cid');
            $vendorsIds[] = $product->getVar('product_vendor_id');
            if ($withXoopsUser) {
                $xoopsUsersIDs[] = $product->getVar('product_submitter');
            }
        }

        $productsManufacturers = $this->handlers->h_oledrion_productsmanu->getFromProductsIds($productsIds);
        // Regroupement des fabricants par produit
        foreach ($productsManufacturers as $item) {
            $manufacturersIds[] = $item->getVar('pm_manu_id');
            $manufacturersPerProduct[$item->getVar('pm_product_id')][] = $item;
        }
        // On récupère la liste des personnes qui ont soumis les produits
        if ($withXoopsUser) {
            $users = oledrion_utils::getUsersFromIds($xoopsUsersIDs);
        }

        // Il faut récupérer la liste des produits relatifs
        if ($withRelatedProducts) {
            $relatedProducts = $this->getRelatedProductsFromProductsIds($productsIds);
        }

        $categoriesIds = array_unique($categoriesIds);
        sort($categoriesIds);

        $vendorsIds = array_unique($vendorsIds);
        sort($vendorsIds);

        $manufacturersIds = array_unique($manufacturersIds);
        sort($manufacturersIds);

        // Récupération des fabricants, des vendeurs et des catégories
        if (count($manufacturersIds) > 0) {
            $manufacturers = $this->handlers->h_oledrion_manufacturer->getManufacturersFromIds($manufacturersIds);
        }
        if (count($categoriesIds) > 0) {
            $categories = $this->handlers->h_oledrion_cat->getCategoriesFromIds($categoriesIds);
        }
        if (count($vendorsIds) > 0) {
            $vendors = $this->handlers->h_oledrion_vendors->getVendorsFromIds($vendorsIds);
        }

        $count = 1;
        $lastTitle = '';
        foreach ($products as $product) {
            $tmp = array();
            $tmp = $product->toArray();
            $lastTitle = $product->getVar('product_title');
            // Le vendeur
            if (isset($vendors[$product->getVar('product_vendor_id')])) {
                $tmp['product_vendor'] = $vendors[$product->getVar('product_vendor_id')]->toArray();
            }
            // La catégorie
            if (isset($categories[$product->getVar('product_cid')])) {
                $tmp['product_category'] = $categories[$product->getVar('product_cid')]->toArray();
            }
            // Les produits relatifs
            if ($withRelatedProducts) {
                if (isset($relatedProducts[$product->getVar('product_id')])) {
                    $productsRelatedToThisOne = $relatedProducts[$product->getVar('product_id')];
                    foreach ($productsRelatedToThisOne as $oneRelatedProdut) {
                        $tmp['product_related_products'][] = $oneRelatedProdut->toArray();
                    }
                }
            }
            // Les fabricants du produit
            if (isset($manufacturersPerProduct[$product->getVar('product_id')])) {
                $productManufacturers = $manufacturersPerProduct[$product->getVar('product_id')];
                $tmpManufacturersList = array();
                foreach ($productManufacturers as $productManufacturer) {
                    if (isset($manufacturers[$productManufacturer->getVar('pm_manu_id')])) {
                        $manufacturer = $manufacturers[$productManufacturer->getVar('pm_manu_id')];
                        $tmp['product_manufacturers'][] = $manufacturer->toArray();
                        $tmpManufacturersList[] = $manufacturer->getVar('manu_commercialname') . ' ' . $manufacturer->getVar('manu_name');
                    }
                }
                if (count($tmpManufacturersList) > 0) {
                    $tmp['product_joined_manufacturers'] = implode(OLEDRION_STRING_TO_JOIN_MANUFACTURERS, $tmpManufacturersList);
                }
            }

            // L'utilisateur Xoops (éventuellement)
            if ($withXoopsUser && isset($users[$product->getVar('product_submitter')])) {
                $thisUser = $users[$product->getVar('product_submitter')];
                if (xoops_trim($thisUser->getVar('name')) != '') {
                    $name = $thisUser->getVar('name');
                } else {
                    $name = $thisUser->getVar('uname');
                }
                $tmp['product_submiter_name'] = $name;
                $userLink = '<a href="' . XOOPS_URL . '/userinfo.php?uid=' . $thisUser->getVar('uid') . '">' . $name . '</a>';
                $tmp['product_submiter_link'] = $userLink;
            }
            $tmp['product_count'] = $count; // Compteur pour les templates (pour gérer les colonnes)
            $ret[] = $tmp;
            $count++;
        }
        $ret['lastTitle'] = $lastTitle;

        return $ret;
    }
}
