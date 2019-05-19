<?php

namespace XoopsModules\Oledrion;

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

/**
 * Management of text files used to display messages to users on certain pages
 */
class Registryfile
{
    public $filename; // File name to process

    /**
     * Access the only instance of this class
     *
     * @return Registryfile
     */
    public static function getInstance()
    {
        static $instance;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Registryfile constructor.
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
        }

        return '';
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
            if (false === @unlink($fw)) {
                throw new \RuntimeException('The file ' . $fw . ' could not be deleted.');
            }
        }
        $fp = fopen($fw, 'wb') || die('Error, impossible to create the file ' . $this->filename);
        fwrite($fp, $content);
        fclose($fp);

        return true;
    }
}
