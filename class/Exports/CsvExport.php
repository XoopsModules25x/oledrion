<?php

namespace XoopsModules\Oledrion\Exports;

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
use XoopsModules\Oledrion\Constants;

/**
 * Export au format CSV
 */
// defined('XOOPS_ROOT_PATH') || die('Restricted access');

class CsvExport extends Export
{
    /**
     * CsvExport constructor.
     * @param string|array $parameters
     */
    public function __construct($parameters = '')
    {
        if (!is_array($parameters)) {
            $this->separator = OLEDRION_CSV_SEP;
            $this->filename  = 'oledrion.csv';
            $this->folder    = OLEDRION_CSV_PATH;
            $this->url       = OLEDRION_CSV_URL;
            $this->orderType = Constants::OLEDRION_STATE_VALIDATED;
        }
        parent::__construct($parameters);
    }

    /**
     * Export des données
     * @return bool Vrai si l'export a réussi sinon faux
     */
    public function doExport()
    {
        $db              = \XoopsDatabaseFactory::getDatabaseConnection();
        $caddyHandler    = new Oledrion\CaddyHandler($db);
        $commandsHandler = new Oledrion\CommandsHandler($db);
        $file            = $this->folder . '/' . $this->filename;
        $fp              = fopen($file, 'wb');
        if (!$fp) {
            $this->success = false;

            return false;
        }

        // Création de l'entête du fichier
        $list = $entete1 = $entete2 = [];
        $s    = $this->separator;
        $cmd  = new Oledrion\Commands();
        foreach ($cmd->getVars() as $fieldName => $properties) {
            $entete1[] = $fieldName;
        }
        // Ajout des infos de caddy
        $cart = new Oledrion\Caddy();
        foreach ($cart->getVars() as $fieldName => $properties) {
            $entete2[] = $fieldName;
        }
        $list[] = array_merge($entete1, $entete2);
        // make item array
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('cmd_id', 0, '<>'));
        $criteria->add(new \Criteria('cmd_state', $this->orderType, '='));
        $criteria->setSort('cmd_date');
        $criteria->setOrder('DESC');
        $orders = $commandsHandler->getObjects($criteria);
        foreach ($orders as $order) {
            $carts = [];
            $carts = $caddyHandler->getObjects(new \Criteria('caddy_cmd_id', $order->getVar('cmd_id'), '='));
            $ligne = [];
            foreach ($carts as $cart) {
                $ligne = [];
                foreach ($entete1 as $commandField) {
                    $ligne[] = $order->getVar($commandField);
                }
                foreach ($entete2 as $cartField) {
                    $ligne[] = $cart->getVar($cartField);
                }
                // Add to main array
                $list[] = $ligne;
            }
        }

        // import information on csv file
        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);
        $this->success = true;

        return true;
    }

    /**
     * Retourne le lien à utiliser pour télécharger le fichier d'export
     * @return bool|string Le lien à utiliser
     */
    public function getDownloadUrl()
    {
        if ($this->success) {
            return $this->url . '/' . $this->filename;
        }

        return false;
    }

    /**
     * @return bool|string
     */
    public function getDownloadPath()
    {
        if ($this->success) {
            return $this->folder . '/' . $this->filename;
        }

        return false;
    }
}
