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
use Xoopsmodules\oledrion\Constants;

/**
 * Gestion des réductions
 */
require_once __DIR__ . '/classheader.php';




/**
 * Class DiscountsHandler
 */
class DiscountsHandler extends OledrionPersistableObjectHandler
{
    /**
     * DiscountsHandler constructor.
     * @param \XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db)
    { //                        Table                   Classe              Id        Libellé
        parent::__construct($db, 'oledrion_discounts', Discounts::class, 'disc_id', 'disc_title');
    }

    /**
     * @param $price
     * @param $discount
     * @return mixed
     */
    private function getDiscountedPrice($price, $discount)
    {
        return $price - ($price * ($discount / 100));
    }

    /**
     * Retourne la liste des règles qui sont applicables sur la période courante
     * @param void
     * @return array objets de type Discounts
     */
    public function getRulesForThisPeriod()
    {
        static $buffer = [];
        if (is_array($buffer) && count($buffer) > 0) {
            return $buffer;
        } else {
            $critere = new \CriteriaCompo();
            $critere->add(new \Criteria('disc_date_from', 0, '='));
            $critere->add(new \Criteria('disc_date_to', 0, '='), 'OR');

            $critere2 = new \CriteriaCompo();
            $critere2->add(new \Criteria('disc_date_from', time(), '>='));
            $critere2->add(new \Criteria('disc_date_to', time(), '<='));
            $critere->add($critere2);

            $buffer = $this->getObjects($critere);
        }

        return $buffer;
    }

    /**
     *
     * @deprecated
     */

    /**
     * Renvoie la liste des règles à appliquer sur chaque produit (avec gestion de cache) pour l'utilisateur courant
     *
     * @return array Tableau d'objets de type Discounts
     */
    public function getRulesOnEachProduct()
    {
        static $buffer = [];
        if (is_array($buffer) && count($buffer) > 0) {
        } else {
            $groups  = oledrion\Utility::getCurrentMemberGroups();
            $critere = new \CriteriaCompo();
            $critere->add(new \Criteria('disc_on_what', Constants::OLEDRION_DISCOUNT_ON3, '='));
            if (count($groups) > 0) {
                $critere->add(new \Criteria('disc_group', '(' . implode(',', $groups) . ')', 'IN'));
            }
            $buffer = $this->getObjects($critere);
        }

        return $buffer;
    }

    /**
     * Renvoie la liste des règles à appliquer sur tous les produits (avec gestion de cache) pour l'utilisateur courant
     *
     * @return array Tableau d'objets de type Discounts
     */
    public function getRulesOnAllProducts()
    {
        static $buffer = [];
        if (is_array($buffer) && count($buffer) > 0) {
        } else {
            $critere = new \CriteriaCompo();
            $critere->add(new \Criteria('disc_on_what', Constants::OLEDRION_DISCOUNT_ON2, '='));
            $tblGroups = oledrion\Utility::getCurrentMemberGroups();
            $critere->add(new \Criteria('disc_group', '(' . implode(',', $tblGroups) . ')', 'IN'));
            $buffer = $this->getObjects($critere);
        }

        return $buffer;
    }

    /**
     * Renvoie la liste des règles à appliquer sur les frais de ports (avec gestion de cache) pour l'utilisateur courant
     *
     * @return array Tableau d'objets de type Discounts
     */
    public function getRulesOnShipping()
    {
        static $buffer = [];
        if (is_array($buffer) && count($buffer) > 0) {
        } else {
            $critere = new \CriteriaCompo();
            $critere->add(new \Criteria('disc_on_what', Constants::OLEDRION_DISCOUNT_ON4, '='));
            $tblGroups = oledrion\Utility::getCurrentMemberGroups();
            $critere->add(new \Criteria('disc_group', '(' . implode(',', $tblGroups) . ')', 'IN'));
            $buffer = $this->getObjects($critere);
        }

        return $buffer;
    }

