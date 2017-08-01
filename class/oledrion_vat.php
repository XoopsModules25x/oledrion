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
 * Gestion des TVA
 */
require_once __DIR__ . '/classheader.php';

/**
 * Class Oledrion_vat
 */
class Oledrion_vat extends Oledrion_Object
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
        $this->initVar('vat_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('vat_rate', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('vat_country', XOBJ_DTYPE_TXTBOX, null, false);
    }

    /**
     * @param  string $format
     * @return array
     */
    public function toArray($format = 's')
    {
        $ret                      = array();
        $ret                      = parent::toArray($format);
        $oledrion_Currency        = Oledrion_Currency::getInstance();
        $ret['vat_rate_formated'] = $oledrion_Currency->amountInCurrency((float)$this->getVar('vat_rate', 'e'));

        return $ret;
    }
}

/**
 * Class OledrionOledrion_vatHandler
 */
class OledrionOledrion_vatHandler extends Oledrion_XoopsPersistableObjectHandler
{
    /**
     * OledrionOledrion_vatHandler constructor.
     * @param XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    { //                        Table           Classe          Id
        parent::__construct($db, 'oledrion_vat', 'oledrion_vat', 'vat_id');
    }

    /**
     * Renvoie la liste de toutes les TVA du module
     *
     * @param  Oledrion_parameters $parameters
     * @return array               tableau d'objets de type TVA
     * @internal param int $start Position de départ
     * @internal param int $limit Nombre total d'enregistrements à renvoyer
     * @internal param string $order Champ sur lequel faire le tri
     * @internal param string $order Ordre du tri
     * @internal param bool $idaskey Indique si le tableau renvoyé doit avoir pour clé l'identifiant unique de l'enregistrement
     */
    public function getAllVats(Oledrion_parameters $parameters)
    {
        $parameters = $parameters->extend(new Oledrion_parameters(array(
                                                                      'start'   => 0,
                                                                      'limit'   => 0,
                                                                      'sort'    => 'vat_id',
                                                                      'order'   => 'ASC',
                                                                      'idaskey' => true
                                                                  )));
        $critere    = new Criteria('vat_id', 0, '<>');
        $critere->setLimit($parameters['limit']);
        $critere->setStart($parameters['start']);
        $critere->setSort($parameters['sort']);
        $critere->setOrder($parameters['order']);
        $vats = array();
        $vats = $this->getObjects($critere, $parameters['idaskey']);

        return $vats;
    }

    /**
     * Renvoie la liste de toutes les TVA du module
     *
     * @param $country
     * @return array tableau d'objets de type TVA
     * @internal param int $start Position de départ
     * @internal param int $limit Nombre total d'enregistrements à renvoyer
     * @internal param string $order Champ sur lequel faire le tri
     * @internal param string $order Ordre du tri
     * @internal param bool $idaskey Indique si le tableau renvoyé doit avoir pour clé l'identifiant unique de l'enregistrement
     */
    public function getCountryVats($country)
    {
        $parameters = new Oledrion_parameters(array(
                                                  'start'   => 0,
                                                  'limit'   => 0,
                                                  'sort'    => 'vat_id',
                                                  'order'   => 'ASC',
                                                  'idaskey' => true
                                              ));
        $critere    = new CriteriaCompo();
        if (!empty($country)) {
            $critere->add(new Criteria('vat_country', $country, 'LIKE'));
        }
        $critere->setLimit($parameters['limit']);
        $critere->setStart($parameters['start']);
        $critere->setSort($parameters['sort']);
        $critere->setOrder($parameters['order']);
        $vats = array();
        $vats = $this->getObjects($critere, $parameters['idaskey']);

        return $vats;
    }

    /**
     * Suppression d'une TVA
     *
     * @param  oledrion_vat $vat
     * @return boolean      Le résultat de la suppressin
     */
    public function deleteVat(oledrion_vat $vat)
    {
        return $this->delete($vat, true);
    }

    /**
     * Retourne le nombre de produits associés à une TVA
     *
     * @param  integer $vat_id L'ID de la TVA
     * @return integer Le nombre de produits
     */
    public function getVatProductsCount($vat_id)
    {
        global $h_oledrion_products;

        return $h_oledrion_products->getVatProductsCount($vat_id);
    }
}
