<?php namespace Xoopsmodules\oledrion;
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

use Xoopsmodules\oledrion;

/**
 * Gestion des options des passerelles de paiement
 */
require_once __DIR__ . '/classheader.php';


/**
 * Class GatewaysOptionsHandler
 */
class GatewaysOptionsHandler extends OledrionPersistableObjectHandler
{
    /**
     * GatewaysOptionsHandler constructor.
     * @param \XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db)
    { //                                Table                       Classe                      Id
        parent::__construct($db, 'oledrion_gateways_options', GatewaysOptions::class, 'option_id');
    }

    /**
     * Retourne toutes les options d'une passerelle de paiement
     *
     * @param  string $option_gateway Le nom de la passerelle de paiement
     * @return array  Tableau d'objets de type GatewaysOptions
     */
    public function getGatewayOptions($option_gateway)
    {
        $criteria = new \Criteria('option_gateway', $option_gateway, '=');

        return $this->getObjects($criteria);
    }

    /**
     * Supprime toutes les options d'une passerelle de paiement
     *
     * @param  string $option_gateway
     * @return boolean Le résultat de la suppression des options
     */
    public function deleteGatewayOptions($option_gateway)
    {
        $criteria = new \Criteria('option_gateway', $option_gateway, '=');

        return $this->deleteAll($criteria);
    }

    /**
     * Retourne une option d'une passerelle de paiement
     *
     * @param  string $option_gateway Le nom de la passerelle de paiement
     * @param  string $option_name    L'option que l'on souhaite récupérer
     * @return array Objet de type GatewaysOptions
     */
    public function getGatewayOption($option_gateway, $option_name)
    {
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('option_gateway', $option_gateway, '='));
        $criteria->add(new \Criteria('option_name', $option_name, '='));

        return $this->getObjects($criteria);
    }

    /**
     * Retourne la VALEUR d'une option d'une passerelle de paiement
     *
     * @param  string  $option_gateway Le nom d'une passerelle de paiement
     * @param  string  $option_name    L'option que l'on souhaite récupérer
     * @param  string  $format         Le format dans lequel on souhaite récupérer la valeur (par rapport au getVar())
     * @param  boolean $unserialize    Indique s'il faut désérialiser la valeur de retour
     * @return mixed   La valeur de l'option ou null si l'option ne peut pas être trouvée
     */
    public function getGatewayOptionValue($option_gateway, $option_name, $format = 'N', $unserialize = false)
    {
        $ret      = [];
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('option_gateway', $option_gateway, '='));
        $criteria->add(new \Criteria('option_name', $option_name, '='));
        $ret = $this->getObjects($criteria);
        if (count($ret) > 0) {
            if ($unserialize) {
                return unserialize($ret[0]->getVar('option_value', $format));
            } else {
                return $ret[0]->getVar('option_value', $format);
            }
        } else {
            return null;
        }
    }

    /**
     * Positionne la valeur d'une option d'une passerelle de paiement et l'enregistre
     *
     * @param  string  $option_gateway Le nom de la passerelle de paiement
     * @param  string  $option_name    Le nom de l'option
     * @param  mixed   $option_value   La valeur de l'option
     * @param  boolean $serialize      Indique s'il faut sérialiser la valeur avant de l'enregistrer
     * @return boolean Le résultat de la mise à jour (vrai si la mise à jour s'est faite sinon faux)
     */
    public function setGatewayOptionValue($option_gateway, $option_name, $option_value, $serialize = false)
    {
        $ret      = [];
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('option_gateway', $option_gateway, '='));
        $criteria->add(new \Criteria('option_name', $option_name, '='));
        $ret = $this->getObjects($criteria);
        if (count($ret) > 0) {
            $option = $ret[0];
            if ($serialize) {
                $option->setVar('option_value', serialize($option_value));
            } else {
                $option->setVar('option_value', $option_value);
            }

            return $this->insert($option, true);
        } else { // Option introuvable, on va la créer
            $option = $this->create(true);
            $option->setVar('option_gateway', $option_gateway);
            $option->setVar('option_name', $option_name);
            $option->setVar('option_value', $option_value);

            return $this->insert($option, true);
        }
    }
}
