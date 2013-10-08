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
 * Plugin chargé de notifier les utilisateurs de la création d'un nouveau produit et d'une nouvelle catégorie
 *
 * @since 2.31
 */
class newelementsAction extends oledrion_action
{
    public static function registerEvents()
    {
        /**
         * La liste des évènements traités par le plugin se présente sous la forme d'un tableau compposé comme ceci :
         *
         * Indice    Signification
         * ----------------------
         *    0        Evènement sur lequel se raccrocher (voir class/oledrion_plugins.php::EVENT_ON_PRODUCT_CREATE
         *    1        Priorité du plugin (de 1 à 5)
         *    2        Script Php à inclure
         *    3        Classe à instancier
         *    4        Méthode à appeler
         */
        $events = array();
        $events[] = array(oledrion_plugins::EVENT_ON_PRODUCT_CREATE,
            oledrion_plugins::EVENT_PRIORITY_1,
            basename(__FILE__),
            __CLASS__,
            'fireNewProduct');
        $events[] = array(oledrion_plugins::EVENT_ON_CATEGORY_CREATE,
            oledrion_plugins::EVENT_PRIORITY_1,
            basename(__FILE__),
            __CLASS__,
            'fireNewCategory');
        return $events;
    }

    /**
     * Méthode appelée pour indiquer qu'un nouveau produit a été crée
     *
     * @param object $product    Le produit qui vient d'être crée
     * @return void
     */
    public function fireNewProduct($parameters)
    {
        $product = $parameters['product'];
        if (intval($product->getVar('product_online')) == 1) {
            $tags = array();
            $notification_handler = xoops_gethandler('notification');
            $tags['PRODUCT_NAME'] = $product->getVar('product_title');
            $tags['PRODUCT_SUMMARY'] = strip_tags($product->getVar('product_summary'));
            $tags['PRODUCT_URL'] = $product->getLink();
            $notification_handler->triggerEvent('global', 0, 'new_product', $tags);
        }
    }

    /**
     * Méthode appelée pour indiquer qu'une nouvelle catégorie a été crée
     *
     * @param object $category
     * @return void
     */
    public function fireNewCategory($parameters)
    {
        $category = $parameters['category'];
        $notification_handler = xoops_gethandler('notification');
        $tags = array();
        $tags['CATEGORY_NAME'] = $category->getVar('cat_title');
        $tags['CATEGORY_URL'] = $category->getLink(); // OLEDRION_URL.'category.php?cat_cid=' . $category->getVar('cat_cid');
        $tags['X_MODULE_URL'] = OLEDRION_URL;
        $notification_handler->triggerEvent('global', 0, 'new_category', $tags);

    }
}
