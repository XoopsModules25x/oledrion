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
 * Gestion des commandes clients
 */
require_once __DIR__ . '/classheader.php';

define('OLEDRION_STATE_NOINFORMATION', 0); // Pas encore d'informations sur la commande
define('OLEDRION_STATE_VALIDATED', 1); // Commande validée par la passerelle de paiement
define('OLEDRION_STATE_PENDING', 2); // En attente
define('OLEDRION_STATE_FAILED', 3); // Echec
define('OLEDRION_STATE_CANCELED', 4); // Annulée
define('OLEDRION_STATE_FRAUD', 5); // Fraude
define('OLEDRION_STATE_PACKED', 6);
define('OLEDRION_STATE_SUBMITED', 7);
define('OLEDRION_STATE_DELIVERYED', 8);

/**
 * Class Oledrion_commands
 */
class Oledrion_commands extends Oledrion_Object
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
        $this->initVar('cmd_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cmd_uid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cmd_date', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_create', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cmd_state', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cmd_ip', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_lastname', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_firstname', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_adress', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('cmd_zip', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_town', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_country', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_telephone', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_mobile', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_email', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_articles_count', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cmd_total', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_shipping', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_packing_price', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_bill', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cmd_password', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_text', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('cmd_cancel', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_comment', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('cmd_vat_number', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('cmd_packing', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_packing_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cmd_location', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_location_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cmd_delivery', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_delivery_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cmd_payment', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_payment_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cmd_status', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cmd_track', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('cmd_gift', XOBJ_DTYPE_TXTBOX, null, false);
    }

    /**
     * Retourne les éléments du produits formatés pour affichage
     *
     * @param  string $format Le format à utiliser
     * @return array  Les informations formatées
     */
    public function toArray($format = 's')
    {
        $ret = array();
        $ret = parent::toArray($format);
        require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
        $countries                           = array();
        $countries                           = XoopsLists::getCountryList();
        $oledrion_Currency                   = Oledrion_Currency::getInstance();
        $ret['cmd_total_fordisplay']         = $oledrion_Currency->amountForDisplay($this->getVar('cmd_total')); // Montant TTC de la commande
        $ret['cmd_shipping_fordisplay']      = $oledrion_Currency->amountForDisplay($this->getVar('cmd_shipping')); // Montant TTC des frais de port
        $ret['cmd_packing_price_fordisplay'] = $oledrion_Currency->amountForDisplay($this->getVar('cmd_packing_price'));
        $ret['cmd_text_fordisplay']          = nl2br($this->getVar('cmd_text')); // Liste des réductions accordées
        if (isset($countries[$this->getVar('cmd_country')])) { // Libellé du pays de l'acheteur
            $ret['cmd_country_label'] = $countries[$this->getVar('cmd_country')];
        }
        if ($this->getVar('cmd_uid') > 0) {
            $ret['cmd_uname'] = XoopsUser::getUnameFromId($this->getVar('cmd_uid'));
        }
        $ret['cmd_create_date'] = formatTimestamp($this->getVar('cmd_create'), _MEDIUMDATESTRING);

        return $ret;
    }
}

/**
 * Class OledrionOledrion_commandsHandler
 */
class OledrionOledrion_commandsHandler extends Oledrion_XoopsPersistableObjectHandler
{
    /**
     * OledrionOledrion_commandsHandler constructor.
     * @param XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    { //                        Table                   Classe           Id
        parent::__construct($db, 'oledrion_commands', 'oledrion_commands', 'cmd_id');
    }

    /**
     * Indique si c'est la première commande d'un client
     *
     * @param  integer $uid Identifiant de l'utilisateur
     * @return boolean Indique si c'est le cas ou pas
     */
    public function isFirstCommand($uid = 0)
    {
        if ($uid == 0) {
            $uid = OledrionUtility::getCurrentUserID();
        }
        $critere = new Criteria('cmd_uid', (int)$uid, '=');
        if ($this->getCount($critere) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Indique si un produit a déajà été acheté par un utilisateur
     *
     * @param  integer $uid       Identifiant de l'utilisateur
     * @param  integer $productId Identifiant du produit
     * @return boolean Indique si c'est le cas ou pas
     */
    public function productAlreadyBought($uid = 0, $productId = 0)
    {
        if ($uid == 0) {
            $uid = OledrionUtility::getCurrentUserID();
        }
        $sql    = 'SELECT Count(*) AS cpt FROM ' . $this->db->prefix('oledrion_caddy') . ' c, ' . $this->db->prefix('oledrion_commands') . ' f WHERE c.caddy_product_id = ' . (int)$productId . ' AND c.caddy_cmd_id = f.cmd_id AND f.cmd_uid = ' . (int)$uid;
        $result = $this->db->query($sql);
        if (!$result) {
            return 0;
        }
        list($count) = $this->db->fetchRow($result);
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Mise à jour des stocks pour chaque produit composant la commande
     *
     * @param  Oledrion_commands $order La commande à traiter
     * @return void
     */
    public function updateStocks($order)
    {
        global $h_oledrion_caddy, $h_oledrion_products, $h_oledrion_persistent_cart;
        $orderId = $order->getVar('cmd_id');
        // Recherche de tous les produits du caddy
        $caddy  = $h_oledrion_caddy->getCaddyFromCommand($orderId);
        $tblTmp = $tblProducts = array();
        foreach ($caddy as $item) {
            $tblTmp[] = $item->getVar('caddy_product_id');
        }
        // Chargement de tous les produits
        $critere     = new Criteria('product_id', '(' . implode(',', $tblTmp) . ')', 'IN');
        $tblProducts = $h_oledrion_products->getObjects($critere, true);
        // Boucle sur le caddy pour mettre à jour les quantités
        foreach ($caddy as $item) {
            if (isset($tblProducts[$item->getVar('caddy_product_id')])) {
                $product = $tblProducts[$item->getVar('caddy_product_id')];
                $h_oledrion_products->decreaseStock($product, $item->getVar('caddy_qte'));
                $h_oledrion_products->verifyLowStock($product); // Vérification du stock d'alerte
                $h_oledrion_persistent_cart->deleteUserProduct($item->getVar('caddy_product_id'), $order->getVar('cmd_uid'));
            }
        }

        return true;
    }

    /**
     * Retourne la liste des URLs de téléchargement liés à une commande
     *
     * @param  Oledrion_commands $order La commande en question
     * @return array                    Les URL
     */
    public function getOrderUrls(Oledrion_commands $order)
    {
        global $h_oledrion_caddy, $h_oledrion_products;
        $retval = array();
        // Recherche des produits du caddy associés à cette commande
        $carts = $productsList = $products = array();
        $carts = $h_oledrion_caddy->getObjects(new Criteria('caddy_cmd_id', $order->getVar('cmd_id'), '='));
        foreach ($carts as $item) {
            $productsList[] = $item->getVar('caddy_product_id');
        }
        if (count($productsList) > 0) {
            $products = $h_oledrion_products->getObjects(new Criteria('product_id', '(' . implode(',', $productsList) . ')', 'IN'), true);
            if (count($products) > 0) {
                foreach ($carts as $item) {
                    $produit = null;
                    if (isset($products[$item->getVar('caddy_product_id')])) {
                        $produit = $products[$item->getVar('caddy_product_id')];
                        if (xoops_trim($produit->getVar('product_download_url')) !== '') {
                            $retval[] = OLEDRION_URL . 'download.php?download_id=' . $item->getVar('caddy_pass');
                        }
                    }
                }
            }
        }

        return $retval;
    }

    /**
     * Envoi du mail chargé de prévenir le client et le magasin qu'une commande est validée
     *
     * @param Oledrion_commands $order   La commande en question
     * @param string                   $comment Optionel, un commentaire pour le webmaster
     */
    public function notifyOrderValidated(Oledrion_commands $order, $comment = '')
    {
        global $xoopsConfig;
        $msg                       = array();
        $Urls                      = array();
        $Urls                      = $this->getOrderUrls($order); // On récupère les URL des fichiers à télécharger
        $msg['ADDITIONAL_CONTENT'] = '';
        $msg['NUM_COMMANDE']       = $order->getVar('cmd_id');
        $msg['COMMENT']            = $comment;
        if (count($Urls) > 0) {
            $msg['ADDITIONAL_CONTENT'] = _OLEDRION_YOU_CAN_DOWNLOAD . "\n" . implode("\n", $Urls);
        }
        OledrionUtility::sendEmailFromTpl('command_shop_verified.tpl', OledrionUtility::getEmailsFromGroup(OledrionUtility::getModuleOption('grp_sold')), _OLEDRION_GATEWAY_VALIDATED, $msg);
        OledrionUtility::sendEmailFromTpl('command_client_verified.tpl', $order->getVar('cmd_email'), sprintf(_OLEDRION_GATEWAY_VALIDATED, $xoopsConfig['sitename']), $msg);
    }

    /**
     * Validation d'une commande et mise à jour des stocks
     *
     * @param  Oledrion_commands $order   La commande à traiter
     * @param  string                   $comment Optionel, un commentaire pour le mail envoyé au webmaster
     * @return bool                     Indique si la validation de la commande s'est bien faite ou pas
     */
    public function validateOrder(Oledrion_commands $order, $comment = '')
    {
        $retval = false;
        $order->setVar('cmd_state', OLEDRION_STATE_VALIDATED);
        $order->setVar('cmd_comment', $comment);
        $retval = $this->insert($order, true);
        if ($retval) {
            $this->updateStocks($order);
            $this->notifyOrderValidated($order, $comment);
        }

        return $retval;
    }

    /**
     * pack d'une commande et mise à jour des stocks
     *
     * @param  Oledrion_commands $order   La commande à traiter
     * @param  string                   $comment Optionel, un commentaire pour le mail envoyé au webmaster
     * @return bool                     Indique si la validation de la commande s'est bien faite ou pas
     */
    public function packOrder(Oledrion_commands $order, $comment = '')
    {
        $retval = false;
        $order->setVar('cmd_state', OLEDRION_STATE_PACKED);
        $order->setVar('cmd_comment', $comment);
        $retval = $this->insert($order, true);

        return $retval;
    }

    /**
     * submit d'une commande et mise à jour des stocks
     *
     * @param  Oledrion_commands $order   La commande à traiter
     * @param  string                   $comment Optionel, un commentaire pour le mail envoyé au webmaster
     * @return bool                     Indique si la validation de la commande s'est bien faite ou pas
     */
    public function submitOrder(Oledrion_commands $order, $comment = '')
    {
        $retval = false;
        $order->setVar('cmd_state', OLEDRION_STATE_SUBMITED);
        $order->setVar('cmd_comment', $comment);
        $retval = $this->insert($order, true);

        return $retval;
    }

    /**
     * delivery d'une commande et mise à jour des stocks
     *
     * @param  Oledrion_commands $order   La commande à traiter
     * @param  string                   $comment Optionel, un commentaire pour le mail envoyé au webmaster
     * @return bool                     Indique si la validation de la commande s'est bien faite ou pas
     */
    public function deliveryOrder(Oledrion_commands $order, $comment = '')
    {
        $retval = false;
        $order->setVar('cmd_state', OLEDRION_STATE_DELIVERYED);
        $order->setVar('cmd_comment', $comment);
        $retval = $this->insert($order, true);

        return $retval;
    }

    /**
     * Informe le propriétaire du site qu'une commande est frauduleuse
     *
     * @param Oledrion_commands $order   La commande en question
     * @param string                   $comment Optionel, un commentaire pour le mail envoyé au webmaster
     */
    public function notifyOrderFraudulent(Oledrion_commands $order, $comment = '')
    {
        $msg                 = array();
        $msg['NUM_COMMANDE'] = $order->getVar('cmd_id');
        $msg['COMMENT']      = $comment;
        OledrionUtility::sendEmailFromTpl('command_shop_fraud.tpl', OledrionUtility::getEmailsFromGroup(OledrionUtility::getModuleOption('grp_sold')), _OLEDRION_GATEWAY_FRAUD, $msg);
    }

    /**
     * Applique le statut de commande frauduleuse à une commande
     *
     * @param obejct|Oledrion_commands $order   La commande à traiter
     * @param string                   $comment Optionel, un commentaire pour le mail envoyé au webmaster
     */
    public function setFraudulentOrder(Oledrion_commands $order, $comment = '')
    {
        $order->setVar('cmd_state', OLEDRION_STATE_FRAUD);
        $order->setVar('cmd_comment', $comment);
        $this->insert($order, true);
        $this->notifyOrderFraudulent($order, $comment);
    }

    /**
     * Informe le propriétaire du site qu'une commande est en attente
     *
     * @param Oledrion_commands $order   La commande en question
     * @param string                   $comment Optionel, un commentaire pour le mail envoyé au webmaster
     */
    public function notifyOrderPending(Oledrion_commands $order, $comment = '')
    {
        $msg                 = array();
        $msg['NUM_COMMANDE'] = $order->getVar('cmd_id');
        $msg['COMMENT']      = $comment;
        OledrionUtility::sendEmailFromTpl('command_shop_pending.tpl', OledrionUtility::getEmailsFromGroup(OledrionUtility::getModuleOption('grp_sold')), _OLEDRION_GATEWAY_PENDING, $msg);
    }

    /**
     * Applique le statut de commande en attente à une commande
     *
     * @param Oledrion_commands $order   La commande à traiter
     * @param string                   $comment Optionel, un commentaire pour le mail envoyé au webmaster
     */
    public function setOrderPending(Oledrion_commands $order, $comment = '')
    {
        $order->setVar('cmd_state', OLEDRION_STATE_PENDING); // En attente
        $order->setVar('cmd_comment', $comment);
        $this->insert($order, true);
        $this->notifyOrderPending($order, $comment);
    }

    /**
     * Informe le propriétaire du site qu'une commande à échoué (le paiement)
     *
     * @param Oledrion_commands $order   La commande en question
     * @param string                   $comment Optionel, un commentaire pour le mail envoyé au webmaster
     */
    public function notifyOrderFailed(Oledrion_commands $order, $comment = '')
    {
        $msg                 = array();
        $msg['NUM_COMMANDE'] = $order->getVar('cmd_id');
        $msg['COMMENT']      = $comment;
        OledrionUtility::sendEmailFromTpl('command_shop_failed.tpl', OledrionUtility::getEmailsFromGroup(OledrionUtility::getModuleOption('grp_sold')), _OLEDRION_GATEWAY_FAILED, $msg);
    }

    /**
     * Applique le statut de commande échouée à une commande
     *
     * @param Oledrion_commands $order   La commande à traiter
     * @param string                   $comment Optionel, un commentaire pour le mail envoyé au webmaster
     */
    public function setOrderFailed(Oledrion_commands $order, $comment = '')
    {
        $order->setVar('cmd_state', OLEDRION_STATE_FAILED); // Echec
        $order->setVar('cmd_comment', $comment);
        $this->insert($order, true);
        $this->notifyOrderFailed($order, $comment);
    }

    /**
     * Informe le propriétaire du site qu'une commande à échoué (le paiement)
     *
     * @param Oledrion_commands $order   La commande en question
     * @param string                   $comment Optionel, un commentaire pour le mail envoyé au webmaster
     */
    public function notifyOrderCanceled(Oledrion_commands $order, $comment = '')
    {
        $msg                 = array();
        $msg['NUM_COMMANDE'] = $order->getVar('cmd_id');
        $msg['COMMENT']      = $comment;
        OledrionUtility::sendEmailFromTpl('command_shop_cancel.tpl', OledrionUtility::getEmailsFromGroup(OledrionUtility::getModuleOption('grp_sold')), _OLEDRION_ORDER_CANCELED, $msg);
        OledrionUtility::sendEmailFromTpl('command_client_cancel.tpl', $order->getVar('cmd_email'), _OLEDRION_ORDER_CANCELED, $msg);
    }

    /**
     * Applique le statut de commande annulée à une commande
     *
     * @param Oledrion_commands $order   La commande à traiter
     * @param string                   $comment Optionel, un commentaire pour le mail envoyé au webmaster
     */
    public function setOrderCanceled(Oledrion_commands $order, $comment = '')
    {
        $order->setVar('cmd_state', OLEDRION_STATE_CANCELED); // Annulée
        $order->setVar('cmd_comment', $comment);
        $this->insert($order, true);
        $this->notifyOrderCanceled($order, $comment);
    }

    /**
     * Retourne une commande à partir de son mot de passe d'annulation
     *
     * @param  string $cmd_cancel Le mot de passe d'annulation
     * @return mixed  Soit un objet soit null
     */
    public function getOrderFromCancelPassword($cmd_cancel)
    {
        $critere = new Criteria('cmd_cancel', $cmd_cancel, '=');
        if ($this->getCount($critere) > 0) {
            $tblCmd = array();
            $tblCmd = $this->getObjects($critere);
            if (count($tblCmd) > 0) {
                return $tblCmd[0];
            }
        }

        return null;
    }

    /**
     * Retourne la dernière commande d'un utilisateur (si elle existe)
     *
     * @param  integer $uid Identifiant de la commande
     * @return null
     */
    public function getLastUserOrder($uid)
    {
        $order    = null;
        $orders   = array();
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('cmd_uid', $uid));
        $criteria->add(new Criteria('cmd_status', 2));
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
     * Supprime une commande et tout ce qui s'y rattache
     *
     * @param  Oledrion_commands $order
     * @return boolean
     */
    public function removeOrder(Oledrion_commands $order)
    {
        $handlers = OledrionHandler::getInstance();
        $cmd_id   = $order->getVar('cmd_id');
        $res      = $this->delete($order);
        // Suppression des objets associés
        // 1) Ses propres caddies
        $handlers->h_oledrion_caddy->removeCartsFromOrderId($cmd_id);
        // 2) Les caddies des attributs
        $handlers->h_oledrion_caddy_attributes->removeCartsFromOrderId($cmd_id);

        return $res;
    }
}
