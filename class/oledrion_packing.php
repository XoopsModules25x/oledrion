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
 * @copyright   {@link http://xoops.org/ XOOPS Project}
 * @license     {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author      Hossein Azizabadi (azizabadi@faragostaresh.com)
 */

require __DIR__ . '/classheader.php';

/**
 * Class Oledrion_packing
 */
class Oledrion_packing extends Oledrion_Object
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
        $this->initVar('packing_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('packing_title', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('packing_width', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('packing_length', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('packing_weight', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('packing_image', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('packing_description', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('packing_price', XOBJ_DTYPE_INT, null, false);
        $this->initVar('packing_online', XOBJ_DTYPE_INT, null, false);
    }

    /**
     * Retourne l'URL de l'image de la catégorie courante
     * @return string L'URL
     */
    public function getPictureUrl()
    {
        if (xoops_trim($this->getVar('product_image_url')) != '') {
            return OLEDRION_PICTURES_URL . '/' . $this->getVar('packing_image');
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
        if (xoops_trim($this->getVar('packing_image')) != ''
            && file_exists(OLEDRION_PICTURES_PATH . '/' . $this->getVar('packing_image'))
        ) {
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
            @unlink(OLEDRION_PICTURES_PATH . '/' . $this->getVar('packing_image'));
        }
        $this->setVar('packing_image', '');
    }

    /**
     * Retourne les éléments du produits formatés pour affichage
     *
     * @param  string $format
     * @return array
     */
    public function toArray($format = 's')
    {
        $oledrion_Currency               = Oledrion_Currency::getInstance();
        $ret                             = array();
        $ret                             = parent::toArray($format);
        $ret['packing_price_fordisplay'] = $oledrion_Currency->amountForDisplay($this->getVar('packing_price'));
        $ret['packing_image_url']        = $this->getPictureUrl();

        return $ret;
    }
}

/**
 * Class OledrionOledrion_packingHandler
 */
class OledrionOledrion_packingHandler extends Oledrion_XoopsPersistableObjectHandler
{
    /**
     * OledrionOledrion_packingHandler constructor.
     * @param XoopsDatabase|null $db
     */
    public function __construct(XoopsDatabase $db)
    { //                                       Table                    Classe              Id
        parent::__construct($db, 'oledrion_packing', 'oledrion_packing', 'packing_id');
    }

    /**
     * @param  Oledrion_parameters $parameters
     * @return array
     */
    public function getAllPacking(Oledrion_parameters $parameters)
    {
        $parameters = $parameters->extend(new Oledrion_parameters(array(
                                                                      'start' => 0,
                                                                      'limit' => 0,
                                                                      'sort'  => 'packing_id',
                                                                      'order' => 'ASC'
                                                                  )));
        $critere    = new Criteria('packing_id', 0, '<>');
        $critere->setLimit($parameters['limit']);
        $critere->setStart($parameters['start']);
        $critere->setSort($parameters['sort']);
        $critere->setOrder($parameters['order']);
        $packings = array();
        $packings = $this->getObjects($critere);

        return $packings;
    }

    /**
     * @return array
     */
    public function getPacking()
    {
        $ret     = array();
        $critere = new CriteriaCompo();
        $critere->add(new Criteria('packing_online', '1'));
        $packings = $this->getObjects($critere);
        foreach ($packings as $root) {
            $tab   = array();
            $tab   = $root->toArray();
            $ret[] = $tab;
        }

        return $ret;
    }
}