    /**
     * Renvoie la liste des règles à appliquer sur les frais de ports (avec gestion de cache) pour l'utilisateur courant
     *
     * @return array Tableau d'objets de type Discounts
     */
    public function getRulesOnShipping2()
    {
        static $buffer = [];
        if (is_array($buffer) && count($buffer) > 0) {
        } else {
            $critere = new \CriteriaCompo();
            $critere->add(new \Criteria('disc_on_what', Constants::OLEDRION_DISCOUNT_ON5, '='));
            $critere->add(new \Criteria('disc_shipping', Constants::OLEDRION_DISCOUNT_SHIPPING2, '='));
            $tblGroups = oledrion\Utility::getCurrentMemberGroups();
            $critere->add(new \Criteria('disc_group', '(' . implode(',', $tblGroups) . ')', 'IN'));
            $buffer = $this->getObjects($critere);
        }

        return $buffer;
    }

    /**
     * Renvoie la liste des règles à appliquer sur l'intégralité de la commande (avec gestion de cache) pour l'utilisateur courant
     *
     * @return array Tableau d'objets de type Discounts
     */
    public function getRulesOnCommand()
    {
        static $buffer = [];
        if (is_array($buffer) && count($buffer) > 0) {
        } else {
            $critere = new \CriteriaCompo();
            $critere->add(new \Criteria('disc_on_what', Constants::OLEDRION_DISCOUNT_ON1, '='));
            $tblGroups = oledrion\Utility::getCurrentMemberGroups();
            $critere->add(new \Criteria('disc_group', '(' . implode(',', $tblGroups) . ')', 'IN'));
            $buffer = $this->getObjects($critere);
        }

        return $buffer;
    }

    /**
     * Deuxième lot de réductions, à appliquer sur les frais de port
     *
     * @param float $montantShipping      Montant des frais de port
     * @param float $commandAmount        Le montant total de la commande
     * @param array $discountsDescription Descriptions des réductions appliquées
     */
    public function applyDiscountOnShipping2(&$montantShipping, $commandAmount, &$discountsDescription)
    {
        $tblRules = [];
        $tblRules = $this->getRulesOnShipping2(); // Renvoie des objets Discounts
        if (count($tblRules) > 0) {
            foreach ($tblRules as $rule) {
                if ($commandAmount > (float)$rule->getVar('disc_if_amount')) {
                    $discountsDescription[] = $rule->getVar('disc_description');
                    $montantShipping        = 0;
                }
            }
        }
    }

    /**
     * Réductions à appliquer sur le montant global de la commande
     *
     * @param float $montantHT            Montant HT des produits
     * @param array $discountsDescription Descriptions des réductions appliquées
     */
    public function applyDiscountOnCommand(&$montantHT, &$discountsDescription)
    {
        global $commandsHandler;
        $tblRules = [];
        $tblRules = $this->getRulesOnCommand(); // Renvoie des objets Discounts
        if (count($tblRules) > 0) {
            $uid = oledrion\Utility::getCurrentUserID();
            foreach ($tblRules as $rule) {
                switch ($rule->getVar('disc_when')) {
                    case Constants::OLEDRION_DISCOUNT_WHEN1: // Dans tous les cas
                        if (OLEDRION_DISCOUNT_PRICE_TYPE1 == $rule->getVar('disc_percent_monney')) { // Réduction de x pourcent
                            $montantHT = $this->getDiscountedPrice($montantHT, $rule->getVar('disc_amount'));
                            if ($montantHT < 0) {
                                $montantHT = 0;
                            }
                        } else { // Réduction de x euros
                            $montantHT -= $rule->getVar('disc_amount');
                            if ($montantHT < 0) {
                                $montantHT = 0;
                            }
                        }
                        $discountsDescription[] = $rule->getVar('disc_description');
                        break;

                    case Constants::OLEDRION_DISCOUNT_WHEN2: // Si c'est le premier achat de l'utilisateur sur le site
                        if ($commandsHandler->isFirstCommand($uid)) {
                            if (Constants::OLEDRION_DISCOUNT_PRICE_TYPE1 == $rule->getVar('disc_percent_monney')) { // Réduction de x pourcent
                                $montantHT = $this->getDiscountedPrice($montantHT, $rule->getVar('disc_amount'));
                                if ($montantHT < 0) {
                                    $montantHT = 0;
                                }
                            } else { // Réduction de x euros
                                $montantHT -= $rule->getVar('disc_amount');
                                if ($montantHT < 0) {
                                    $montantHT = 0;
                                }
                            }
                            $discountsDescription[] = $rule->getVar('disc_description');
                        }
                        break;
                }
            }
        }
    }

