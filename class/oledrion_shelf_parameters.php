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
 * @version     $Id: oledrion_shelf_parameters.php 12290 2014-02-07 11:05:17Z beckmi $
 */
/**
 * Classe interne dont le but est de passer des paramètres à la classe oeldrion_shelf
 */
defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

/**
 * Utilisé comme paramètre dans la façcade oledrion_shelf
 */
class oledrion_shelf_parameters
{
    /**
     * Le conteneur de paramètres
     *
     * @var array
     */
    private $parameters = array();

    public function __construct()
    {
        $this->resetDefaultValues();
    }

    /**
     * Réinitialisation des valeurs
     *
     * @return object
     */
    public function resetDefaultValues()
    {
        $this->parameters['start'] = 0;
        $this->parameters['limit'] = 0;
        $this->parameters['category'] = 0;
        $this->parameters['sort'] = 'product_submitted DESC, product_title';
        $this->parameters['order'] = 'ASC';
        $this->parameters['excluded'] = 0;
        $this->parameters['withXoopsUser'] = false;
        $this->parameters['withRelatedProducts'] = false;
        $this->parameters['withQuantity'] = false;
        $this->parameters['thisMonthOnly'] = false;
        $this->parameters['productsType'] = '';

        return $this;
    }

    /**
     * Retourne le tableau des paramètres
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Positione la valeur de début
     *
     * @param  integer $value
     * @return object
     */
    public function setStart($value)
    {
        $this->parameters['start'] = intval($value);

        return $this;
    }

    /**
     * Fixe le nombre maximum d'enregistrements à retourner
     *
     * @param  integer $value
     * @return object
     */
    public function setLimit($value)
    {
        $this->parameters['limit'] = intval($value);

        return $this;
    }

    /**
     * Fixe la catégorie à utiliser
     *
     * @param  integer $value
     * @return object
     */
    public function setCategory($value)
    {
        $this->parameters['category'] = $value;

        return $this;
    }

    /**
     * Fixe la zone qui sert de tri
     *
     * @param  string $value
     * @return object
     */
    public function setSort($value)
    {
        $this->parameters['sort'] = $value;

        return $this;
    }

    /**
     * Fixe l'ordre de tri
     *
     * @param  string $value
     * @return array
     */
    public function setOrder($value)
    {
        $this->parameters['order'] = $value;

        return $this;
    }

    /**
     * Fixe la liste des produits à exclure
     *
     * @param  mixed  $value
     * @return string
     */
    public function setExcluded($value)
    {
        $this->parameters['excluded'] = $value;

        return $this;
    }

    /**
     * Indique s'il faut retourner les utilisateurs Xoops
     *
     * @param  boolean $value
     * @return object
     */
    public function setWithXoopsUser($value)
    {
        $this->parameters['withXoopsUser'] = $value;

        return $this;
    }

    /**
     * Indique s'il faut retourner les produits relatifs
     *
     * @param  boolean $value
     * @return object
     */
    public function setWithRelatedProducts($value)
    {
        $this->parameters['withRelatedProducts'] = $value;

        return $this;
    }

    /**
     * Indique s'il faut retourner les quantités
     *
     * @param  boolean $value
     * @return object
     */
    public function setWithQuantity($value)
    {
        $this->parameters['withQuantity'] = $value;

        return $this;
    }

    /**
     * Fixe le type de produits à retourner
     *
     * @param  string $value
     * @return object
     */
    public function setProductsType($value)
    {
        $this->parameters['productsType'] = $value;

        return $this;
    }

    /**
     * Indique s'il faut retourner seulement les mois
     *
     * @param  boolean $value
     * @return object
     */
    public function setThisMonthOnly($value)
    {
        $this->parameters['thisMonthOnly'] = $value;

        return $this;
    }
}
