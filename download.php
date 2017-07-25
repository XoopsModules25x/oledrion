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
 * Téléchargement de fichier après passage d'une commande (et validation de celle-ci)
 */
require_once __DIR__ . '/header.php';
error_reporting(0);
@$xoopsLogger->activated = false;

$download_id = isset($_GET['download_id']) ? $_GET['download_id'] : '';

// TODO: Permettre au webmaster de réactiver un téléchargement

if (xoops_trim($download_id) == '') {
    OledrionUtility::redirect(_OLEDRION_ERROR13, OLEDRION_URL, 5);
}

// Recherche dans les caddy du produit associé
$caddy = null;
$caddy = $h_oledrion_caddy->getCaddyFromPassword($download_id);
if (!is_object($caddy)) {
    OledrionUtility::redirect(_OLEDRION_ERROR14, OLEDRION_URL, 5);
}

// Recherche du produit associé
$product = null;
$product = $h_oledrion_products->get($caddy->getVar('caddy_product_id'));
if ($product == null) {
    OledrionUtility::redirect(_OLEDRION_ERROR15, OLEDRION_URL, 5);
}

// On vérifie que la commande associée est payée
$order = null;
$order = $h_oledrion_commands->get($caddy->getVar('caddy_cmd_id'));
if ($order == null) {
    OledrionUtility::redirect(_OLEDRION_ERROR16, OLEDRION_URL, 5);
}

// Tout est bon, on peut envoyer le fichier au navigateur, s'il y a un fichier à télécharger, et s'il existe
$file = '';
$file = $product->getVar('product_download_url');
if (xoops_trim($file) == '') {
    OledrionUtility::redirect(_OLEDRION_ERROR17, OLEDRION_URL, 5);
}
if (!file_exists($file)) {
    OledrionUtility::redirect(_OLEDRION_ERROR18, OLEDRION_URL, 5);
}

// Mise à jour, le fichier n'est plus disponible au téléchargement
$h_oledrion_caddy->markCaddyAsNotDownloadableAnyMore($caddy);

$fileContent = file_get_contents($file);
// Plugins ************************************************
$plugins    = Oledrion_plugins::getInstance();
$parameters = new Oledrion_parameters(array(
                                          'fileContent'  => $fileContent,
                                          'product'      => $product,
                                          'order'        => $order,
                                          'fullFilename' => $file
                                      ));
$parameters = $plugins->fireFilter(Oledrion_plugins::EVENT_ON_PRODUCT_DOWNLOAD, $parameters);
if (trim($parameters['fileContent']) != '') {
    $fileContent = $parameters['fileContent'];
}
// *********************************************************
// Et affichage du fichier avec le type mime qui va bien
header('Content-Type: ' . OledrionUtility::getMimeType($file));
header('Content-disposition: inline; filename="' . basename($file) . '"');
echo $fileContent;
