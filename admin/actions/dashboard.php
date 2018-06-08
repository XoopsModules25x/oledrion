<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/


use XoopsModules\Oledrion;

/**
 * oledrion
 *
 * @copyright   {@link https://xoops.org/ XOOPS Project}
 * @license     {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author      Hervé Thouzard (http://www.herve-thouzard.com/)
 */

/**
 * Actions relatives au Dashboard (affichage et suppression d'un vote)
 */

if (!defined('OLEDRION_ADMIN')) {
    exit();
}
switch ($action) {
    // ****************************************************************************************************************
    case 'default': // Affichage du dashboard
        // ****************************************************************************************************************
        xoops_cp_header();

        require_once  dirname(dirname(__DIR__)) . '/class/directorychecker.php';

        //        Oledrion\Utility::htitle(_MI_OLEDRION_ADMENU10, 4);
        $adminObject = \Xmf\Module\Admin::getInstance();
        //$adminObject->addConfigBoxLine(OLEDRION_UPLOAD_PATH, 'folder');
        //$adminObject->addConfigBoxLine(array(OLEDRION_UPLOAD_PATH, '777'), 'chmod');
        //$adminObject->addConfigBoxLine(OLEDRION_ATTACHED_FILES_PATH, 'folder');
        //$adminObject->addConfigBoxLine(array(OLEDRION_ATTACHED_FILES_PATH, '777'), 'chmod');
        //$adminObject->addConfigBoxLine(OLEDRION_PICTURES_PATH, 'folder');
        //$adminObject->addConfigBoxLine(array(OLEDRION_PICTURES_PATH, '777'), 'chmod');
        //$adminObject->addConfigBoxLine(OLEDRION_CSV_PATH, 'folder');
        //$adminObject->addConfigBoxLine(array(OLEDRION_CSV_PATH, '777'), 'chmod');
        //$adminObject->addConfigBoxLine(OLEDRION_CACHE_PATH, 'folder');
        //$adminObject->addConfigBoxLine(array(OLEDRION_CACHE_PATH, '777'), 'chmod');

        $categories = $categoryHandler->getCategoriesCount();
        if (0 == $categories) {
            $link = OLEDRION_ADMIN_URL . 'index.php?op=maintain&action=import';
            $link = sprintf('<a href="%s">%s</a>', $link, _AM_OLEDRION_IMPORT_DATA_TITLE);
            $text = sprintf(_AM_OLEDRION_IMPORT_DATA_TEXT, $link);
            $adminObject->addInfoBox(_AM_OLEDRION_IMPORT_DATA);
            $adminObject->addInfoBoxLine(sprintf($text), '');
        }

        //------ check directories ---------------

        $adminObject->addConfigBoxLine('');
        $redirectFile = $_SERVER['PHP_SELF'];

        $languageConstants = [
            _AM_OLEDRION_AVAILABLE,
            _AM_OLEDRION_NOTAVAILABLE,
            _AM_OLEDRION_CREATETHEDIR,
            _AM_OLEDRION_NOTWRITABLE,
            _AM_OLEDRION_SETMPERM,
            _AM_OLEDRION_DIRCREATED,
            _AM_OLEDRION_DIRNOTCREATED,
            _AM_OLEDRION_PERMSET,
            _AM_OLEDRION_PERMNOTSET
        ];

        //$path =  $helper->getConfig('uploaddir') . '/';
        $adminObject->addConfigBoxLine(Oledrion\Directorychecker::getDirectoryStatus(OLEDRION_UPLOAD_PATH, 0777, $languageConstants, $redirectFile));

        //$path = XOOPS_ROOT_PATH . '/' . $helper->getConfig('screenshots') . '/';
        $adminObject->addConfigBoxLine(Oledrion\Directorychecker::getDirectoryStatus(OLEDRION_ATTACHED_FILES_PATH, 0777, $languageConstants, $redirectFile));

        //$path = XOOPS_ROOT_PATH . '/' . $helper->getConfig('catimage') . '/';
        $adminObject->addConfigBoxLine(Oledrion\Directorychecker::getDirectoryStatus(OLEDRION_PICTURES_PATH, 0777, $languageConstants, $redirectFile));

        //$path = XOOPS_ROOT_PATH . '/' . $helper->getConfig('mainimagedir') . '/';
        $adminObject->addConfigBoxLine(Oledrion\Directorychecker::getDirectoryStatus(OLEDRION_CSV_PATH, 0777, $languageConstants, $redirectFile));

        //$path = XOOPS_ROOT_PATH . '/' . $helper->getConfig('catimage') . '/';
        $adminObject->addConfigBoxLine(Oledrion\Directorychecker::getDirectoryStatus(OLEDRION_CACHE_PATH, 0777, $languageConstants, $redirectFile));

        //$path = XOOPS_ROOT_PATH . '/' . $helper->getConfig('mainimagedir') . '/';
        $adminObject->addConfigBoxLine(Oledrion\Directorychecker::getDirectoryStatus(OLEDRION_TEXT_PATH, 0777, $languageConstants, $redirectFile));

        //$adminObject->displayNavigation(basename(__FILE__));
        //$adminObject->displayIndex();
        //echo wfd_serverstats();
        //---------------------------

        $adminObject->displayNavigation('index.php?op=dashboard');
        //------------- Test Data ----------------------------

        if ($helper->getConfig('displaySampleButton')) {
            xoops_loadLanguage('admin/modulesadmin', 'system');
            require_once  dirname(dirname(__DIR__)) . '/testdata/index.php';

            $adminObject->addItemButton(constant('CO_' . $moduleDirNameUpper . '_' . 'ADD_SAMPLEDATA'), '__DIR__ . /../../testdata/index.php?op=load', 'add');

            $adminObject->addItemButton(constant('CO_' . $moduleDirNameUpper . '_' . 'SAVE_SAMPLEDATA'), '__DIR__ . /../../testdata/index.php?op=save', 'add');

            //    $adminObject->addItemButton(constant('CO_' . $moduleDirNameUpper . '_' . 'EXPORT_SCHEMA'), '__DIR__ . /../../testdata/index.php?op=exportschema', 'add');

            $adminObject->displayButton('left', '');
        }

        //------------- End Test Data ----------------------------
        $adminObject->displayIndex();

        echo $utility::getServerStats();

        $itemsCount = 5; // Nombre d'éléments à afficher
        if ($productsHandler->getCount() > 0) {
            echo "<table border='0' width='100%' cellpadding='2' cellspacing='2'>";
            echo "<tr>\n";
            // Dernières commandes ************************************************
            echo "<td valign='top' width='50%' align='center'><b>" . _AM_OLEDRION_LAST_ORDERS . '</b>';
            $tblTmp   = [];
            $criteria = new \CriteriaCompo();
            $criteria->add(new \Criteria('cmd_id', 0, '<>'));
            $criteria->setSort('cmd_date');
            $criteria->setOrder('DESC');
            $criteria->setLimit($itemsCount);
            $criteria->setStart(0);
            $tblTmp = $commandsHandler->getObjects($criteria);
            echo "<table border='0' cellpadding='2' cellspacing='2' width='100%'>";
            echo "<tr><th align='center'>" . _AM_OLEDRION_DATE . "</th><th align='center'>" . _AM_OLEDRION_ID . "</th><th align='center'>" . _OLEDRION_TOTAL . "</th></tr>\n";
            foreach ($tblTmp as $item) {
                $date = formatTimestamp(strtotime($item->getVar('cmd_date')), 's');
                echo "<tr><td align='center'>" . $date . "</td><td align='center'>" . $item->getVar('cmd_id') . "</td><td align='right'>" . $oledrion_Currency->amountForDisplay($item->getVar('cmd_total')) . '</td></tr>';
            }
            echo '</table>';

            // Stocks bas *********************************************************
            echo "</td><td valign='top' width='50%' align='center'><b>" . _MI_OLEDRION_ADMENU9 . '</b>';
            $tblTmp = [];
            $tblTmp = $productsHandler->getLowStocks(0, $itemsCount);
            echo "<table border='0' cellpadding='2' cellspacing='2' width='100%'>";
            echo "<tr><th align='center'>" . _OLEDRION_TITLE . "</th><th align='center'>" . _OLEDRION_STOCK_QUANTITY . "</th></tr>\n";
            foreach ($tblTmp as $item) {
                $link = "<a href='" . OLEDRION_URL . 'product.php?product_id=' . $item->getVar('product_id') . "'>" . $item->getVar('product_title') . '</a>';
                echo '<tr><td>' . $link . "</td><td align='right'>" . $item->getVar('product_stock') . '</td></tr>';
            }
            echo '</table>';
            echo '</td></tr>';

            echo "<tr><td colspan='2'>&nbsp;</td></tr>";

            // produits les plus vendus *********************************************
            echo "<td valign='top' width='50%' align='center'><b>" . _MI_OLEDRION_BNAME4 . '</b>';
            if ($commandsHandler->getCount() > 0) {
                $tblTmp  = $tblTmp2 = [];
                $tblTmp2 = $caddyHandler->getMostSoldProducts(0, $itemsCount, 0, true);

                $tblTmp = $productsHandler->getObjects(new \Criteria('product_id', '(' . implode(',', array_keys($tblTmp2)) . ')', 'IN'), true);
                echo "<table border='0' cellpadding='2' cellspacing='2' width='100%'>";
                echo "<tr><th align='center'>" . _OLEDRION_TITLE . "</th><th align='center'>" . _OLEDRION_QUANTITY . "</th></tr>\n";
                foreach ($tblTmp2 as $key => $value) {
                    $item = $tblTmp[$key];
                    $link = "<a href='" . OLEDRION_URL . 'product.php?product_id=' . $item->getVar('product_id') . "'>" . $item->getVar('product_title') . '</a>';
                    echo '<tr><td>' . $link . "</td><td align='right'>" . $value . '</td></tr>';
                }
                echo '</table>';
            }
            // produits les plus vus ************************************************
            $tblTmp = [];
            $tblTmp = $productsHandler->getMostViewedProducts(new Oledrion\Parameters([
                                                                                              'start' => 0,
                                                                                              'limit' => $itemsCount
                                                                                          ]));
            echo "</td><td valign='top' width='50%' align='center'><b>" . _MI_OLEDRION_BNAME2 . '</b>';
            echo "<table border='0' cellpadding='2' cellspacing='2' width='100%'>";
            echo "<tr><th align='center'>" . _OLEDRION_TITLE . "</th><th align='center'>" . _OLEDRION_HITS . "</th></tr>\n";
            foreach ($tblTmp as $item) {
                $link = "<a href='" . OLEDRION_URL . 'product.php?product_id=' . $item->getVar('product_id') . "'>" . $item->getVar('product_title') . '</a>';
                echo '<tr><td>' . $link . "</td><td align='right'>" . $item->getVar('product_hits') . '</td></tr>';
            }
            echo '</table>';
            echo '</td></tr>';

            echo "<tr><td colspan='2'>&nbsp;</td></tr>";

            // Derniers votes *****************************************************
            echo "</td><td colspan='2' valign='top' align='center'><b>" . _AM_OLEDRION_LAST_VOTES . '</b>';
            if ($votedataHandler->getCount() > 0) {
                $tblTmp  = $tblTmp2 = $tblTmp3 = [];
                $tblTmp3 = $votedataHandler->getLastVotes(0, $itemsCount);
                foreach ($tblTmp3 as $item) {
                    $tblTmp2[] = $item->getVar('vote_product_id');
                }
                $tblTmp = $productsHandler->getObjects(new \Criteria('product_id', '(' . implode(',', $tblTmp2) . ')', 'IN'), true);
                echo "<table border='0' cellpadding='2' cellspacing='2' width='100%'>";
                echo "<tr><th align='center'>" . _OLEDRION_TITLE . "</th><th align='center'>" . _AM_OLEDRION_DATE . "</th><th colspan='2' align='center'>" . _AM_OLEDRION_NOTE . '</th></tr>';
                foreach ($tblTmp3 as $vote) {
                    $item          = $tblTmp[$vote->getVar('vote_product_id')];
                    $link          = "<a href='" . OLEDRION_URL . 'product.php?product_id=' . $item->getVar('product_id') . "'>" . $item->getVar('product_title') . '</a>';
                    $action_delete = "<a href='$baseurl?op=dashboard&action=deleterating&id=" . $vote->getVar('vote_ratingid') . "' title='" . _OLEDRION_DELETE . "'" . $conf_msg . '>' . $icons['delete'] . '</a>';
                    echo '<tr><td>' . $link . "</td><td align='center'>" . formatTimestamp($vote->getVar('vote_ratingtimestamp'), 's') . "</td><td align='right'>" . $vote->getVar('vote_rating') . '</td><td>' . $action_delete . '</td></tr>';
                }
                echo "</table>\n";
            }
            echo "</td></tr>\n";
            echo "</table>\n";
        }

        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    // ****************************************************************************************************************
    case 'deleterating': // Delete a rating
        // ****************************************************************************************************************
        $id = \Xmf\Request::getInt('id', 0, 'GET');
        if (empty($id)) {
            Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $opRedirect = 'dashboard';
        $item       = $votedataHandler->get($id);
        if (is_object($item)) {
            $res = $votedataHandler->delete($item, true);
            if ($res) {
                $product_id = $item->getVar('vote_product_id');
                $product    = null;
                $product    = $productsHandler->get($product_id);
                if (is_object($product)) { // Update Product's rating
                    $totalVotes = $sumRating = $ret = $finalrating = 0;
                    $ret        = $votedataHandler->getCountRecordSumRating($product->getVar('product_id'), $totalVotes, $sumRating);
                    if ($totalVotes > 0) {
                        $finalrating = $sumRating / $totalVotes;
                        $finalrating = number_format($finalrating, 4);
                    }
                    $productsHandler->updateRating($product_id, $finalrating, $totalVotes);
                }
                Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
            } else {
                Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
            }
        } else {
            Oledrion\Utility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;
}
