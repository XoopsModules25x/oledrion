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
 * Classe interne dont le but est de passer des paramètres à la classe oeldrion_shelf
 */
// defined('XOOPS_ROOT_PATH') || exit('Restricted access.');

/**
 * Utilisé comme paramètre dans la façcade oledrion_shelf
 */
class Oledrion_shelf_parameters
{
    /**
     * Le conteneur de paramètres
     *
     * @var array
     */
    private $parameters = [];

    /**
     * Oledrion_shelf_parameters constructor.
     */
    public function __construct()
    {
        $this->resetDefaultValues();
    }

    /**
     * Réinitialisation des valeurs
     *
     * @return Oledrion_shelf_parameters
     */
    public function resetDefaultValues()
    {
        $this->parameters['start']               = 0;
        $this->parameters['limit']               = 0;
        $this->parameters['category']            = 0;
        $this->parameters['sort']                = 'product_submitted DESC, product_title';
        $this->parameters['order']               = 'ASC';
        $this->parameters['excluded']            = 0;
        $this->parameters['withXoopsUser']       = false;
        $this->parameters['withRelatedProducts'] = false;
        $this->parameters['withQuantity']        = false;
        $this->parameters['thisMonthOnly']       = false;
        $this->parameters['productsType']        = '';

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
     * @return Oledrion_shelf_parameters
     */
    public function setStart($value)
    {
        $this->parameters['start'] = (int)$value;

        return $this;
    }

    /**
     * Fixe le nombre maximum d'enregistrements à retourner
     *
     * @param  integer $value
     * @return Oledrion_shelf_parameters
     */
    public function setLimit($value)
    {
        $this->parameters['limit'] = (int)$value;

        return $this;
    }

    /**
     * Fixe la catégorie à utiliser
     *
     * @param  integer $value
     * @return Oledrion_shelf_parameters
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
     * @return Oledrion_shelf_parameters
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
     * @param  mixed $value
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
     * @return Oledrion_shelf_parameters
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
     * @return Oledrion_shelf_parameters
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
     * @return Oledrion_shelf_parameters
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
     * @return Oledrion_shelf_parameters
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
     * @return Oledrion_shelf_parameters
     */
    public function setThisMonthOnly($value)
    {
        $this->parameters['thisMonthOnly'] = $value;

        return $this;
    }
}
