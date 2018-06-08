<?php namespace XoopsModules\Oledrion;

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

// require_once __DIR__ . '/classheader.php';



/**
 * Class FilesHandler
 */
class FilesHandler extends OledrionPersistableObjectHandler
{
    /**
     * FilesHandler constructor.
     * @param \XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db)
    { //                            Table           Classe          Id          Libellé
        parent::__construct($db, 'oledrion_files', Files::class, 'file_id', 'file_filename');
    }

    /**
     * Supprime un fichier (son fichier joint ET l'enregistrement dans la base de données)
     *
     * @param  Files $file
     * @return boolean        Le résultat de la suppression
     */
    public function deleteAttachedFile(Files $file)
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
     * @return array   tableau d'objets de type Files
     */
    public function getProductFiles($file_product_id, $start = 0, $limit = 0)
    {
        $criteria = new \Criteria('file_product_id', $file_product_id, '=');
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
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('file_product_id', $file_product_id, '='));
        $criteria->add(new \Criteria('file_mimetype', 'audio/mpeg', '='));

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
        $criteria = new \Criteria('file_product_id', $file_product_id, '=');

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
        $criteria = new \Criteria('file_product_id', $file_product_id, '=');
        $files    = $this->getObjects($criteria);
        if (count($files) > 0) {
            foreach ($files as $file) {
                $file->deleteAttachedFile();
                $this->delete($file, true);
            }
        }
    }
}
