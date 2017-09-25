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
 * Gestion des fichies attachés aux produits
 */

require_once __DIR__ . '/classheader.php';

/**
 * Class Oledrion_files
 */
class Oledrion_files extends Oledrion_Object
{
    /**
     * constructor
     *
     * normally, this is called from child classes only
     *
     * @access public
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
        @unlink(OLEDRION_ATTACHED_FILES_PATH . '/' . $this->getVar('file_filename'));
    }

    /**
     * Indique si le fichier courant est un fichier MP3
     * @return boolean
     */
    public function isMP3()
    {
        return 'audio/mpeg' === strtolower($this->getVar('file_mimetype')) ? true : false;
    }

    /**
     * Indique si le fichier attaché existe physiquement sur le site
     * @return boolean
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

/**
 * Class OledrionOledrion_filesHandler
 */
class OledrionOledrion_filesHandler extends Oledrion_XoopsPersistableObjectHandler
{
    /**
     * OledrionOledrion_filesHandler constructor.
     * @param XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    { //                            Table           Classe          Id          Libellé
        parent::__construct($db, 'oledrion_files', 'oledrion_files', 'file_id', 'file_filename');
    }

    /**
     * Supprime un fichier (son fichier joint ET l'enregistrement dans la base de données)
     *
     * @param  oledrion_files $file
     * @return boolean        Le résultat de la suppression
     */
    public function deleteAttachedFile(Oledrion_files $file)
    {
        if ($file->fileExists()) {
            $file->deleteAttachedFile();
        }

        return $this->delete($file, true);
    }

    /**
     * Retourne les fichiers attachés à un produit
     *
     * @param  integer $file_product_id L'Id du produit
     * @param  integer $start           Position de départ
     * @param  integer $limit           Nombre maxi de produits à retourner
     * @return array   tableau d'objets de type oledrion_files
     */
    public function getProductFiles($file_product_id, $start = 0, $limit = 0)
    {
        $criteria = new Criteria('file_product_id', $file_product_id, '=');
        $criteria->setStart($start);
        $criteria->setLimit($limit);

        return $this->getObjects($criteria);
    }

    /**
     * Retourne le nombre de fichiers attachés à un produit qui sont des MP3
     *
     * @param  integer $file_product_id L'Id du produit
     * @return integer le nombre de fichiers MP3
     */
    public function getProductMP3Count($file_product_id)
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('file_product_id', $file_product_id, '='));
        $criteria->add(new Criteria('file_mimetype', 'audio/mpeg', '='));

        return $this->getCount($criteria);
    }

    /**
     * Retourne le nombre de fichiers attachés à un produit
     *
     * @param  integer $file_product_id L'Id du produit
     * @return integer le nombre de fichiers
     */
    public function getProductFilesCount($file_product_id)
    {
        $criteria = new Criteria('file_product_id', $file_product_id, '=');

        return $this->getCount($criteria);
    }

    /**
     * Supprime les fichiers attachés à un produit
     *
     * @param  integer $file_product_id L'Id du produit
     * @return void
     */
    public function deleteProductFiles($file_product_id)
    {
        $files    = [];
        $criteria = new Criteria('file_product_id', $file_product_id, '=');
        $files    = $this->getObjects($criteria);
        if (count($files) > 0) {
            foreach ($files as $file) {
                $file->deleteAttachedFile();
                $this->delete($file, true);
            }
        }
    }
}
