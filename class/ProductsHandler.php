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
 * Gestion des produits mis en vente
 */

use XoopsModules\Oledrion;

require_once __DIR__ . '/classheader.php';


/**
 * Class ProductsHandler
 */
class ProductsHandler extends OledrionPersistableObjectHandler
{
    /**
     * ProductsHandler constructor.
     * @param \XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db)
    { //                            Table               Classe              Id          Libellé
        parent::__construct($db, 'oledrion_products', Products::class, 'product_id', 'product_title');
    }

    /**
     * Retourne la liste des x produits les plus vus par les visiteurs
     *
     * @param  Parameters $parameters
     * @return array               Tableau de produits (sous la forme d'objets)
     * @internal param int $start Début des données
     * @internal param int $limit Nombre maximum d'enregistrements à renvoyer
     * @internal param int $category Identifiant de la catégorie (évenutellement)
     * @internal param string $sort Champ sur lequel trier
     * @internal param string $order Sens du tri
     */
    public function getMostViewedProducts(Parameters $parameters)
    {
        $parameters = $parameters->extend(new Oledrion\Parameters([
                                                                      'start'    => 0,
                                                                      'limit'    => 0,
                                                                      'category' => 0,
                                                                      'sort'     => 'product_hits',
                                                                      'order'    => 'DESC'
                                                                  ]));
        $data       = [];
        $criteria   = new \CriteriaCompo();
        $criteria->add(new \Criteria('product_online', 1, '='));
        if (0 == Oledrion\Utility::getModuleOption('show_unpublished')) { // Ne pas afficher les produits qui ne sont pas publiés
            $criteria->add(new \Criteria('product_submitted', time(), '<='));
        }
        if (0 == Oledrion\Utility::getModuleOption('nostock_display')) { // Se limiter aux seuls produits encore en stock
            $criteria->add(new \Criteria('product_stock', 0, '>'));
        }
        if (is_array($parameters['category']) && count($parameters['category']) > 0) {
            $criteria->add(new \Criteria('product_cid', '(' . implode(',', $parameters['category']) . ')', 'IN'));
        } elseif (0 != $parameters['category']) {
            $criteria->add(new \Criteria('product_cid', (int)$parameters['category'], '='));
        }
        $criteria->add(new \Criteria('product_hits', 0, '>'));

        $criteria->setLimit($parameters['limit']);
        $criteria->setStart($parameters['start']);
        $criteria->setSort($parameters['sort']);
        $criteria->setOrder($parameters['order']);
        $data = $this->getObjects($criteria, true);

        return $data;
    }

    /**
     * Retourne la liste des x produits les mieux notés par les visiteurs
     *
     * @param  Parameters $parameters
     * @return array               Tableau de produits (sous la forme d'objets)
     * @internal param int $start Début des données
     * @internal param int $limit Nombre maximum d'enregistrements à renvoyer
     * @internal param int $category Identifiant de la catégorie (évenutellement)
     */
    public function getBestRatedProducts(Parameters $parameters)
    {
        $parameters = $parameters->extend(new Oledrion\Parameters([
                                                                      'start'    => 0,
                                                                      'limit'    => 0,
                                                                      'category' => 0,
                                                                      'sort'     => 'product_rating',
                                                                      'order'    => 'DESC'
                                                                  ]));
        $data       = [];
        $criteria   = new \CriteriaCompo();
        $criteria->add(new \Criteria('product_online', 1, '='));
        $criteria->add(new \Criteria('product_rating', 0, '>')); // Se limiter aux seuls produits qui ont été vraiment notés
        if (0 == Oledrion\Utility::getModuleOption('show_unpublished')) { // Ne pas afficher les produits qui ne sont pas publiés
            $criteria->add(new \Criteria('product_submitted', time(), '<='));
        }
        if (0 == Oledrion\Utility::getModuleOption('nostock_display')) { // Se limiter aux seuls produits encore en stock
            $criteria->add(new \Criteria('product_stock', 0, '>'));
        }
        if (is_array($parameters['category']) && count($parameters['category']) > 0) {
            $criteria->add(new \Criteria('product_cid', '(' . implode(',', $parameters['category']) . ')', 'IN'));
        } elseif (0 != $parameters['category']) {
            $criteria->add(new \Criteria('product_cid', (int)$parameters['category'], '='));
        }
        $criteria->setLimit($parameters['limit']);
        $criteria->setStart($parameters['start']);
        $criteria->setSort($parameters['sort']);
        $criteria->setOrder($parameters['order']);
        $data = $this->getObjects($criteria, true);

        return $data;
    }

