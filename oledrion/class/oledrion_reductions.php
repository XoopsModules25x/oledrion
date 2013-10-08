<?php
/**
 * ****************************************************************************
 * oledrion - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         oledrion
 * @author 			Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 *
 * Version : $Id:
 * ****************************************************************************
 */

/**
 * Calcul du panier et de ses réductions en fonction des règles de remises
 * Cette classe ne gère pas de fichier (elle sert uniquement aux calculs)
 *
 * Détail des tableaux :
 * categoriesProductsCount => Nombre de produits par catégorie
 * [clé] = Id Catégorie, [valeur] = Nombre de produits
 *
 * categoriesProductsQuantities => Quantités de produits par catégorie
 * [clé] = Id Catégorie, [valeur] = Quantité de produits
 *
 * totalProductsQuantities => Quantité totale de tous les produits
 *
 * associatedManufacturers => Contient la liste des ID uniques de produits
 * [clé] = Id Produit, [valeur] = Id produit
 *
 * associatedVendors => Contient la liste des vendeurs de produits
 * [clé] = Id Vendeur, [valeur] = Id Vendeur
 *
 * associatedAttributesPerProduct => Contient les attributs de chaque produit
 * [clé] = Id Produit, [valeurS] = Tous les attributs du produit sous la forme d'objets de type Attributs
 *
 * associatedCategories => Contient la liste des ID de catégories
 * [clé] = Id Catégorie, [valeur] = Id Catégorie
 *
 * totalAmountBeforeDiscounts => Montant total de la commande avant les réductions
 *
 * associatedManufacturersPerProduct => Contient la liste des ID des fabricants par produit
 * [clé] = Id produit, [valeur] = array(Ids des fabricants)
 *
 * Les 3 tableaux suivants évoluent ensuite comme ceci :
 * associatedManufacturers => Tableau d'objets de type Fabricants
 * [clé] = id Fabricant [valeur] = Fabricant sous la forme d'un objet
 *
 * associatedVendors => Tableau d'ojets de type Vendeurs
 * [clé] = Id Vendeur [valeur] = Vendeur sous la forme d'un objet
 *
 * associatedCategories => Tableau d'objets de type Categories
 * [clé] = Id Catégorie [valeur] = Catéagorie sous la forme d'un objet
 *
 */
class oledrion_reductions {
	// Ne contient que la liste des règles actives au moment du calcul
	private $allActiveRules = array();

	// Nombre de produits par catégorie
	private $categoriesProductsCount = array();

	// Quantité de produits par catégorie
	private $categoriesProductsQuantities = array();

	/**
	 * le caddy en mémoire
	 * 	$cart['number'] = Indice du produit
	 * 	$cart['id'] = Identifiant du produit
	 * 	$cart['qty'] = Quantité voulue
	 *  $cart['product'] = L'objet produit correspondant au panier
	 */
	private $cart = array();

	/**
	 * Le caddy pour le template. Consulter les détails du caddy dans la métode ComputeCart
	 */
	private $cartForTemplate = array();

	/**
	 * Les règles à appliquer à la fin, sur l'intégralité du panier
	 */
	private $rulesForTheWhole = array();

	// Le total des quantités de produits avant les réductions
	private $totalProductsQuantities = 0;
	// Montant total de la commande avant les réductions
	private $totalAmountBeforeDiscounts = 0;

	// Handlers vers les tables du module
	private $handlers;

	// Les fabricants associés aux produits du panier
	private $associatedManufacturers = array();

	// Les vendeur associés aux produits du panier
	private $associatedVendors = array();

	// Les catégories associées aux produits du panier
	private $associatedCategories = array();

	// Fabricants associés par produit du panier
	private $associatedManufacturersPerProduct = array();

	// Attributs par produit du panier
	private $associatedAttributesPerProduct = array();

	/**
	 * Chargement des handlers et des règles actives
	 */
	public function __construct() {
		$this -> initHandlers();
		$this -> loadAllActiveRules();
	}

	/**
	 * Chargement des handlers
	 */
	private function initHandlers() {
		$this -> handlers = oledrion_handler::getInstance();
	}

