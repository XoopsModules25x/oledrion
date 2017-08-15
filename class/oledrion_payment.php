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
 * Class Oledrion_payment
 */
class Oledrion_payment extends Oledrion_Object
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
        $this->initVar('payment_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('payment_title', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('payment_description', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('payment_online', XOBJ_DTYPE_INT, null, false);
        $this->initVar('payment_type', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('payment_gateway', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('payment_image', XOBJ_DTYPE_TXTBOX, null, false);
    }

    /**
     * Retourne l'URL de l'image de la catégorie courante
     * @return string L'URL
     */
    public function getPictureUrl()
    {
        if (xoops_trim($this->getVar('product_image_url')) !== '') {
            return OLEDRION_PICTURES_URL . '/' . $this->getVar('payment_image');
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
        if (xoops_trim($this->getVar('payment_image')) !== ''
            && file_exists(OLEDRION_PICTURES_PATH . '/' . $this->getVar('payment_image'))) {
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
            @unlink(OLEDRION_PICTURES_PATH . '/' . $this->getVar('payment_image'));
        }
        $this->setVar('payment_image', '');
    }

    /**
     * Retourne les éléments du produits formatés pour affichage
     *
     * @param  string $format
     * @return array
     */
    public function toArray($format = 's')
    {
        $ret                      = [];
        $ret                      = parent::toArray($format);
        $ret['payment_image_url'] = $this->getPictureUrl();

        return $ret;
    }
}

/**
 * Class OledrionOledrion_paymentHandler
 */
class OledrionOledrion_paymentHandler extends Oledrion_XoopsPersistableObjectHandler
{
    /**
     * OledrionOledrion_paymentHandler constructor.
     * @param XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    { //                                       Table                    Classe              Id
        parent::__construct($db, 'oledrion_payment', 'oledrion_payment', 'payment_id');
    }

    /**
     * @param  Oledrion_parameters $parameters
     * @return array
     */
    public function getAllPayment(Oledrion_parameters $parameters)
    {
        $parameters = $parameters->extend(new Oledrion_parameters([
                                                                      'start' => 0,
                                                                      'limit' => 0,
                                                                      'sort'  => 'payment_id',
                                                                      'order' => 'ASC'
                                                                  ]));
        $critere    = new Criteria('payment_id', 0, '<>');
        $critere->setLimit($parameters['limit']);
        $critere->setStart($parameters['start']);
        $critere->setSort($parameters['sort']);
        $critere->setOrder($parameters['order']);
        $categories = [];
        $categories = $this->getObjects($critere);

        return $categories;
    }

    /**
     * @param $delivery_id
     * @return array
     */
    public function getThisDeliveryPayment($delivery_id)
    {
        global $h_oledrion_delivery_payment;
        $ret              = [];
        $parameters       = ['delivery' => $delivery_id];
        $delivery_payment = $h_oledrion_delivery_payment->getDeliveryPaymantId($parameters);
        foreach ($delivery_payment as $payment) {
            $id[] = $payment['dp_payment'];
        }

        $critere = new CriteriaCompo();
        $critere->add(new Criteria('payment_id', '(' . implode(',', $id) . ')', 'IN'));
        $critere->add(new Criteria('payment_online', 1));
        $obj = $this->getObjects($critere);
        if ($obj) {
            foreach ($obj as $root) {
                $tab   = [];
                $tab   = $root->toArray();
                $ret[] = $tab;
            }
        }

        return $ret;
    }
}