    /**
     * Retourne la liste des x derniers produits recommandés
     *
     * @param  Parameters $parameters
     * @return array               Tableau de produits (sous la forme d'objets)
     * @internal param int $start Indice de départ
     * @internal param int $limit Nombre maximum d'enregistrements à renvoyer
     * @internal param int $category Identifiant de la catégorie (évenutellement)
     */
    public function getRecentRecommended(Parameters $parameters)
    {
        $parameters = $parameters->extend(new Oledrion\Parameters([
                                                                      'start'    => 0,
                                                                      'limit'    => 0,
                                                                      'category' => 0,
                                                                      'sort'     => 'product_recommended',
                                                                      'order'    => 'DESC'
                                                                  ]));
        $data       = [];
        $criteria   = new \CriteriaCompo();
        $criteria->add(new \Criteria('product_online', 1, '='));
        $criteria->add(new \Criteria('product_recommended', '0000-00-00', '<>'));
        if (0 == Oledrion\Utility::getModuleOption('show_unpublished')) { // Ne pas afficher les produits qui ne sont pas publiés
            $criteria->add(new \Criteria('product_submitted', time(), '<='));
        }
        if (0 == Oledrion\Utility::getModuleOption('nostock_display')) { // Se limiter aux seuls produits encore en stock
            $criteria->add(new \Criteria('product_stock', 0, '>'));
        }
        if (is_array($parameters['category'])) {
            $criteria->add(new \Criteria('product_cid', '(' . implode(',', $parameters['category']) . ')', 'IN'));
        } elseif (0 != $parameters['category']) {
            $criteria->add(new \Criteria('product_cid', (int)$parameters['category'], '='));
        }
        $criteria->setLimit($parameters['limit']);
        $criteria->setStart($parameters['start']);
        $criteria->setSort($parameters['sort']);
        $criteria->setOrder($parameters['order']);
        $data = $this->getObjects($criteria, true);

        return $data;
    }

    /**
     * Retourne le nombre total de produits recommandés
     *
     * @return integer Le nombre total de produits recommandés
     */
    public function getRecommendedCount()
    {
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('product_online', 1, '='));
        $criteria->add(new \Criteria('product_recommended', '0000-00-00', '<>'));
        if (0 == Oledrion\Utility::getModuleOption('show_unpublished')) { // Ne pas afficher les produits qui ne sont pas publiés
            $criteria->add(new \Criteria('product_submitted', time(), '<='));
        }
        if (0 == Oledrion\Utility::getModuleOption('nostock_display')) { // Se limiter aux seuls produits encore en stock
            $criteria->add(new \Criteria('product_stock', 0, '>'));
        }