	/**
	 * Chargement de toutes les règles actives de réductions (sans date définie ou avec une période correspondante à aujourd'hui)
	 */
	public function loadAllActiveRules() {
		$critere = new CriteriaCompo();
		$critere1 = new CriteriaCompo();
		$critere1 -> add(new Criteria('disc_date_from', 0, '='));
		$critere1 -> add(new Criteria('disc_date_to', 0, '='));
		$critere -> add($critere1);

		$critere2 = new CriteriaCompo();
		$critere2 -> add(new Criteria('disc_date_from', time(), '<='));
		$critere2 -> add(new Criteria('disc_date_to', time(), '>='));
		$critere -> add($critere2, 'OR');

		$this -> allActiveRules = $this -> handlers -> h_oledrion_discounts -> getObjects($critere);
	}

	/**
	 * Calcul des quantités de produits par catégorie et du nombre de produits par catégorie
	 *
	 * @param oledrion_products $product
	 * @param integer $quantity
	 */
	public function computePerCategories(oledrion_products $product, $quantity) {
		// Nombre de produits par catégories
		if (isset($this -> categoriesProductsCount[$product -> product_cid])) {
			$this -> categoriesProductsCount[$product -> product_cid] += 1;
		} else {
			$this -> categoriesProductsCount[$product -> product_cid] = 1;
		}

		// Mise à jour des quantités par catégories
		if (isset($this -> categoriesProductsQuantities[$product -> product_cid])) {
			$this -> categoriesProductsQuantities[$product -> product_cid] += $quantity;
		} else {
			$this -> categoriesProductsQuantities[$product -> product_cid] = $quantity;
		}
		$this -> totalProductsQuantities += $quantity;
		// Quantité totale de tous les produits
	}

	/**
	 * Ajoute à un tableau interne, le fabricant associé à un produit
	 *
	 * @param oledrion_products $product
	 */
	private function addAssociatedManufacturers(oledrion_products $product) {
		if (!isset($this -> associatedManufacturers[$product -> product_id])) {
			$this -> associatedManufacturers[$product -> product_id] = $product -> product_id;
		}
	}

	/**
	 * Recherche des attributs associés à chaque produit
	 *
	 * @param oledrion_products $product
	 * @param attray $attributes
	 * @since 2.3
	 */
	private function addAssociatedAttributes(oledrion_products $product, $attributes) {
		if (!isset($this -> associatedAttributesPerProduct[$product -> product_id])) {
			$this -> associatedAttributesPerProduct[$product -> product_id] = $product -> getProductsAttributesList($attributes);
		}
	}

	/**
	 * Ajoute à un tableau interne, le vendeur associé à un produit
	 *
	 * @param oledrion_products $product
	 */
	private function addAssociatedVendors(oledrion_products $product) {
		if (!isset($this -> associatedVendors[$product -> product_vendor_id])) {
			$this -> associatedVendors[$product -> product_vendor_id] = $product -> product_vendor_id;
		}
	}

	/**
	 * Ajoute à un tableau interne, la catégorie associée à un produit
	 *
	 * @param oledrion_products $product
	 */
	private function addAssociatedCategories(oledrion_products $product) {
		if (!isset($this -> associatedCategories[$product -> product_cid])) {
			$this -> associatedCategories[$product -> product_cid] = $product -> product_cid;
		}
	}

	/**
	 * Charge les fabricants associés aux produits du panier
	 */
	private function loadAssociatedManufacturers() {
		if (count($this -> associatedManufacturers) > 0) {
			sort($this -> associatedManufacturers);
			$productsIds = $this -> associatedManufacturers;
			$this -> associatedManufacturers = array();
			// au cas où cela échouerait
			$productsManufacturers = $manufacturersIds = array();
			$productsManufacturers = $this -> handlers -> h_oledrion_productsmanu -> getFromProductsIds($productsIds);
			if (count($productsManufacturers) > 0) {
				foreach ($productsManufacturers as $productManufacturer) {
					if (!isset($manufacturersIds[$productManufacturer -> pm_manu_id])) {
						$manufacturersIds[$productManufacturer -> pm_manu_id] = $productManufacturer -> pm_manu_id;
					}
					$this -> associatedManufacturersPerProduct[$productManufacturer -> pm_product_id][] = $productManufacturer -> pm_manu_id;
				}
				if (count($manufacturersIds) > 0) {
					sort($manufacturersIds);
					$this -> associatedManufacturers = $this -> handlers -> h_oledrion_manufacturer -> getManufacturersFromIds($manufacturersIds);
				}
			}
		}
	}

