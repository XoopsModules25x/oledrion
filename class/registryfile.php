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
 * Gestion des fichiers textes utilisés pour afficher des messages aux utilisateurs sur certaines pages
 */
class Oledrion_registryfile
{
    public $filename; // Nom du fichier à traiter

    /**
     * Access the only instance of this class
     *
     * @return Oledrion_registryfile
     */
    public function getInstance()
    {
        static $instance;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Oledrion_registryfile constructor.
     * @param null $fichier
     */
    public function __construct($fichier = null)
    {
        $this->setfile($fichier);
    }

    /**
     * @param null $fichier
     */
    public function setfile($fichier = null)
    {
        if ($fichier) {
            $this->filename = OLEDRION_TEXT_PATH . $fichier;
        }
    }

    /**
     * @param  null $fichier
     * @return string
     */
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

    /**
     * @param       $content
     * @param  null $fichier
     * @return bool
     */
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
        $fp = fopen($fw, 'w') || die('Error, impossible to create the file ' . $this->filename);
        fwrite($fp, $content);
        fclose($fp);

        return true;
    }
}
