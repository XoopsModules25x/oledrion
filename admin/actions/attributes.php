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
 * Gestion des attributs produits
 */

use XoopsModules\Oledrion;
use XoopsModules\Oledrion\Constants;

if (!defined('OLEDRION_ADMIN')) {
    exit();
}

//require_once  dirname(dirname(__DIR__)) . '/class/Attributes.php';

global $baseurl; // Pour faire taire les warnings de Zend Studio
$operation = 'attributes';

//global $xoopsDB;
//$attributesHandler       = new Oledrion\AttributesHandler($xoopsDB);

/**
 * Suppression de l'attribut qui se trouve en session
 */
function removeAttributInSession()
{
    if (\Xmf\Request::hasVar('oledrion_attribute', 'SESSION')) {
        $_SESSION['oledrion_attribute'] = null;
        unset($_SESSION['oledrion_attribute']);
    }
}

switch ($action) {
    // ****************************************************************************************************************
    case 'default': // Liste des attributs produits

        // ****************************************************************************************************************
        xoops_cp_header();

        $adminObject = \Xmf\Module\Admin::getInstance();

        global $xoopsConfig;
        $productsIds = $products = $productsIdsForList = $productsForList = [];
        $class       = '';
        removeAttributInSession();

        $form = "<form method='post' action='$baseurl' name='frmadd$operation' id='frmadd$operation'><input type='hidden' name='op' id='op' value='$operation'><input type='hidden' name='action' id='action' value='add'><input type='submit' name='btngo' id='btngo' value='"
                . _AM_OLEDRION_ADD_ITEM
                . "'></form>";
        echo $form;

        //        Oledrion\Utility::htitle(_AM_OLEDRION_ATTRIBUTES_LIST, 4);

        $start    = \Xmf\Request::getInt('start', 0, 'GET');
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('attribute_id', 0, '<>'));

        $filter_attribute_id    = $filter_attribute_id = $filter_attribute_weight = $filter_attribute_type = $filter_attribute_product_id = 0;
        $filter_attribute_title = '';

        $newFilter = false;

        if (\Xmf\Request::hasVar('filter_attribute_id', 'POST')) {
            if (0 !== \Xmf\Request::getInt('filter_attribute_id', 0, 'POST')) {
                $criteria->add(new \Criteria('attribute_id', \Xmf\Request::getInt('filter_attribute_id', 0, 'POST')), '=');
            }
            $filter_attribute_id = \Xmf\Request::getInt('filter_attribute_id', 0, 'POST');
            $newFilter           = true;
        }
        if (\Xmf\Request::hasVar('filter_attribute_title', 'POST') && '' !== xoops_trim($_POST['filter_attribute_title'])) {
            $criteria->add(new \Criteria('attribute_title', '%' . $_POST['filter_attribute_title'] . '%', 'LIKE'));
            $filter_attribute_title = $_POST['filter_attribute_title'];
            $newFilter              = true;
        }
        if (\Xmf\Request::hasVar('filter_attribute_product_id', 'POST') && 0 !== \Xmf\Request::getInt('filter_attribute_product_id', 0, 'POST')) {
            $criteria->add(new \Criteria('attribute_product_id', \Xmf\Request::getInt('filter_attribute_product_id', 0, 'POST')), '=');
            $filter_attribute_product_id = \Xmf\Request::getInt('filter_attribute_product_id', 0, 'POST');
            $newFilter                   = true;
        }
        if (\Xmf\Request::hasVar('filter_attribute_weight', 'POST') && 0 !== \Xmf\Request::getInt('filter_attribute_weight', 0, 'POST')) {
            $criteria->add(new \Criteria('attribute_weight', \Xmf\Request::getInt('filter_attribute_weight', 0, 'POST')), '=');
            $filter_attribute_weight = \Xmf\Request::getInt('filter_attribute_weight', 0, 'POST');
            $newFilter               = true;
        }
        if (\Xmf\Request::hasVar('filter_attribute_type', 'POST') && 0 !== \Xmf\Request::getInt('filter_attribute_type', 0, 'POST')) {
            $criteria->add(new \Criteria('attribute_type', \Xmf\Request::getInt('filter_attribute_type', 0, 'POST')), '=');
            $filter_attribute_type = \Xmf\Request::getInt('filter_attribute_type', 0, 'POST');
            $newFilter             = true;
        }

        if (0 === $filter_attribute_id && '' === $filter_attribute_title && 0 === $filter_attribute_weight && 0 === $filter_attribute_type) {
            $newFilter = true;
        }

        if (!$newFilter && isset($_SESSION['oledrion_filter_attributes'])) {
            $criteria                    = unserialize($_SESSION['oledrion_filter_attributes']);
            $filter_attribute_id         = $_SESSION['filter_attribute_id'];
            $filter_attribute_title      = $_SESSION['filter_attribute_title'];
            $filter_attribute_product_id = $_SESSION['filter_attribute_product_id'];
            $filter_attribute_weight     = $_SESSION['filter_attribute_weight'];
            $filter_attribute_type       = $_SESSION['filter_attribute_type'];
        }

        $_SESSION['oledrion_filter_attributes']  = serialize($criteria);
        $_SESSION['filter_attribute_id']         = $filter_attribute_id;
        $_SESSION['filter_attribute_title']      = $filter_attribute_title;
        $_SESSION['filter_attribute_product_id'] = $filter_attribute_product_id;
        $_SESSION['filter_attribute_weight']     = $filter_attribute_weight;
        $_SESSION['filter_attribute_type']       = $filter_attribute_type;

        $itemsCount = $attributesHandler->getCount($criteria);
        if ($itemsCount > $limit) {
            $pagenav = new \XoopsPageNav($itemsCount, $limit, $start, 'start', 'op=' . $operation);
        }

        $criteria->setLimit($limit);
        $criteria->setStart($start);
        $criteria->setSort('attribute_product_id, attribute_weight');
        $items = $attributesHandler->getObjects($criteria);
        if (count($items) > 0) {
            foreach ($items as $item) {
                if (!isset($productsIds[$item->getVar('attribute_product_id')])) {
                    $productsIds[] = $item->getVar('attribute_product_id');
                }
            }
            if (count($productsIds) > 0) {
                $products = $productsHandler->getProductsFromIDs($productsIds, true);
            }
        }
        $typeSelect = Oledrion\Utility::htmlSelect('filter_attribute_type', [
            Constants::OLEDRION_ATTRIBUTE_RADIO    => _AM_OLEDRION_TYPE_RADIO,
            Constants::OLEDRION_ATTRIBUTE_CHECKBOX => _AM_OLEDRION_TYPE_CHECKBOX,
            Constants::OLEDRION_ATTRIBUTE_SELECT   => _AM_OLEDRION_TYPE_LIST,
        ], $filter_attribute_type);

        $productsIdsForList = $attributesHandler->getDistinctsProductsIds();
        if (count($productsIdsForList) > 0) {
            $productsForList = $productsHandler->getList(new \Criteria('product_id', '(' . implode(',', $productsIdsForList) . ')', 'IN'));
        }
        $selectProduct = Oledrion\Utility::htmlSelect('filter_attribute_product_id', $productsForList, $filter_attribute_product_id);

        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        if (isset($pagenav) && is_object($pagenav)) {
            echo "<tr><td colspan='2' align='left'>" . $pagenav->renderNav() . "</td><td align='right' colspan='3'>&nbsp;</td></tr>\n";
        }
        echo "<tr><th align='center'>"
             . _AM_OLEDRION_ID
             . "</th><th align='center'>"
             . _AM_OLEDRION_TITLE
             . "</th><th align='center'>"
             . _AM_OLEDRION_DISCOUNT_PRODUCT
             . "</th><th align='center'>"
             . _AM_OLEDRION_WEIGHT
             . "</th><th align='center'>"
             . _AM_OLEDRION_TYPE
             . "</th><th align='center'>"
             . _AM_OLEDRION_ACTION
             . '</th></tr>';

        // Les filtres
        echo "<tr><form method='post' action='$baseurl'><th align='center'><input type='text' size='3' name='filter_attribute_id' id='filter_attribute_id' value='$filter_attribute_id'></th>\n";
        echo "<th align='center'><input type='text' size='25' name='filter_attribute_title' id='filter_attribute_title' value='$filter_attribute_title'></th>\n";
        echo "<th align='center'>" . $selectProduct . "</th>\n";
        echo "<th align='center'><input type='text' size='5' name='filter_attribute_weight' id='filter_attribute_weight' value='$filter_attribute_weight'></th>\n";
        echo "<th align='center'>" . $typeSelect . "</th>\n";
        echo "<th align='center'><input type='hidden' name='op' id='op' value='attributes'><input type='submit' name='btngo' id='btngo' value='" . _GO . "'></th></form></tr>\n";

        foreach ($items as $item) {
            $class        = ('even' === $class) ? 'odd' : 'even';
            $id           = $item->getVar('attribute_id');
            $actions      = [];
            $actions[]    = "<a href='$baseurl?op=$operation&action=edit&id=" . $id . "' title='" . _OLEDRION_EDIT . "'>" . $icons['edit'] . '</a>';
            $actions[]    = "<a href='$baseurl?op=$operation&action=delete&id=" . $id . "' title='" . _OLEDRION_DELETE . "'" . $conf_msg . '>' . $icons['delete'] . '</a>';
            $actions[]    = "<a href='$baseurl?op=$operation&action=copy&id=" . $id . "' title='" . _OLEDRION_DUPLICATE_ATTRIBUTE . "'>" . $icons['copy'] . '</a>';
            $productTitle = isset($products[$item->getVar('attribute_product_id')]) ? $products[$item->getVar('attribute_product_id')]->getVar('product_title') : '';
            $productLink  = isset($products[$item->getVar('attribute_product_id')]) ? $products[$item->getVar('attribute_product_id')]->getLink() : '';
            echo "<tr class='" . $class . "'>\n";
            echo "<td align='right'>" . $item->attribute_id . '</a></td>';
            echo "<td align='left'><a target='_blank' href='" . $productLink . "'>" . $item->attribute_title . '</a></td>';
            $urlProductEdit = $baseurl . '?op=products&action=edit&id=' . $item->getVar('attribute_product_id');

            echo "<td align='center'><a  title='" . _EDIT . "' href='" . $urlProductEdit . "'><img src='" . OLEDRION_IMAGES_URL . "smalledit.png'> " . $productTitle . '</a></td>';
            echo "<td align='center'>" . $item->attribute_weight . "</td>\n";
            echo "<td align='center'>" . $item->getTypeName() . "</td>\n";
            echo "<td align='center'>" . implode(' ', $actions) . "</td>\n";
            echo "<tr>\n";
        }

        $adminObject->displayNavigation('index.php?op=attributes');

        $class = ('even' === $class) ? 'odd' : 'even';
        echo "<tr class='" . $class . "'>\n";
        echo "<td colspan='6' align='center'>" . $form . "</td>\n";
        echo "</tr>\n";
        echo '</table>';
        if (isset($pagenav) && is_object($pagenav)) {
            echo "<div align='right'>" . $pagenav->renderNav() . '</div>';
        }
        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';

        break;
    // ****************************************************************************************************************
    case 'copy': // Dupliquer un attribut

        // ****************************************************************************************************************
        xoops_cp_header();
        $id = \Xmf\Request::getInt('id', 0, 'GET');
        if (empty($id)) {
            Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $attribute = null;
        $attribute = $attributesHandler->get($id);
        if (is_object($attribute)) {
            $newAttribute   = $attributesHandler->cloneAttribute($attribute);
            $newAttributeId = $newAttribute->attribute_id;
            if (false !== $newAttribute) {
                Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $operation . '&action=edit&id=' . $newAttributeId, 2);
            } else {
                Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $operation, 5);
            }
        }

        break;
    // ****************************************************************************************************************
    case 'delete': // Suppression d'un attribut

        // ****************************************************************************************************************
        xoops_cp_header();
        $id = \Xmf\Request::getInt('id', 0, 'GET');
        if (empty($id)) {
            Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $attribute = null;
        $attribute = $attributesHandler->get($id);
        if (!is_object($attribute)) {
            Oledrion\Utility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl . '?op=' . $operation, 5);
        }
        $attributesCountInCaddy = $caddyAttributesHandler->getCaddyCountFromAttributeId($id);
        if (0 == $attributesCountInCaddy) {
            $res = $attributesHandler->deleteAttribute($attribute);
            if ($res) {
                Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $operation, 2);
            } else {
                Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $operation, 5);
            }
        } else {
            Oledrion\Utility::htitle(_AM_OLEDRION_SORRY_NOREMOVE2, 4);
            $tblTmp  = $caddyAttributesHandler->getCommandIdFromAttribute($id);
            $tblTmp2 = $commandsHandler->getObjects(new \Criteria('cmd_id', '(' . implode(',', $tblTmp) . ')', 'IN'), true);
            echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
            $class = '';
            echo "<tr><th align='center'>" . _AM_OLEDRION_ID . "</th><th align='center'>" . _AM_OLEDRION_DATE . "</th><th align='center'>" . _AM_OLEDRION_CLIENT . "</th><th align='center'>" . _AM_OLEDRION_TOTAL_SHIPP . '</th></tr>';
            foreach ($tblTmp2 as $item) {
                $class = ('even' === $class) ? 'odd' : 'even';
                $date  = formatTimestamp(strtotime($item->getVar('cmd_date')), 's');
                echo "<tr class='" . $class . "'>\n";
                echo "<td align='right'>"
                     . $item->getVar('cmd_id')
                     . "</td><td align='center'>"
                     . $date
                     . "</td><td align='center'>"
                     . $item->getVar('cmd_lastname')
                     . ' '
                     . $item->getVar('cmd_firstname')
                     . "</td><td align='center'>"
                     . $item->getVar('cmd_total')
                     . ' '
                     . Oledrion\Utility::getModuleOption('money_short')
                     . ' / '
                     . $item->getVar('cmd_shipping')
                     . ' '
                     . Oledrion\Utility::getModuleOption('money_short')
                     . "</td>\n";
                echo "<tr>\n";
            }
            echo '</table>';
            require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        }

        break;
    // ****************************************************************************************************************
    case 'add': // Ajout d'un attribut

    case 'edit': // Edition d'un attribut

        // ****************************************************************************************************************
        xoops_cp_header();
        removeAttributInSession();

        if ('edit' === $action) {
            $title = _AM_OLEDRION_EDIT_ATTRIBUTE;
            $id    = \Xmf\Request::getInt('id', 0, 'GET');
            if (empty($id)) {
                Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
            }
            // Item exits ?
            $item = null;
            $item = $attributesHandler->get($id);
            if (!is_object($item)) {
                Oledrion\Utility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $edit         = true;
            $label_submit = _AM_OLEDRION_MODIFY;
        } else {
            $title = _AM_OLEDRION_ADD_ATTRIBUTE;
            $item  = $attributesHandler->create(true);
            $item->setVar('attribute_id', 0);
            $label_submit = _AM_OLEDRION_ADD;
            $edit         = false;
        }
        // Appel à jQuery
        $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
        Oledrion\Utility::callJavascriptFile('noconflict.js', false, true);
        // Appel du fichier langue
        Oledrion\Utility::callJavascriptFile('messages.js', true, true);

        $sform = new \XoopsThemeForm($title, 'frm' . $operation, $baseurl);
        $sform->addElement(new \XoopsFormHidden('op', $operation));
        $sform->addElement(new \XoopsFormHidden('action', 'saveedit'));
        $sform->addElement(new \XoopsFormHidden('attribute_id', $item->getVar('attribute_id')));
        $sform->addElement(new \XoopsFormText(_AM_OLEDRION_TITLE, 'attribute_title', 50, 255, $item->getVar('attribute_title', 'e')), true);
        $sform->addElement(new \XoopsFormText(_AM_OLEDRION_ATTRIBUTE_NAME, 'attribute_name', 50, 255, $item->getVar('attribute_name', 'e')), true);

        $products       = [];
        $products       = $productsHandler->getList();
        $productsSelect = $productsHandler->productSelector(new Oledrion\Parameters([
                                                                                        'caption'  => _AM_OLEDRION_ATTRIBUTE_PRODUCT,
                                                                                        'name'     => 'attribute_product_id',
                                                                                        'value'    => $item->getVar('attribute_product_id', 'e'),
                                                                                        'size'     => 1,
                                                                                        'multiple' => false,
                                                                                        'values'   => null,
                                                                                        'showAll'  => true,
                                                                                        'sort'     => 'product_title',
                                                                                        'order'    => 'ASC',
                                                                                        'formName' => 'frm' . $operation,
                                                                                    ]));
        $sform->addElement($productsSelect);

        $sform->addElement(new \XoopsFormText(_AM_OLEDRION_WEIGHT, 'attribute_weight', 10, 10, $item->getVar('attribute_weight', 'e')), true);

        $typeSelect = new \XoopsFormSelect(_AM_OLEDRION_TYPE, 'attribute_type', $item->getVar('attribute_type', 'e'));
        $typeSelect->addOptionArray($item->getTypesList());
        $sform->addElement($typeSelect, true);

        // Paramétrage (pour les boutons radio et cases à cocher, le délimiteur, pour les listes déroulantes, le nombre d'éléments visibles et la sélection multiple)
        // Les boutons radio et cases à cocher
        $attributeParameters = "<div name='attributeParameters' id='attributeParameters'>\n";
        $defaultValue        = Constants::OLEDRION_ATTRIBUTE_CHECKBOX_WHITE_SPACE;
        if ($edit) {
            if (Constants::OLEDRION_ATTRIBUTE_RADIO == $item->getVar('attribute_type') || Constants::OLEDRION_ATTRIBUTE_CHECKBOX == $item->getVar('attribute_type')) {
                $defaultValue = $item->getVar('attribute_option1', 'e');
            }
        }
        $options               = [
            Constants::OLEDRION_ATTRIBUTE_CHECKBOX_WHITE_SPACE => _AM_OLEDRION_ATTRIBUTE_DELIMITER1,
            Constants::OLEDRION_ATTRIBUTE_CHECKBOX_NEW_LINE    => _AM_OLEDRION_ATTRIBUTE_DELIMITER2,
        ];
        $parameterButtonOption = _AM_OLEDRION_ATTRIBUTE_DELIMITER . ' ' . Oledrion\Utility::htmlSelect('option1', $options, $defaultValue, false);
        $attributeParameters   .= "<div name='attributeParametersCheckbox' id='attributeParametersCheckbox'>\n";
        $attributeParameters   .= $parameterButtonOption . "\n";
        $attributeParameters   .= "</div>\n";

        // Les listes déroulantes
        $attributeParameters .= "<div name='attributeParametersSelect' id='attributeParametersSelect'>\n";
        $defaultValue1       = Constants::OLEDRION_ATTRIBUTE_SELECT_VISIBLE_OPTIONS;
        $defaultValue2       = Constants::OLEDRION_ATTRIBUTE_SELECT_MULTIPLE;
        if ($edit) {
            if (Constants::OLEDRION_ATTRIBUTE_SELECT == $item->getVar('attribute_type')) {
                $defaultValue1 = $item->getVar('attribute_option1', 'e');
                $defaultValue2 = $item->getVar('attribute_option2', 'e');
            }
        }
        $checked1 = $checked2 = '';
        if (1 == $defaultValue2) {
            $checked1 = 'checked';
        } else {
            $checked2 = 'checked';
        }
        $attributeParameters .= _AM_OLEDRION_ATTRIBUTE_VISIBLE_OPTIONS . " <input type='text' name='option2' id='option2' size='3' maxlength='3' value='$defaultValue1'>";
        $attributeParameters .= '<br>' . _AM_OLEDRION_ATTRIBUTE_MULTI_OPTIONS . " <input type='radio' name='option3' id='option3' value='1' $checked1>" . _YES . " <input type='radio' name='option3' id='option3' value='0' $checked2>" . _NO;
        $attributeParameters .= "</div>\n";
        // ****
        $attributeParameters .= '</div>';
        $sform->addElement(new \XoopsFormLabel(_AM_OLEDRION_ATTRIBUTE_PARAMETERS, $attributeParameters));
        // *******************************************

        // Attribut requis
        $sform->addElement(new \XoopsFormRadioYN(_AM_OLEDRION_ATTRIBUTE_REQUIRED, 'attribute_mandatory', $item->getVar('attribute_mandatory')), true);

        // Les options
        $divContent  = "<div class='ajaxOptions' id='ajaxOptions'></div>";
        $ajaxOptions = new \XoopsFormLabel(_AM_OLEDRION_ATTRIBUTE_OPTIONS, $divContent);
        $sform->addElement($ajaxOptions, false);

        $buttonTray = new \XoopsFormElementTray('', '');
        $submit_btn = new \XoopsFormButton('', 'post', $label_submit, 'submit');
        $buttonTray->addElement($submit_btn);
        $sform->addElement($buttonTray);
        Oledrion\Utility::callJavascriptFile('attributes.js', false, true);
        $sform = Oledrion\Utility::formMarkRequiredFields($sform);
        $sform->display();
        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';

        break;
    // ****************************************************************************************************************
    case 'saveedit': // Sauvegarde de l'option

        // ****************************************************************************************************************
        xoops_cp_header();
        $id = \Xmf\Request::getInt('attribute_id', 0, 'POST');
        if (!empty($id)) {
            $edit = true;
            $item = $attributesHandler->get($id);
            if (!is_object($item)) {
                Oledrion\Utility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $item->unsetNew();
        } else {
            $item = $attributesHandler->create(true);
        }

        $item->setVars($_POST);
        $attribute_type = \Xmf\Request::getInt('attribute_type', 0, 'POST');
        if (Constants::OLEDRION_ATTRIBUTE_SELECT == $attribute_type) {
            // Liste déroulante
            $item->setVar('attribute_option1', \Xmf\Request::getInt('option2', 0, 'POST'));
            $item->setVar('attribute_option2', \Xmf\Request::getInt('option3', 0, 'POST'));
        } else {
            // Bouton radio ou case à cocher
            $item->setVar('attribute_option1', \Xmf\Request::getInt('option1', 0, 'POST'));
        }

        $default      = \Xmf\Request::getInt('default', 0, 'POST');
        $optionsCount = \Xmf\Request::getInt('optionsCount', 0, 'POST');
        $item->resetOptions();
        for ($i = 0; $i < $optionsCount; ++$i) {
            $name  = $value = $price = $stock = '';
            $name  = \Xmf\Request::getString('name' . $i, '', 'POST');
            $value = \Xmf\Request::getString('value' . $i, '', 'POST');
            $price = \Xmf\Request::getString('price' . $i, '', 'POST');
            $stock = \Xmf\Request::getString('stock' . $i, '', 'POST');
            $item->addOption($name, $value, $price, $stock);
            if ($i == $default) {
                $item->setVar('attribute_default_value', $value);
            }
        }

        $res = $attributesHandler->insert($item);
        if ($res) {
            Oledrion\Utility::updateCache();
            Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $operation, 2);
        } else {
            Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $operation, 5);
        }

        break;
    // ****************************************************************************************************************
    case 'ajaxoptions': // Traitement en Ajax des options

        // ****************************************************************************************************************
        if (!isset($xoopsUser) || !is_object($xoopsUser)) {
            exit;
        }
        if (!Oledrion\Utility::isAdmin()) {
            exit;
        }
        error_reporting(0);
        @$xoopsLogger->activated = false;
        $attribute_id = \Xmf\Request::getInt('attribute_id', 0, 'POST');
        $content      = $class = '';
        $attribute    = null;
        $counter      = 0;
        $options      = [];
        $delete       = _OLEDRION_DELETE;
        $span         = 4;
        require_once OLEDRION_CLASS_PATH . 'oledrion_attributes.php';

        if (!isset($_SESSION['oledrion_attribute'])) {
            if (0 == $attribute_id) {
                // Création, rajouter une zone
                $attribute = $attributesHandler->create(true);
            } else {
                $attribute = $attributesHandler->get($attribute_id);
                if (!is_object($attribute)) {
                    return null;
                }
            }
            $_SESSION['oledrion_attribute'] = serialize($attribute);
        } else {
            $attribute = unserialize($_SESSION['oledrion_attribute']);
        }

        if (\Xmf\Request::hasVar('formcontent', 'POST')) {
            // Traitement du contenu actuel
            $data = [];
            parse_str(urldecode($_POST['formcontent']), $data);
            $optionsCount = isset($data['optionsCount']) ? (int)$data['optionsCount'] : 0;
            for ($i = 0; $i < $optionsCount; ++$i) {
                $name  = $value = $price = $stock = '';
                $name  = isset($data['name' . $i]) ? $data['name' . $i] : '';
                $value = isset($data['value' . $i]) ? $data['value' . $i] : '';
                $price = isset($data['price' . $i]) ? $data['price' . $i] : '';
                $stock = isset($data['stock' . $i]) ? $data['stock' . $i] : '';
                $attribute->setOptionValue($i, $name, $value, $price, $stock);
            }
            if (isset($data['default'])) {
                $defaultIndex = (int)$data['default'];
                $defaultValue = isset($data['value' . $defaultIndex]) ? $data['value' . $defaultIndex] : '';
                $attribute->setVar('attribute_default_value', $defaultValue);
                unset($defaultValue);
            }
        }

        if (\Xmf\Request::hasVar('subaction', 'POST')) {
            switch (xoops_trim(mb_strtolower($_POST['subaction']))) {
                case 'delete': // Suppression d'une option de l'attribut

                    $option = \Xmf\Request::getInt('option', 0, 'POST');
                    if (0 !== $option) {
                        $attribute->deleteOption($option);
                    }

                    break;
                case 'add': // Ajout d'une option vide (à la fin)

                    $attribute->addEmptyOption();

                    break;
                case 'up': // Déplacement d'une option vers le haut

                    $option = \Xmf\Request::getInt('option', 0, 'POST');
                    if (0 !== $option) {
                        $attribute->moveOptionUp($option);
                    }

                    break;
                case 'down': // Déplacement d'une option vers le haut

                    $option = \Xmf\Request::getInt('option', 0, 'POST');
                    if (0 !== $option) {
                        $attribute->moveOptionDown($option);
                    }

                    break;
            }
        }
        $_SESSION['oledrion_attribute'] = serialize($attribute);

        $content .= "<table border='0'>\n";
        $content .= "<tr>\n";
        $content .= "<th align='center'>" . _AM_OLEDRION_ATTRIBUTE_DEFAULT_VALUE . "</th><th align='center'>" . _AM_OLEDRION_ATTRIBUTE_TITLE . "</th><th align='center'>" . _AM_OLEDRION_ATTRIBUTE_VALUE . '</th>';
        if (Oledrion\Utility::getModuleOption('use_price')) {
            $content .= "<th align='center'>" . _AM_OLEDRION_ATTRIBUTE_PRICE . '</th>';
            ++$span;
        }
        if (Oledrion\Utility::getModuleOption('attributes_stocks')) {
            $content .= "<th align='center'>" . _AM_OLEDRION_ATTRIBUTE_STOCK . '</th>';
            ++$span;
        }
        $content .= "<th align='center'>" . _AM_OLEDRION_ACTION . "</th>\n";
        $content .= "</tr>\n";

        $up           = _AM_OLEDRION_ATTRIBUTE_MOVE_UP;
        $down         = _AM_OLEDRION_ATTRIBUTE_MOVE_DOWN;
        $defaultValue = xoops_trim($attribute->getVar('attribute_default_value', 'e'));

        $options      = $attribute->getAttributeOptions('e');
        $optionsCount = count($options);

        if ($optionsCount > 0) {
            foreach ($options as $option) {
                $class   = ('even' === $class) ? 'odd' : 'even';
                $content .= "<tr class='" . $class . "'>\n";
                $checked = '';
                if ($option['value'] == $defaultValue) {
                    $checked = "checked = 'checked' ";
                }
                $content .= "<td align='center'><input type='radio' name='default' id='default' value='$counter' $checked></td>\n";
                $content .= "<td align='center'><input type='text' name='name$counter' id='names$counter' size='15' maxlength='255' value='" . $option['name'] . "'></td>\n";
                $content .= "<td align='center'><input type='text' name='value$counter' id='value$counter' size='15' maxlength='255' value='" . $option['value'] . "'></td>\n";
                if (Oledrion\Utility::getModuleOption('use_price')) {
                    $content .= "<td align='center'><input type='text' name='price$counter' id='price$counter' size='15' maxlength='10' value='" . $option['price'] . "'></td>\n";
                }
                if (Oledrion\Utility::getModuleOption('attributes_stocks')) {
                    $content .= "<td align='center'><input type='text' name='stock$counter' id='stock$counter' size='15' maxlength='10' value='" . $option['stock'] . "'></td>\n";
                }
                // Les actions
                $content .= "<td align='center'>";
                // Suppression
                $content .= "<img class='btnremove' alt='$delete' title='$delete' style='border: 0; cursor:pointer;' name='btnremove-$counter' id='btnremove-$counter' src='" . OLEDRION_IMAGES_URL . "smalldelete.png'>";
                if ($counter > 0) {
                    // Up
                    $content .= "<img class='btnUp' alt='$up' title='$up' style='border: 0; cursor:pointer;' name='btnUp-$counter' id='btnUp-$counter' src='" . OLEDRION_IMAGES_URL . "smallup.png'>";
                } else {
                    $content .= "<img src='" . OLEDRION_IMAGES_URL . "blankholder.png'>";
                }
                if ($counter < $optionsCount - 1) {
                    // Down
                    $content .= "<img class='btnDown' alt='$down' title='$down' style='border: 0; cursor:pointer;' name='btnDown-$counter' id='btnDown-$counter' src='" . OLEDRION_IMAGES_URL . "smalldown.png'>";
                } else {
                    $content .= "<img src='" . OLEDRION_IMAGES_URL . "blankholder.png'>";
                }
                $content .= "</td>\n";
                $content .= "</tr>\n";
                ++$counter;
            }
        }
        $class   = ('even' === $class) ? 'odd' : 'even';
        $content .= "<tr class='" . $class . "'>\n";
        $content .= "<td colspan='$span' align='center'><input type='button'' name='bntAdd'' id='bntAdd' value='" . _AM_OLEDRION_ATTRIBUTE_ADD_OPTION . "'></td>\n";
        $content .= "</tr>\n";

        $content .= "</table>\n";
        $content .= "<input type='hidden' name='optionsCount' id='optionsCount' value='$counter'>\n";
        echo $content;

        exit;
        break;
}
