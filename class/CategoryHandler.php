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
 * Gestion des catégories de produits
 */



/**
 * Class CategoryHandler
 */
class CategoryHandler extends OledrionPersistableObjectHandler
{
    /**
     * CategoryHandler constructor.
     * @param \XoopsDatabase|null $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        //                        Table               Classe       Id       Libellé
        parent::__construct($db, 'oledrion_cat', Category::class, 'cat_cid', 'cat_title');
    }

    /**
     * Renvoie (sous forme d'objets) la liste de toutes les catégories
     *
     * @param  Parameters $parameters
     * @return array               Taleau d'objets (catégories)
     * @internal param int $start Indice de début de recherche
     * @internal param int $limit Nombre maximum d'enregsitrements à renvoyer
     * @internal param string $sort Champ à utiliser pour le tri
     * @internal param string $order Ordre du tire (asc ou desc)
     * @internal param bool $idaskey Indique s'il faut renvoyer un tableau dont la clé est l'identifiant de l'enregistrement
     */
    public function getAllCategories(Parameters $parameters)
    {
        $parameters = $parameters->extend(new Oledrion\Parameters([
                                                                      'start'   => 0,
                                                                      'limit'   => 0,
                                                                      'sort'    => 'cat_title',
                                                                      'order'   => 'ASC',
                                                                      'idaskey' => true,
                                                                  ]));
        $critere    = new \Criteria('cat_cid', 0, '<>');
        $critere->setLimit($parameters['limit']);
        $critere->setStart($parameters['start']);
        $critere->setSort($parameters['sort']);
        $critere->setOrder($parameters['order']);
        $categories = [];
        $categories = $this->getObjects($critere, $parameters['idaskey']);

        return $categories;
    }

    /**
     * Internal function to make an expanded view of categories via <li>
     *
     * @param  string                         $fieldName
     * @param  string                         $key
     * @param  string                         $ret
     * @param  array $tree
     * @return string
     */
    private function _makeLi($fieldName, $key, &$ret, $tree)
    {
        if ($key > 0) {
            $ret .= '<li><a href="';
            $ret .= $tree[$key]['obj']->getLink() . '">' . $tree[$key]['obj']->getVar('cat_title') . '</a>';
        }
        if (isset($tree[$key]['child']) && !empty($tree[$key]['child'])) {
            $ret .= "\n<ul>\n";
            foreach ($tree[$key]['child'] as $childkey) {
                $this->_makeLi($fieldName, $childkey, $ret, $tree);
            }
            $ret .= "</ul>\n";
        }
        $ret .= "</li>\n";
    }

    /**
     * Make a menu from the categories list
     *
     * @param  string $fieldName Name of the member variable from the node objects that should be used as the title for the options.
     * @param int     $key       ID of the object to display as the root of select options
     * @return string  HTML select box
     */
    public function getUlMenu($fieldName, $key = 0)
    {
//        require_once XOOPS_ROOT_PATH . '/class/tree.php';
        $items      = $this->getAllCategories(new Oledrion\Parameters());
        $treeObject = new Oledrion\XoopsObjectTree($items, 'cat_cid', 'cat_pid');
        $tree       = $treeObject->getTree();

        $ret = '';
        $this->_makeLi($fieldName, $key, $ret, $tree);
        if ('' !== xoops_trim($ret)) {
            $ret = mb_substr($ret, 0, -6);
        }

        return $ret;
    }

    /**
     * Supprime une catégorie (et tout ce qui lui est relatif)
     *
     * @param  Category $category
     * @return bool      Le résultat de la suppression
     */
    public function deleteCategory(Category $category)
    {
        global $xoopsModule;
        $category->deletePicture();
        xoops_notification_deletebyitem($xoopsModule->getVar('mid'), 'new_category', $category->getVar('cat_cid'));

        return $this->delete($category, true);
    }

    /**
     * Retourne le nombre de produits d'une ou de plusieurs catégories
     *
     * @param int   $cat_cid    L'identifiant de la catégorie dont on veut récupérer le nombre de produits
     * @param  bool $withNested Faut il inclure les sous-catégories ?
     * @return int Le nombre de produits
     */
    public function getCategoryProductsCount($cat_cid, $withNested = true)
    {
        global $productsHandler;
        $childsIDs   = [];
        $childsIDs[] = $cat_cid;

        if ($withNested) {
            // Recherche des sous catégories de cette catégorie
            $items = $childs = [];
            require_once XOOPS_ROOT_PATH . '/class/tree.php';
            $items  = $this->getAllCategories(new Oledrion\Parameters());
            $mytree = new Oledrion\XoopsObjectTree($items, 'cat_cid', 'cat_pid');
            $childs = $mytree->getAllChild($cat_cid);
            if (count($childs) > 0) {
                foreach ($childs as $onechild) {
                    $childsIDs[] = $onechild->getVar('cat_cid');
                }
            }
        }

        return $productsHandler->getCategoryProductsCount($childsIDs);
    }

    /**
     * Retourne des catégories selon leur ID
     *
     * @param  array $ids Les ID des catégories à retrouver
     * @return array Objets de type Category
     */
    public function getCategoriesFromIds($ids)
    {
        $ret = [];
        if (is_array($ids) && count($ids) > 0) {
            $criteria = new \Criteria('cat_cid', '(' . implode(',', $ids) . ')', 'IN');
            $ret      = $this->getObjects($criteria, true, true, '*', false);
        }

        return $ret;
    }

    /**
     * Retourne la liste des catégories mères (sous forme d'un tableau d'objets)
     *
     * @return array Objets de type Category
     */
    public function getMotherCategories()
    {
        $ret      = [];
        $criteria = new \Criteria('cat_pid', 0, '=');
        $criteria->setSort('cat_title');
        $ret = $this->getObjects($criteria);

        return $ret;
    }

    /**
     * Get category count
     *
     * @return int number of category
     */
    public function getCategoriesCount()
    {
        $criteria = new \CriteriaCompo();

        return $this->getCount($criteria);
    }
}
