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

use XoopsModules\Oledrion;

/**
 * Gestion des TVA
 */
require_once __DIR__ . '/classheader.php';


/**
 * Class VatHandler
 */
class VatHandler extends OledrionPersistableObjectHandler
{
    /**
     * VatHandler constructor.
     * @param \XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db)
    { //                        Table           Classe          Id
        parent::__construct($db, 'oledrion_vat', Vat::class, 'vat_id');
    }

    /**
     * Renvoie la liste de toutes les TVA du module
     *
     * @param  Parameters $parameters
     * @return array               tableau d'objets de type TVA
     * @internal param int $start Position de départ
     * @internal param int $limit Nombre total d'enregistrements à renvoyer
     * @internal param string $order Champ sur lequel faire le tri
     * @internal param string $order Ordre du tri
     * @internal param bool $idaskey Indique si le tableau renvoyé doit avoir pour clé l'identifiant unique de l'enregistrement
     */
    public function getAllVats(Parameters $parameters)
    {
        $parameters = $parameters->extend(new Oledrion\Parameters([
                                                                      'start'   => 0,
                                                                      'limit'   => 0,
                                                                      'sort'    => 'vat_id',
                                                                      'order'   => 'ASC',
                                                                      'idaskey' => true
                                                                  ]));
        $critere    = new \Criteria('vat_id', 0, '<>');
        $critere->setLimit($parameters['limit']);
        $critere->setStart($parameters['start']);
        $critere->setSort($parameters['sort']);
        $critere->setOrder($parameters['order']);
        $vats = [];
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
        $parameters = new Oledrion\Parameters([
                                                  'start'   => 0,
                                                  'limit'   => 0,
                                                  'sort'    => 'vat_id',
                                                  'order'   => 'ASC',
                                                  'idaskey' => true
                                              ]);
        $critere    = new \CriteriaCompo();
        if (!empty($country)) {
            $critere->add(new \Criteria('vat_country', $country, 'LIKE'));
        }
        $critere->setLimit($parameters['limit']);
        $critere->setStart($parameters['start']);
        $critere->setSort($parameters['sort']);
        $critere->setOrder($parameters['order']);
        $vats = [];
        $vats = $this->getObjects($critere, $parameters['idaskey']);

        return $vats;
    }

    /**
     * Suppression d'une TVA
     *
     * @param  Vat $vat
     * @return boolean      Le résultat de la suppressin
     */
    public function deleteVat(Vat $vat)
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
        global $productsHandler;

        return $productsHandler->getVatProductsCount($vat_id);
    }
}
