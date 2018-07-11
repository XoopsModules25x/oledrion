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
 * Sales order management
 */


/**
 * Class CommandsHandler
 */
class CommandsHandler extends OledrionPersistableObjectHandler
{
    /**
     * CommandsHandler constructor.
     * @param \XoopsDatabase|null $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        //                        Table                   Classe           Id
        parent::__construct($db, 'oledrion_commands', Commands::class, 'cmd_id');
    }

    /**
     * Indicates if this is the first order of a customer
     *
     * @param int $uid Identifiant de l'utilisateur
     * @return bool Indique si c'est le cas ou pas
     */
    public function isFirstCommand($uid = 0)
    {
        if (0 == $uid) {
            $uid = Oledrion\Utility::getCurrentUserID();
        }
        $critere = new \Criteria('cmd_uid', (int)$uid, '=');
        return $this->getCount($critere) > 0;
    }

    /**
     * Indicates if a product has already been purchased by a user
     *
     * @param int $uid       User ID
     * @param int $productId Product ID
     * @return bool Indicates whether this is the case or not
     */
    public function productAlreadyBought($uid = 0, $productId = 0)
    {
        if (0 == $uid) {
            $uid = Oledrion\Utility::getCurrentUserID();
        }
        $sql    = 'SELECT Count(*) AS cpt FROM ' . $this->db->prefix('oledrion_caddy') . ' c, ' . $this->db->prefix('oledrion_commands') . ' f WHERE c.caddy_product_id = ' . (int)$productId . ' AND c.caddy_cmd_id = f.cmd_id AND f.cmd_uid = ' . (int)$uid;
        $result = $this->db->query($sql);
        if (!$result) {
            return false;
        }
        list($count) = $this->db->fetchRow($result);
        return $count > 0;
    }

    /**
     * Inventory update for each product composing the order
     *
     * @param  Commands $order La commande à traiter
     * @return bool
     */
    public function updateStocks($order)
    {
        global $caddyHandler, $productsHandler, $persistentCartHandler;
        $orderId = $order->getVar('cmd_id');
        // Recherche de tous les produits du caddy
        $caddy  = $caddyHandler->getCaddyFromCommand($orderId);
        $tblTmp = $tblProducts = [];
        foreach ($caddy as $item) {
            $tblTmp[] = $item->getVar('caddy_product_id');
        }
        // Chargement de tous les produits
        $critere     = new \Criteria('product_id', '(' . implode(',', $tblTmp) . ')', 'IN');
        $tblProducts = $productsHandler->getObjects($critere, true);
        // Boucle sur le caddy pour mettre à jour les quantités
        foreach ($caddy as $item) {
            if (isset($tblProducts[$item->getVar('caddy_product_id')])) {
                $product = $tblProducts[$item->getVar('caddy_product_id')];
                $productsHandler->decreaseStock($product, $item->getVar('caddy_qte'));
                $productsHandler->verifyLowStock($product); // Vérification du stock d'alerte
                $persistentCartHandler->deleteUserProduct($item->getVar('caddy_product_id'), $order->getVar('cmd_uid'));
            }
        }

        return true;
    }

    /**
     * Returns the list of download URLs linked to a command
     *
     * @param  Commands $order La commande en question
     * @return array                    Les URL
     */
    public function getOrderUrls(Commands $order)
    {
        global $caddyHandler, $productsHandler;
        $retval = $carts = $productsList = $products = [];
        // Recherche des produits du caddy associés à cette commande
        $carts = $caddyHandler->getObjects(new \Criteria('caddy_cmd_id', $order->getVar('cmd_id'), '='));
        foreach ($carts as $item) {
            $productsList[] = $item->getVar('caddy_product_id');
        }
        if (count($productsList) > 0) {
            $products = $productsHandler->getObjects(new \Criteria('product_id', '(' . implode(',', $productsList) . ')', 'IN'), true);
            if (count($products) > 0) {
                foreach ($carts as $item) {
                    $produit = null;
                    if (isset($products[$item->getVar('caddy_product_id')])) {
                        $produit = $products[$item->getVar('caddy_product_id')];
                        if ('' !== xoops_trim($produit->getVar('product_download_url'))) {
                            $retval[] = OLEDRION_URL . 'download.php?download_id=' . $item->getVar('caddy_pass');
                        }
                    }
                }
            }
        }

        return $retval;
    }

