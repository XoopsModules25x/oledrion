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
 * Facade pour les produits
 */
// defined('XOOPS_ROOT_PATH') || die('Restricted access');

use XoopsModules\Oledrion;

include  dirname(__DIR__) . '/preloads/autoloader.php';
include  dirname(__DIR__) . '/include/common.php';

/**
 * Class Shelf
 * @package XoopsModules\Oledrion
 */
class Shelf
{
    private $handlers;

    /**
     * Shelf constructor.
     */
    public function __construct()
    {
        //mb        $this->initHandlers();
    }

    /**
     * Chargement des handlers
     */
    private function initHandlers()
    {
        //mb        $this->handlers = HandlerManager::getInstance();
    }

    /**
     * Retourne le nombre de produits d'un certain type
     *
     * @param  string $type Le type de produits dont on veut récupérer le nombre
     * @param  int    $category
     * @param  int    $excluded
     * @return int
     */
    public function getProductsCount($type = 'recent', $category = 0, $excluded = 0)
    {
        switch (strtolower($type)) {
            case 'recent':
                $db              = \XoopsDatabaseFactory::getDatabaseConnection();
                $productsHandler = new Oledrion\ProductsHandler($db);

                return $productsHandler->getRecentProductsCount($category, $excluded);
                break;
        }

        return 0;
    }

    /**
     * Supprime un produit (et tout ce qui lui est relatif)
     * @param Products $product
     * @return mixed
     */
    public function deleteProduct(Products $product)
    {
        global $xoopsModule;
        $id = $product->getVar('product_id');

        // On commence par supprimer les commentaires
        $mid = $xoopsModule->getVar('mid');
        xoops_comment_delete($mid, $id);

        // Puis les votes
        $votedataHandler->deleteProductRatings($id);

        // Puis les produits relatifs
        $relatedHandler->deleteProductRelatedProducts($id);

        // Les images (la grande et la miniature)
        $product->deletePictures();

        // Le fichier attaché
        $product->deleteAttachment();

        // Les fichiers attachés
        $filesHandler->deleteProductFiles($id);

        // Suppression dans les paniers persistants enregistrés
        $persistentCartHandler->deleteProductForAllCarts($id);

        // Les attributs qui lui sont rattachés
        $attributesHandler->deleteProductAttributes($id);

        // Le produit dans les listes
        $productsListHandler->deleteProductFromLists($id);

        // La relation entre le produit et le fabricant
        $productsmanuHandler->removeManufacturerProduct($id);

        // Le produit dans les remises
        $discountsHandler->removeProductFromDiscounts($id);

        // Et le produit en lui même, à la fin
        return $productsHandler->delete($product, true);
    }