    /**
     * Réductions à appliquer sur les frais de port de chaque produit
     *
     * @param float   $montantHT            Montant HT des produits
     * @param array   $discountsDescription Descriptions des réductions appliquées
     * @param integer $productQty           Quantité commandée du produit
     */
    public function applyDiscountOnShipping(&$montantHT, &$discountsDescription, $productQty)
    {
        global $commandsHandler;
        $tblRules = [];
        $tblRules = $this->getRulesOnShipping(); // Renvoie des objets Discounts
        if (count($tblRules) > 0) {
            $uid = oledrion\Utility::getCurrentUserID();
            foreach ($tblRules as $rule) {
                switch ($rule->getVar('disc_when')) {
                    case Constants::OLEDRION_DISCOUNT_WHEN1: // Dans tous les cas
                        if (Constants::OLEDRION_DISCOUNT_PRICE_TYPE1 == $rule->getVar('disc_percent_monney')) { // Réduction de x pourcent
                            $montantHT = $this->getDiscountedPrice($montantHT, $rule->getVar('disc_amount'));
                            if ($montantHT < 0) {
                                $montantHT = 0;
                            }
                        } else { // Réduction de x euros
                            $montantHT -= $rule->getVar('disc_amount');
                            if ($montantHT < 0) {
                                $montantHT = 0;
                            }
                        }
                        $discountsDescription[] = $rule->getVar('disc_description');
                        break;

                    case Constants::OLEDRION_DISCOUNT_WHEN2: // Si c'est le premier achat de l'utilisateur sur le site
                        if ($commandsHandler->isFirstCommand($uid)) {
                            if (Constants::OLEDRION_DISCOUNT_PRICE_TYPE1 == $rule->getVar('disc_percent_monney')) { // Réduction de x pourcent
                                $montantHT = $this->getDiscountedPrice($montantHT, $rule->getVar('disc_amount'));
                                if ($montantHT < 0) {
                                    $montantHT = 0;
                                }
                            } else { // Réduction de x euros
                                $montantHT -= $rule->getVar('disc_amount');
                                if ($montantHT < 0) {
                                    $montantHT = 0;
                                }
                            }
                            $discountsDescription[] = $rule->getVar('disc_description');
                        }
                        break;

                    case Constants::OLEDRION_DISCOUNT_WHEN4: // Si la quantité est =, >, >=, <, <= à ...
                        $qtyDiscount = false;
                        switch ($rule->getVar('disc_qty_criteria')) {
                            case 0: // =
                                if ($productQty == $rule->getVar('disc_qty_value')) {
                                    $qtyDiscount = true;
                                }
                                break;

                            case 1: // >
                                if ($productQty > $rule->getVar('disc_qty_value')) {
                                    $qtyDiscount = true;
                                }
                                break;

                            case 2: // >=
                                if ($productQty >= $rule->getVar('disc_qty_value')) {
                                    $qtyDiscount = true;
                                }
                                break;

                            case 3: // <
                                if ($productQty < $rule->getVar('disc_qty_value')) {
                                    $qtyDiscount = true;
                                }
                                break;

                            case 4: // <=
                                if ($productQty <= $rule->getVar('disc_qty_value')) {
                                    $qtyDiscount = true;
                                }
                                break;

                        }
                        if ($qtyDiscount) {
                            if (Constants::OLEDRION_DISCOUNT_PRICE_TYPE1 == $rule->getVar('disc_percent_monney')) { // Réduction de x pourcents
                                $montantHT = $this->getDiscountedPrice($montantHT, $rule->getVar('disc_amount'));
                                if ($montantHT < 0) {
                                    $montantHT = 0;
                                }
                            } else { // Réduction de x euros
                                $montantHT -= $rule->getVar('disc_amount');
                                if ($montantHT < 0) {
                                    $montantHT = 0;
                                }
                            }
                            $discountsDescription[] = $rule->getVar('disc_description');
                        }
                        break;
                }
            }
        }
    }

