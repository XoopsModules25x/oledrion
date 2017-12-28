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

use Xmf\Request;
use Xoopsmodules\oledrion;

/**
 * Gestion des textes affichés sur certaines pages pour les utilisateurs
 */
if (!defined('OLEDRION_ADMIN')) {
    exit();
}
switch ($action) {
    // ****************************************************************************************************************
    case 'default': // Gestion des textes
        // ****************************************************************************************************************
        xoops_cp_header();
        // require_once OLEDRION_PATH . 'class/Registryfile.php';
        $registry = new oledrion\Registryfile();

        $sform = new \XoopsThemeForm(_MI_OLEDRION_ADMENU8, 'frmatxt', $baseurl);
        $sform->addElement(new \XoopsFormHidden('op', 'texts'));
        $sform->addElement(new \XoopsFormHidden('action', 'savetexts'));
        $editor1 = oledrion\Utility::getWysiwygForm(_AM_OLEDRION_INDEX_PAGE, 'welcome1', $registry->getfile(OLEDRION_TEXTFILE1), 5, 60, 'hometext1_hidden');
        if ($editor1) {
            $sform->addElement($editor1, false);
        }

        $editor2 = oledrion\Utility::getWysiwygForm(_OLEDRION_CGV, 'welcome2', $registry->getfile(OLEDRION_TEXTFILE2), 5, 60, 'hometext2_hidden');
        if ($editor2) {
            $sform->addElement($editor2, false);
        }

        $editor3 = oledrion\Utility::getWysiwygForm(_AM_OLEDRION_RECOMM_TEXT, 'welcome3', $registry->getfile(OLEDRION_TEXTFILE3), 5, 60, 'hometext3_hidden');
        if ($editor3) {
            $sform->addElement($editor3, false);
        }

        $editor4 = oledrion\Utility::getWysiwygForm(_AM_OLEDRION_OFFLINEPAY_TEXT, 'welcome4', $registry->getfile(OLEDRION_TEXTFILE4), 5, 60, 'hometext4_hidden');
        if ($editor4) {
            $sform->addElement($editor4, false);
        }

        $editor5 = oledrion\Utility::getWysiwygForm(_AM_OLEDRION_RESTRICT_TEXT, 'welcome5', $registry->getfile(OLEDRION_TEXTFILE5), 5, 60, 'hometext5_hidden');
        if ($editor5) {
            $sform->addElement($editor5, false);
        }

        $editor6 = oledrion\Utility::getWysiwygForm(_AM_OLEDRION_CHECKOUT_TEXT1, 'welcome6', $registry->getfile(OLEDRION_TEXTFILE6), 5, 60, 'hometext6_hidden');
        if ($editor6) {
            $sform->addElement($editor6, false);
        }

        $editor7 = oledrion\Utility::getWysiwygForm(_AM_OLEDRION_CHECKOUT_TEXT2, 'welcome7', $registry->getfile(OLEDRION_TEXTFILE7), 5, 60, 'hometext7_hidden');
        if ($editor7) {
            $sform->addElement($editor7, false);
        }

        $button_tray = new \XoopsFormElementTray('', '');
        $submit_btn  = new \XoopsFormButton('', 'post', _AM_OLEDRION_MODIFY, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        $sform = oledrion\Utility::formMarkRequiredFields($sform);
        $sform->display();
        break;

    // ****************************************************************************************************************
    case 'savetexts': // Sauvegarde des textes d'accueil ********************************************************
        // ****************************************************************************************************************
        // require_once OLEDRION_PATH . 'class/Registryfile.php';
        $registry = new oledrion\Registryfile();
        $registry->savefile(Request::getText('welcome1', '', 'POST'), OLEDRION_TEXTFILE1);
        $registry->savefile(Request::getText('welcome2', '', 'POST'), OLEDRION_TEXTFILE2);
        $registry->savefile(Request::getText('welcome3', '', 'POST'), OLEDRION_TEXTFILE3);
        $registry->savefile(Request::getText('welcome4', '', 'POST'), OLEDRION_TEXTFILE4);
        $registry->savefile(Request::getText('welcome4', '', 'POST'), OLEDRION_TEXTFILE5);
        $registry->savefile(Request::getText('welcome5', '', 'POST'), OLEDRION_TEXTFILE6);
        $registry->savefile(Request::getText('welcome7', '', 'POST'), OLEDRION_TEXTFILE7);
        oledrion\Utility::updateCache();
        oledrion\Utility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=texts', 2);
        break;
}