    /**
     * Cherche et retourne la liste de produits relatifs à une liste de produits
     *
     * @param  array $productsIds La liste des produits dont on cherche les produits relatifs
     * @return array Clé = ID Produit, valeurs (deuxième dimension) = liste des produits relatifs
     */
    private function getRelatedProductsFromProductsIds($productsIds)
    {
        $relatedProducts = $relatedProductsIds = [];
        $db              = \XoopsDatabaseFactory::getDatabaseConnection();
        $relatedHandler  = new Oledrion\RelatedHandler($db);
        $productsHandler = new Oledrion\ProductsHandler($db);
        if (is_array($productsIds) && count($productsIds) > 0) {
            $relatedProductsIds = $relatedHandler->getRelatedProductsFromProductsIds($productsIds);
            if (count($relatedProductsIds) > 0) {
                $tmp = [];
                foreach ($relatedProductsIds as $relatedProductId) {
                    $tmp[] = $relatedProductId->getVar('related_product_related');
                }
                $tmp = array_unique($tmp);
                sort($tmp);
                if (count($tmp) > 0) {
                    $tempRelatedProducts = $productsHandler->getProductsFromIDs($tmp);
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
     * @param  ShelfParameters $parameters Les paramètres de filtrage
     * @return array                     Tableau prêt à être utilisé dans les templates
     */
    public function getProducts(ShelfParameters $parameters)
    {
        $db              = \XoopsDatabaseFactory::getDatabaseConnection();
        $productsHandler = new Oledrion\ProductsHandler($db);
        $vendorsHandler  = new Oledrion\VendorsHandler($db);
        $caddyHandler    = new Oledrion\CaddyHandler($db);

        $parametersValues    = $parameters->getParameters();
        $productType         = $parametersValues['productsType'];
        $start               = $parametersValues['start'];
        $limit               = $parametersValues['limit'];
        $category            = $parametersValues['category'];
        $sort                = $parametersValues['sort'];
        $order               = $parametersValues['order'];
        $excluded            = $parametersValues['excluded'];
        $withXoopsUser       = $parametersValues['withXoopsUser'];
        $withRelatedProducts = $parametersValues['withRelatedProducts'];
        $withQuantity        = $parametersValues['withQuantity'];
        $thisMonthOnly       = $parametersValues['thisMonthOnly'];
        $ret                 = $xoopsUsersIDs = $users = $relatedProducts = $productsManufacturers = $manufacturersPerProduct = $products = $productsIds = $categoriesIds = $vendorsIds = $manufacturersIds = $manufacturers = $categories = $vendors = [];
        // On commence par récupérer la liste des produits
        switch (strtolower($productType)) {
            case 'recent':

                $products = $productsHandler->getRecentProducts(new Oledrion\Parameters([
                                                                                            'start'         => $start,
                                                                                            'limit'         => $limit,
                                                                                            'category'      => $category,
                                                                                            'sort'          => $sort,
                                                                                            'order'         => $order,
                                                                                            'excluded'      => $excluded,
                                                                                            'thisMonthOnly' => $thisMonthOnly
                                                                                        ]));
                break;

            case 'mostsold':
                $tempProductsIds = [];
                $tempProductsIds = $caddyHandler->getMostSoldProducts($start, $limit, $category, $withQuantity);
                if (count($tempProductsIds) > 0) {
                    $products = $productsHandler->getProductsFromIDs(array_keys($tempProductsIds));
                }
                break;

            case 'recentlysold':
                $tempProductsIds = [];
                $tempProductsIds = $caddyHandler->getRecentlySoldProducts($start, $limit);
                if (count($tempProductsIds) > 0) {
                    $tempProductsIds = array_unique($tempProductsIds);
                }
                if (count($tempProductsIds) > 0) {
                    $products = $productsHandler->getProductsFromIDs(array_keys($tempProductsIds));
                }
                break;

            case 'mostviewed':
                $products = $productsHandler->getMostViewedProducts(new Oledrion\Parameters([
                                                                                                'start'    => $start,
                                                                                                'limit'    => $limit,
                                                                                                'category' => $category,
                                                                                                'sort'     => $sort,
                                                                                                'order'    => $order
                                                                                            ]));
                break;

            case 'bestrated':
                $products = $productsHandler->getBestRatedProducts(new Oledrion\Parameters([
                                                                                               'start'    => $start,
                                                                                               'limit'    => $limit,
                                                                                               'category' => $category,
                                                                                               'sort'     => $sort,
                                                                                               'order'    => $order
                                                                                           ]));
                break;

            case 'recommended':
                $products = $productsHandler->getRecentRecommended(new Oledrion\Parameters([
                                                                                               'start'    => $start,
                                                                                               'limit'    => $limit,
                                                                                               'category' => $category,
                                                                                               'sort'     => $sort,
                                                                                               'order'    => $order
                                                                                           ]));
                break;

            case 'promotional':
                $products = $productsHandler->getPromotionalProducts(new Oledrion\Parameters([
                                                                                                 'start'    => $start,
                                                                                                 'limit'    => $limit,
                                                                                                 'category' => $category,
                                                                                                 'sort'     => $sort,
                                                                                                 'order'    => $order
                                                                                             ]));
                break;

            case 'random':
                $products = $productsHandler->getRandomProducts(new Oledrion\Parameters([
                                                                                            'start'         => $start,
                                                                                            'limit'         => $limit,
                                                                                            'category'      => $category,
                                                                                            'sort'          => $sort,
                                                                                            'order'         => $order,
                                                                                            'thisMonthOnly' => $thisMonthOnly
                                                                                        ]));
        }

        if (count($products) > 0) {
            $productsIds = array_keys($products);
        } else {
            return $ret;
        }

        // Recherche des Id des catégories et des vendeurs
        foreach ($products as $product) {
            $categoriesIds[] = $product->getVar('product_cid');
            $vendorsIds[]    = $product->getVar('product_vendor_id');
            if ($withXoopsUser) {
                $xoopsUsersIDs[] = $product->getVar('product_submitter');
            }
        }

        $db                    = \XoopsDatabaseFactory::getDatabaseConnection();
        $productsmanuHandler   = new Oledrion\ProductsmanuHandler($db);
        $categoryHandler       = new Oledrion\CategoryHandler($db);
        $productsManufacturers = $productsmanuHandler->getFromProductsIds($productsIds);
        $vendorsHandler        = new Oledrion\VendorsHandler($db);
        $manufacturerHandler   = new Oledrion\ManufacturerHandler($db);
        // Regroupement des fabricants par produit
        foreach ($productsManufacturers as $item) {
            $manufacturersIds[]                                        = $item->getVar('pm_manu_id');
            $manufacturersPerProduct[$item->getVar('pm_product_id')][] = $item;
        }
        // On récupère la liste des personnes qui ont soumis les produits
        if ($withXoopsUser) {
            $users = Oledrion\Utility::getUsersFromIds($xoopsUsersIDs);
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
            $manufacturers = $manufacturerHandler->getManufacturersFromIds($manufacturersIds);
        }
        if (count($categoriesIds) > 0) {
            //mb            $categories = $this->handlers->h_oledrion_cat->getCategoriesFromIds($categoriesIds);
            $categories = $categoryHandler->getCategoriesFromIds($categoriesIds);
        }
        if (count($vendorsIds) > 0) {
            $vendors = $vendorsHandler->getVendorsFromIds($vendorsIds);
        }

        $count     = 1;
        $lastTitle = '';
        foreach ($products as $product) {
            $tmp       = [];
            $tmp       = $product->toArray();
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
                $tmpManufacturersList = [];
                foreach ($productManufacturers as $productManufacturer) {
                    if (isset($manufacturers[$productManufacturer->getVar('pm_manu_id')])) {
                        $manufacturer                   = $manufacturers[$productManufacturer->getVar('pm_manu_id')];
                        $tmp['product_manufacturers'][] = $manufacturer->toArray();
                        $tmpManufacturersList[]         = $manufacturer->getVar('manu_commercialname') . ' ' . $manufacturer->getVar('manu_name');
                    }
                }
                if (count($tmpManufacturersList) > 0) {
                    $tmp['product_joined_manufacturers'] = implode(OLEDRION_STRING_TO_JOIN_MANUFACTURERS, $tmpManufacturersList);
                }
            }

            // L'utilisateur Xoops (éventuellement)
            if ($withXoopsUser && isset($users[$product->getVar('product_submitter')])) {
                $thisUser = $users[$product->getVar('product_submitter')];
                if ('' !== xoops_trim($thisUser->getVar('name'))) {
                    $name = $thisUser->getVar('name');
                } else {
                    $name = $thisUser->getVar('uname');
                }
                $tmp['product_submiter_name'] = $name;
                $userLink                     = '<a href="' . XOOPS_URL . '/userinfo.php?uid=' . $thisUser->getVar('uid') . '">' . $name . '</a>';
                $tmp['product_submiter_link'] = $userLink;
            }
            $tmp['product_count'] = $count; // Compteur pour les templates (pour gérer les colonnes)
            $ret[]                = $tmp;
            ++$count;
        }
        $ret['lastTitle'] = $lastTitle;

        return $ret;
    }
}