    /**
     * Réductions à appliquer sur le montant HT de TOUS les produits
     *
     * @param float   $montantHT            Montant HT des produits
     * @param array   $discountsDescription Descriptions des réductions appliquées
     * @param integer $productQty           Quantité commandée du produit
     */
    public function applyDiscountOnAllProducts(&$montantHT, &$discountsDescription, $productQty)
    {
        global $commandsHandler;
        $tblRules = [];
        $tblRules = $this->getRulesOnAllProducts(); // Renvoie des objets Discounts
        if (count($tblRules) > 0) {
            $uid = oledrion\Utility::getCurrentUserID();
            foreach ($tblRules as $rule) {
                switch ($rule->getVar('disc_when')) {
                    case Constants::OLEDRION_DISCOUNT_WHEN1: // Dans tous les cas
                        if (Constants::OLEDRION_DISCOUNT_PRICE_TYPE1 == $rule->getVar('disc_percent_monney')) { // Réduction de x pourcent
                            $montantHT = $this->getDiscountedPrice($montantHT, $rule->getVar('disc_amount'));
                            if ($montantHT < 0) {
                                $montantHT = 0;
                            }
                        } else { // Réduction de x euros
                            $montantHT -= $rule->getVar('disc_amount');
                            if ($montantHT < 0) {
                                $montantHT = 0;
                            }
                        }
                        $discountsDescription[] = $rule->getVar('disc_description');
                        break;

                    case Constants::OLEDRION_DISCOUNT_WHEN2: // Si c'est le premier achat de l'utilisateur sur le site
                        if ($commandsHandler->isFirstCommand($uid)) {
                            if (OLEDRION_DISCOUNT_PRICE_TYPE1 == $rule->getVar('disc_percent_monney')) { // Réduction de x pourcent
                                $montantHT = $this->getDiscountedPrice($montantHT, $rule->getVar('disc_amount'));
                                if ($montantHT < 0) {
                                    $montantHT = 0;
                                }
                            } else { // Réduction de x euros
                                $montantHT -= $rule->getVar('disc_amount');
                                if ($montantHT < 0) {
                                    $montantHT = 0;
                                }
                            }
                            $discountsDescription[] = $rule->getVar('disc_description');
                        }
                        break;

                    case Constants::OLEDRION_DISCOUNT_WHEN4: // Si la quantité est =, >, >=, <, <= à ...
                        $qtyDiscount = false;
                        switch ($rule->getVar('disc_qty_criteria')) {
                            case 0: // =
                                if ($productQty == $rule->getVar('disc_qty_value')) {
                                    $qtyDiscount = true;
                                }
                                break;

                            case 1: // >
                                if ($productQty > $rule->getVar('disc_qty_value')) {
                                    $qtyDiscount = true;
                                }
                                break;

                            case 2: // >=
                                if ($productQty >= $rule->getVar('disc_qty_value')) {
                                    $qtyDiscount = true;
                                }
                                break;

                            case 3: // <
                                if ($productQty < $rule->getVar('disc_qty_value')) {
                                    $qtyDiscount = true;
                                }
                                break;

                            case 4: // <=
                                if ($productQty <= $rule->getVar('disc_qty_value')) {
                                    $qtyDiscount = true;
                                }
                                break;

                        }
                        if ($qtyDiscount) {
                            if (Constants::OLEDRION_DISCOUNT_PRICE_TYPE1 == $rule->getVar('disc_percent_monney')) { // Réduction de x pourcent
                                $montantHT = $this->getDiscountedPrice($montantHT, $rule->getVar('disc_amount'));
                                if ($montantHT < 0) {
                                    $montantHT = 0;
                                }
                            } else { // Réduction de x euros
                                $montantHT -= $rule->getVar('disc_amount');
                                if ($montantHT < 0) {
                                    $montantHT = 0;
                                }
                            }
                            $discountsDescription[] = $rule->getVar('disc_description');
                        }
                        break;
                }
            }
        }
    }