    /**
     * Sending the mail to inform the customer and the store that an order is validated
     *
     * @param Commands $order   La commande en question
     * @param string   $comment Optionel, un commentaire pour le webmaster
     */
    public function notifyOrderValidated(Commands $order, $comment = '')
    {
        global $xoopsConfig;
        $msg                       = [];
        $Urls                      = [];
        $Urls                      = $this->getOrderUrls($order); // On récupère les URL des fichiers à télécharger
        $msg['ADDITIONAL_CONTENT'] = '';
        $msg['NUM_COMMANDE']       = $order->getVar('cmd_id');
        $msg['COMMENT']            = $comment;
        if (count($Urls) > 0) {
            $msg['ADDITIONAL_CONTENT'] = _OLEDRION_YOU_CAN_DOWNLOAD . "\n" . implode("\n", $Urls);
        }
        Oledrion\Utility::sendEmailFromTpl('command_shop_verified.tpl', Oledrion\Utility::getEmailsFromGroup(Oledrion\Utility::getModuleOption('grp_sold')), _OLEDRION_GATEWAY_VALIDATED, $msg);
        Oledrion\Utility::sendEmailFromTpl('command_client_verified.tpl', $order->getVar('cmd_email'), sprintf(_OLEDRION_GATEWAY_VALIDATED, $xoopsConfig['sitename']), $msg);
    }

    /**
     * Validation of an order and inventory update
     *
     * @param  Commands $order   La commande à traiter
     * @param  string   $comment Optionel, un commentaire pour le mail envoyé au webmaster
     * @return bool                     Indique si la validation de la commande s'est bien faite ou pas
     */
    public function validateOrder(Commands $order, $comment = '')
    {
        $retval = false;
        $order->setVar('cmd_state', Constants::OLEDRION_STATE_VALIDATED);
        $order->setVar('cmd_comment', $comment);
        $retval = $this->insert($order, true);
        if ($retval) {
            $this->updateStocks($order);
            // B.R. Validation emails redundant since order emails now sent @gateway (paypal) validation
            // B.R. $this->notifyOrderValidated($order, $comment);
        }

        return $retval;
    }

    /**
     * pack an order and update inventory
     *
     * @param  Commands $order   La commande à traiter
     * @param  string   $comment Optionel, un commentaire pour le mail envoyé au webmaster
     * @return bool                     Indique si la validation de la commande s'est bien faite ou pas
     */
    public function packOrder(Commands $order, $comment = '')
    {
        $retval = false;
        $order->setVar('cmd_state', Constants::OLEDRION_STATE_PACKED);
        $order->setVar('cmd_comment', $comment);
        $retval = $this->insert($order, true);

        return $retval;
    }

    /**
     * submit an order and inventory update
     *
     * @param  Commands $order   La commande à traiter
     * @param  string   $comment Optionel, un commentaire pour le mail envoyé au webmaster
     * @return bool                     Indique si la validation de la commande s'est bien faite ou pas
     */
    public function submitOrder(Commands $order, $comment = '')
    {
        $retval = false;
        $order->setVar('cmd_state', Constants::OLEDRION_STATE_SUBMITED);
        $order->setVar('cmd_comment', $comment);
        $retval = $this->insert($order, true);

        return $retval;
    }

    /**
     * delivery an order and inventory update
     *
     * @param  Commands $order   La commande à traiter
     * @param  string   $comment Optionel, un commentaire pour le mail envoyé au webmaster
     * @return bool                     Indique si la validation de la commande s'est bien faite ou pas
     */
    public function deliveryOrder(Commands $order, $comment = '')
    {
        $retval = false;
        $order->setVar('cmd_state', Constants::OLEDRION_STATE_DELIVERED);
        $order->setVar('cmd_comment', $comment);
        $retval = $this->insert($order, true);

        return $retval;
    }

    /**
     * Inform the site owner that an order is fraudulent
     *
     * @param Commands $order   La commande en question
     * @param string   $comment Optionel, un commentaire pour le mail envoyé au webmaster
     */
    public function notifyOrderFraudulent(Commands $order, $comment = '')
    {
        $msg                 = [];
        $msg['NUM_COMMANDE'] = $order->getVar('cmd_id');
        $msg['COMMENT']      = $comment;
        Oledrion\Utility::sendEmailFromTpl('command_shop_fraud.tpl', Oledrion\Utility::getEmailsFromGroup(Oledrion\Utility::getModuleOption('grp_sold')), _OLEDRION_GATEWAY_FRAUD, $msg);
    }

    /**
     * Apply fraudulent order status to an order
     *
     * @param object|Commands $order   La commande à traiter
     * @param string          $comment Optionel, un commentaire pour le mail envoyé au webmaster
     */
    public function setFraudulentOrder(Commands $order, $comment = '')
    {
        $order->setVar('cmd_state', Constants::OLEDRION_STATE_FRAUD);
        $order->setVar('cmd_comment', $comment);
        $this->insert($order, true);
        $this->notifyOrderFraudulent($order, $comment);
    }

    /**
     * Inform the site owner that an order is pending
     *
     * @param Commands $order   La commande en question
     * @param string   $comment Optionel, un commentaire pour le mail envoyé au webmaster
     */
    public function notifyOrderPending(Commands $order, $comment = '')
    {
        $msg                 = [];
        $msg['NUM_COMMANDE'] = $order->getVar('cmd_id');
        $msg['COMMENT']      = $comment;
        Oledrion\Utility::sendEmailFromTpl('command_shop_pending.tpl', Oledrion\Utility::getEmailsFromGroup(Oledrion\Utility::getModuleOption('grp_sold')), _OLEDRION_GATEWAY_PENDING, $msg);
    }

