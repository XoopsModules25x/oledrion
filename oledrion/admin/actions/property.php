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
 * @copyright   The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author      Hervé Thouzard (http://www.herve-thouzard.com/)
 * @version     $Id$
 */

/**
 * Gestion des textes affichés sur certaines pages pour les utilisateurs
 */
if (!defined("OLEDRION_ADMIN")) exit();

$configHandler = xoops_gethandler('config');
$moduleIdCriteria = new Criteria('conf_modid', $xoopsModule->getVar('mid'));

switch ($action) {

    case 'default':

        xoops_cp_header();

        $sform = new XoopsThemeForm(_MI_OLEDRION_ADMENU17, 'property', $baseurl);
        $sform->addElement(new XoopsFormHidden('op', 'property'));
        $sform->addElement(new XoopsFormHidden('action', 'fromproperty'));

        $property1 = new XoopsFormElementTray (_AM_OLEDRION_PRODUCT_PROPERTY1, '<br />');
        $property1->addElement(new XoopsFormText(_AM_OLEDRION_PRODUCT_PROPERTY_TITLE, 'product_property1_title', 50, 255, oledrion_utils::getModuleOption('product_property1_title')));
        $property1->addElement(new XoopsFormTextArea ('', 'product_property1', oledrion_utils::getModuleOption('product_property1'), 5, 90));
        $property1->setDescription(_AM_OLEDRION_PRODUCT_PROPERTY_DESC);
        $sform->addElement($property1);

        $property2 = new XoopsFormElementTray (_AM_OLEDRION_PRODUCT_PROPERTY2, '<br />');
        $property2->addElement(new XoopsFormText(_AM_OLEDRION_PRODUCT_PROPERTY_TITLE, 'product_property2_title', 50, 255, oledrion_utils::getModuleOption('product_property2_title')));
        $property2->addElement(new XoopsFormTextArea ('', 'product_property2', oledrion_utils::getModuleOption('product_property2'), 5, 90));
        $property2->setDescription(_AM_OLEDRION_PRODUCT_PROPERTY_DESC);
        $sform->addElement($property2);

        $property3 = new XoopsFormElementTray (_AM_OLEDRION_PRODUCT_PROPERTY3, '<br />');
        $property3->addElement(new XoopsFormText(_AM_OLEDRION_PRODUCT_PROPERTY_TITLE, 'product_property3_title', 50, 255, oledrion_utils::getModuleOption('product_property3_title')));
        $property3->addElement(new XoopsFormTextArea ('', 'product_property3', oledrion_utils::getModuleOption('product_property3'), 5, 90));
        $property3->setDescription(_AM_OLEDRION_PRODUCT_PROPERTY_DESC);
        $sform->addElement($property3);

        $property4 = new XoopsFormElementTray (_AM_OLEDRION_PRODUCT_PROPERTY4, '<br />');
        $property4->addElement(new XoopsFormText(_AM_OLEDRION_PRODUCT_PROPERTY_TITLE, 'product_property4_title', 50, 255, oledrion_utils::getModuleOption('product_property4_title')));
        $property4->addElement(new XoopsFormTextArea ('', 'product_property4', oledrion_utils::getModuleOption('product_property4'), 5, 90));
        $property4->setDescription(_AM_OLEDRION_PRODUCT_PROPERTY_DESC);
        $sform->addElement($property4);

        $property5 = new XoopsFormElementTray (_AM_OLEDRION_PRODUCT_PROPERTY5, '<br />');
        $property5->addElement(new XoopsFormText(_AM_OLEDRION_PRODUCT_PROPERTY_TITLE, 'product_property5_title', 50, 255, oledrion_utils::getModuleOption('product_property5_title')));
        $property5->addElement(new XoopsFormTextArea ('', 'product_property5', oledrion_utils::getModuleOption('product_property5'), 5, 90));
        $property5->setDescription(_AM_OLEDRION_PRODUCT_PROPERTY_DESC);
        $sform->addElement($property5);

        $property6 = new XoopsFormElementTray (_AM_OLEDRION_PRODUCT_PROPERTY6, '<br />');
        $property6->addElement(new XoopsFormText(_AM_OLEDRION_PRODUCT_PROPERTY_TITLE, 'product_property6_title', 50, 255, oledrion_utils::getModuleOption('product_property6_title')));
        $property6->addElement(new XoopsFormTextArea ('', 'product_property6', oledrion_utils::getModuleOption('product_property6'), 5, 90));
        $property6->setDescription(_AM_OLEDRION_PRODUCT_PROPERTY_DESC);
        $sform->addElement($property6);

        $property7 = new XoopsFormElementTray (_AM_OLEDRION_PRODUCT_PROPERTY7, '<br />');
        $property7->addElement(new XoopsFormText(_AM_OLEDRION_PRODUCT_PROPERTY_TITLE, 'product_property7_title', 50, 255, oledrion_utils::getModuleOption('product_property7_title')));
        $property7->addElement(new XoopsFormTextArea ('', 'product_property7', oledrion_utils::getModuleOption('product_property7'), 5, 90));
        $property7->setDescription(_AM_OLEDRION_PRODUCT_PROPERTY_DESC);
        $sform->addElement($property7);

        $property8 = new XoopsFormElementTray (_AM_OLEDRION_PRODUCT_PROPERTY8, '<br />');
        $property8->addElement(new XoopsFormText(_AM_OLEDRION_PRODUCT_PROPERTY_TITLE, 'product_property8_title', 50, 255, oledrion_utils::getModuleOption('product_property8_title')));
        $property8->addElement(new XoopsFormTextArea ('', 'product_property8', oledrion_utils::getModuleOption('product_property8'), 5, 90));
        $property8->setDescription(_AM_OLEDRION_PRODUCT_PROPERTY_DESC);
        $sform->addElement($property8);

        $property9 = new XoopsFormElementTray (_AM_OLEDRION_PRODUCT_PROPERTY9, '<br />');
        $property9->addElement(new XoopsFormText(_AM_OLEDRION_PRODUCT_PROPERTY_TITLE, 'product_property9_title', 50, 255, oledrion_utils::getModuleOption('product_property9_title')));
        $property9->addElement(new XoopsFormTextArea ('', 'product_property9', oledrion_utils::getModuleOption('product_property9'), 5, 90));
        $property9->setDescription(_AM_OLEDRION_PRODUCT_PROPERTY_DESC);
        $sform->addElement($property9);

        $property10 = new XoopsFormElementTray (_AM_OLEDRION_PRODUCT_PROPERTY10, '<br />');
        $property10->addElement(new XoopsFormText(_AM_OLEDRION_PRODUCT_PROPERTY_TITLE, 'product_property10_title', 50, 255, oledrion_utils::getModuleOption('product_property10_title')));
        $property10->addElement(new XoopsFormTextArea ('', 'product_property10', oledrion_utils::getModuleOption('product_property10'), 5, 90));
        $property10->setDescription(_AM_OLEDRION_PRODUCT_PROPERTY_DESC);
        $sform->addElement($property10);

        $button_tray = new XoopsFormElementTray('', '');
        $submit_btn = new XoopsFormButton('', 'post', _AM_OLEDRION_MODIFY, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        $sform = oledrion_utils::formMarkRequiredFields($sform);
        $sform->display();

        include_once OLEDRION_ADMIN_PATH . 'admin_footer.php';

        break;

    case 'fromproperty':

        if (isset($_POST['product_property1'])) {
            if (oledrion_utils::getModuleOption('product_property1') != $_POST['product_property1']) {
                $criteria = new CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new Criteria('conf_name', 'product_property1'));
                $config = $configHandler->getConfigs($criteria);
                $config = $config[0];
                $configValue = array(
                    'conf_modid' => $xoopsModule->getVar('mid'),
                    'conf_catid' => 0,
                    'conf_name' => 'product_property1',
                    'conf_value' => $_POST['product_property1'],
                    'conf_formtype' => 'hidden',
                    'conf_valuetype' => 'text'
                );
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (isset($_POST['product_property1_title'])) {
            if (oledrion_utils::getModuleOption('product_property1_title') != $_POST['product_property1_title']) {
                $criteria = new CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new Criteria('conf_name', 'product_property1_title'));
                $config = $configHandler->getConfigs($criteria);
                $config = $config[0];
                $configValue = array(
                    'conf_modid' => $xoopsModule->getVar('mid'),
                    'conf_catid' => 0,
                    'conf_name' => 'product_property1_title',
                    'conf_value' => $_POST['product_property1_title'],
                    'conf_formtype' => 'hidden',
                    'conf_valuetype' => 'text'
                );
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (isset($_POST['product_property2'])) {
            if (oledrion_utils::getModuleOption('product_property2') != $_POST['product_property2']) {
                $criteria = new CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new Criteria('conf_name', 'product_property2'));
                $config = $configHandler->getConfigs($criteria);
                $config = $config[0];
                $configValue = array(
                    'conf_modid' => $xoopsModule->getVar('mid'),
                    'conf_catid' => 0,
                    'conf_name' => 'product_property2',
                    'conf_value' => $_POST['product_property2'],
                    'conf_formtype' => 'hidden',
                    'conf_valuetype' => 'text'
                );
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (isset($_POST['product_property2_title'])) {
            if (oledrion_utils::getModuleOption('product_property2_title') != $_POST['product_property2_title']) {
                $criteria = new CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new Criteria('conf_name', 'product_property2_title'));
                $config = $configHandler->getConfigs($criteria);
                $config = $config[0];
                $configValue = array(
                    'conf_modid' => $xoopsModule->getVar('mid'),
                    'conf_catid' => 0,
                    'conf_name' => 'product_property2_title',
                    'conf_value' => $_POST['product_property2_title'],
                    'conf_formtype' => 'hidden',
                    'conf_valuetype' => 'text'
                );
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (isset($_POST['product_property3'])) {
            if (oledrion_utils::getModuleOption('product_property3') != $_POST['product_property3']) {
                $criteria = new CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new Criteria('conf_name', 'product_property3'));
                $config = $configHandler->getConfigs($criteria);
                $config = $config[0];
                $configValue = array(
                    'conf_modid' => $xoopsModule->getVar('mid'),
                    'conf_catid' => 0,
                    'conf_name' => 'product_property3',
                    'conf_value' => $_POST['product_property3'],
                    'conf_formtype' => 'hidden',
                    'conf_valuetype' => 'text'
                );
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (isset($_POST['product_property3_title'])) {
            if (oledrion_utils::getModuleOption('product_property3_title') != $_POST['product_property3_title']) {
                $criteria = new CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new Criteria('conf_name', 'product_property3_title'));
                $config = $configHandler->getConfigs($criteria);
                $config = $config[0];
                $configValue = array(
                    'conf_modid' => $xoopsModule->getVar('mid'),
                    'conf_catid' => 0,
                    'conf_name' => 'product_property3_title',
                    'conf_value' => $_POST['product_property3_title'],
                    'conf_formtype' => 'hidden',
                    'conf_valuetype' => 'text'
                );
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (isset($_POST['product_property4'])) {
            if (oledrion_utils::getModuleOption('product_property4') != $_POST['product_property4']) {
                $criteria = new CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new Criteria('conf_name', 'product_property4'));
                $config = $configHandler->getConfigs($criteria);
                $config = $config[0];
                $configValue = array(
                    'conf_modid' => $xoopsModule->getVar('mid'),
                    'conf_catid' => 0,
                    'conf_name' => 'product_property4',
                    'conf_value' => $_POST['product_property4'],
                    'conf_formtype' => 'hidden',
                    'conf_valuetype' => 'text'
                );
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (isset($_POST['product_property4_title'])) {
            if (oledrion_utils::getModuleOption('product_property4_title') != $_POST['product_property4_title']) {
                $criteria = new CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new Criteria('conf_name', 'product_property4_title'));
                $config = $configHandler->getConfigs($criteria);
                $config = $config[0];
                $configValue = array(
                    'conf_modid' => $xoopsModule->getVar('mid'),
                    'conf_catid' => 0,
                    'conf_name' => 'product_property4_title',
                    'conf_value' => $_POST['product_property4_title'],
                    'conf_formtype' => 'hidden',
                    'conf_valuetype' => 'text'
                );
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (isset($_POST['product_property5'])) {
            if (oledrion_utils::getModuleOption('product_property5') != $_POST['product_property5']) {
                $criteria = new CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new Criteria('conf_name', 'product_property5'));
                $config = $configHandler->getConfigs($criteria);
                $config = $config[0];
                $configValue = array(
                    'conf_modid' => $xoopsModule->getVar('mid'),
                    'conf_catid' => 0,
                    'conf_name' => 'product_property5',
                    'conf_value' => $_POST['product_property5'],
                    'conf_formtype' => 'hidden',
                    'conf_valuetype' => 'text'
                );
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (isset($_POST['product_property5_title'])) {
            if (oledrion_utils::getModuleOption('product_property5_title') != $_POST['product_property5_title']) {
                $criteria = new CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new Criteria('conf_name', 'product_property5_title'));
                $config = $configHandler->getConfigs($criteria);
                $config = $config[0];
                $configValue = array(
                    'conf_modid' => $xoopsModule->getVar('mid'),
                    'conf_catid' => 0,
                    'conf_name' => 'product_property5_title',
                    'conf_value' => $_POST['product_property5_title'],
                    'conf_formtype' => 'hidden',
                    'conf_valuetype' => 'text'
                );
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (isset($_POST['product_property6'])) {
            if (oledrion_utils::getModuleOption('product_property6') != $_POST['product_property6']) {
                $criteria = new CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new Criteria('conf_name', 'product_property6'));
                $config = $configHandler->getConfigs($criteria);
                $config = $config[0];
                $configValue = array(
                    'conf_modid' => $xoopsModule->getVar('mid'),
                    'conf_catid' => 0,
                    'conf_name' => 'product_property6',
                    'conf_value' => $_POST['product_property6'],
                    'conf_formtype' => 'hidden',
                    'conf_valuetype' => 'text'
                );
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (isset($_POST['product_property6_title'])) {
            if (oledrion_utils::getModuleOption('product_property6_title') != $_POST['product_property6_title']) {
                $criteria = new CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new Criteria('conf_name', 'product_property6_title'));
                $config = $configHandler->getConfigs($criteria);
                $config = $config[0];
                $configValue = array(
                    'conf_modid' => $xoopsModule->getVar('mid'),
                    'conf_catid' => 0,
                    'conf_name' => 'product_property6_title',
                    'conf_value' => $_POST['product_property6_title'],
                    'conf_formtype' => 'hidden',
                    'conf_valuetype' => 'text'
                );
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (isset($_POST['product_property7'])) {
            if (oledrion_utils::getModuleOption('product_property7') != $_POST['product_property7']) {
                $criteria = new CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new Criteria('conf_name', 'product_property7'));
                $config = $configHandler->getConfigs($criteria);
                $config = $config[0];
                $configValue = array(
                    'conf_modid' => $xoopsModule->getVar('mid'),
                    'conf_catid' => 0,
                    'conf_name' => 'product_property7',
                    'conf_value' => $_POST['product_property7'],
                    'conf_formtype' => 'hidden',
                    'conf_valuetype' => 'text'
                );
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (isset($_POST['product_property7_title'])) {
            if (oledrion_utils::getModuleOption('product_property7_title') != $_POST['product_property7_title']) {
                $criteria = new CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new Criteria('conf_name', 'product_property7_title'));
                $config = $configHandler->getConfigs($criteria);
                $config = $config[0];
                $configValue = array(
                    'conf_modid' => $xoopsModule->getVar('mid'),
                    'conf_catid' => 0,
                    'conf_name' => 'product_property7_title',
                    'conf_value' => $_POST['product_property7_title'],
                    'conf_formtype' => 'hidden',
                    'conf_valuetype' => 'text'
                );
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (isset($_POST['product_property8'])) {
            if (oledrion_utils::getModuleOption('product_property8') != $_POST['product_property8']) {
                $criteria = new CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new Criteria('conf_name', 'product_property8'));
                $config = $configHandler->getConfigs($criteria);
                $config = $config[0];
                $configValue = array(
                    'conf_modid' => $xoopsModule->getVar('mid'),
                    'conf_catid' => 0,
                    'conf_name' => 'product_property8',
                    'conf_value' => $_POST['product_property8'],
                    'conf_formtype' => 'hidden',
                    'conf_valuetype' => 'text'
                );
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (isset($_POST['product_property8_title'])) {
            if (oledrion_utils::getModuleOption('product_property8_title') != $_POST['product_property8_title']) {
                $criteria = new CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new Criteria('conf_name', 'product_property8_title'));
                $config = $configHandler->getConfigs($criteria);
                $config = $config[0];
                $configValue = array(
                    'conf_modid' => $xoopsModule->getVar('mid'),
                    'conf_catid' => 0,
                    'conf_name' => 'product_property8_title',
                    'conf_value' => $_POST['product_property8_title'],
                    'conf_formtype' => 'hidden',
                    'conf_valuetype' => 'text'
                );
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (isset($_POST['product_property9'])) {
            if (oledrion_utils::getModuleOption('product_property9') != $_POST['product_property9']) {
                $criteria = new CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new Criteria('conf_name', 'product_property9'));
                $config = $configHandler->getConfigs($criteria);
                $config = $config[0];
                $configValue = array(
                    'conf_modid' => $xoopsModule->getVar('mid'),
                    'conf_catid' => 0,
                    'conf_name' => 'product_property9',
                    'conf_value' => $_POST['product_property9'],
                    'conf_formtype' => 'hidden',
                    'conf_valuetype' => 'text'
                );
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (isset($_POST['product_property9_title'])) {
            if (oledrion_utils::getModuleOption('product_property9_title') != $_POST['product_property9_title']) {
                $criteria = new CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new Criteria('conf_name', 'product_property9_title'));
                $config = $configHandler->getConfigs($criteria);
                $config = $config[0];
                $configValue = array(
                    'conf_modid' => $xoopsModule->getVar('mid'),
                    'conf_catid' => 0,
                    'conf_name' => 'product_property9_title',
                    'conf_value' => $_POST['product_property9_title'],
                    'conf_formtype' => 'hidden',
                    'conf_valuetype' => 'text'
                );
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (isset($_POST['product_property10'])) {
            if (oledrion_utils::getModuleOption('product_property10') != $_POST['product_property10']) {
                $criteria = new CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new Criteria('conf_name', 'product_property10'));
                $config = $configHandler->getConfigs($criteria);
                $config = $config[0];
                $configValue = array(
                    'conf_modid' => $xoopsModule->getVar('mid'),
                    'conf_catid' => 0,
                    'conf_name' => 'product_property10',
                    'conf_value' => $_POST['product_property10'],
                    'conf_formtype' => 'hidden',
                    'conf_valuetype' => 'text'
                );
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (isset($_POST['product_property10_title'])) {
            if (oledrion_utils::getModuleOption('product_property10_title') != $_POST['product_property10_title']) {
                $criteria = new CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new Criteria('conf_name', 'product_property10_title'));
                $config = $configHandler->getConfigs($criteria);
                $config = $config[0];
                $configValue = array(
                    'conf_modid' => $xoopsModule->getVar('mid'),
                    'conf_catid' => 0,
                    'conf_name' => 'product_property10_title',
                    'conf_value' => $_POST['product_property10_title'],
                    'conf_formtype' => 'hidden',
                    'conf_valuetype' => 'text'
                );
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        oledrion_utils::updateCache();
        oledrion_utils::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=property', 2);
        break;
}
