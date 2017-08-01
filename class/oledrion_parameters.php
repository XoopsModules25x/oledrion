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
 * Class used for parameters passing to classes methods
 *
 * @copyright          Hervé Thouzard (http://www.herve-thouzard.com/)
 * @license            http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package            oledrion
 * @author             Hervé Thouzard (http://www.herve-thouzard.com/)
 *
 * Example :
 *
 * // Instanciate it like this
 * $param = new Oledrion_parameters();
 *
 * // Create several parameters in one time:
 * $param->setLimit(10)->setSort('manu_name');
 *
 * // Set a parameter with the array convention:
 * $param['sort'] = 'first_name';
 *
 * // Set a parameter as a class property:
 * $param->order = 'DESC';
 *
 * // Display a parameter, first way:
 * echo "<br>value=".$param['sort'];    // DESC
 *
 * // Another method to show it, as a class method:
 * echo $param->limit();    // 10
 *
 * // Set the default values
 * $newParameters = $param->extend(new Oledrion_parameters(array('sort' => 'firstName', 'start' => 0, 'limit' => 15, 'showAll' => true)));
 *
 */
class Oledrion_parameters extends ArrayObject
{
    /**
     * Permet de valoriser un indice de la classe comme si c'était une propriété de la classe
     *
     * @example $enregistrement->nom_du_champ = 'ma chaine'
     *
     * @param  string $key   Le nom du champ à traiter
     * @param  mixed  $value La valeur à lui attribuer
     * @return OledrionHandler
     */
    public function __set($key, $value)
    {
        parent::offsetSet($key, $value);

        return $this;
    }

    /**
     * Valorisation d'un indice de la classe en utilisant un appel de fonction basé sur le principe suivant :
     *         $maClasse->setLimit(10);
     * Il est possible de chainer comme ceci : $maClasse->setStart(0)->setLimit(10);
     *
     * @param  string $method
     * @param  mixed  $args
     * @return Oledrion_parameters|ArrayObject
     */
    public function __call($method, $args)
    {
        if (substr($method, 0, 3) === 'set') {
            parent::offsetSet(strtolower(substr($method, 3, 1)) . substr($method, 4), $args[0]);

            return $this;
        } else { // Affichage de la valeur

            return parent::offsetGet($method);
        }
    }

    /**
     * Méthode qui essaye de faire la même chose que la méthode extend() de jQuery
     *
     * On lui passe les valeurs par défaut que l'on attend et la méthode les compare avec les valeurs actuelles
     * Si des valeurs manquent, elles sont ajoutées
     *
     * @param  Oledrion_parameters $defaultValues
     * @return oledrion_parameters
     */
    public function extend(self $defaultValues)
    {
        $result = new self;
        $result = $this;
        foreach ($defaultValues as $key => $value) {
            if (!isset($result[$key])) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