    /**
     * Apply pending order status to an order
     *
     * @param Commands $order   La commande à traiter
     * @param string   $comment Optionel, un commentaire pour le mail envoyé au webmaster
     */
    public function setOrderPending(Commands $order, $comment = '')
    {
        $order->setVar('cmd_state', Constants::OLEDRION_STATE_PENDING); // En attente
        $order->setVar('cmd_comment', $comment);
        $this->insert($order, true);
        $this->notifyOrderPending($order, $comment);
    }

    /**
     * Inform the site owner that an order has failed (payment)
     *
     * @param Commands $order   La commande en question
     * @param string   $comment Optionel, un commentaire pour le mail envoyé au webmaster
     */
    public function notifyOrderFailed(Commands $order, $comment = '')
    {
        $msg                 = [];
        $msg['NUM_COMMANDE'] = $order->getVar('cmd_id');
        $msg['COMMENT']      = $comment;
        Oledrion\Utility::sendEmailFromTpl('command_shop_failed.tpl', Oledrion\Utility::getEmailsFromGroup(Oledrion\Utility::getModuleOption('grp_sold')), _OLEDRION_GATEWAY_FAILED, $msg);
    }

    /**
     * Applies failed order status to an order
     *
     * @param Commands $order   La commande à traiter
     * @param string   $comment Optionel, un commentaire pour le mail envoyé au webmaster
     */
    public function setOrderFailed(Commands $order, $comment = '')
    {
        $order->setVar('cmd_state', Constants::OLEDRION_STATE_FAILED); // Echec
        $order->setVar('cmd_comment', $comment);
        $this->insert($order, true);
        $this->notifyOrderFailed($order, $comment);
    }

    /**
     * Inform the site owner that an order has failed (payment)
     *
     * @param Commands $order   La commande en question
     * @param string   $comment Optionel, un commentaire pour le mail envoyé au webmaster
     */
    public function notifyOrderCanceled(Commands $order, $comment = '')
    {
        $msg                 = [];
        $msg['NUM_COMMANDE'] = $order->getVar('cmd_id');
        $msg['COMMENT']      = $comment;
        Oledrion\Utility::sendEmailFromTpl('command_shop_cancel.tpl', Oledrion\Utility::getEmailsFromGroup(Oledrion\Utility::getModuleOption('grp_sold')), _OLEDRION_ORDER_CANCELED, $msg);
        Oledrion\Utility::sendEmailFromTpl('command_client_cancel.tpl', $order->getVar('cmd_email'), _OLEDRION_ORDER_CANCELED, $msg);
    }

    /**
     * Apply canceled order status to an order
     *
     * @param Commands $order   La commande à traiter
     * @param string   $comment Optionel, un commentaire pour le mail envoyé au webmaster
     */
    public function setOrderCanceled(Commands $order, $comment = '')
    {
        $order->setVar('cmd_state', Constants::OLEDRION_STATE_CANCELED); // Annulée
        $order->setVar('cmd_comment', $comment);
        $this->insert($order, true);
        $this->notifyOrderCanceled($order, $comment);
    }

    /**
     * Returns an order from its cancellation password
     *
     * @param  string $cmd_cancel Le mot de passe d'annulation
     * @return mixed  Soit un objet soit null
     */
    public function getOrderFromCancelPassword($cmd_cancel)
    {
        $critere = new \Criteria('cmd_cancel', $cmd_cancel, '=');
        if ($this->getCount($critere) > 0) {
            $tblCmd = [];
            $tblCmd = $this->getObjects($critere);
            if (count($tblCmd) > 0) {
                return $tblCmd[0];
            }
        }

        return null;
    }

    /**
     * Returns the last command of a user (if it exists)
     *
     * @param int $uid Identifiant de la commande
     * @return null|string
     */
    public function getLastUserOrder($uid)
    {
        $order    = null;
        $orders   = [];
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('cmd_uid', $uid));
        $criteria->add(new \Criteria('cmd_status', 2));
        $criteria->setSort('cmd_date');
        $criteria->setOrder('DESC');
        $criteria->setLimit(1);
        $orders = $this->getObjects($criteria, false);
        if (count($orders) > 0) {
            $order = $orders[0];
        }

        return $order;
    }

    /**
     * Deletes an order and everything related to it
     *
     * @param  Commands $order
     * @return bool
     */
    public function removeOrder(Commands $order)
    {
        //        $handlers = HandlerManager::getInstance();
        $cmd_id = $order->getVar('cmd_id');
        $res    = $this->delete($order);
        // Suppression des objets associés
        // 1) Ses propres caddies
        $caddyHandler = new Oledrion\CaddyHandler();
        $caddyHandler->removeCartsFromOrderId($cmd_id);
        // 2) Les caddies des attributs
        $caddyAttributesHandler = new Oledrion\CaddyAttributesHandler();
        $caddyAttributesHandler->removeCartsFromOrderId($cmd_id);

        return $res;
    }
}
