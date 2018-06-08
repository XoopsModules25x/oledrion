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
/**
 * Gestion des listes utilisateurs
 *
 * @since 2.3.2009.06.13
 */
use XoopsModules\Oledrion;
use XoopsModules\Oledrion\Constants;

// require_once __DIR__ . '/classheader.php';


/**
 * Class Lists
 */
class Lists extends OledrionObject
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
        $this->initVar('list_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('list_uid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('list_title', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('list_date', XOBJ_DTYPE_INT, null, false);
        $this->initVar('list_productscount', XOBJ_DTYPE_INT, null, false);
        $this->initVar('list_views', XOBJ_DTYPE_INT, null, false);
        $this->initVar('list_password', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('list_type', XOBJ_DTYPE_INT, null, false);
        $this->initVar('list_description', XOBJ_DTYPE_TXTAREA, null, false);
    }

    /**
     * Indique si la liste courante est accessible de l'utilisateur courant
     *
     * @return boolean
     */
    public function isSuitableForCurrentUser()
    {
        $uid = Oledrion\Utility::getCurrentUserID();
        if (Constants::OLEDRION_LISTS_PRIVATE == $this->getVar('list_type')) {
            if (0 == $uid || $uid != $this->getVar('list_uid')) {
                return false;
            }
        }

        return true;
    }

    /**
     * Retourne un tableau associatif qui pour chaque type de liste indique son type sous forme de texte
     *
     * @return array
     */
    public static function getTypesArray()
    {
        return [
            Constants::OLEDRION_LISTS_PRIVATE   => _OLEDRION_LIST_PRIVATE,
            Constants::OLEDRION_LISTS_WISH      => _OLEDRION_LIST_PUBLIC_WISH_LIST,
            Constants::OLEDRION_LISTS_RECOMMEND => _OLEDRION_LIST_PUBLIC_RECOMMENDED_LIST
        ];
    }

    /**
     * Retourne la description de la liste courante
     *
     * @return string
     */
    public function getListTypeDescription()
    {
        $description = $this->getTypesArray();

        return $description[$this->list_type];
    }

    /**
     * Retourne l'url à utiliser pour accéder à la liste en tenant compte des préférences du module
     *
     * @return string L'url à utiliser
     */
    public function getLink()
    {
        $url = '';
        if (1 == Oledrion\Utility::getModuleOption('urlrewriting')) { // On utilise l'url rewriting
            $url = OLEDRION_URL . 'list-' . $this->getVar('list_id') . Oledrion\Utility::makeSeoUrl($this->getVar('list_title', 'n')) . '.html';
        } else { // Pas d'utilisation de l'url rewriting
            $url = OLEDRION_URL . 'list.php?list_id=' . $this->getVar('list_id');
        }

        return $url;
    }

    /**
     * Retourne la date de création de la liste formatée
     *
     * @param  string $format
     * @return string
     */
    public function getFormatedDate($format = 's')
    {
        return formatTimestamp($this->list_date, $format);
    }

    /**
     * Rentourne la chaine à utiliser dans une balise <a> pour l'attribut href
     *
     * @return string
     */
    public function getHrefTitle()
    {
        return Oledrion\Utility::makeHrefTitle($this->getVar('list_title'));
    }

    /**
     * Retourne le nom de l'auteur de la liste courante
     *
     * @return string
     */
    public function getListAuthorName()
    {
        return \XoopsUser::getUnameFromId($this->getVar('list_uid', true));
    }

    /**
     * Retourne les éléments formatés pour affichage (en général)
     *
     * @param  string $format
     * @return array
     */
    public function toArray($format = 's')
    {
        $ret                          = [];
        $ret                          = parent::toArray($format);
        $ret['list_type_description'] = $this->getListTypeDescription();
        $ret['list_href_title']       = $this->getHrefTitle();
        $ret['list_url_rewrited']     = $this->getLink();
        $ret['list_formated_date']    = $this->getFormatedDate();
        $ret['list_username']         = $this->getListAuthorName();
        $ret['list_formated_count']   = sprintf(_OLEDRION_PRODUCTS_COUNT, $this->getVar('list_productscount'));

        return $ret;
    }
}
