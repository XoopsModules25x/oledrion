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
 * Gestion des fichiers textes utilisés pour afficher des messages aux utilisateurs sur certaines pages
 */
class oledrion_registryfile
{
    public $filename; // Nom du fichier à traiter

    /**
     * Access the only instance of this class
     *
     * @return    object
     *
     * @static
     * @staticvar   object
     */
    public function getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new oledrion_registryfile();
        }
        return $instance;
    }


    public function __construct($fichier = null)
    {
        $this->setfile($fichier);
    }

    public function setfile($fichier = null)
    {
        if ($fichier) {
            $this->filename = OLEDRION_TEXT_PATH . $fichier;
        }
    }

    public function getfile($fichier = null)
    {
        $fw = '';
        if (!$fichier) {
            $fw = $this->filename;
        } else {
            $fw = OLEDRION_TEXT_PATH . $fichier;
        }

        if (file_exists($fw)) {
            return file_get_contents($fw);
        } else {
            return '';
        }
    }

    public function savefile($content, $fichier = null)
    {
        $fw = '';
        if (!$fichier) {
            $fw = $this->filename;
        } else {
            $fw = OLEDRION_TEXT_PATH . $fichier;
        }
        if (file_exists($fw)) {
            @unlink($fw);
        }
        $fp = fopen($fw, 'w') or die("Error, impossible to create the file " . $this->filename);
        fwrite($fp, $content);
        fclose($fp);
        return true;
    }
}
