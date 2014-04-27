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
 * @version     $Id: csv.php 12290 2014-02-07 11:05:17Z beckmi $
 */

/**
 * Export au format CSV
 */
defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

class oledrion_csv_export extends oledrion_export
{
    function __construct($parameters = '')
    {
        if (!is_array($parameters)) {
            $this->separator = OLEDRION_CSV_SEP;
            $this->filename = 'oledrion.csv';
            $this->folder = OLEDRION_CSV_PATH;
            $this->url = OLEDRION_CSV_URL;
            $this->orderType = OLEDRION_STATE_VALIDATED;
        }
        parent::__construct($parameters);
    }

    /**
     * Export des données
     * @return boolean Vrai si l'export a réussi sinon faux
     */
    function export()
    {
        $file = $this->folder . DIRECTORY_SEPARATOR . $this->filename;
        $fp = fopen($file, 'w');
        if (!$fp) {
            $this->success = false;

            return false;
        }

        // Création de l'entête du fichier
        $list = $entete1 = $entete2 = array();
        $s = $this->separator;
        $cmd = new oledrion_commands();
        foreach ($cmd->getVars() as $fieldName => $properties) {
            $entete1[] = $fieldName;
        }
        // Ajout des infos de caddy
        $cart = new oledrion_caddy();
        foreach ($cart->getVars() as $fieldName => $properties) {
            $entete2[] = $fieldName;
        }
        $list[] = array_merge($entete1, $entete2);
        // make item array
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('cmd_id', 0, '<>'));
        $criteria->add(new Criteria('cmd_state', $this->orderType, '='));
        $criteria->setSort('cmd_date');
        $criteria->setOrder('DESC');
        $orders = $this->handlers->h_oledrion_commands->getObjects($criteria);
        foreach ($orders as $order) {
            $carts = array();
            $carts = $this->handlers->h_oledrion_caddy->getObjects(new Criteria('caddy_cmd_id', $order->getVar('cmd_id'), '='));
            $ligne = array();
            foreach ($carts as $cart) {
                $ligne = array();
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
     * @return string Le lien à utiliser
     */
    function getDownloadUrl()
    {
        if ($this->success) {
            return $this->url . '/' . $this->filename;
        } else {
            return false;
        }
    }

    function getDownloadPath()
    {
        if ($this->success) {
            return $this->folder . DIRECTORY_SEPARATOR . $this->filename;
        } else {
            return false;
        }
    }
}