	/**
	 * Charge la liste des vendeurs associés aux produits
	 */
	private function loadAssociatedVendors() {
		if (count($this -> associatedVendors) > 0) {
			sort($this -> associatedVendors);
			$ids = $this -> associatedVendors;
			$this -> associatedVendors = $this -> handlers -> h_oledrion_vendors -> getVendorsFromIds($ids);
		}
	}

	/**
	 * Charge les catégories associées aux produits du panier
	 */
	private function loadAssociatedCategories() {
		if (count($this -> associatedCategories) > 0) {
			sort($this -> associatedCategories);
			$ids = $this -> associatedCategories;
			$this -> associatedCategories = $this -> handlers -> h_oledrion_cat -> getCategoriesFromIds($ids);
		}
	}

	/**
	 * Recherche les fabricants, catégories et vendeurs associés à chaque produit
	 */
	public function loadElementsAssociatedToProducts() {
		$this -> loadAssociatedManufacturers();
		$this -> loadAssociatedVendors();
		$this -> loadAssociatedCategories();
	}

	/**
	 * Recherche les (objets) produits associés à chaque produit du panier (et lance le calcul des quantités)
	 */
	public function loadProductsAssociatedToCart() {
		$newCart = array();
		foreach ($this->cart as $cartProduct) {
			$data = array();
			$data['id'] = $cartProduct['id'];
			$data['number'] = $cartProduct['number'];
			$data['qty'] = $cartProduct['qty'];
			$data['attributes'] = $cartProduct['attributes'];

			$product = null;
			$product = $this -> handlers -> h_oledrion_products -> get($data['id']);
			if (!is_object($product)) {
				trigger_error(_OLEDRION_ERROR9);
				continue;
				// Pour éviter le cas de la suppression d'un produit (dans l'admin) alors qu'un client l'a toujours dans son panier (et donc en session)
			}
			$data['product'] = $product;
			// Mise à jour des calculs par catégorie
			$this -> computePerCategories($product, $data['qty']);
			// Recherche des éléments associés à chaque produit
			$this -> addAssociatedManufacturers($product);
			$this -> addAssociatedVendors($product);
			$this -> addAssociatedAttributes($product, $data['attributes']);
			$this -> addAssociatedCategories($product);

			// Calcul du total de la commande avant réductions
			if (floatval($product -> getVar('product_discount_price', 'n')) > 0) {
				$ht = floatval($product -> getVar('product_discount_price', 'n'));
			} else {
				$ht = floatval($product -> getVar('product_price', 'n'));
			}
			// S'il y a des options, on rajoute leur montant
			if (is_array($data['attributes']) && count($data['attributes']) > 0) {
				$ht += $this -> handlers -> h_oledrion_attributes -> getProductOptionsPrice($data['attributes'], $product -> getVar('product_vat_id'));
			}

			$this -> totalAmountBeforeDiscounts += ($data['qty'] * $ht);

			$newCart[] = $data;
		}
		$this -> loadElementsAssociatedToProducts();
		$this -> cart = $newCart;
	}

	/**
	 * Calcul du montant HT auquel on applique un pourcentage de réduction
	 *
	 * @param float $price	Le prix auquel appliquer la réduction
	 * @param integer $discount	Le pourcentage de réduction
	 * @return float	Le montant réduit
	 */
	private function getDiscountedPrice($price, $discount) {
		return floatval($price - ($price * ($discount / 100)));
	}

	/**
	 * Remise à zéro des membres internes
	 */
	private function initializePrivateData() {
		$this -> totalProductsQuantities = 0;
		$this -> totalAmountBeforeDiscounts = 0;
		$this -> rulesForTheWhole = array();
		$this -> cartForTemplate = array();
		$this -> associatedManufacturers = array();
		$this -> associatedVendors = array();
		$this -> associatedCategories = array();
		$this -> associatedManufacturersPerProduct = array();
		$this -> associatedAttributesPerProduct = array();
	}

