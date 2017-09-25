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
 * Affiche le bloc des catégories en fonction de la catégorie en cours (fonctionne de paire avec les pages du module)
 * @param $options
 * @return array
 */
function b_oledrion_category_show($options)
{
    global $xoTheme;
    $block = [];
    include XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
    $xoTheme->addStylesheet(OLEDRION_URL . 'assets/css/oledrion.css');

    $block['nostock_msg'] = OledrionUtility::getModuleOption('nostock_msg');

    if (0 == (int)$options[0]) { // Catégories selon la page en cours
        $block['block_option'] = 0;
        if (!isset($GLOBALS['current_category']) || $GLOBALS['current_category'] == -1) {
            return false;
        }
        $cat_cid = (int)$GLOBALS['current_category'];
        include XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';

        if ($cat_cid > 0) {
            require_once XOOPS_ROOT_PATH . '/class/tree.php';
            $tbl_categories = $tblChilds = $tbl_tmp = [];
            $tbl_categories = $h_oledrion_cat->getAllCategories(new Oledrion_parameters());
            $mytree         = new XoopsObjectTree($tbl_categories, 'cat_cid', 'cat_pid');
            $tblChilds      = $mytree->getAllChild($cat_cid);
            //$tblChilds = array_reverse($tblChilds);
            foreach ($tblChilds as $item) {
                $tbl_tmp[] = "<a href='" . $item->getLink() . "' title='" . OledrionUtility::makeHrefTitle($item->getVar('cat_title')) . "'>" . $item->getVar('cat_title') . '</a>';
            }
            $block['block_categories'] = $tbl_tmp;

            $category = null;
            if ($cat_cid > 0) {
                $category = $h_oledrion_cat->get($cat_cid);
                if (is_object($category)) {
                    $block['block_current_category'] = $category->toArray();
                }
            }
        } else { // On est à la racine, on n'affiche donc que les catégories mères
            $tbl_categories = [];
            $criteria       = new Criteria('cat_pid', 0, '=');
            $criteria->setSort('cat_title');
            $tbl_categories = $h_oledrion_cat->getObjects($criteria, true);
            foreach ($tbl_categories as $item) {
                $tbl_tmp[] = "<a href='" . $item->getLink() . "' title='" . OledrionUtility::makeHrefTitle($item->getVar('cat_title')) . "'>" . $item->getVar('cat_title') . '</a>';
            }
            $block['block_categories'] = $tbl_tmp;
        }
    } elseif (1 == (int)$options[0]) { // Affichage classique
        $block['block_option'] = 1;
        include XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
        require_once OLEDRION_PATH . 'class/tree.php';
        $tbl_categories = $h_oledrion_cat->getAllCategories(new Oledrion_parameters());
        $mytree         = new Oledrion_XoopsObjectTree($tbl_categories, 'cat_cid', 'cat_pid');
        $jump           = OLEDRION_URL . 'category.php?cat_cid=';
        $additional     = "onchange='location=\"" . $jump . "\"+this.options[this.selectedIndex].value'";
        if (isset($GLOBALS['current_category']) && $GLOBALS['current_category'] != -1) {
            $cat_cid = (int)$GLOBALS['current_category'];
        } else {
            $cat_cid = 0;
        }
        $htmlSelect          = $mytree->makeSelBox('cat_cid', 'cat_title', '-', $cat_cid, false, 0, $additional);
        $block['htmlSelect'] = $htmlSelect;
    } else { // Affichage de toute l'arborescence, dépliée
        $block['block_option'] = 2;
        $block['liMenu']       = $h_oledrion_cat->getUlMenu('category_title');
    }

    return $block;
}

/**
 * @param $options
 * @return string
 */
function b_oledrion_category_edit($options)
{
    global $xoopsConfig;
    include XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';

    $checkeds              = ['', '', ''];
    $checkeds[$options[0]] = 'checked';
    $form                  = '';
    $form                  .= '<b>'
                              . _MB_OLEDRION_TYPE_BLOCK
                              . "</b><br><input type='radio' name='options[]' id='options[]' value='0' "
                              . $checkeds[0]
                              . '>'
                              . _MB_OLEDRION_TYPE_BLOCK2
                              . "<br><input type='radio' name='options[]' id='options[]' value='1' "
                              . $checkeds[1]
                              . '>'
                              . _MB_OLEDRION_TYPE_BLOCK1
                              . "<br><input type='radio' name='options[]' id='options[]' value='2' "
                              . $checkeds[2]
                              . '>'
                              . _MB_OLEDRION_TYPE_BLOCK3
                              . '</td></tr>';

    return $form;
}

/**
 * Bloc à la volée
 * @param $options
 */
function b_oledrion_category_duplicatable($options)
{
    $options = explode('|', $options);
    $block   = b_oledrion_category($options);

    $tpl = new XoopsTpl();
    $tpl->assign('block', $block);
    $tpl->display('db:oledrion_block_categories.tpl');
}
