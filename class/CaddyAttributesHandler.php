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
 * Gestion des options (attributs) produits dans les commandes
 */
// require_once __DIR__ . '/classheader.php';

/**
 * Class CaddyAttributesHandler
 */
class CaddyAttributesHandler extends OledrionPersistableObjectHandler
{
    /**
     * CaddyAttributesHandler constructor.
     * @param \XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        //                                Table                   Classe                      Id
        parent::__construct($db, 'oledrion_caddy_attributes', CaddyAttributes::class, 'ca_id');
    }

    /**
     * Retourne le nombre d'attributs liés à un caddy
     *
     * @param int $ca_caddy_id L'ID du caddy concerné
     * @return int
     * @since 2.3.2009.03.23
     */
    public function getAttributesCountForCaddy($ca_caddy_id)
    {
        return $this->getCount(new \Criteria('ca_caddy_id', $ca_caddy_id, '='));
    }

    /**
     * Retourne la liste formatée des attributs liés à un caddy
     *
     * @param int       $ca_caddy_id L'identifiant de caddy
     * @param  Products $product     Le produit concerné par le caddy
     * @return array
     * @since 2.3.2009.03.23
     */
    public function getFormatedAttributesForCaddy($ca_caddy_id, Products $product)
    {
        //        $handlers   = HandlerManager::getInstance();
        $attributes = $ret = [];
        $attributes = $this->getObjects(new \Criteria('ca_caddy_id', $ca_caddy_id, '='));
        if (0 === count($attributes)) {
            return $ret;
        }
        foreach ($attributes as $caddyAttribute) {
            $data      = [];
            $attribute = null;
            $attribute = $attributesHandler->get($caddyAttribute->getVar('ca_attribute_id'));
            if (is_object($attribute)) {
                $data = $attribute->toArray();
            }
            $data['attribute_options'] = $caddyAttribute->renderForInvoice($product);
            $ret[]                     = $data;
        }

        return $ret;
    }

    /**
     * Retourne le nombre de caddy attributs liés à un attribut
     *
     * @param int $ca_attribute_id L'Identifiant de l'attribut concerné
     * @return int
     * @since 2.3.2009.03.23
     */
    public function getCaddyCountFromAttributeId($ca_attribute_id)
    {
        return $this->getCount(new \Criteria('ca_attribute_id', $ca_attribute_id, '='));
    }

    /**
     * Retourne la liste des numéros de commandes "liés" à un attribut
     *
     * @param int $ca_attribute_id
     * @return array
     */
    public function getCommandIdFromAttribute($ca_attribute_id)
    {
        $ret       = $ordersIds = [];
        $criteria  = new \Criteria('ca_attribute_id', $ca_attribute_id, '=');
        $ordersIds = $this->getObjects($criteria, false, true, 'ca_cmd_id', false);
        foreach ($ordersIds as $order) {
            $ret[] = $order->ca_cmd_id;
        }

        return $ret;
    }

    /**
     * Supprime les caddies associés à une commande
     *
     * @param $ca_cmd_id
     * @return bool
     * @internal param int $caddy_cmd_id
     */
    public function removeCartsFromOrderId($ca_cmd_id)
    {
        $ca_cmd_id = (int)$ca_cmd_id;

        return $this->deleteAll(new \Criteria('ca_cmd_id', $ca_cmd_id, '='));
    }
}