	/**
	 * Calcul de la facture en fonction du panier
	 * Contenu du panier en session :
	 *
	 * 	$datas['number'] = Indice du produit dans le panier
	 * 	$datas['id'] = Identifiant du produit dans la base
	 * 	$datas['qty'] = Quantité voulue
	 *  $datas['attributes'] = Attributs produit array('attr_id' => id attribut, 'values' => array(valueId1, valueId2 ...))
	 *
	 * En variable privé, le panier (dans $cart) contient la même chose + un objet 'oledrion_products' dans la clé 'product'
	 *
	 * @param array $cartForTemplate Contenu du caddy à passer au template (en fait la liste des produits)
	 * @param boolean emptyCart Indique si le panier est vide ou pas
	 * @param float $shippingAmount Montant des frais de port
	 * @param float $commandAmount Montant HT de la commande
	 * @param float $vatAmount Montant de la TVA
	 * @param string $goOn Adresse vers laquelle renvoyer le visiteur après qu'il ait ajouté un produit dans son panier (cela correspond en fait à la catégorie du dernier produit ajouté dans le panier)
	 * @param float $commandAmountTTC Montant TTC de la commande
	 * @param array $discountsDescription Descriptions des remises GLOBALES appliquées (et pas les remises par produit !)
	 * @param integer $discountsCount	Le nombre TOTAL de réductions appliquées (individuellement ou sur la globalité du panier)
	 *
	 * TODO: Passer les paramètres sous forme d'objet
	 */
	public function computeCart(&$cartForTemplate, &$emptyCart, &$shippingAmount, &$commandAmount, &$vatAmount, &$goOn, &$commandAmountTTC, &$discountsDescription, &$discountsCount) {
		$emptyCart = false;
		$goOn = '';
		$vats = array();
		$cpt = 0;
		$discountsCount = 0;
		$this -> cart = isset($_SESSION[OledrionOledrion_caddyHandler::CADDY_NAME]) ? $_SESSION[OledrionOledrion_caddyHandler::CADDY_NAME] : array();
		$cartCount = count($this -> cart);
		if ($cartCount == 0) {
			$emptyCart = true;
			return true;
		}

		// Réinitialisation des données privées
		$this -> initializePrivateData();
		// Chargement des objets produits associés aux produits du panier et calcul des quantités par catégorie
		$this -> loadProductsAssociatedToCart();
		// Chargement des TVA
		if (!isset($_POST['cmd_country']) || empty($_POST['cmd_country'])) {
			$_POST['cmd_country'] = OLEDRION_DEFAULT_COUNTRY;
		}
		$vats = $this -> handlers -> h_oledrion_vat -> getCountryVats($_POST['cmd_country']);
		$oledrion_Currency = &oledrion_Currency::getInstance();
		$caddyCount = count($this -> cart);

		// Initialisation des totaux généraux (ht, tva et frais de port)
		$totalHT = $totalVAT = $totalShipping = 0.0;

		// Boucle sur tous les produits et sur chacune des règles pour calculer le prix du produit (et ses frais de port) et voir si on doit y appliquer une réduction
		foreach ($this->cart as $cartProduct) {
			if (floatval($cartProduct['product'] -> getVar('product_discount_price', 'n')) > 0) {
				$ht = floatval($cartProduct['product'] -> getVar('product_discount_price', 'n'));
			} else {
				$ht = floatval($cartProduct['product'] -> getVar('product_price', 'n'));
			}
			// S'il y a des options, on rajoute leur montant
			$productAttributes = array();
			if (is_array($cartProduct['attributes']) && count($cartProduct['attributes']) > 0) {
				$ht += $this -> handlers -> h_oledrion_attributes -> getProductOptionsPrice($cartProduct['attributes'], $cartProduct['product'] -> getVar('product_vat_id'), $productAttributes);

			}

			$discountedPrice = $ht;
			$quantity = intval($cartProduct['qty']);

			if (oledrion_utils::getModuleOption('shipping_quantity')) {
				$discountedShipping = floatval($cartProduct['product'] -> getVar('product_shipping_price', 'n') * $quantity);
			} else {
				$discountedShipping = floatval($cartProduct['product'] -> getVar('product_shipping_price', 'n'));
			}
			$totalPrice = 0.0;
			$reduction = '';

			$cpt++;
			if (($cpt == $caddyCount)) {// On arrive sur le dernier produit
				$category = null;
				$category = $this -> handlers -> h_oledrion_cat -> get($cartProduct['product'] -> getVar('product_cid'));
				if (is_object($category)) {
					$goOn = $category -> getLink();
				}
			}

			// Boucle sur les règles
			foreach ($this->allActiveRules as $rule) {
				$applyRule = false;
				if (($rule -> disc_group != 0 && oledrion_utils::isMemberOfGroup($rule -> disc_group)) || $rule -> disc_group == 0) {
					if (($rule -> disc_cat_cid != 0 && $cartProduct['product'] -> getVar('product_cid') == $rule -> disc_cat_cid) || $rule -> disc_cat_cid == 0) {
						if (($rule -> disc_vendor_id != 0 && $cartProduct['product'] -> getVar('disc_vendor_id') == $rule -> disc_vendor_id) || $rule -> disc_vendor_id == 0) {
							if (($rule -> disc_product_id != 0 && $cartProduct['product'] -> getVar('product_id') == $rule -> disc_product_id) || $rule -> disc_product_id == 0) {
								// Dans quel cas appliquer la réduction ?
								switch($rule->disc_price_case) {
									case OLEDRION_DISCOUNT_PRICE_CASE_ALL :
										// Dans tous les cas
										$applyRule = true;
										break;
									case OLEDRION_DISCOUNT_PRICE_CASE_FIRST_BUY :
										// Si c'est le premier achat de l'utilisateur sur le site
										if ($this -> handlers -> h_oledrion_commands -> isFirstCommand()) {
											$applyRule = true;
										}
										break;
									case OLEDRION_DISCOUNT_PRICE_CASE_PRODUCT_NEVER :
										// Si le produit n'a jamais été acheté par le client
										if (!$this -> handlers -> h_oledrion_commands -> productAlreadyBought(0, $cartProduct['product'] -> getVar('product_id'))) {
											$applyRule = true;
										}
										break;
									case OLEDRION_DISCOUNT_PRICE_CASE_QTY_IS :
										// Si la quantité de produit est ... à ...
										switch($rule->disc_price_case_qty_cond) {
											case OLEDRION_DISCOUNT_PRICE_QTY_COND1 :
												// >
												if ($cartProduct['qty'] > $rule -> disc_price_case_qty_value) {
													$applyRule = true;
												}
												break;
											case OLEDRION_DISCOUNT_PRICE_QTY_COND2 :
												// >=
												if ($cartProduct['qty'] >= $rule -> disc_price_case_qty_value) {
													$applyRule = true;
												}
												break;
											case OLEDRION_DISCOUNT_PRICE_QTY_COND3 :
												// <
												if ($cartProduct['qty'] < $rule -> disc_price_case_qty_value) {
													$applyRule = true;
												}
												break;
											case OLEDRION_DISCOUNT_PRICE_QTY_COND4 :
												// <=
												if ($cartProduct['qty'] <= $rule -> disc_price_case_qty_value) {
													$applyRule = true;
												}
												break;
											case OLEDRION_DISCOUNT_PRICE_QTY_COND5 :
												// ==
												if ($cartProduct['qty'] == $rule -> disc_price_case_qty_value) {
													$applyRule = true;
												}
												break;
										}
								}
							}
						}
					}
				}
				if ($applyRule) {// Il faut appliquer la règle
					// On calcule le nouveau prix ht du produit
					switch($rule->disc_price_type) {
						case OLEDRION_DISCOUNT_PRICE_TYPE1 :
							// Montant dégressif selon les quantités
							if ($quantity >= $rule -> disc_price_degress_l1qty1 && $quantity <= $rule -> disc_price_degress_l1qty2) {
								$discountedPrice = floatval($rule -> getVar('disc_price_degress_l1total', 'n'));
							}
							if ($quantity >= $rule -> disc_price_degress_l2qty1 && $quantity <= $rule -> disc_price_degress_l2qty2) {
								$discountedPrice = floatval($rule -> getVar('disc_price_degress_l2total', 'n'));
							}
							if ($quantity >= $rule -> disc_price_degress_l3qty1 && $quantity <= $rule -> disc_price_degress_l3qty2) {
								$discountedPrice = floatval($rule -> getVar('disc_price_degress_l3total', 'n'));
							}
							if ($quantity >= $rule -> disc_price_degress_l4qty1 && $quantity <= $rule -> disc_price_degress_l4qty2) {
								$discountedPrice = floatval($rule -> getVar('disc_price_degress_l4total', 'n'));
							}
							if ($quantity >= $rule -> disc_price_degress_l5qty1 && $quantity <= $rule -> disc_price_degress_l5qty2) {
								$discountedPrice = floatval($rule -> getVar('disc_price_degress_l5total', 'n'));
							}
							$reduction = $rule -> disc_description;
							$discountsCount++;
							break;

						case OLEDRION_DISCOUNT_PRICE_TYPE2 :
							// D'un montant ou d'un pourcentage
							if ($rule -> disc_price_amount_on == OLEDRION_DISCOUNT_PRICE_AMOUNT_ON_PRODUCT) {// Réduction sur le produit
								if ($rule -> disc_price_amount_type == OLEDRION_DISCOUNT_PRICE_REDUCE_PERCENT) {// Réduction en pourcentage
									$discountedPrice = $this -> getDiscountedPrice($discountedPrice, $rule -> getVar('disc_price_amount_amount', 'n'));
								} elseif ($rule -> disc_price_amount_type == OLEDRION_DISCOUNT_PRICE_REDUCE_MONEY) {// Réduction d'un montant en euros
									$discountedPrice -= floatval($rule -> getVar('disc_price_amount_amount', 'n'));
								}

								// Pas de montants négatifs
								oledrion_utils::doNotAcceptNegativeAmounts($discountedPrice);
								$reduction = $rule -> disc_description;
								$discountsCount++;
							} elseif ($rule -> disc_price_amount_on == OLEDRION_DISCOUNT_PRICE_AMOUNT_ON_CART) {// Règle à appliquer sur le panier
								if (!isset($this -> rulesForTheWhole[$rule -> disc_id])) {
									$this -> rulesForTheWhole[$rule -> disc_id] = $rule;
								}
							}
							break;
					}

					// On passe au montant des frais de port
					switch($rule->disc_shipping_type) {
						case OLEDRION_DISCOUNT_SHIPPING_TYPE1 :
							// A payer dans leur intégralité, rien à faire
							break;
						case OLEDRION_DISCOUNT_SHIPPING_TYPE2 :
							// Totalement gratuits si le client commande plus de X euros d'achat
							if ($this -> totalAmountBeforeDiscounts > $rule -> disc_shipping_free_morethan) {
								$discountedShipping = 0.0;
							}
							break;
						case OLEDRION_DISCOUNT_SHIPPING_TYPE3 :
							// Frais de port réduits de X euros si la commande est > x
							if ($this -> totalAmountBeforeDiscounts > $rule -> disc_shipping_reduce_cartamount) {
								$discountedShipping -= floatval($rule -> getVar('disc_shipping_reduce_amount', 'n'));
							}
							// Pas de montants négatifs
							oledrion_utils::doNotAcceptNegativeAmounts($discountedShipping);
							break;
						case OLEDRION_DISCOUNT_SHIPPING_TYPE4 :
							// Frais de port dégressifs
							if ($quantity >= $rule -> disc_shipping_degress_l1qty1 && $quantity <= $rule -> disc_shipping_degress_l1qty2) {
								$discountedShipping = floatval($rule -> getVar('disc_shipping_degress_l1total', 'n'));
							}
							if ($quantity >= $rule -> disc_shipping_degress_l2qty1 && $quantity <= $rule -> disc_shipping_degress_l2qty2) {
								$discountedShipping = floatval($rule -> getVar('disc_shipping_degress_l2total', 'n'));
							}
							if ($quantity >= $rule -> disc_shipping_degress_l3qty1 && $quantity <= $rule -> disc_shipping_degress_l3qty2) {
								$discountedShipping = floatval($rule -> getVar('disc_shipping_degress_l3total', 'n'));
							}
							if ($quantity >= $rule -> disc_shipping_degress_l4qty1 && $quantity <= $rule -> disc_shipping_degress_l4qty2) {
								$discountedShipping = floatval($rule -> getVar('disc_shipping_degress_l4total', 'n'));
							}
							if ($quantity >= $rule -> disc_shipping_degress_l5qty1 && $quantity <= $rule -> disc_shipping_degress_l5qty2) {
								$discountedShipping = floatval($rule -> getVar('disc_shipping_degress_l5total', 'n'));
							}
							break;
					}	// Sélection du type de réduction sur les frais de port
				}	// Il faut appliquer la règle de réduction
			}// Boucle sur les réductions

			// Calcul de la TVA du produit
			$vatId = $cartProduct['product'] -> getVar('product_vat_id');
			if (is_array($vats) && isset($vats[$vatId])) {
				$vatRate = floatval($vats[$vatId] -> getVar('vat_rate', 'n'));
				$vatAmount = oledrion_utils::getVAT(($discountedPrice * $quantity), $vatRate);
			} else {
				$vatRate = 0.0;
				$vatAmount = 0.0;
			}

			// Calcul du TTC du produit ((ht * qte) + tva + frais de port)
			$totalPrice = floatval(($discountedPrice * $quantity) + $vatAmount + $discountedShipping);

			// Les totaux généraux
			$totalHT += ($discountedPrice * $quantity);
			$totalVAT += $vatAmount;
			$totalShipping += $discountedShipping;

			// Recherche des éléments associés au produit
			$associatedVendor = $associatedCategory = $associatedManufacturers = array();
			$manufacturersJoinList = '';
			// Le vendeur
			if (isset($this -> associatedVendors[$cartProduct['product'] -> product_vendor_id])) {
				$associatedVendor = $this -> associatedVendors[$cartProduct['product'] -> product_vendor_id] -> toArray();
			}

			// La catégorie
			if (isset($this -> associatedCategories[$cartProduct['product'] -> product_cid])) {
				$associatedCategory = $this -> associatedCategories[$cartProduct['product'] -> product_cid] -> toArray();
			}

			// Les fabricants
			$product_id = $cartProduct['product'] -> product_id;
			if (isset($this -> associatedManufacturersPerProduct[$product_id])) {// Recherche de la liste des fabricants associés à ce produit
				$manufacturers = $this -> associatedManufacturersPerProduct[$product_id];
				$manufacturersList = array();
				foreach ($manufacturers as $manufacturer_id) {
					if (isset($this -> associatedManufacturers[$manufacturer_id]))
						$associatedManufacturers[] = $this -> associatedManufacturers[$manufacturer_id] -> toArray();
					$manufacturersList[] = $this -> associatedManufacturers[$manufacturer_id] -> manu_commercialname . ' ' . $this -> associatedManufacturers[$manufacturer_id] -> manu_name;
				}
				$manufacturersJoinList = implode(OLEDRION_STRING_TO_JOIN_MANUFACTURERS, $manufacturersList);

			}
			$productTemplate = array();
			$productTemplate = $cartProduct['product'] -> toArray();
			$productTemplate['attributes'] = $productAttributes;
			$productTemplate['number'] = $cartProduct['number'];
			$productTemplate['id'] = $cartProduct['id'];
			$productTemplate['product_qty'] = $cartProduct['qty'];

			$productTemplate['unitBasePrice'] = $ht;
			// Prix unitaire HT SANS réduction
			$productTemplate['discountedPrice'] = $discountedPrice;
			// Prix unitaire HT AVEC réduction
			$productTemplate['discountedPriceWithQuantity'] = $discountedPrice * $quantity;
			// Prix HT AVEC réduction et la quantité
			// Les même prix mais formatés
			$productTemplate['unitBasePriceFormated'] = $oledrion_Currency -> amountForDisplay($ht);
			// Prix unitaire HT SANS réduction
			$productTemplate['discountedPriceFormated'] = $oledrion_Currency -> amountForDisplay($discountedPrice);
			// Prix unitaire HT AVEC réduction
			$productTemplate['discountedPriceWithQuantityFormated'] = $oledrion_Currency -> amountForDisplay($discountedPrice * $quantity);
			// Prix HT AVEC réduction et la quantité

			// Add by voltan
			$productTemplate['discountedPriceFormatedOrg'] = $oledrion_Currency -> amountForDisplay($ht - $discountedPrice);
			$productTemplate['discountedPriceOrg'] = $ht - $discountedPrice;

			$productTemplate['vatRate'] = $oledrion_Currency -> amountInCurrency($vatRate);
			$productTemplate['vatAmount'] = $vatAmount;
			$productTemplate['normalShipping'] = $cartProduct['product'] -> getVar('product_shipping_price', 'n');
			$productTemplate['discountedShipping'] = $discountedShipping;
			$productTemplate['totalPrice'] = $totalPrice;
			$productTemplate['reduction'] = $reduction;
			$productTemplate['templateProduct'] = $cartProduct['product'] -> toArray();

			$productTemplate['vatAmountFormated'] = $oledrion_Currency -> amountInCurrency($vatAmount);
			$productTemplate['normalShippingFormated'] = $oledrion_Currency -> amountForDisplay($cartProduct['product'] -> getVar('product_shipping_price', 'n'));
			$productTemplate['discountedShippingFormated'] = $oledrion_Currency -> amountForDisplay($discountedShipping);
			$productTemplate['totalPriceFormated'] = $oledrion_Currency -> amountInCurrency($totalPrice);
			$productTemplate['templateCategory'] = $associatedCategory;
			$productTemplate['templateVendor'] = $associatedVendor;
			$productTemplate['templateManufacturers'] = $associatedManufacturers;
			$productTemplate['manufacturersJoinList'] = $manufacturersJoinList;
			$this -> cartForTemplate[] = $productTemplate;
		}// foreach sur les produits du panier

		// Traitement des règles générales s'il y en a
		if (count($this -> rulesForTheWhole) > 0) {
			// $discountsDescription
			foreach ($this->rulesForTheWhole as $rule) {
				switch($rule->disc_price_type) {
					case OLEDRION_DISCOUNT_PRICE_TYPE2 :
						// D'un montant ou d'un pourcentage
						if ($rule -> disc_price_amount_on == OLEDRION_DISCOUNT_PRICE_AMOUNT_ON_CART) {// Règle à appliquer sur le panier
							if ($rule -> disc_price_amount_type == OLEDRION_DISCOUNT_PRICE_REDUCE_PERCENT) {// Réduction en pourcentage
								$totalHT = $this -> getDiscountedPrice($totalHT, $rule -> getVar('disc_price_amount_amount'));
								$totalVAT = $this -> getDiscountedPrice($totalVAT, $rule -> getVar('disc_price_amount_amount'));
							} elseif ($rule -> disc_price_amount_type == OLEDRION_DISCOUNT_PRICE_REDUCE_MONEY) {// Réduction d'un montant en euros
								$totalHT -= floatval($rule -> getVar('disc_price_amount_amount'));
								$totalVAT -= floatval($rule -> getVar('disc_price_amount_amount'));
							}

							// Pas de montants négatifs
							oledrion_utils::doNotAcceptNegativeAmounts($totalHT);
							oledrion_utils::doNotAcceptNegativeAmounts($totalVAT);
							$discountsDescription[] = $rule -> disc_description;
							$discountsCount++;
						}// Règle à appliquer sur le panier
						break;
				}	// Switch
			}	// Foreach
		}// S'il y a des règles globales
		// Les totaux "renvoyés" à l'appelant
		$shippingAmount = $totalShipping;
		$commandAmount = $totalHT;

		$vatAmount = $totalVAT;
		$commandAmountTTC = $totalHT + $totalVAT + $totalShipping;

		$cartForTemplate = $this -> cartForTemplate;
		return true;
	}

}
