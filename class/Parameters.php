<?php

namespace XoopsModules\Oledrion;

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
 * Class used for parameters passing to classes methods
 *
 * @copyright          Hervé Thouzard (http://www.herve-thouzard.com/)
 * @license            http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author             Hervé Thouzard (http://www.herve-thouzard.com/)
 *
 * Example :
 *
 * // Instantiate it like this
 * $param = new Oledrion\Parameters();
 *
 * // Create several parameters in one time:
 * $param->setLimit(10)->setSort('manu_name');
 *
 * // Set a parameter with the array convention:
 * $param['sort'] = 'first_name';
 *
 * // Set a parameter as a class property:
 * $param->order = 'DESC';
 *
 * // Display a parameter, first way:
 * echo "<br>value=".$param['sort'];    // DESC
 *
 * // Another method to show it, as a class method:
 * echo $param->limit();    // 10
 *
 * // Set the default values
 * $newParameters = $param->extend(new Oledrion\Parameters(array('sort' => 'firstName', 'start' => 0, 'limit' => 15, 'showAll' => true)));
 */

use XoopsModules\Oledrion;

/**
 * Class Parameters
 */
class Parameters extends \ArrayObject
{
    /**
     * Allows you to value an index of the class as if it were a property of the class
     *
     * @example $record->field_name = 'my channel'
     *
     * @param  string $key   The name of the field to be treated
     * @param  mixed  $value The value to assign
     * @return \XoopsModules\Oledrion\Parameters
     */
    public function __set($key, $value)
    {
        parent::offsetSet($key, $value);

        return $this;
    }

    /**
     * Valuation of an index of the class using a function call based on the following principle:
     *         $maClasse->setLimit(10);
     * It is possible to chain it like this : $maClasse->setStart(0)->setLimit(10);
     *
     * @param  string $method
     * @param  mixed  $args
     * @return Parameters|\ArrayObject
     */
    public function __call($method, $args)
    {
        if (0 === mb_strpos($method, 'set')) {
            parent::offsetSet(mb_strtolower($method[3]) . mb_substr($method, 4), $args[0]);

            return $this;
        }

        // Value display

        return parent::offsetGet($method);
    }

    /**
     * Method that tries to do the same thing as jQuery's extend() method
     *
     * We pass the default values ​​that we expect and the method compares them with the current values
     * If values ​​are missing, they are added
     *
     * @param self $defaultValues
     * @return Parameters
     */
    public function extend(self $defaultValues)
    {
        $result = new self();
        $result = $this;
        foreach ($defaultValues as $key => $value) {
            if (!isset($result[$key])) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
