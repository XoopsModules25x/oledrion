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
 * Script chargé d'afficher un média d'un produit
 */

require_once __DIR__ . '/header.php';
$type       = isset($_GET['type']) ? strtolower($_GET['type']) : 'picture';
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
if ($product_id > 0) {
    $product = null;
    $product = $h_oledrion_products->get($product_id);
    if (!is_object($product)) {
        exit(_OLEDRION_ERROR1);
    }

    // Produit en ligne ?
    if ($product->getVar('product_online') == 0) {
        exit(_OLEDRION_ERROR2);
    }

    // Le produit est publié ?
    if (OledrionUtility::getModuleOption('show_unpublished') == 0 && $product->getVar('product_submitted') > time()) {
        exit(_OLEDRION_ERROR3);
    }
} else {
    exit(_ERRORS);
}

switch ($type) {
    case 'attachment': // Un fichier attaché à un produit
        $file_id = isset($_GET['file_id']) ? (int)$_GET['file_id'] : 0;
        if ($file_id == 0) {
            exit(_OLEDRION_ERROR13);
        }
        $attachedFile = null;
        $attachedFile = $h_oledrion_files->get($file_id);
        if (!is_object($attachedFile)) {
            exit(_OLEDRION_ERROR19);
        }
        header('Content-Type: ' . $attachedFile->getVar('file_mimetype'));
        header('Content-disposition: inline; filename="' . $attachedFile->getVar('file_filename') . '"');
        readfile($attachedFile->getPath());
        break;

    case 'picture': // L'image principale d'un produit
        xoops_header(true);
        echo "<div align='center' style='font-weight: bold;'><a href=\"javascript:self.close();\" title=\"" . _CLOSE . '">';
        if ($product->pictureExists()) {
            echo "<img src='" . $product->getPictureUrl() . "' alt=''>";
        } else {
            echo _OLEDRION_SORRY_NOPICTURE;
        }
        ?>
        </a></div><br>
        <br>
        <div align='center'><input value="<?php echo _CLOSE ?>" type="button" onclick="window.close();">
        </div>
        <?php
        xoops_footer();
        break;
}
?>
