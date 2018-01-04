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

use XoopsModules\Oledrion;

/**
 * Gestion des stocks bas (dans l'administration)
 */
if (!defined('OLEDRION_ADMIN')) {
    exit();
}
switch ($action) {
    // ****************************************************************************************************************
    case 'default': // Stock bas
        // ****************************************************************************************************************
        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation('index.php?op=lowstock');

        //        Oledrion\Utility::htitle(_MI_OLEDRION_ADMENU9, 4);
        $start    = isset($_GET['start']) ? (int)$_GET['start'] : 0;
        $criteria = new \CriteriaCompo();
        // Recherche des produits dont la quantité en stock est inférieure ou égale à la quantité d'alerte et ou la quantité d'alerte est supérieure à 0
        $itemsCount = $productsHandler->getLowStocksCount();
        if ($itemsCount > $limit) {
            $pagenav = new \XoopsPageNav($itemsCount, $limit, $start, 'start', 'op=lowstock');
        }
        $products = $productsHandler->getLowStocks($start, $limit);
        $class    = $name = '';
        $names    = [];
        echo "<form name='frmupdatequant' id='frmupdatequant' method='post' action='$baseurl'><input type='hidden' name='op' id='op' value='lowstock'><input type='hidden' name='action' id='action' value='updatequantities'>";
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>" . _OLEDRION_TITLE . "</th><th align='center'>" . _OLEDRION_STOCK_QUANTITY . "</th><th align='center'>" . _OLEDRION_STOCK_ALERT . "</th><th align='center'>" . _AM_OLEDRION_NEW_QUANTITY . '</th></tr>';
        foreach ($products as $item) {
            $id    = $item->getVar('product_id');
            $class = ('even' === $class) ? 'odd' : 'even';
            $link  = "<a href='" . $item->getLink() . "'>" . $item->getVar('product_title') . '</a>';
            echo "<tr class='" . $class . "'>\n";
            $name    = 'qty_' . $id;
            $names[] = $id;
            echo '<td>' . $link . "</td><td align='center'>" . $item->getVar('product_stock') . "</td><td align='center'>" . $item->getVar('product_alert_stock') . "</td><td align='center'><input type='text' name='$name' id='$name' size='3' maxlength='5' value=''></td>\n";
            echo "<tr>\n";
        }
        $class = ('even' === $class) ? 'odd' : 'even';
        if (count($names) > 0) {
            echo "<tr class='$class'><td colspan='3' align='center'>&nbsp;</td><td align='center'><input type='hidden' name='names' id='names' value='" . implode('|', $names) . "'><input type='submit' name='btngo' id='btngo' value='" . _AM_OLEDRION_UPDATE_QUANTITIES . "'></td></tr>";
        }
        echo '</table></form>';
        if (isset($pagenav) && is_object($pagenav)) {
            echo "<div align='right'>" . $pagenav->renderNav() . '</div>';
        }
        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    // ****************************************************************************************************************
    case 'updatequantities': // Mise à jour des quantités des produits
        // ****************************************************************************************************************
        $names = [];
        if (isset($_POST['names'])) {
            $names = explode('|', $_POST['names']);
            foreach ($names as $item) {
                $name = 'qty_' . $item;
                if (isset($_POST[$name]) && '' !== xoops_trim($_POST[$name])) {
                    $quantity   = (int)$_POST[$name];
                    $product_id = (int)$item;
                    $product    = null;
                    $product    = $productsHandler->get($product_id);
                    if (is_object($product)) {
                        $productsHandler->updateAll('product_stock', $quantity, new \Criteria('product_id', $product_id, '='), true);
                    }
                }
            }
        }
        Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=lowstock', 2);
        break;

}
