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
 * Gestion des fichies attachés aux produits
 */



/**
 * Class Files
 */
class Files extends OledrionObject
{
    /**
     * constructor
     *
     * normally, this is called from child classes only
     */
    public function __construct()
    {
        $this->initVar('file_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('file_product_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('file_filename', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('file_description', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('file_mimetype', XOBJ_DTYPE_TXTBOX, null, false);
    }

    /**
     * Supprime un fichier
     */
    public function deleteAttachedFile()
    {
        if (!defined('OLEDRION_ATTACHED_FILES_PATH')) {
            include OLEDRION_PATH . 'config.php';
        }
        if (false === @unlink(OLEDRION_ATTACHED_FILES_PATH . '/' . $this->getVar('file_filename'))) {
            throw new \RuntimeException('The file '.OLEDRION_ATTACHED_FILES_PATH . '/' . $this->getVar('file_filename').' could not be deleted.');
        }
    }

    /**
     * Indique si le fichier courant est un fichier MP3
     * @return bool
     */
    public function isMP3()
    {
        return 'audio/mpeg' === mb_strtolower($this->getVar('file_mimetype'));
    }

    /**
     * Indique si le fichier attaché existe physiquement sur le site
     * @return bool
     */
    public function fileExists()
    {
        if (!defined('OLEDRION_ATTACHED_FILES_PATH')) {
            include OLEDRION_PATH . 'config.php';
        }

        return file_exists(OLEDRION_ATTACHED_FILES_PATH . '/' . $this->getVar('file_filename'));
    }

    /**
     * Retourne l'url pour accéder au fichier
     * @return string
     */
    public function getURL()
    {
        if (!defined('OLEDRION_ATTACHED_FILES_URL')) {
            include OLEDRION_PATH . 'config.php';
        }

        return OLEDRION_ATTACHED_FILES_URL . '/' . $this->getVar('file_filename');
    }

    /**
     * Retourne le chemin physique pour accéder au fichier
     * @return string
     */
    public function getPath()
    {
        if (!defined('OLEDRION_ATTACHED_FILES_URL')) {
            include OLEDRION_PATH . 'config.php';
        }

        return OLEDRION_ATTACHED_FILES_PATH . '/' . $this->getVar('file_filename');
    }

    /**
     * @param  string $format
     * @return array
     */
    public function toArray($format = 's')
    {
        $ret                      = parent::toArray($format);
        $ret['file_is_mp3']       = $this->isMP3();
        $ret['file_download_url'] = $this->getURL();

        return $ret;
    }
}
