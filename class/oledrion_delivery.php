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
 * @author      Hossein Azizabadi (azizabadi@faragostaresh.com)
 */

require_once __DIR__ . '/classheader.php';

/**
 * Class Oledrion_delivery
 */
class Oledrion_delivery extends Oledrion_Object
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
        $this->initVar('delivery_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('delivery_title', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('delivery_description', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('delivery_online', XOBJ_DTYPE_INT, null, false);
        $this->initVar('delivery_image', XOBJ_DTYPE_TXTBOX, null, false);

        $this->initVar('dohtml', XOBJ_DTYPE_INT, 1, false);
    }

    /**
     * Retourne l'URL de l'image de la catégorie courante
     * @return string L'URL
     */
    public function getPictureUrl()
    {
        if (xoops_trim($this->getVar('product_image_url')) != '') {
            return OLEDRION_PICTURES_URL . '/' . $this->getVar('delivery_image');
        } else {
            return '';
        }
    }

    /**
     * Indique si l'image de la catégorie existe
     *
     * @return boolean Vrai si l'image existe sinon faux
     */
    public function pictureExists()
    {
        $return = false;
        if (xoops_trim($this->getVar('delivery_image')) != ''
            && file_exists(OLEDRION_PICTURES_PATH . '/' . $this->getVar('delivery_image'))) {
            $return = true;
        }

        return $return;
    }

    /**
     * Supprime l'image associée à une catégorie
     * @return void
     */
    public function deletePicture()
    {
        if ($this->pictureExists()) {
            @unlink(OLEDRION_PICTURES_PATH . '/' . $this->getVar('delivery_image'));
        }
        $this->setVar('delivery_image', '');
    }

    /**
     * Retourne les éléments du produits formatés pour affichage
     *
     * @param  string $format
     * @return array
     */
    public function toArray($format = 's')
    {
        global $h_oledrion_location_delivery;
        $ret                       = array();
        $ret                       = parent::toArray($format);
        $ret['delivery_image_url'] = $this->getPictureUrl();

        return $ret;
    }
}

/**
 * Class OledrionOledrion_deliveryHandler
 */
class OledrionOledrion_deliveryHandler extends Oledrion_XoopsPersistableObjectHandler
{
    /**
     * OledrionOledrion_deliveryHandler constructor.
     * @param object $db
     */
    public function __construct($db)
    { //                                        Table                   Classe              Id
        parent::__construct($db, 'oledrion_delivery', 'oledrion_delivery', 'delivery_id');
    }

    /**
     * @param  Oledrion_parameters $parameters
     * @return array
     */
    public function getAllDelivery(Oledrion_parameters $parameters)
    {
        $parameters = $parameters->extend(new Oledrion_parameters(array(
                                                                      'start' => 0,
                                                                      'limit' => 0,
                                                                      'sort'  => 'delivery_id',
                                                                      'order' => 'ASC'
                                                                  )));
        $critere    = new Criteria('delivery_id', 0, '<>');
        $critere->setLimit($parameters['limit']);
        $critere->setStart($parameters['start']);
        $critere->setSort($parameters['sort']);
        $critere->setOrder($parameters['order']);
        $categories = array();
        $categories = $this->getObjects($critere);

        return $categories;
    }

    /**
     * @param  Oledrion_parameters $parameters
     * @return array
     */
    public function getLocationDelivery(Oledrion_parameters $parameters)
    {
        global $h_oledrion_location_delivery;
        $ret               = array();
        $parameters        = $parameters->extend(new Oledrion_parameters(array(
                                                                             'start'    => 0,
                                                                             'limit'    => 0,
                                                                             'sort'     => 'delivery_id',
                                                                             'order'    => 'ASC',
                                                                             'location' => ''
                                                                         )));
        $location_delivery = $h_oledrion_location_delivery->getLocationDeliveryId($parameters);

        $critere = new CriteriaCompo();
        $critere->setLimit($parameters['limit']);
        $critere->setStart($parameters['start']);
        $critere->setSort($parameters['sort']);
        $critere->setOrder($parameters['order']);
        $obj = $this->getObjects($critere);
        if ($obj) {
            foreach ($obj as $root) {
                $tab = array();
                $tab = $root->toArray();
                if (isset($location_delivery[$root->getVar('delivery_id')]['ld_delivery'])
                    && $location_delivery[$root->getVar('delivery_id')]['ld_delivery'] == $root->getVar('delivery_id')) {
                    $tab['ld_id']['delivery_select']  = 1;
                    $tab['ld_id']['ld_id']            = $location_delivery[$root->getVar('delivery_id')]['ld_id'];
                    $tab['ld_id']['ld_location']      = $location_delivery[$root->getVar('delivery_id')]['ld_location'];
                    $tab['ld_id']['ld_delivery']      = $location_delivery[$root->getVar('delivery_id')]['ld_delivery'];
                    $tab['ld_id']['ld_price']         = $location_delivery[$root->getVar('delivery_id')]['ld_price'];
                    $tab['ld_id']['ld_delivery_time'] = $location_delivery[$root->getVar('delivery_id')]['ld_delivery_time'];
                }
                $ret[] = $tab;
            }
        }

        return $ret;
    }

    /**
     * @param $location_id
     * @return array
     */
    public function getThisLocationDelivery($location_id)
    {
        global $h_oledrion_location_delivery;
        $oledrion_Currency = Oledrion_Currency::getInstance();
        $ret               = array();
        $parameters        = array('location' => $location_id);
        $location_delivery = $h_oledrion_location_delivery->getLocationDeliveryId($parameters);
        foreach ($location_delivery as $location) {
            $id[] = $location['ld_delivery'];
        }

        $critere = new CriteriaCompo();
        $critere->add(new Criteria('delivery_id', '(' . implode(',', $id) . ')', 'IN'));
        $critere->add(new Criteria('delivery_online', 1));
        $obj = $this->getObjects($critere);
        if ($obj) {
            foreach ($obj as $root) {
                $tab                              = array();
                $tab                              = $root->toArray();
                $tab['delivery_price']            = $location_delivery[$root->getVar('delivery_id')]['ld_price'];
                $tab['delivery_price_fordisplay'] = $oledrion_Currency->amountForDisplay($tab['delivery_price']);
                $tab['delivery_time']             = $location_delivery[$root->getVar('delivery_id')]['ld_delivery_time'];
                $ret[]                            = $tab;
            }
        }

        return $ret;
    }

    /**
     * @param $location_id
     * @param $delivery_id
     * @return array
     */
    public function getThisLocationThisDelivery($location_id, $delivery_id)
    {
        global $h_oledrion_location_delivery;
        $location_delivery     = $h_oledrion_location_delivery->getDelivery($location_id, $delivery_id);
        $ret                   = array();
        $obj                   = $this->get($location_id);
        $ret                   = $obj->toArray();
        $ret['delivery_price'] = $location_delivery['ld_price'];
        $ret['delivery_time']  = $location_delivery['ld_delivery_time'];

        return $ret;
    }
}
