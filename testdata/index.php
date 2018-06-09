<?php
/**
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package
 * @since           2.5.9
 * @author          Michael Beck (aka Mamba)
 */

use XoopsModules\Oledrion;
use XoopsModules\Oledrion\Common;

require_once dirname(dirname(dirname(__DIR__))) . '/mainfile.php';

include dirname(__DIR__) . '/preloads/autoloader.php';

$op = \Xmf\Request::getCmd('op', '');

switch ($op) {
    case 'load':
        loadSampleData();
        break;
    case 'save':
        saveSampleData();
        break;
    case 'exportschema':
        exportSchema();
        break;
}

// XMF TableLoad for SAMPLE data

function loadSampleData()
{
    $moduleDirName      = basename(dirname(__DIR__));
    $moduleDirNameUpper = strtoupper($moduleDirName); //$capsDirName
    /** @var Oledrion\Helper $helper */
    $helper       = Oledrion\Helper::getInstance();
    $utility      = new Oledrion\Utility();
    $configurator = new Common\Configurator();
    // Load language files
    $helper->loadLanguage('admin');
    $helper->loadLanguage('modinfo');
    $helper->loadLanguage('common');

    $tables = [
        'manufacturer',
        'products',
        'productsmanu',
        'caddy',
        'cat',
        'commands',
        'vat',
        'discounts',
        'vendors',
        'files',
        'persistent_cart',
        'lists',
        'products_list',
        'attributes',
        'packing',
        'location',
        'delivery',
        'payment',
        'location_delivery',
        'delivery_payment',
    ];

    foreach ($tables as $table) {
        $tabledata = \Xmf\Yaml::readWrapped($moduleDirName . '_' . $table . '.yml');
        \Xmf\Database\TableLoad::truncateTable($moduleDirName . '_' . $table);
        \Xmf\Database\TableLoad::loadTableFromArray($moduleDirName . '_' . $table, $tabledata);
    }
    /*
        $oledrion_manufacturerData = \Xmf\Yaml::readWrapped('oledrion_manufacturer.yml');
        \Xmf\Database\TableLoad::truncateTable('oledrion_manufacturer');
        \Xmf\Database\TableLoad::loadTableFromArray($moduleDirName . '_manufacturer', $oledrion_manufacturerData);

        $oledrion_productsData = \Xmf\Yaml::readWrapped('oledrion_products.yml');
        \Xmf\Database\TableLoad::truncateTable('oledrion_products');
        \Xmf\Database\TableLoad::loadTableFromArray($moduleDirName . '_products', $oledrion_productsData);

        $oledrion_productsmanuData = \Xmf\Yaml::readWrapped('oledrion_productsmanu.yml');
        \Xmf\Database\TableLoad::truncateTable('oledrion_productsmanu');
        \Xmf\Database\TableLoad::loadTableFromArray($moduleDirName . '_productsmanu', $oledrion_productsmanuData);

        $oledrion_caddyData = \Xmf\Yaml::readWrapped('oledrion_caddy.yml');
        \Xmf\Database\TableLoad::truncateTable('oledrion_caddy');
        \Xmf\Database\TableLoad::loadTableFromArray($moduleDirName . '_caddy', $oledrion_caddyData);

        $oledrion_catData = \Xmf\Yaml::readWrapped('oledrion_cat.yml');
        \Xmf\Database\TableLoad::truncateTable('oledrion_cat');
        \Xmf\Database\TableLoad::loadTableFromArray($moduleDirName . '_cat', $oledrion_catData);

        $oledrion_commandsData = \Xmf\Yaml::readWrapped('oledrion_commands.yml');
        \Xmf\Database\TableLoad::truncateTable('oledrion_commands');
        \Xmf\Database\TableLoad::loadTableFromArray($moduleDirName . '_commands', $oledrion_commandsData);

        //    $oledrion_relatedData = \Xmf\Yaml::readWrapped('oledrion_related.yml');
        //    \Xmf\Database\TableLoad::truncateTable('oledrion_related');
        //    \Xmf\Database\TableLoad::loadTableFromArray($moduleDirName . '_related', $oledrion_relatedData);

        $oledrion_vatData = \Xmf\Yaml::readWrapped('oledrion_vat.yml');
        \Xmf\Database\TableLoad::truncateTable('oledrion_vat');
        \Xmf\Database\TableLoad::loadTableFromArray($moduleDirName . '_vat', $oledrion_vatData);

        //    $oledrion_votedataData = \Xmf\Yaml::readWrapped('oledrion_votedata.yml');
        //    \Xmf\Database\TableLoad::truncateTable('oledrion_votedata');
        //    \Xmf\Database\TableLoad::loadTableFromArray($moduleDirName . '_votedata', $oledrion_votedataData);

        $oledrion_discountsData = \Xmf\Yaml::readWrapped('oledrion_discounts.yml');
        \Xmf\Database\TableLoad::truncateTable('oledrion_discounts');
        \Xmf\Database\TableLoad::loadTableFromArray($moduleDirName . '_discounts', $oledrion_discountsData);

        $oledrion_vendorsData = \Xmf\Yaml::readWrapped('oledrion_vendors.yml');
        \Xmf\Database\TableLoad::truncateTable('oledrion_vendors');
        \Xmf\Database\TableLoad::loadTableFromArray($moduleDirName . '_vendors', $oledrion_vendorsData);

        $oledrion_filesData = \Xmf\Yaml::readWrapped('oledrion_files.yml');
        \Xmf\Database\TableLoad::truncateTable('oledrion_files');
        \Xmf\Database\TableLoad::loadTableFromArray($moduleDirName . '_files', $oledrion_filesData);

        $oledrion_persistent_cartData = \Xmf\Yaml::readWrapped('oledrion_persistent_cart.yml');
        \Xmf\Database\TableLoad::truncateTable('oledrion_persistent_cart');
        \Xmf\Database\TableLoad::loadTableFromArray($moduleDirName . '_persistent_cart', $oledrion_persistent_cartData);

        //    $oledrion_gateways_optionsData = \Xmf\Yaml::readWrapped('oledrion_gateways_options.yml');
        //    \Xmf\Database\TableLoad::truncateTable('oledrion_gateways_options');
        //    \Xmf\Database\TableLoad::loadTableFromArray($moduleDirName . '_gateways_options', $oledrion_gateways_optionsData);

        $oledrion_listsData = \Xmf\Yaml::readWrapped('oledrion_lists.yml');
        \Xmf\Database\TableLoad::truncateTable('oledrion_lists');
        \Xmf\Database\TableLoad::loadTableFromArray($moduleDirName . '_lists', $oledrion_listsData);

        $oledrion_products_listData = \Xmf\Yaml::readWrapped('oledrion_products_list.yml');
        \Xmf\Database\TableLoad::truncateTable('oledrion_products_list');
        \Xmf\Database\TableLoad::loadTableFromArray($moduleDirName . '_products_list', $oledrion_products_listData);

        $oledrion_attributesData = \Xmf\Yaml::readWrapped('oledrion_attributes.yml');
        \Xmf\Database\TableLoad::truncateTable('oledrion_attributes');
        \Xmf\Database\TableLoad::loadTableFromArray($moduleDirName . '_attributes', $oledrion_attributesData);

        //    $oledrion_caddy_attributesData = \Xmf\Yaml::readWrapped('oledrion_caddy_attributes.yml');
        //    \Xmf\Database\TableLoad::truncateTable('oledrion_caddy_attributes');
        //    \Xmf\Database\TableLoad::loadTableFromArray($moduleDirName . '_caddy_attributes', $oledrion_caddy_attributesData);

        $oledrion_packingData = \Xmf\Yaml::readWrapped('oledrion_packing.yml');
        \Xmf\Database\TableLoad::truncateTable('oledrion_packing');
        \Xmf\Database\TableLoad::loadTableFromArray($moduleDirName . '_packing', $oledrion_packingData);

        $oledrion_locationData = \Xmf\Yaml::readWrapped('oledrion_location.yml');
        \Xmf\Database\TableLoad::truncateTable('oledrion_location');
        \Xmf\Database\TableLoad::loadTableFromArray($moduleDirName . '_location', $oledrion_locationData);

        $oledrion_deliveryData = \Xmf\Yaml::readWrapped('oledrion_delivery.yml');
        \Xmf\Database\TableLoad::truncateTable('oledrion_delivery');
        \Xmf\Database\TableLoad::loadTableFromArray($moduleDirName . '_delivery', $oledrion_deliveryData);

        $oledrion_paymentData = \Xmf\Yaml::readWrapped('oledrion_payment.yml');
        \Xmf\Database\TableLoad::truncateTable('oledrion_payment');
        \Xmf\Database\TableLoad::loadTableFromArray($moduleDirName . '_payment', $oledrion_paymentData);

        $oledrion_location_deliveryData = \Xmf\Yaml::readWrapped('oledrion_location_delivery.yml');
        \Xmf\Database\TableLoad::truncateTable('oledrion_location_delivery');
        \Xmf\Database\TableLoad::loadTableFromArray($moduleDirName . '_location_delivery', $oledrion_location_deliveryData);

        $oledrion_delivery_paymentData = \Xmf\Yaml::readWrapped('oledrion_delivery_payment.yml');
        \Xmf\Database\TableLoad::truncateTable('oledrion_delivery_payment');
        \Xmf\Database\TableLoad::loadTableFromArray($moduleDirName . '_delivery_payment', $oledrion_delivery_paymentData);

        //    $oledrion_payment_logData = \Xmf\Yaml::readWrapped('oledrion_payment_log.yml');
        //    \Xmf\Database\TableLoad::truncateTable('oledrion_payment_log');
        //    \Xmf\Database\TableLoad::loadTableFromArray($moduleDirName . '_payment_log', $oledrion_payment_logData);
    */

    //  ---  COPY test folder files ---------------
    if (count($configurator->copyTestFolders) > 0) {
        //        $file =  dirname(__DIR__) . '/testdata/images/';
        foreach (array_keys($configurator->copyTestFolders) as $i) {
            $src  = $configurator->copyTestFolders[$i][0];
            $dest = $configurator->copyTestFolders[$i][1];
            $utility::rcopy($src, $dest);
        }
    }

    redirect_header('../admin/index.php', 0, constant('CO_' . $moduleDirNameUpper . '_SAMPLEDATA_SUCCESS'));
}

function saveSampleData()
{
    $moduleDirName      = basename(dirname(__DIR__));
    $moduleDirNameUpper = strtoupper($moduleDirName);

    $tables = ['customer', 'part', 'service', 'servpart', 'vehicle', 'workorder', 'workserv',];

    foreach ($tables as $table) {
        \Xmf\Database\TableLoad::saveTableToYamlFile($moduleDirName . '_' . $table, $table . '.yml');
    }

    redirect_header('../admin/index.php', 1, constant('CO_' . $moduleDirNameUpper . '_' . 'SAMPLEDATA_SUCCESS'));
}

function exportSchema()
{
    try {
        $moduleDirName      = basename(dirname(__DIR__));
        $moduleDirNameUpper = strtoupper($moduleDirName);

        $migrate = new  Wfdownloads\Migrate($moduleDirName);
        $migrate->saveCurrentSchema();

        redirect_header('../admin/index.php', 1, constant('CO_' . $moduleDirNameUpper . '_' . 'EXPORT_SCHEMA_SUCCESS'));
    } catch (Exception $e) {
        exit(constant('CO_' . $moduleDirNameUpper . '_' . 'EXPORT_SCHEMA_ERROR'));
    }
}