    /**
     * Recalcul du prix HT du produit en appliquant les réductions, s'il y a lieu
     *
     * @param integer $productId            Identifiant du produit
     * @param float   $prixHT               Prix HT du produit
     * @param array   $discountsDescription Descriptions des réductions appliquées
     * @param integer $productQty           Quantité commandée du produit
     */
    public function applyDiscountOnEachProduct($productId, &$prixHT, &$discountsDescription, $productQty)
    {
        global $commandsHandler;
        $rules = [];
        $rules = $this->getRulesOnEachProduct(); // Renvoie des objets Discounts
        if (count($rules) > 0) {
            $uid = oledrion\Utility::getCurrentUserID();
            foreach ($rules as $rule) {
                switch ($rule->getVar('disc_when')) {
                    case Constants::OLEDRION_DISCOUNT_WHEN1: // Dans tous les cas
                        if (Constants::OLEDRION_DISCOUNT_PRICE_TYPE1 == $rule->getVar('disc_percent_monney')) { // Réduction de x pourcent
                            $prixHT = $this->getDiscountedPrice($prixHT, $rule->getVar('disc_amount'));
                            if ($prixHT < 0) {
                                $prixHT = 0;
                            }
                        } else { // Réduction de x euros
                            $prixHT -= $rule->getVar('disc_amount');
                            if ($prixHT < 0) {
                                $prixHT = 0;
                            }
                        }
                        $discountsDescription[] = $rule->getVar('disc_description');
                        break;

                    case Constants::OLEDRION_DISCOUNT_WHEN2: // Si c'est le premier achat de l'utilisateur sur le site
                        if ($commandsHandler->isFirstCommand($uid)) {
                            if (Constants::OLEDRION_DISCOUNT_PRICE_TYPE1 == $rule->getVar('disc_percent_monney')) { // Réduction de x pourcent
                                $prixHT = $this->getDiscountedPrice($prixHT, $rule->getVar('disc_amount'));
                                if ($prixHT < 0) {
                                    $prixHT = 0;
                                }
                            } else { // Réduction de x euros
                                $prixHT -= $rule->getVar('disc_amount');
                                if ($prixHT < 0) {
                                    $prixHT = 0;
                                }
                            }
                            $discountsDescription[] = $rule->getVar('disc_description');
                        }
                        break;

                    case Constants::OLEDRION_DISCOUNT_WHEN3: // Si le produit n'a jamais été acheté
                        if (!$commandsHandler->productAlreadyBought($uid, $productId)) {
                            if (Constants::OLEDRION_DISCOUNT_PRICE_TYPE1 == $rule->getVar('disc_percent_monney')) { // Réduction de x pourcent
                                $prixHT = $this->getDiscountedPrice($prixHT, $rule->getVar('disc_amount'));
                                if ($prixHT < 0) {
                                    $prixHT = 0;
                                }
                            } else { // Réduction de x euros
                                $prixHT -= $rule->getVar('disc_amount');
                                if ($prixHT < 0) {
                                    $prixHT = 0;
                                }
                            }
                            $discountsDescription[] = $rule->getVar('disc_description');
                        }
                        break;

                    case Constants::OLEDRION_DISCOUNT_WHEN4: // Si la quantité est =, >, >=, <, <= à ...
                        $qtyDiscount = false;
                        switch ($rule->getVar('disc_qty_criteria')) {
                            case 0: // =
                                if ($productQty == $rule->getVar('disc_qty_value')) {
                                    $qtyDiscount = true;
                                }
                                break;

                            case 1: // >
                                if ($productQty > $rule->getVar('disc_qty_value')) {
                                    $qtyDiscount = true;
                                }
                                break;

                            case 2: // >=
                                if ($productQty >= $rule->getVar('disc_qty_value')) {
                                    $qtyDiscount = true;
                                }
                                break;

                            case 3: // <
                                if ($productQty < $rule->getVar('disc_qty_value')) {
                                    $qtyDiscount = true;
                                }
                                break;

                            case 4: // <=
                                if ($productQty <= $rule->getVar('disc_qty_value')) {
                                    $qtyDiscount = true;
                                }
                                break;

                        }
                        if ($qtyDiscount) {
                            if (Constants::OLEDRION_DISCOUNT_PRICE_TYPE1 == $rule->getVar('disc_percent_monney')) { // Réduction de x pourcent
                                $prixHT = $this->getDiscountedPrice($prixHT, $rule->getVar('disc_amount'));
                                if ($prixHT < 0) {
                                    $prixHT = 0;
                                }
                            } else { // Réduction de x euros
                                $prixHT -= $rule->getVar('disc_amount');
                                if ($prixHT < 0) {
                                    $prixHT = 0;
                                }
                            }
                            $discountsDescription[] = $rule->getVar('disc_description');
                        }
                        break;
                }
            }
        }
    }

    /**
     * Supprime les remises associées à un produit
     *
     * @param  integer $disc_product_id
     * @return boolean
     */
    public function removeProductFromDiscounts($disc_product_id)
    {
        $disc_product_id = (int)$disc_product_id;

        return $this->deleteAll(new \Criteria('disc_product_id', $disc_product_id, '='));
    }
}
