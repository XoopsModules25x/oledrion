<?php

namespace XoopsModules\Oledrion\Common;

/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Breadcrumb Class
 *
 * @copyright   XOOPS Project (https://xoops.org)
 * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author      lucio <lucio.rota@gmail.com>
 */
defined('XOOPS_ROOT_PATH') || die('XOOPS Root Path not defined');

/**
 * Class Breadcrumb
 */
class Breadcrumb
{
    public  $dirname;
    private $bread = [];

    public function __construct()
    {
        $this->dirname = basename(dirname(dirname(__DIR__)));
    }

    /**
     * Add link to breadcrumb
     *
     * @param string $title
     * @param string $link
     */
    public function addLink($title = '', $link = '')
    {
        $this->bread[] = [
            'link'  => $link,
            'title' => $title,
        ];
    }

    /**
     * Render BreadCrumb
     */
    public function render()
    {
        if (!isset($GLOBALS['xoTheme']) || !is_object($GLOBALS['xoTheme'])) {
            require_once $GLOBALS['xoops']->path('class/theme.php');
            $GLOBALS['xoTheme'] = new \xos_opal_Theme();
        }

        require_once $GLOBALS['xoops']->path('class/template.php');
        $breadcrumbTpl = new \XoopsTpl();
        $breadcrumbTpl->assign('breadcrumb', $this->bread);
        $html = $breadcrumbTpl->fetch('db:' . $this->dirname . '_common_breadcrumb.tpl');
        unset($breadcrumbTpl);

        return $html;
    }
}
