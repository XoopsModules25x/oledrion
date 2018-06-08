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
 * Module: oledrion
 *
 * @category        Module
 * @package         xoopstube
 * @author          XOOPS Development Team <https://xoops.org>
 * @copyright       {@link https://xoops.org/ XOOPS Project}
 * @license         GPL 2.0 or later
 * @link            https://xoops.org/
 * @since           1.0.0
 */
//use XoopsModules\Oledrion;

/**
 *
 * Class to define Oledrion module constant values. These constants are
 * used to make the code easier to read and to keep values in central
 * location if they need to be changed.  These should not normally need
 * to be modified. If they are to be modified it is recommended to change
 * the value(s) before module installation. Additionally the module may not
 * work correctly if trying to upgrade if these values have been changed.
 *
 **/

class Constants
{
    // Les types d'option
    const OLEDRION_ATTRIBUTE_RADIO = 1;
    const OLEDRION_ATTRIBUTE_CHECKBOX = 2;
    const OLEDRION_ATTRIBUTE_SELECT = 3;

    // Le séparateur de données utilisé en interne
    const OLEDRION_ATTRIBUTE_SEPARATOR = '|';
    const OLEDRION_EMPTY_OPTION = '';

    // Le séparateur de ligne lorsque l'option est un bouton radio ou des cases à cocher
    const OLEDRION_ATTRIBUTE_CHECKBOX_WHITE_SPACE = 1;     // Séparateur de ligne = espace blanc
    const OLEDRION_ATTRIBUTE_CHECKBOX_NEW_LINE = 2;        // Séparateur de ligne = retour à la ligne

    // Les options par défaut lorsque l'option est une liste déroulante
    const OLEDRION_ATTRIBUTE_SELECT_VISIBLE_OPTIONS = 1;    // Valeur par défaut, nombre d'options visibles
    const OLEDRION_ATTRIBUTE_SELECT_MULTIPLE = false;       // Valeur par défaut, sélecteur multiple ?

    // Commands

    const OLEDRION_STATE_NOINFORMATION = 0; // Pas encore d'informations sur la commande
    const OLEDRION_STATE_VALIDATED = 1; // Commande validée par la passerelle de paiement
    const OLEDRION_STATE_PENDING = 2; // En attente
    const OLEDRION_STATE_FAILED = 3; // Echec
    const OLEDRION_STATE_CANCELED = 4; // Annulée
    const OLEDRION_STATE_FRAUD = 5; // Fraude
    const OLEDRION_STATE_PACKED = 6;
    const OLEDRION_STATE_SUBMITED = 7;
    const OLEDRION_STATE_DELIVERED = 8;

    // Les nouveaux define relatifs aux réductions ************************************************************************
    const OLEDRION_DISCOUNT_PRICE_TYPE0 = 0; // Réduction non définie
    const OLEDRION_DISCOUNT_PRICE_TYPE1 = 1; // Réduction dégressive
    const OLEDRION_DISCOUNT_PRICE_TYPE2 = 2; // Réduction d'un montant ou pourcentage

    const OLEDRION_DISCOUNT_PRICE_REDUCE_PERCENT = 1; // Pourcent
    const OLEDRION_DISCOUNT_PRICE_REDUCE_MONEY = 2; // Euros

    const OLEDRION_DISCOUNT_PRICE_AMOUNT_ON_PRODUCT = 1; // Réduction d'un montant ou d'un pourcentage sur le produit
    const OLEDRION_DISCOUNT_PRICE_AMOUNT_ON_CART = 2; // Réduction d'un montant ou d'un pourcentage sur le panier

    const OLEDRION_DISCOUNT_PRICE_CASE_ALL = 1; // Dans tous les cas
    const OLEDRION_DISCOUNT_PRICE_CASE_FIRST_BUY = 2; // si c'est le premier achat du client sur le site
    const OLEDRION_DISCOUNT_PRICE_CASE_PRODUCT_NEVER = 3; // si le produit n'a jamais été acheté par le client
    const OLEDRION_DISCOUNT_PRICE_CASE_QTY_IS = 4; // si la quantité de produit est ...

    const OLEDRION_DISCOUNT_PRICE_QTY_COND1 = 1; // si la quantité de produit est > à
    const OLEDRION_DISCOUNT_PRICE_QTY_COND2 = 2; // si la quantité de produit est >= à
    const OLEDRION_DISCOUNT_PRICE_QTY_COND3 = 3; // si la quantité de produit est < à
    const OLEDRION_DISCOUNT_PRICE_QTY_COND4 = 4; // si la quantité de produit est <= à
    const OLEDRION_DISCOUNT_PRICE_QTY_COND5 = 5; // si la quantité de produit est = à

    const OLEDRION_DISCOUNT_PRICE_QTY_COND1_TEXT = '>'; // si la quantité de produit est > à
    const OLEDRION_DISCOUNT_PRICE_QTY_COND2_TEXT = '>='; // si la quantité de produit est >= à
    const OLEDRION_DISCOUNT_PRICE_QTY_COND3_TEXT = '<'; // si la quantité de produit est < à
    const OLEDRION_DISCOUNT_PRICE_QTY_COND4_TEXT = '<='; // si la quantité de produit est <= à
    const OLEDRION_DISCOUNT_PRICE_QTY_COND5_TEXT = '='; // si la quantité de produit est = à

    const OLEDRION_DISCOUNT_SHIPPING_TYPE1 = 1; // Les frais de port sont à payer dans leur intégralité
    const OLEDRION_DISCOUNT_SHIPPING_TYPE2 = 2; // Les frais de port sont totalement gratuits
    const OLEDRION_DISCOUNT_SHIPPING_TYPE3 = 3; // Les frais de port sont réduits de ...
    const OLEDRION_DISCOUNT_SHIPPING_TYPE4 = 4; // Les frais de port sont dégressifs
    // ********************************************************************************************************************

    /**
     * Définition des types de listes
     */
    const OLEDRION_LISTS_ALL_PUBLIC = -2; // Que les publiques
    const OLEDRION_LISTS_ALL = -1; // Toutes sans distinction
    const OLEDRION_LISTS_PRIVATE = 0;
    const OLEDRION_LISTS_WISH = 1;
    const OLEDRION_LISTS_RECOMMEND = 2;
}
