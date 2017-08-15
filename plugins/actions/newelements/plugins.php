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
 * @copyright   {@link https://xoops.org/ XOOPS Project}
 * @license     {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author      Hervé Thouzard (http://www.herve-thouzard.com/)
 */

/**
 * Plugin to notify users of the creation of a new product and a new category
 *
 * @since 2.31
 */
class NewelementsAction extends Oledrion_action
{
    /**
     * Returns the list of events processed by the plugin
     * @return array
     */
    public static function registerEvents()
    {
        /**
         * The list of events processed by the plugin is presented in the form of an array as follows:
         *
         *  Index     Meaning
         * ----------------------
         *    0        Event to hang up (see class/oledrion_plugins.php::EVENT_ON_PRODUCT_CREATE
         *    1        Plugin priority  (between 1 and 5)
         *    2        PHP Script to include
         *    3        Class to instantiate
         *    4        Method to call
         */
        $events   = [];
        $events[] = [
            Oledrion_plugins::EVENT_ON_PRODUCT_CREATE,
            Oledrion_plugins::EVENT_PRIORITY_1,
            basename(__FILE__),
            __CLASS__,
            'fireNewProduct'
        ];
        $events[] = [
            Oledrion_plugins::EVENT_ON_CATEGORY_CREATE,
            Oledrion_plugins::EVENT_PRIORITY_1,
            basename(__FILE__),
            __CLASS__,
            'fireNewCategory'
        ];

        return $events;
    }

    /**
     * Method called to indicate that a new product has been created
     *      
     * @param $parameters
     * @param XoopsObject $product The product that has just been created
     */
    public function fireNewProduct($parameters)
    {
        $product = $parameters['product'];
        if ((int)$product->getVar('product_online') == 1) {
            $tags                    = [];
            /** @var \XoopsNotificationHandler $notificationHandler */
            $notificationHandler     = xoops_getHandler('notification');
            $tags['PRODUCT_NAME']    = $product->getVar('product_title');
            $tags['PRODUCT_SUMMARY'] = strip_tags($product->getVar('product_summary'));
            $tags['PRODUCT_URL']     = $product->getLink();
            $notificationHandler->triggerEvent('global', 0, 'new_product', $tags);
        }
    }

    /**
     * A method called to indicate that a new category has been created
     *
     * @param array $parameters
     * @param XoopsObject $category
     */
    public function fireNewCategory($parameters)
    {
        $category              = $parameters['category'];
        /** @var \XoopsNotificationHandler $notificationHandler */
        $notificationHandler   = xoops_getHandler('notification');
        $tags                  = [];
        $tags['CATEGORY_NAME'] = $category->getVar('cat_title');
        $tags['CATEGORY_URL']  = $category->getLink(); // OLEDRION_URL.'category.php?cat_cid=' . $category->getVar('cat_cid');
        $tags['X_MODULE_URL']  = OLEDRION_URL;
        $notificationHandler->triggerEvent('global', 0, 'new_category', $tags);
    }
}
