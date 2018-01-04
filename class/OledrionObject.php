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
// defined('XOOPS_ROOT_PATH') || exit('Restricted access.');

class OledrionObject extends \XoopsObject
{
    /**
     * @param  string $format
     * @return array
     */
    public function toArray($format = 's')
    {
        $ret = [];
        foreach ($this->vars as $k => $v) {
            $ret[$k] = $this->getVar($k, $format);
        }

        return $ret;
    }

    // TODO: Rajouter une méthode intsert() et delete()

    /**
     * Permet de valoriser un champ de la table comme si c'était une propriété de la classe
     *
     * @example $enregistrement->nom_du_champ = 'ma chaine'
     *
     * @param  string $key   Le nom du champ à traiter
     * @param  mixed  $value La valeur à lui attribuer
     * @return void
     */
    public function __set($key, $value)
    {
        return $this->setVar($key, $value);
    }

    /**
     * Permet d'accéder aux champs de la table comme à des propriétés de la classe
     *
     * @example echo $enregistrement->nom_du_champ;
     *
     * @param  string $key Le nom du champ que l'on souhaite récupérer
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getVar($key);
    }
}

