<?php namespace XoopsModules\Oledrion;

/**
 * XOOPS tree class
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @since               2.0.0
 * @author              Kazumi Ono (http://www.myweb.ne.jp/, http://jp.xoops.org/)
 */

use XoopsModules\Oledrion;

defined('XOOPS_ROOT_PATH') || die('Restricted access');

require_once dirname(dirname(dirname(__DIR__))) . '/class/tree.php';

/**
 * A tree structures with {@link XoopsObject}s as nodes
 *
 * @package          kernel
 * @subpackage       core
 * @author           Kazumi Ono     <onokazu@xoops.org>
 */
class XoopsObjectTree extends \XoopsObjectTree
{
    /**
     * @access    private
     */
    protected $parentId;
    protected $myId;
    protected $rootId;
    protected $tree = [];
    protected $objects;

    /**
     * Constructor
     *
     * @param array  $objectArr Array of {@link XoopsObject}s
     * @param string $myId      field name of object ID
     * @param string $parentId  field name of parent object ID
     * @param string $rootId    field name of root object ID
     */
    public function __construct($objectArr, $myId, $parentId, $rootId = null)
    {
        parent::__construct($objectArr, $myId, $parentId, $rootId);
    }

    /**
     * Make a select box with options from the tree
     *
     * @param  string      $name           Name of the select box
     * @param  string      $fieldName      Name of the member variable from the
     *                                     node objects that should be used as the title for the options.
     * @param  string      $prefix         String to indent deeper levels
     * @param  string      $selected       Value to display as selected
     * @param  bool|string $addEmptyOption Set TRUE to add an empty option with value "0" at the top of the hierarchy
     * @param  integer     $key            ID of the object to display as the root of select options
     * @param  string      $additional
     * @return string      HTML select box
     *
     * @deprecated since 2.5.9, please use makeSelectElement()
     */
    public function makeSelBox(
        $name,
        $fieldName,
        $prefix = '-',
        $selected = '',
        $addEmptyOption = '',
        $key = 0,
        $additional = '')
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        trigger_error("makeSelBox() is deprecated since 2.5.9, please use makeSelectElement(), accessed from {$trace[0]['file']} line {$trace[0]['line']},");

        $ret2 = '<select name="' . $name . '" id="' . $name . '" ' . $additional . '>'; //mb needs to test
        if (false !== (bool)$addEmptyOption) {
            $ret2 .= '<option value="0"></option>';
        }

        $ret = "<select id='" . $name . "' name='" . $name . "'";
        if ('' !== $additional) {
            $ret .= $additional;
        }
        $ret .= '>';
        if ('' !== $addEmptyOption) {
            $tmpSelected = '';
            if (0 == $selected) {
                $tmpSelected = ' selected';
            }
            $ret .= '<option' . $tmpSelected . ' value="0">' . $addEmptyOption . '</option>';
        }

        $this->makeSelBoxOptions($fieldName, $selected, $key, $ret, $prefix);

        return $ret . '</select>';
    }

    /**
     * Magic __get method
     *
     * Some modules did not respect the leading underscore is private convention and broke
     * when code was modernized. This will keep them running for now.
     *
     * @param string $name  unknown variable name requested
     *                      currently only '_tree' is supported
     *
     * @return mixed value
     */
    public function __get($name)
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        if ('_tree' === $name) {
            trigger_error("XoopsObjectTree::\$_tree is deprecated, accessed from {$trace[0]['file']} line {$trace[0]['line']},");

            return $this->tree;
        }
        trigger_error('Undefined property: XoopsObjectTree::$' . $name . " in {$trace[0]['file']} line {$trace[0]['line']}, ", E_USER_NOTICE);

        return null;
    }

    /**
     * Internal function used by makeTreeAsArray
     * @param        $fieldName
     * @param        $key
     * @param        $ret
     * @param        $prefix_orig
     * @param string $prefix_curr
     */
    public function _recursiveMakeTreeAsArray($fieldName, $key, &$ret, $prefix_orig, $prefix_curr = '')
    {
        if ($key > 0) {
            $value       = $this->tree[$key]['obj']->getVar($this->myId);
            $ret[$value] = $prefix_curr . $this->tree[$key]['obj']->getVar($fieldName);
            $prefix_curr .= $prefix_orig;
        }
        if (isset($this->tree[$key]['child']) && !empty($this->tree[$key]['child'])) {
            foreach ($this->tree[$key]['child'] as $childkey) {
                $this->_recursiveMakeTreeAsArray($fieldName, $childkey, $ret, $prefix_orig, $prefix_curr);
            }
        }
    }

    /**
     * Identical function as makeSelBox but returns an array
     *
     * @param  string  $fieldName Name of the member variable from the node objects that should be used as the title for the options.
     * @param  string  $prefix    String to indent deeper levels
     * @param  integer $key       ID of the object to display as the root of select options
     * @param  null    $empty
     * @return array   key = object ID, value = $fieldName
     */
    public function makeTreeAsArray($fieldName, $prefix = '-', $key = 0, $empty = null)
    {
        $ret = [];
        if (null != $empty) {
            $ret[0] = $empty;
        }
        $this->_recursiveMakeTreeAsArray($fieldName, $key, $ret, $prefix);

        return $ret;
    }
}
