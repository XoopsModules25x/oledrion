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
 * @author      Hervé Thouzard (http://www.herve-thouzard.com/)
 */

/**
 * block to display ajax search
 * @param $options
 * @return array
 */
function b_oledrion_ajax_search_show($options)
{
    global $xoopsConfig, $xoTheme;

    include XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';

    if ($options[0] == 1) {
        $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
        $xoTheme->addScript(OLEDRION_URL . 'assets/js/autocomplete.js');
        $xoTheme->addStylesheet(OLEDRION_URL . 'assets/css/autocomplete.css');
        $xoTheme->addStylesheet(OLEDRION_URL . 'assets/css/oledrion.css');
    }

    $block           = array();
    $block['custom'] = $options[0];

    return $block;
}

/**
 * @param $options
 * @return string
 */
function b_oledrion__ajax_search_edit($options)
{
    $form                  = '';
    $checkeds              = array('', '');
    $checkeds[$options[0]] = 'checked';
    $form .= "<table border='0'>";
    $form .= '<tr><td>' . _MB_OLEDRION_USE_STYLE_JS . "</td><td><input type='radio' name='options[]' id='options[]' value='0' " . $checkeds[0] . ' />' . _NO . " <input type='radio' name='options[]' id='options[]' value='1' " . $checkeds[1] . ' />'
             . _YES . '</td></tr>';
    $form .= '</table>';

    return $form;
}
