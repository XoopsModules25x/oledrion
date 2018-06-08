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
 * Gestion des catégories de produits
 */

require_once __DIR__ . '/classheader.php';

/**
 * Class Oledrion_cat
 */
class Oledrion_cat extends Oledrion_Object
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
        $this->initVar('cat_cid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cat_pid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cat_title', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cat_imgurl', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cat_description', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('cat_advertisement', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('cat_metakeywords', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cat_metadescription', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cat_metatitle', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cat_footer', XOBJ_DTYPE_TXTAREA, null, false);
        // Pour autoriser le html
        $this->initVar('dohtml', XOBJ_DTYPE_INT, 1, false);
    }

    /**
     * Retourne l'URL de l'image de la catégorie courante
     * @return string L'URL
     */
    public function getPictureUrl()
    {
        return OLEDRION_PICTURES_URL . '/' . $this->getVar('cat_imgurl');
    }

    /**
     * Retourne le chemin de l'image de la catégorie courante
     * @return string Le chemin
     */
    public function getPicturePath()
    {
        return OLEDRION_PICTURES_PATH . '/' . $this->getVar('cat_imgurl');
    }

    /**
     * Indique si l'image de la catégorie existe
     *
     * @return boolean Vrai si l'image existe sinon faux
     */
    public function pictureExists()
    {
        $return = false;
        if (xoops_trim($this->getVar('cat_imgurl')) != ''
            && file_exists(OLEDRION_PICTURES_PATH . '/' . $this->getVar('cat_imgurl'))) {
            $return = true;
        }

        return $return;
    }

    /**
     * Supprime l'image associée à une catégorie
     * @return void
     */
    public function deletePicture()
    {
        if ($this->pictureExists()) {
            @unlink(OLEDRION_PICTURES_PATH . '/' . $this->getVar('cat_imgurl'));
        }
        $this->setVar('cat_imgurl', '');
    }

    /**
     * Retourne l'url à utiliser pour accéder à la catégorie en tenant compte des préférences du module
     *
     * @return string L'url à utiliser
     */
    public function getLink()
    {
        require_once XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
        $url = '';
        if (OledrionUtility::getModuleOption('urlrewriting') == 1) { // On utilise l'url rewriting
            $url = OLEDRION_URL . 'category-' . $this->getVar('cat_cid') . OledrionUtility::makeSeoUrl($this->getVar('cat_title', 'n')) . '.html';
        } else { // Pas d'utilisation de l'url rewriting
            $url = OLEDRION_URL . 'category.php?cat_cid=' . $this->getVar('cat_cid');
        }

        return $url;
    }

    /**
     * Rentourne la chaine à envoyer dans une balise <a> pour l'attribut href
     *
     * @return string
     */
    public function getHrefTitle()
    {
        return OledrionUtility::makeHrefTitle($this->getVar('cat_title'));
    }

    /**
     * Retourne les éléments du produits formatés pour affichage
     *
     * @param  string $format
     * @return array
     */
    public function toArray($format = 's')
    {
        $ret                     = array();
        $ret                     = parent::toArray($format);
        $ret['cat_full_imgurl']  = $this->getPictureUrl();
        $ret['cat_href_title']   = $this->getHrefTitle();
        $ret['cat_url_rewrited'] = $this->getLink();

        return $ret;
    }
}

/**
 * Class OledrionOledrion_catHandler
 */
class OledrionOledrion_catHandler extends Oledrion_XoopsPersistableObjectHandler
{
    /**
     * OledrionOledrion_catHandler constructor.
     * @param XoopsDatabase|null $db
     */
    public function __construct(XoopsDatabase $db)
    { //                        Table               Classe       Id       Libellé
        parent::__construct($db, 'oledrion_cat', 'oledrion_cat', 'cat_cid', 'cat_title');
    }

    /**
     * Renvoie (sous forme d'objets) la liste de toutes les catégories
     *
     * @param  Oledrion_parameters $parameters
     * @return array               Taleau d'objets (catégories)
     * @internal param int $start Indice de début de recherche
     * @internal param int $limit Nombre maximum d'enregsitrements à renvoyer
     * @internal param string $sort Champ à utiliser pour le tri
     * @internal param string $order Ordre du tire (asc ou desc)
     * @internal param bool $idaskey Indique s'il faut renvoyer un tableau dont la clé est l'identifiant de l'enregistrement
     */
    public function getAllCategories(Oledrion_parameters $parameters)
    {
        $parameters = $parameters->extend(new Oledrion_parameters(array(
                                                                      'start'   => 0,
                                                                      'limit'   => 0,
                                                                      'sort'    => 'cat_title',
                                                                      'order'   => 'ASC',
                                                                      'idaskey' => true
                                                                  )));
        $critere    = new Criteria('cat_cid', 0, '<>');
        $critere->setLimit($parameters['limit']);
        $critere->setStart($parameters['start']);
        $critere->setSort($parameters['sort']);
        $critere->setOrder($parameters['order']);
        $categories = array();
        $categories = $this->getObjects($critere, $parameters['idaskey']);

        return $categories;
    }

    /**
     * Internal function to make an expanded view of categories via <li>
     *
     * @param  string $fieldName
     * @param  string $key
     * @param  string $ret
     * @param  object $tree
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
     * @param  string  $fieldName Name of the member variable from the node objects that should be used as the title for the options.
     * @param  integer $key       ID of the object to display as the root of select options
     * @return string  HTML select box
     */
    public function getUlMenu($fieldName, $key = 0)
    {
        require_once XOOPS_ROOT_PATH . '/class/tree.php';
        $items      = $this->getAllCategories(new Oledrion_parameters());
        $treeObject = new XoopsObjectTree($items, 'cat_cid', 'cat_pid');
        $tree       =& $treeObject->getTree();

        $ret = '';
        $this->_makeLi($fieldName, $key, $ret, $tree);
        if (xoops_trim($ret) != '') {
            $ret = substr($ret, 0, -6);
        }

        return $ret;
    }

    /**
     * Supprime une catégorie (et tout ce qui lui est relatif)
     *
     * @param  Oledrion_cat $category
     * @return boolean      Le résultat de la suppression
     */
    public function deleteCategory(Oledrion_cat $category)
    {
        global $xoopsModule;
        $category->deletePicture();
        xoops_notification_deletebyitem($xoopsModule->getVar('mid'), 'new_category', $category->getVar('cat_cid'));

        return $this->delete($category, true);
    }

    /**
     * Retourne le nombre de produits d'une ou de plusieurs catégories
     *
     * @param  integer $cat_cid    L'identifiant de la catégorie dont on veut récupérer le nombre de produits
     * @param  boolean $withNested Faut il inclure les sous-catégories ?
     * @return integer Le nombre de produits
     */
    public function getCategoryProductsCount($cat_cid, $withNested = true)
    {
        global $h_oledrion_products;
        $childsIDs   = array();
        $childsIDs[] = $cat_cid;

        if ($withNested) { // Recherche des sous catégories de cette catégorie
            $items = $childs = array();
            require_once XOOPS_ROOT_PATH . '/class/tree.php';
            $items  = $this->getAllCategories(new Oledrion_parameters());
            $mytree = new XoopsObjectTree($items, 'cat_cid', 'cat_pid');
            $childs = $mytree->getAllChild($cat_cid);
            if (count($childs) > 0) {
                foreach ($childs as $onechild) {
                    $childsIDs[] = $onechild->getVar('cat_cid');
                }
            }
        }

        return $h_oledrion_products->getCategoryProductsCount($childsIDs);
    }

    /**
     * Retourne des catégories selon leur ID
     *
     * @param  array $ids Les ID des catégories à retrouver
     * @return array Objets de type Oledrion_cat
     */
    public function getCategoriesFromIds($ids)
    {
        $ret = array();
        if (is_array($ids) && count($ids) > 0) {
            $criteria = new Criteria('cat_cid', '(' . implode(',', $ids) . ')', 'IN');
            $ret      = $this->getObjects($criteria, true, true, '*', false);
        }

        return $ret;
    }

    /**
     * Retourne la liste des catégories mères (sous forme d'un tableau d'objets)
     *
     * @return array Objets de type Oledrion_cat
     */
    public function getMotherCategories()
    {
        $ret      = array();
        $criteria = new Criteria('cat_pid', 0, '=');
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
        $criteria = new CriteriaCompo();

        return $this->getCount($criteria);
    }
}
