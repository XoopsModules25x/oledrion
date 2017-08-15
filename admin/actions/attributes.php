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
if (!defined('OLEDRION_ADMIN')) {
    exit();
}
global $baseurl; // Pour faire taire les warnings de Zend Studio
$operation = 'attributes';

/**
 * Suppression de l'attribut qui se trouve en session
 *
 * @return void
 */
function removeAttributInSession()
{
    if (isset($_SESSION['oledrion_attribute'])) {
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
        $adminObject->displayNavigation('index.php?op=attributes');

        global $xoopsConfig;
        $productsIds = $products = $productsIdsForList = $productsForList = [];
        $class       = '';
        removeAttributInSession();

        $form = "<form method='post' action='$baseurl' name='frmadd$operation' id='frmadd$operation'><input type='hidden' name='op' id='op' value='$operation'><input type='hidden' name='action' id='action' value='add'><input type='submit' name='btngo' id='btngo' value='"
                . _AM_OLEDRION_ADD_ITEM
                . "'></form>";
        echo $form;

        //        OledrionUtility::htitle(_AM_OLEDRION_ATTRIBUTES_LIST, 4);

        $start    = isset($_GET['start']) ? (int)$_GET['start'] : 0;
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('attribute_id', 0, '<>'));

        $filter_attribute_id    = $filter_attribute_id = $filter_attribute_weight = $filter_attribute_type = $filter_attribute_product_id = 0;
        $filter_attribute_title = '';

        $newFilter = false;

        if (isset($_POST['filter_attribute_id'])) {
            if ((int)$_POST['filter_attribute_id'] != 0) {
                $criteria->add(new Criteria('attribute_id', (int)$_POST['filter_attribute_id']), '=');
            }
            $filter_attribute_id = (int)$_POST['filter_attribute_id'];
            $newFilter           = true;
        }
        if (isset($_POST['filter_attribute_title']) && xoops_trim($_POST['filter_attribute_title']) !== '') {
            $criteria->add(new Criteria('attribute_title', '%' . $_POST['filter_attribute_title'] . '%', 'LIKE'));
            $filter_attribute_title = $_POST['filter_attribute_title'];
            $newFilter              = true;
        }
        if (isset($_POST['filter_attribute_product_id']) && (int)$_POST['filter_attribute_product_id'] != 0) {
            $criteria->add(new Criteria('attribute_product_id', (int)$_POST['filter_attribute_product_id']), '=');
            $filter_attribute_product_id = (int)$_POST['filter_attribute_product_id'];
            $newFilter                   = true;
        }
        if (isset($_POST['filter_attribute_weight']) && (int)$_POST['filter_attribute_weight'] != 0) {
            $criteria->add(new Criteria('attribute_weight', (int)$_POST['filter_attribute_weight']), '=');
            $filter_attribute_weight = (int)$_POST['filter_attribute_weight'];
            $newFilter               = true;
        }
        if (isset($_POST['filter_attribute_type']) && (int)$_POST['filter_attribute_type'] != 0) {
            $criteria->add(new Criteria('attribute_type', (int)$_POST['filter_attribute_type']), '=');
            $filter_attribute_type = (int)$_POST['filter_attribute_type'];
            $newFilter             = true;
        }

        if ($filter_attribute_id == 0 && $filter_attribute_title === '' && $filter_attribute_weight == 0
            && $filter_attribute_type == 0) {
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

        $itemsCount = $oledrionHandlers->h_oledrion_attributes->getCount($criteria);
        if ($itemsCount > $limit) {
            $pagenav = new XoopsPageNav($itemsCount, $limit, $start, 'start', 'op=' . $operation);
        }

        $criteria->setLimit($limit);
        $criteria->setStart($start);
        $criteria->setSort('attribute_product_id, attribute_weight');
        $items = $oledrionHandlers->h_oledrion_attributes->getObjects($criteria);
        if (count($items) > 0) {
            foreach ($items as $item) {
                if (!isset($productsIds[$item->getVar('attribute_product_id')])) {
                    $productsIds[] = $item->getVar('attribute_product_id');
                }
            }
            if (count($productsIds) > 0) {
                $products = $oledrionHandlers->h_oledrion_products->getProductsFromIDs($productsIds, true);
            }
        }
        $typeSelect = OledrionUtility::htmlSelect('filter_attribute_type', [
            OLEDRION_ATTRIBUTE_RADIO    => _AM_OLEDRION_TYPE_RADIO,
            OLEDRION_ATTRIBUTE_CHECKBOX => _AM_OLEDRION_TYPE_CHECKBOX,
            OLEDRION_ATTRIBUTE_SELECT   => _AM_OLEDRION_TYPE_LIST
        ], $filter_attribute_type);

        $productsIdsForList = $oledrionHandlers->h_oledrion_attributes->getDistinctsProductsIds();
        if (count($productsIdsForList) > 0) {
            $productsForList = $oledrionHandlers->h_oledrion_products->getList(new Criteria('product_id', '(' . implode(',', $productsIdsForList) . ')', 'IN'));
        }
        $selectProduct = OledrionUtility::htmlSelect('filter_attribute_product_id', $productsForList, $filter_attribute_product_id);

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
            $class        = ($class === 'even') ? 'odd' : 'even';
            $id           = $item->getVar('attribute_id');
            $actions      = [];
            $actions[]    = "<a href='$baseurl?op=$operation&action=edit&id=" . $id . "' title='" . _OLEDRION_EDIT . "'>" . $icones['edit'] . '</a>';
            $actions[]    = "<a href='$baseurl?op=$operation&action=copy&id=" . $id . "' title='" . _OLEDRION_DUPLICATE_ATTRIBUTE . "'>" . $icones['copy'] . '</a>';
            $actions[]    = "<a href='$baseurl?op=$operation&action=delete&id=" . $id . "' title='" . _OLEDRION_DELETE . "'" . $conf_msg . '>' . $icones['delete'] . '</a>';
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
        $class = ($class === 'even') ? 'odd' : 'even';
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
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (empty($id)) {
            OledrionUtility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $attribute = null;
        $attribute = $oledrionHandlers->h_oledrion_attributes->get($id);
        if (is_object($attribute)) {
            $newAttribute   = $oledrionHandlers->h_oledrion_attributes->cloneAttribute($attribute);
            $newAttributeId = $newAttribute->attribute_id;
            if ($newAttribute !== false) {
                OledrionUtility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $operation . '&action=edit&id=' . $newAttributeId, 2);
            } else {
                OledrionUtility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $operation, 5);
            }
        }
        break;

    // ****************************************************************************************************************
    case 'delete': // Suppression d'un attribut
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (empty($id)) {
            OledrionUtility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $attribute = null;
        $attribute = $oledrionHandlers->h_oledrion_attributes->get($id);
        if (!is_object($attribute)) {
            OledrionUtility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl . '?op=' . $operation, 5);
        }
        $attributesCountInCaddy = $oledrionHandlers->h_oledrion_caddy_attributes->getCaddyCountFromAttributeId($id);
        if ($attributesCountInCaddy == 0) {
            $res = $oledrionHandlers->h_oledrion_attributes->deleteAttribute($attribute);
            if ($res) {
                OledrionUtility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $operation, 2);
            } else {
                OledrionUtility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $operation, 5);
            }
        } else {
            OledrionUtility::htitle(_AM_OLEDRION_SORRY_NOREMOVE2, 4);
            $tblTmp  = $oledrionHandlers->h_oledrion_caddy_attributes->getCommandIdFromAttribute($id);
            $tblTmp2 = $h_oledrion_commands->getObjects(new Criteria('cmd_id', '(' . implode(',', $tblTmp) . ')', 'IN'), true);
            echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
            $class = '';
            echo "<tr><th align='center'>" . _AM_OLEDRION_ID . "</th><th align='center'>" . _AM_OLEDRION_DATE . "</th><th align='center'>" . _AM_OLEDRION_CLIENT . "</th><th align='center'>" . _AM_OLEDRION_TOTAL_SHIPP . '</th></tr>';
            foreach ($tblTmp2 as $item) {
                $class = ($class === 'even') ? 'odd' : 'even';
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
                     . OledrionUtility::getModuleOption('money_short')
                     . ' / '
                     . $item->getVar('cmd_shipping')
                     . ' '
                     . OledrionUtility::getModuleOption('money_short')
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

        if ($action === 'edit') {
            $title = _AM_OLEDRION_EDIT_ATTRIBUTE;
            $id    = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            if (empty($id)) {
                OledrionUtility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
            }
            // Item exits ?
            $item = null;
            $item = $oledrionHandlers->h_oledrion_attributes->get($id);
            if (!is_object($item)) {
                OledrionUtility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $edit         = true;
            $label_submit = _AM_OLEDRION_MODIFY;
        } else {
            $title = _AM_OLEDRION_ADD_ATTRIBUTE;
            $item  = $oledrionHandlers->h_oledrion_attributes->create(true);
            $item->setVar('attribute_id', 0);
            $label_submit = _AM_OLEDRION_ADD;
            $edit         = false;
        }
        // Appel à jQuery
        $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
        OledrionUtility::callJavascriptFile('noconflict.js', false, true);
        // Appel du fichier langue
        OledrionUtility::callJavascriptFile('messages.js', true, true);

        $sform = new XoopsThemeForm($title, 'frm' . $operation, $baseurl);
        $sform->addElement(new XoopsFormHidden('op', $operation));
        $sform->addElement(new XoopsFormHidden('action', 'saveedit'));
        $sform->addElement(new XoopsFormHidden('attribute_id', $item->getVar('attribute_id')));
        $sform->addElement(new XoopsFormText(_AM_OLEDRION_TITLE, 'attribute_title', 50, 255, $item->getVar('attribute_title', 'e')), true);
        $sform->addElement(new XoopsFormText(_AM_OLEDRION_ATTRIBUTE_NAME, 'attribute_name', 50, 255, $item->getVar('attribute_name', 'e')), true);

        $products       = [];
        $products       = $oledrionHandlers->h_oledrion_products->getList();
        $productsSelect = $oledrionHandlers->h_oledrion_products->productSelector(new Oledrion_parameters([
                                                                                                              'caption'  => _AM_OLEDRION_ATTRIBUTE_PRODUCT,
                                                                                                              'name'     => 'attribute_product_id',
                                                                                                              'value'    => $item->getVar('attribute_product_id', 'e'),
                                                                                                              'size'     => 1,
                                                                                                              'multiple' => false,
                                                                                                              'values'   => null,
                                                                                                              'showAll'  => true,
                                                                                                              'sort'     => 'product_title',
                                                                                                              'order'    => 'ASC',
                                                                                                              'formName' => 'frm' . $operation
                                                                                                          ]));
        $sform->addElement($productsSelect);

        $sform->addElement(new XoopsFormText(_AM_OLEDRION_WEIGHT, 'attribute_weight', 10, 10, $item->getVar('attribute_weight', 'e')), true);

        $typeSelect = new XoopsFormSelect(_AM_OLEDRION_TYPE, 'attribute_type', $item->getVar('attribute_type', 'e'));
        $typeSelect->addOptionArray($item->getTypesList());
        $sform->addElement($typeSelect, true);

        // Paramétrage (pour les boutons radio et cases à cocher, le délimiteur, pour les listes déroulantes, le nombre d'éléments visibles et la sélection multiple)
        // Les boutons radio et cases à cocher
        $attributeParameters = "<div name='attributeParameters' id='attributeParameters'>\n";
        $defaultValue        = OLEDRION_ATTRIBUTE_CHECKBOX_WHITE_SPACE;
        if ($edit) {
            if ($item->getVar('attribute_type') == OLEDRION_ATTRIBUTE_RADIO
                || $item->getVar('attribute_type') == OLEDRION_ATTRIBUTE_CHECKBOX) {
                $defaultValue = $item->getVar('attribute_option1', 'e');
            }
        }
        $options               = [
            OLEDRION_ATTRIBUTE_CHECKBOX_WHITE_SPACE => _AM_OLEDRION_ATTRIBUTE_DELIMITER1,
            OLEDRION_ATTRIBUTE_CHECKBOX_NEW_LINE    => _AM_OLEDRION_ATTRIBUTE_DELIMITER2
        ];
        $parameterButtonOption = _AM_OLEDRION_ATTRIBUTE_DELIMITER . ' ' . OledrionUtility::htmlSelect('option1', $options, $defaultValue, false);
        $attributeParameters   .= "<div name='attributeParametersCheckbox' id='attributeParametersCheckbox'>\n";
        $attributeParameters   .= $parameterButtonOption . "\n";
        $attributeParameters   .= "</div>\n";

        // Les listes déroulantes
        $attributeParameters .= "<div name='attributeParametersSelect' id='attributeParametersSelect'>\n";
        $defaultValue1       = OLEDRION_ATTRIBUTE_SELECT_VISIBLE_OPTIONS;
        $defaultValue2       = OLEDRION_ATTRIBUTE_SELECT_MULTIPLE;
        if ($edit) {
            if ($item->getVar('attribute_type') == OLEDRION_ATTRIBUTE_SELECT) {
                $defaultValue1 = $item->getVar('attribute_option1', 'e');
                $defaultValue2 = $item->getVar('attribute_option2', 'e');
            }
        }
        $checked1 = $checked2 = '';
        if ($defaultValue2 == 1) {
            $checked1 = 'checked';
        } else {
            $checked2 = 'checked';
        }
        $attributeParameters .= _AM_OLEDRION_ATTRIBUTE_VISIBLE_OPTIONS . " <input type='text' name='option2' id='option2' size='3' maxlength='3' value='$defaultValue1'>";
        $attributeParameters .= '<br>' . _AM_OLEDRION_ATTRIBUTE_MULTI_OPTIONS . " <input type='radio' name='option3' id='option3' value='1' $checked1>" . _YES . " <input type='radio' name='option3' id='option3' value='0' $checked2>" . _NO;
        $attributeParameters .= "</div>\n";
        // ****
        $attributeParameters .= '</div>';
        $sform->addElement(new XoopsFormLabel(_AM_OLEDRION_ATTRIBUTE_PARAMETERS, $attributeParameters));
        // *******************************************

        // Attribut requis
        $sform->addElement(new XoopsFormRadioYN(_AM_OLEDRION_ATTRIBUTE_REQUIRED, 'attribute_mandatory', $item->getVar('attribute_mandatory')), true);

        // Les options
        $divContent  = "<div class='ajaxOptions' id='ajaxOptions'></div>";
        $ajaxOptions = new XoopsFormLabel(_AM_OLEDRION_ATTRIBUTE_OPTIONS, $divContent);
        $sform->addElement($ajaxOptions, false);

        $button_tray = new XoopsFormElementTray('', '');
        $submit_btn  = new XoopsFormButton('', 'post', $label_submit, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        OledrionUtility::callJavascriptFile('attributes.js', false, true);
        $sform = OledrionUtility::formMarkRequiredFields($sform);
        $sform->display();
        require_once __DIR__ . '/admin_footer.php';
        break;

    // ****************************************************************************************************************
    case 'saveedit': // Sauvegarde de l'option
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = isset($_POST['attribute_id']) ? (int)$_POST['attribute_id'] : 0;
        if (!empty($id)) {
            $edit = true;
            $item = $oledrionHandlers->h_oledrion_attributes->get($id);
            if (!is_object($item)) {
                OledrionUtility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $item->unsetNew();
        } else {
            $item = $oledrionHandlers->h_oledrion_attributes->create(true);
        }

        $item->setVars($_POST);
        $attribute_type = isset($_POST['attribute_type']) ? (int)$_POST['attribute_type'] : 0;
        if ($attribute_type == OLEDRION_ATTRIBUTE_SELECT) { // Liste déroulante
            $item->setVar('attribute_option1', (int)$_POST['option2']);
            $item->setVar('attribute_option2', (int)$_POST['option3']);
        } else { // Bouton radio ou case à cocher
            $item->setVar('attribute_option1', (int)$_POST['option1']);
        }

        $default      = isset($_POST['default']) ? (int)$_POST['default'] : 0;
        $optionsCount = isset($_POST['optionsCount']) ? (int)$_POST['optionsCount'] : 0;
        $item->resetOptions();
        for ($i = 0; $i < $optionsCount; ++$i) {
            $name  = $value = $price = $stock = '';
            $name  = isset($_POST['name' . $i]) ? $_POST['name' . $i] : '';
            $value = isset($_POST['value' . $i]) ? $_POST['value' . $i] : '';
            $price = isset($_POST['price' . $i]) ? $_POST['price' . $i] : '';
            $stock = isset($_POST['stock' . $i]) ? $_POST['stock' . $i] : '';
            $item->addOption($name, $value, $price, $stock);
            if ($i == $default) {
                $item->setVar('attribute_default_value', $value);
            }
        }

        $res = $oledrionHandlers->h_oledrion_attributes->insert($item);
        if ($res) {
            OledrionUtility::updateCache();
            OledrionUtility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $operation, 2);
        } else {
            OledrionUtility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $operation, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'ajaxoptions': // Traitement en Ajax des options
        // ****************************************************************************************************************
        if (!isset($xoopsUser) || !is_object($xoopsUser)) {
            exit;
        }
        if (!OledrionUtility::isAdmin()) {
            exit;
        }
        error_reporting(0);
        @$xoopsLogger->activated = false;
        $attribute_id = isset($_POST['attribute_id']) ? (int)$_POST['attribute_id'] : 0;
        $content      = $class = '';
        $attribute    = null;
        $counter      = 0;
        $options      = [];
        $delete       = _OLEDRION_DELETE;
        $span         = 4;
        require_once OLEDRION_CLASS_PATH . 'oledrion_attributes.php';

        if (!isset($_SESSION['oledrion_attribute'])) {
            if ($attribute_id == 0) { // Création, rajouter une zone
                $attribute = $oledrionHandlers->h_oledrion_attributes->create(true);
            } else {
                $attribute = $oledrionHandlers->h_oledrion_attributes->get($attribute_id);
                if (!is_object($attribute)) {
                    return null;
                }
            }
            $_SESSION['oledrion_attribute'] = serialize($attribute);
        } else {
            $attribute = unserialize($_SESSION['oledrion_attribute']);
        }

        if (isset($_POST['formcontent'])) { // Traitement du contenu actuel
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

        if (isset($_POST['subaction'])) {
            switch (xoops_trim(strtolower($_POST['subaction']))) {
                case 'delete': // Suppression d'une option de l'attribut
                    $option = isset($_POST['option']) ? (int)$_POST['option'] : false;
                    if ($option !== false) {
                        $attribute->deleteOption($option);
                    }
                    break;

                case 'add': // Ajout d'une option vide (à la fin)
                    $attribute->addEmptyOption();
                    break;

                case 'up': // Déplacement d'une option vers le haut
                    $option = isset($_POST['option']) ? (int)$_POST['option'] : false;
                    if ($option !== false) {
                        $attribute->moveOptionUp($option);
                    }
                    break;

                case 'down': // Déplacement d'une option vers le haut
                    $option = isset($_POST['option']) ? (int)$_POST['option'] : false;
                    if ($option !== false) {
                        $attribute->moveOptionDown($option);
                    }
                    break;
            }
        }
        $_SESSION['oledrion_attribute'] = serialize($attribute);

        $content .= "<table border='0'>\n";
        $content .= "<tr>\n";
        $content .= "<th align='center'>" . _AM_OLEDRION_ATTRIBUTE_DEFAULT_VALUE . "</th><th align='center'>" . _AM_OLEDRION_ATTRIBUTE_TITLE . "</th><th align='center'>" . _AM_OLEDRION_ATTRIBUTE_VALUE . '</th>';
        if (OledrionUtility::getModuleOption('use_price')) {
            $content .= "<th align='center'>" . _AM_OLEDRION_ATTRIBUTE_PRICE . '</th>';
            ++$span;
        }
        if (OledrionUtility::getModuleOption('attributes_stocks')) {
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
                $class   = ($class === 'even') ? 'odd' : 'even';
                $content .= "<tr class='" . $class . "'>\n";
                $checked = '';
                if ($option['value'] == $defaultValue) {
                    $checked = "checked = 'checked' ";
                }
                $content .= "<td align='center'><input type='radio' name='default' id='default' value='$counter' $checked></td>\n";
                $content .= "<td align='center'><input type='text' name='name$counter' id='names$counter' size='15' maxlength='255' value='" . $option['name'] . "'></td>\n";
                $content .= "<td align='center'><input type='text' name='value$counter' id='value$counter' size='15' maxlength='255' value='" . $option['value'] . "'></td>\n";
                if (OledrionUtility::getModuleOption('use_price')) {
                    $content .= "<td align='center'><input type='text' name='price$counter' id='price$counter' size='15' maxlength='10' value='" . $option['price'] . "'></td>\n";
                }
                if (OledrionUtility::getModuleOption('attributes_stocks')) {
                    $content .= "<td align='center'><input type='text' name='stock$counter' id='stock$counter' size='15' maxlength='10' value='" . $option['stock'] . "'></td>\n";
                }
                // Les actions
                $content .= "<td align='center'>";
                // Suppression
                $content .= "<img class='btnremove' alt='$delete' title='$delete' style='border: 0; cursor:pointer;' name='btnremove-$counter' id='btnremove-$counter' src='" . OLEDRION_IMAGES_URL . "smalldelete.png'>";
                if ($counter > 0) { // Up
                    $content .= "<img class='btnUp' alt='$up' title='$up' style='border: 0; cursor:pointer;' name='btnUp-$counter' id='btnUp-$counter' src='" . OLEDRION_IMAGES_URL . "smallup.png'>";
                } else {
                    $content .= "<img src='" . OLEDRION_IMAGES_URL . "blankholder.png'>";
                }
                if ($counter < $optionsCount - 1) { // Down
                    $content .= "<img class='btnDown' alt='$down' title='$down' style='border: 0; cursor:pointer;' name='btnDown-$counter' id='btnDown-$counter' src='" . OLEDRION_IMAGES_URL . "smalldown.png'>";
                } else {
                    $content .= "<img src='" . OLEDRION_IMAGES_URL . "blankholder.png'>";
                }
                $content .= "</td>\n";
                $content .= "</tr>\n";
                ++$counter;
            }
        }
        $class   = ($class === 'even') ? 'odd' : 'even';
        $content .= "<tr class='" . $class . "'>\n";
        $content .= "<td colspan='$span' align='center'><input type='button'' name='bntAdd'' id='bntAdd' value='" . _AM_OLEDRION_ATTRIBUTE_ADD_OPTION . "'></td>\n";
        $content .= "</tr>\n";

        $content .= "</table>\n";
        $content .= "<input type='hidden' name='optionsCount' id='optionsCount' value='$counter'>\n";
        echo $content;
        exit;
        break;
}