        return $this->getCount($criteria);
    }

    /**
     * Retourne la liste des x derniers produits parus toutes catégories confondues ou dans une catégorie spécifique
     *
     * @param  Parameters $parameters
     * @return array               Tableau de produits (sous la forme d'objets)
     * @internal param int $start Début des données
     * @internal param int $limit Nombre maximum d'enregistrements à renvoyer
     * @internal param mixed $category Identifiant de la catégorie (évenutellement) ou tableau d'ID ou rien du tout
     * @internal param string $sort Champ(s) à utiliser pour le tri
     * @internal param string $order Ordre de tri
     * @internal param int $excluded Produit à exclure de la liste (éventuellement)
     * @internal param bool $thisMonthOnly Indique s'il ne faut prendre que les produits du mois
     */
    public function getRecentProducts(Parameters $parameters)
    {
        $parameters = $parameters->extend(new Oledrion\Parameters([
                                                                      'start'         => 0,
                                                                      'limit'         => 0,
                                                                      'category'      => 0,
                                                                      'sort'          => 'product_submitted DESC, product_title',
                                                                      'order'         => '',
                                                                      'excluded'      => 0,
                                                                      'thisMonthOnly' => false
                                                                  ]));
        $data       = [];
        $criteria   = new \CriteriaCompo();
        $criteria->add(new \Criteria('product_online', 1, '='));
        if (0 == Oledrion\Utility::getModuleOption('show_unpublished')) { // Ne pas afficher les produits qui ne sont pas publiés
            $criteria->add(new \Criteria('product_submitted', time(), '<='));
        }
        if (0 == Oledrion\Utility::getModuleOption('nostock_display')) { // Se limiter aux seuls produits encore en stock
            $criteria->add(new \Criteria('product_stock', 0, '>'));
        }
        if (is_array($parameters['category']) && count($parameters['category']) > 0) {
            $criteria->add(new \Criteria('product_cid', '(' . implode(',', $parameters['category']) . ')', 'IN'));
        } elseif ($parameters['category'] > 0) {
            $criteria->add(new \Criteria('product_cid', (int)$parameters['category'], '='));
        }
        if ($parameters['excluded'] > 0) {
            $criteria->add(new \Criteria('product_id', $parameters['excluded'], '<>'));
        }

        if ($parameters['thisMonthOnly']) {
            $criteria->add(Oledrion\Utility::getThisMonthCriteria());
        }

        $criteria->setLimit($parameters['limit']);
        $criteria->setStart($parameters['start']);
        $criteria->setSort($parameters['sort']);
        if ('' !== xoops_trim($parameters['order'])) {
            $criteria->setOrder($parameters['order']);
        }
        $data = $this->getObjects($criteria, true);

        return $data;
    }

    /**
     * Retourne le nombre total de produits récents (éventuellement dans une catégorie)
     *
     * @param  mixed   $category        Array ou Integer
     * @param  integer $excludedProduct ID d'un produit à exclure
     * @return integer
     */
    public function getRecentProductsCount($category = 0, $excludedProduct = 0)
    {
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('product_online', 1, '='));
        if (0 == Oledrion\Utility::getModuleOption('show_unpublished')) { // Ne pas afficher les produits qui ne sont pas publiés
            $criteria->add(new \Criteria('product_submitted', time(), '<='));
        }
        if (0 == Oledrion\Utility::getModuleOption('nostock_display')) { // Se limiter aux seuls produits encore en stock
            $criteria->add(new \Criteria('product_stock', 0, '>'));
        }
        if (is_array($category)) {
            $criteria->add(new \Criteria('product_cid', '(' . implode(',', $category) . ')', 'IN'));
        } elseif ($category > 0) {
            $criteria->add(new \Criteria('product_cid', (int)$category, '='));
        }
        if ($excludedProduct > 0) {
            $criteria->add(new \Criteria('product_id', $excludedProduct, '<>'));
        }

        return $this->getCount($criteria);
    }

    /**
     * Retourne la liste des produits qui correspondent à des "critères" de manière à les utiliser pour la newsletter
     *
     * @param  Parameters $parameters
     * @return array               Des objects de type produits
     * @internal param int $startingDate Date de soumission du produit à prendre comme borne inférieure
     * @internal param int $endingDate Date de soumission du produit à prendre comme borne supérieure
     * @internal param mixed $category Soit un tableau d'ID de catégories soit un ID unique de catégorie
     * @internal param int $start Position de départ
     * @internal param int $limit Nombre d'enregistrements à retourner
     */
    public function getProductsForNewsletter(Parameters $parameters)
    {
        $parameters = $parameters->extend(new Oledrion\Parameters([
                                                                      'startingDate' => 0,
                                                                      'endingDate'   => 0,
                                                                      'category'     => 0,
                                                                      'start'        => 0,
                                                                      'limit'        => 0
                                                                  ]));
        $data       = [];
        $criteria   = new \CriteriaCompo();
        $criteria->add(new \Criteria('product_online', 1, '='));
        $criteria->add(new \Criteria('product_submitted', $parameters['startingDate'], '>='));
        $criteria->add(new \Criteria('product_submitted', $parameters['endingDate'], '<='));
        if (0 == Oledrion\Utility::getModuleOption('show_unpublished')) { // Ne pas afficher les produits qui ne sont pas publiés
            $criteria->add(new \Criteria('product_submitted', time(), '<='));
        }
        if (0 == Oledrion\Utility::getModuleOption('nostock_display')) { // Se limiter aux seuls produits encore en stock
            $criteria->add(new \Criteria('product_stock', 0, '>'));
        }
        if (is_array($parameters['category'])) {
            $criteria->add(new \Criteria('product_cid', '(' . implode(',', $parameters['category']) . ')', 'IN'));
        } elseif ($parameters['category'] > 0) {
            $criteria->add(new \Criteria('product_cid', (int)$parameters['category'], '='));
        }
        $criteria->setLimit($parameters['limit']);
        $criteria->setStart($parameters['start']);
        $criteria->setSort('product_title');
        $data = $this->getObjects($criteria, true);

        return $data;
    }

    /**
     * Retourne le nombre total de produits publiés dans la base en tenant compte des préférences du module
     *
     * @param  int|integer $product_cid Catégorie du produit
     * @return int         Le nombre de produits publiés
     */
    public function getTotalPublishedProductsCount($product_cid = 0)
    {
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('product_online', 1, '='));
        if (0 == Oledrion\Utility::getModuleOption('show_unpublished')) { // Ne pas afficher les produits qui ne sont pas publiés
            $criteria->add(new \Criteria('product_submitted', time(), '<='));
        }
        if (0 == Oledrion\Utility::getModuleOption('nostock_display')) { // Se limiter aux seuls produits encore en stock
            $criteria->add(new \Criteria('product_stock', 0, '>'));
        }
        if ($product_cid > 0) {
            $criteria->add(new \Criteria('product_cid', (int)$product_cid, '='));
        }

        return $this->getCount($criteria);
    }

    /**
     * Récupération de l'ID et du titre d'une série de produits répondants à un critère
     *
     * @param  \CriteriaElement $criteria critère de sélection
     * @return array  Tableau dont la clé = ID produit et la valeur le titre du produit
     */
    public function getIdTitle($criteria)
    {
        global $myts;
        $ret = [];
        $sql = 'SELECT product_id, product_title FROM ' . $this->table;
        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ('' !== $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $ret[$myrow['product_id']] = $myts->htmlSpecialChars($myrow['product_title']);
        }

        return $ret;
    }

    /**
     * Mise à jour du compteur de lectures du produit
     *
     * @param  integer $product_id L'identifiant du produit dont il faut mettre à jour le compteur de lectures
     * @return boolean Le résultat de la mise à jour
     */
    public function addCounter($product_id)
    {
        $sql = 'UPDATE ' . $this->table . ' SET product_hits = product_hits + 1 WHERE product_id= ' . (int)$product_id;

        // Note, pas de mise à jour du cache !
        return $this->db->queryF($sql);
    }

    /**
     * Mise à jour de la notation d'un produit
     *
     * @param  integer $product_id Identifiant du produit
     * @param  float   $rating     la notation
     * @param  integer $votes      Le nombre de votes du produit
     * @return boolean Le résultat de la mise à jour
     */
    public function updateRating($product_id, $rating, $votes)
    {
        $sql = 'UPDATE ' . $this->table . ' SET product_rating = ' . (int)$rating . ', product_votes = ' . (int)$votes . ' WHERE product_id = ' . (int)$product_id;

        return $this->db->queryF($sql);
    }

    /**
     * Mise à jour du nombre de commentaires d'un produit
     *
     * @param integer $product_id    Identifiant du produit
     * @param integer $commentsCount Nombre total de commentaires
     */
    public function updateCommentsCount($product_id, $commentsCount)
    {
        $product = null;
        $product = $this->get($product_id);
        if (is_object($product)) {
            $criteria = new \Criteria('product_id', $product_id, '=');
            $this->updateAll('product_comments', $commentsCount, $criteria, true);
        }
    }

    /**
     * Retourne x produits au hasard
     *
     * @param  Parameters $parameters
     * @return array               Tableau de produits (sous la forme d'objets)
     * @internal param int $start Début des données
     * @internal param int $limit Nombre maximum d'enregistrements à renvoyer
     * @internal param int $category Identifiant de la catégorie (évenutellement)
     * @internal param string $sort Zone sur laquelle faire le tri
     * @internal param string $order Ordre de tri
     * @internal param bool $thisMonthOnly Uniquement les produits du mois en cours ?
     */
    public function getRandomProducts(Parameters $parameters)
    {
        $parameters = $parameters->extend(new Oledrion\Parameters([
                                                                      'start'         => 0,
                                                                      'limit'         => 0,
                                                                      'category'      => 0,
                                                                      'sort'          => 'RAND()',
                                                                      'order'         => 'ASC',
                                                                      'thisMonthOnly' => false
                                                                  ]));
        $data       = [];
        $criteria   = new \CriteriaCompo();
        $criteria->add(new \Criteria('product_online', 1, '='));
        if (0 == Oledrion\Utility::getModuleOption('show_unpublished')) { // Ne pas afficher les produits qui ne sont pas publiés
            $criteria->add(new \Criteria('product_submitted', time(), '<='));
        }
        if (0 == Oledrion\Utility::getModuleOption('nostock_display')) { // Se limiter aux seuls produits encore en stock
            $criteria->add(new \Criteria('product_stock', 0, '>'));
        }
        if (is_array($parameters['category'])) {
            $criteria->add(new \Criteria('product_cid', '(' . implode(',', $parameters['category']) . ')', 'IN'));
        } elseif (0 != $parameters['category']) {
            $criteria->add(new \Criteria('product_cid', (int)$parameters['category'], '='));
        }

        if ($parameters['thisMonthOnly']) {
            $criteria->add(Oledrion\Utility::getThisMonthCriteria());
        }

        $criteria->setLimit($parameters['limit']);
        $criteria->setStart($parameters['start']);
        $criteria->setSort($parameters['sort']);
        $criteria->setOrder($parameters['order']);
        $data = $this->getObjects($criteria, true);

        return $data;
    }

    /**
     * Retourne x produits en promo
     *
     * @param  Parameters $parameters
     * @return array               Tableau de produits (sous la forme d'objets)
     * @internal param int $start Début des données
     * @internal param int $limit Nombre maximum d'enregistrements à renvoyer
     * @internal param int $category Identifiant de la catégorie (évenutellement)
     */
    public function getPromotionalProducts(Parameters $parameters)
    {
        $parameters = $parameters->extend(new Oledrion\Parameters([
                                                                      'start'    => 0,
                                                                      'limit'    => 0,
                                                                      'category' => 0,
                                                                      'sort'     => 'product_title',
                                                                      'order'    => 'DESC'
                                                                  ]));
        $data       = [];
        $criteria   = new \CriteriaCompo();
        $criteria->add(new \Criteria('product_online', 1, '='));
        if (0 == Oledrion\Utility::getModuleOption('show_unpublished')) { // Ne pas afficher les produits qui ne sont pas publiés
            $criteria->add(new \Criteria('product_submitted', time(), '<='));
        }
        if (0 == Oledrion\Utility::getModuleOption('nostock_display')) { // Se limiter aux seuls produits encore en stock
            $criteria->add(new \Criteria('product_stock', 0, '>'));
        }
        if (is_array($parameters['category'])) {
            $criteria->add(new \Criteria('product_cid', '(' . implode(',', $parameters['category']) . ')', 'IN'));
        } elseif (0 != $parameters['category']) {
            $criteria->add(new \Criteria('product_cid', (int)$parameters['category'], '='));
        }
        $criteria->add(new \Criteria('product_discount_price', 0, '>'));
        $criteria->setLimit($parameters['limit']);
        $criteria->setStart($parameters['start']);
        $criteria->setSort($parameters['sort']);
        $criteria->setOrder($parameters['order']);
        $data = $this->getObjects($criteria, true);

        return $data;
    }

    /**
     * Retourne les produits dont les stocks sont bas
     *
     * @param  integer $start Début des données
     * @param  integer $limit Nombre maximum d'enregistrements à renvoyer
     * @return array   Tableau de produits (sous la forme d'objets)
     */
    public function getLowStocks($start = 0, $limit = 0)
    {
        $ret = [];
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE product_online = 1';
        if (0 == Oledrion\Utility::getModuleOption('show_unpublished')) { // Ne pas afficher les produits qui ne sont pas publiés
            $sql .= ' AND product_submitted <= ' . time();
        }
        $sql    .= ' AND product_stock <= product_alert_stock ';
        $sql    .= ' AND product_alert_stock > 0';
        $sql    .= ' ORDER BY product_stock';
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }

        $ret = $this->convertResultSet($result, true, true);

        return $ret;
    }

    /**
     * Retourne le nombre de produits dont la quantité en stock est inférieure ou égale à la quantité d'alerte
     *
     * @return integer Le nombre de produits concernés
     */
    public function getLowStocksCount()
    {
        $ret = [];
        $sql = 'SELECT Count(*) AS cpt FROM ' . $this->table . ' WHERE product_online = 1';
        if (0 == Oledrion\Utility::getModuleOption('show_unpublished')) { // Ne pas afficher les produits qui ne sont pas publiés
            $sql .= ' AND product_submitted <= ' . time();
        }
        $sql    .= ' AND product_stock <= product_alert_stock ';
        $sql    .= ' AND product_alert_stock > 0';
        $result = $this->db->query($sql);
        if (!$result) {
            return $ret;
        }
        $count = 0;
        list($count) = $this->db->fetchRow($result);

        return $count;
    }

    /**
     * Augmente les quantités en stock d'un produit
     *
     * @param  Products $product  Objet produit
     * @param  int               $quantity $quantity Quantité à rajouter
     * @return bool
     */
    public function increaseStock($product, $quantity = 1)
    {
        $product->setVar('product_stock', $product->getVar('product_stock') + $quantity);
        $this->insert($product, true);

        return true;
    }

    /**
     * Diminue les quantités en stock d'un produit
     *
     * @param  Products $product  Objet produit
     * @param  int               $quantity $quantity Quantité à soustraire
     * @return bool
     */
    public function decreaseStock(&$product, $quantity = 1)
    {
        if ($product->getVar('product_stock') - $quantity > 0) {
            $product->setVar('product_stock', $product->getVar('product_stock') - $quantity);
            $this->insert($product, true);
        } else {
            $product->setVar('product_stock', 0);
        }

        return true;
    }

    /**
     * Indique si la quantité d'alerte d'un produit est atteinte
     *
     * @param $product
     * @return bool Vrai si la quantité d'alerte est atteinte, sinon faux
     * @internal param object $products L'objet produit concerné
     */
    public function isAlertStockReached(&$product)
    {
        if ($product->getVar('product_stock') < $product->getVar('product_alert_stock')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Méthode chargée de vérifier si le stock d'alerte est atteint et si c'est le cas, d'envoyer une alerte
     *
     * @param  Products $product Produit dont il faut faire la vérification
     * @return boolean vrai si l'alerte à du être générée sinon faux
     */
    public function verifyLowStock(&$product)
    {
        if ($this->isAlertStockReached($product)) {
            $msg                    = [];
            $msg['PRODUCT_NAME']    = $product->getVar('product_title');
            $msg['ACTUAL_QUANTITY'] = $product->getVar('product_stock');
            $msg['ALERT_QUANTITY']  = $product->getVar('product_alert_stock');
            $msg['PUBLIC_URL']      = $product->getLink();
            $msg['ADMIN_URL']       = OLEDRION_URL . 'admin/index.php?op=editproduct&id=' . $product->getVar('product_id');
            Oledrion\Utility::sendEmailFromTpl('shop_lowstock.tpl', Oledrion\Utility::getEmailsFromGroup(Oledrion\Utility::getModuleOption('stock_alert_email')), _OLEDRION_STOCK_ALERT, $msg);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Retourne la plus petite date de création d'un produit ainsi que la "plus grande" date de création d'un produit
     *
     * @param  integer $minDate Date mini (parmètre de sortie)
     * @param  integer $maxDate Date maxi (paramètre de sortie)
     * @return boolean Vrai si on a pu récupérer ces valeurs, faux sinon
     */
    public function getMinMaxPublishedDate(&$minDate, &$maxDate)
    {
        $sql    = 'SELECT Min(product_submitted) AS minDate, Max(product_submitted) AS maxDate FROM ' . $this->table . ' WHERE product_online = 1 ';
        $result = $this->db->query($sql);
        if (!$result) {
            return false;
        }
        $myrow   = $this->db->fetchArray($result);
        $minDate = $myrow['minDate'];
        $maxDate = $myrow['maxDate'];

        return true;
    }

    /**
     * Retourne des produits en fonction de leur IDs tout en tenant compte du fait qu'ils sont en ligne et payés !
     *
     * @param  array   $ids     Les identifiants des produits
     * @param  boolean $showAll Afficher les produits même s'ils ne sont plus en stock ?
     * @return array   Tableau d'objets de type Products, Clé = Id Produit
     */
    public function getProductsFromIDs($ids, $showAll = false)
    {
        $ret = [];
        if (is_array($ids)) {
            $criteria = new \CriteriaCompo();
            if (0 == Oledrion\Utility::getModuleOption('show_unpublished')) { // Ne pas afficher les produits qui ne sont pas publiés
                $criteria->add(new \Criteria('product_submitted', time(), '<='));
            }
            if (0 == Oledrion\Utility::getModuleOption('nostock_display')
                && !$showAll) { // Se limiter aux seuls produits encore en stock
                $criteria->add(new \Criteria('product_stock', 0, '>'));
            }
            $criteria->add(new \Criteria('product_id', '(' . implode(',', $ids) . ')', 'IN'));
            $ret = $this->getObjects($criteria, true, true, '*', false);
        }

        return $ret;
    }

    /**
     * Retourne le nombre de produits d'une ou de plusieurs catégories
     *
     * @param  mixed $cat_cid Soit un ID de catégorie unique soit un tableau d'ID de catégories
     * @return integer Le nombre de produits associés à cette catégorie
     */
    public function getCategoryProductsCount($cat_cid)
    {
        if (is_array($cat_cid)) {
            $lst_ids  = implode(',', $cat_cid);
            $criteria = new \Criteria('product_cid', '(' . $lst_ids . ')', 'IN');
        } else {
            $criteria = new \Criteria('product_cid', $cat_cid, '=');
        }

        return $this->getCount($criteria);
    }

    /**
     * Retourne le nombre de produits associés à un vendeur
     *
     * @param  integer $product_vendor_id L'ID du vendeur
     * @return integer Le nombre de produits
     */
    public function getVendorProductsCount($product_vendor_id)
    {
        $criteria = new \Criteria('product_vendor_id', $product_vendor_id, '=');

        return $this->getCount($criteria);
    }

    /**
     * Retourne le nombre de produits associés à une TVA
     *
     * @param  integer $product_vat_id L'identifiant de la TVA
     * @return integer Le nombre de produits
     */
    public function getVatProductsCount($product_vat_id)
    {
        $criteria = new \Criteria('product_vat_id', $product_vat_id, '=');

        return $this->getCount($criteria);
    }

    /**
     * Clone d'un produit
     *
     * @param  Products $originalProduct Le produit à cloner
     * @return mixed                    Soit l'objet représentant le nouveau produit soit false
     */
    public function cloneProduct(Products $originalProduct)
    {
        global $productsmanuHandler, $filesHandler, $productsmanuHandler, $relatedHandler, $oledrionHandlers;
        $newProduct = $originalProduct->xoopsClone();
        if (OLEDRION_DUPLICATED_PLACE === 'right') {
            $newProduct->setVar('product_title', $originalProduct->getVar('product_title') . ' ' . _AM_OLEDRION_DUPLICATED);
        } else {
            $newProduct->setVar('product_title', _AM_OLEDRION_DUPLICATED . ' ' . $originalProduct->getVar('product_title'));
        }
        $newProduct->setVar('product_id', 0);
        $newProduct->setNew();

        // Copie des 2 images
        if ('' !== xoops_trim($originalProduct->getVar('product_image_url'))) {
            $resCopy = Oledrion\Utility::duplicateFile(OLEDRION_PICTURES_PATH, $originalProduct->getVar('product_image_url'));
            if (false !== $resCopy) {
                $newProduct->setVar('product_image_url', $resCopy);
            }
        }
        if ('' !== xoops_trim($originalProduct->getVar('product_thumb_url'))) {
            $resCopy = Oledrion\Utility::duplicateFile(OLEDRION_PICTURES_PATH, $originalProduct->getVar('product_thumb_url'));
            if (false !== $resCopy) {
                $newProduct->setVar('product_thumb_url', $resCopy);
            }
        }

        // Copie du fichier attaché
        if ('' !== xoops_trim($originalProduct->getVar('product_attachment'))) {
            $resCopy = Oledrion\Utility::duplicateFile(OLEDRION_ATTACHED_FILES_PATH, $originalProduct->getVar('product_attachment'));
            if (false !== $resCopy) {
                $newProduct->setVar('product_attachment', $resCopy);
            }
        }

        $res = $this->insert($newProduct, true);
        if ($res) {
            $newProductId = $newProduct->getVar('product_id');
            // Copie des fichiers liés
            if ($filesHandler->getProductFilesCount($originalProduct->product_id) > 0) {
                $attachedFiles = [];
                $attachedFiles = $filesHandler->getProductFiles($originalProduct->product_id);
                if (count($attachedFiles) > 0) {
                    foreach ($attachedFiles as $oneFile) {
                        $newAttachedFile = $oneFile->xoopsClone();
                        $newAttachedFile->setVar('file_product_id', $newProductId);
                        $resCopy = Oledrion\Utility::duplicateFile(OLEDRION_ATTACHED_FILES_PATH, $oneFile->getVar('file_filename'));
                        if (false !== $resCopy) {
                            $newAttachedFile->setVar('file_filename', $resCopy);
                        }
                        $newAttachedFile->setNew();
                        $filesHandler->insert($newAttachedFile, true);
                    }
                }
            }

            // Copie des fabricants
            $tblTmp   = [];
            $criteria = new \Criteria('pm_product_id', $originalProduct->getVar('product_id'), '=');
            $tblTmp   = $productsmanuHandler->getObjects($criteria);
            foreach ($tblTmp as $productAuthor) {
                $newProductAuthor = $productAuthor->xoopsClone();
                $newProductAuthor->setVar('pm_product_id', $newProductId);
                $newProductAuthor->setVar('pm_id', 0);
                $newProductAuthor->setNew();
                $productsmanuHandler->insert($newProductAuthor, true);
            }

            // Copie des produits relatifs
            $tblTmp   = [];
            $criteria = new \Criteria('related_product_id', $originalProduct->getVar('product_id'), '=');
            $tblTmp   = $relatedHandler->getObjects($criteria);
            foreach ($tblTmp as $related) {
                $newRelated = $related->xoopsClone();
                $newRelated->setVar('related_product_id', $newProductId);
                $newRelated->setVar('related_id', 0);
                $newRelated->setNew();
                $relatedHandler->insert($newRelated, true);
            }

            // Copie des attributs
            if ($attributesHandler->getProductAttributesCount($originalProduct->product_id) > 0) {
                $criteria = new \Criteria('attribute_product_id', $originalProduct->product_id, '=');
                $tblTmp   = $attributesHandler->getObjects($criteria);
                foreach ($tblTmp as $attribute) {
                    $newAttribute = $attribute->xoopsClone();
                    $newAttribute->setVar('attribute_product_id', $newProductId);
                    $newAttribute->setVar('attribute_id', 0);
                    $newAttribute->setNew();
                    $attributesHandler->insert($newAttribute, true);
                }
            }

            return $newProduct;
        } else {
            return false;
        }
    }

    /**
     * Construit un sélecteur de produit(s) en fonction des paramètres et en tenant compte du nombre total de produits dans la base
     *
     * @todo     : Remplacer les paramètres par un objet paramètre et/ou un tableau
     * @param  Parameters $parameters
     * @return \XoopsFormElementTray|\XoopsFormSelect Retourne soit un objet de type tray <a href='psi_element://XoopsFormElementTray'>XoopsFormElementTray</a> soit un select <a href='psi_element://XoopsFormSelect'>XoopsFormSelect</a>
     * @internal param string $caption Le titre du sélecteur
     * @internal param string $name Le nom du champ qui receuille les produits
     * @internal param mixed $value La valeur sélectionnées
     * @internal param int $size Le nombre d'éléments visibles dans le sélecteur
     * @internal param bool $multiple Indique si c'est un sélecteur multiple ou pas
     * @internal param array $values Les valeurs sélectionnées ou les valeurs qui font le sélecteur
     * @internal param bool $showAll Indique s'il faut voir tous les produits ou pas (pas publiés et en stock)
     * @internal param string $sort Zone de tri
     * @internal param string $order Ordre de tri
     * @internal param string $formName Nom du formulaire
     * @internal param string $description Description à rajouter à la zone
     * @internal param mixed $withNull Option à rajouter en premier
     */
    public function productSelector(Parameters $parameters)
    {
        $parameters = $parameters->extend(new Oledrion\Parameters([
                                                                      'caption'     => '',
                                                                      'name'        => '',
                                                                      'value'       => null,
                                                                      'size'        => 1,
                                                                      'multiple'    => false,
                                                                      'values'      => null,
                                                                      'showAll'     => true,
                                                                      'sort'        => 'product_title',
                                                                      'order'       => 'ASC',
                                                                      'formName'    => '',
                                                                      'description' => '',
                                                                      'withNull'    => null
                                                                  ]));
        static $jqueryIncluded = null;
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('product_id', 0, '<>'));
        if (!$parameters['showAll']) {
            if (0 == Oledrion\Utility::getModuleOption('show_unpublished')) { // Ne pas afficher les produits qui ne sont pas publiés
                $criteria->add(new \Criteria('product_submitted', time(), '<='));
            }
            if (0 == Oledrion\Utility::getModuleOption('nostock_display')) { // Se limiter aux seuls produits encore en stock
                $criteria->add(new \Criteria('product_stock', 0, '>'));
            }
        }
        $criteria->setSort($parameters['sort']);
        $criteria->setOrder($parameters['order']);
        $itemsCount = $this->getCount($criteria);
        if ($itemsCount > Oledrion\Utility::getModuleOption('max_products')) { // Il faut créer notre propre sélecteur
            if ($parameters['multiple']) {
                if (null === $jqueryIncluded) {
                    $jqueryIncluded = true;
                    global $xoTheme;
                    $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
                }
                Oledrion\Utility::callJavascriptFile('select/select.js', false, true);
                $productTray  = new \XoopsFormElementTray($parameters['caption'], '');
                $productsList = new \XoopsFormSelect('', $parameters['name'], $parameters['values'], $parameters['size'], $parameters['multiple']);
                // Recherche des produits
                $selectedProducts = $this->getList(new \Criteria('product_id', '(' . implode(',', $parameters['values']) . ')', 'IN'));
                $productsList->addOptionArray($selectedProducts); // Les valeurs sélectionnées
                $productTray->addElement($productsList);
                $removeButton = new \XoopsFormButton('', 'removeProduct', _AM_OLEDRION_REMOVE_SELECTED, 'button');
                if (Oledrion\Utility::isX20()) {
                    $removeButton->setExtra(" onclick=\"removeOptionSelected('" . $parameters['name'] . "[]');\"");
                } else {
                    $removeButton->setExtra(" onclick=\"removeOptionSelected('" . $parameters['name'] . "');\"");
                }
                $productTray->addElement($removeButton);
                if (Oledrion\Utility::isX20()) {
                    $link = "<a href=\"javascript:openWithSelfMain('"
                            . OLEDRION_ADMIN_URL
                            . 'productsselector.php?mutipleSelect=1&amp;callerName='
                            . $parameters['name']
                            . "[]', '', "
                            . OLEDRION_MAX_PRODUCTS_POPUP_WIDTH
                            . ', '
                            . OLEDRION_MAX_PRODUCTS_POPUP_HEIGHT
                            . ');">'
                            . _AM_OLEDRION_SELECT_OTHER_P
                            . '</a>';
                } else {
                    $link = "<a href=\"javascript:openWithSelfMain('"
                            . OLEDRION_ADMIN_URL
                            . 'productsselector.php?mutipleSelect=1&amp;callerName='
                            . $parameters['name']
                            . "', '', "
                            . OLEDRION_MAX_PRODUCTS_POPUP_WIDTH
                            . ', '
                            . OLEDRION_MAX_PRODUCTS_POPUP_HEIGHT
                            . ');">'
                            . _AM_OLEDRION_SELECT_OTHER_P
                            . '</a>';
                }
                $linkLabel = new \XoopsFormLabel('', '<br>' . $link);
                $productTray->addElement($linkLabel);
                echo "<script type=\"text/javascript\">\n";
                echo "jQuery().ready(function($) {\n";
                echo '$("#' . $parameters['formName'] . "\").submit( function() {\n";
                echo "  selectAll('" . $parameters['name'] . "', true);\n";
                echo "} );\n";
                echo "});\n";
                echo "</script>\n";
            } else { // Pas de sélection multiple
                $productTray  = new \XoopsFormElementTray($parameters['caption'], '');
                $productsList = new \XoopsFormSelect('', $parameters['name'], $parameters['value'], $parameters['size'], $parameters['multiple']);
                // Recherche des produits
                if ($parameters['value'] > 0) {
                    $selectedProducts = $this->getList(new \Criteria('product_id', $parameters['value'], '='));
                    if (null !== $parameters['withNull']) {
                        $selectedProducts[0] = $parameters['withNull'];
                        ksort($selectedProducts);
                    }
                    $productsList->addOptionArray($selectedProducts); // Les valeurs sélectionnées
                }
                $productTray->addElement($productsList);
                $link      = "<a href=\"javascript:openWithSelfMain('"
                             . OLEDRION_ADMIN_URL
                             . 'productsselector.php?mutipleSelect=0&amp;callerName='
                             . $parameters['name']
                             . "', '', "
                             . OLEDRION_MAX_PRODUCTS_POPUP_WIDTH
                             . ', '
                             . OLEDRION_MAX_PRODUCTS_POPUP_HEIGHT
                             . ');">'
                             . _AM_OLEDRION_SELECT_PRODUCT
                             . '</a>';
                $linkLabel = new \XoopsFormLabel('', '<br>' . $link);
                $productTray->addElement($linkLabel);
            }
        } else {
            $productTray = new \XoopsFormSelect($parameters['caption'], $parameters['name'], $parameters['value'], $parameters['size'], $parameters['multiple']);
            $products    = $this->getList($criteria);
            $productTray->addOptionArray($products);
        }

        if ('' !== trim($parameters['description'])) {
            $productTray->setDescription($parameters['description']);
        }

        return $productTray;
    }
}
