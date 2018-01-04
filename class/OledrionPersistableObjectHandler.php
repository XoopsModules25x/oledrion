<?php namespace XoopsModules\Oledrion;

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
 * Persistable Object Handler class.
 * This class is responsible for providing data access mechanisms to the data source
 * of derived class objects. Original Author : Mithrandir
 *
 */
class OledrionPersistableObjectHandler extends \XoopsPersistableObjectHandler
{
    /**#@+
     * Information about the class, the handler is managing
     *
     * @var string
     */
    public $table;
    public $keyName;
    public $className;
    public $identifierName;
    public $cacheOptions = [];

    /**#@-*/

    /**
     * Constructor - called from child classes
     * @param null|\XoopsDatabase $db             {@link XoopsDatabase}
     *                                           object
     * @param string             $tablename      Name of database table
     * @param string             $classname      Name of Class, this handler is managing
     * @param string             $keyname        Name of the property, holding the key
     * @param string             $idenfierName   Name of the property, holding the label
     * @param array              $cacheOptions   Optional, options for the cache
     */
    public function __construct(\XoopsDatabase $db, $tablename, $classname, $keyname, $idenfierName = '', $cacheOptions = null)
    {
        //require_once __DIR__ . '/../include/common.php';
        require_once XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
        //        $this->XoopsObjectHandler($db);
        parent::__construct($db);
        $this->table     = $db->prefix($tablename);
        $this->keyName   = $keyname;
        $this->className = $classname;
        if ('' !== trim($idenfierName)) {
            $this->identifierName = $idenfierName;
        }
        // To diable cache, add this line after the first one : 'caching' => false,
        if (null === $cacheOptions) {
            $this->setCachingOptions([
                                         'cacheDir'               => OLEDRION_CACHE_PATH,
                                         'lifeTime'               => null,
                                         'automaticSerialization' => true,
                                         'fileNameProtection'     => false
                                     ]);
        } else {
            $this->setCachingOptions($cacheOptions);
        }
    }

    /**
     * @param $cacheOptions
     */
    public function setCachingOptions($cacheOptions)
    {
        $this->cacheOptions = $cacheOptions;
    }

    /**
     * Generates a unique ID for a Sql Query
     *
     * @param  string  $query The SQL query for which we want a unidque ID
     * @param  integer $start Which record to start at
     * @param  integer $limit Max number of objects to fetch
     * @return string  An MD5 of the query
     */
    protected function _getIdForCache($query, $start, $limit)
    {
        $id = md5($query . '-' . (string)$start . '-' . (string)$limit);

        return $id;
    }



    /**
     * Convert a database resultset to a returnable array
     *
     * @param \mysqli_result $result    database resultset
     * @param boolean       $id_as_key - should NOT be used with joint keys
     * @param boolean       $as_object
     * @param string        $fields    Requested fields from the query
     *
     * @return array
     */
    public function convertResultSet($result, $id_as_key = false, $as_object = true, $fields = '*')
    {
        $ret = [];
        while ($myrow = $this->db->fetchArray($result)) {
            $obj = $this->create(false);
            $obj->assignVars($myrow);
            if (!$id_as_key) {
                if ($as_object) {
                    $ret[] = $obj;
                } else {
                    $row     = [];
                    $vars    = $obj->getVars();
                    $tbl_tmp = array_keys($vars);
                    foreach ($tbl_tmp as $i) {
                        $row[$i] = $obj->getVar($i);
                    }
                    $ret[] = $row;
                }
            } else {
                if ($as_object) {
                    if ('*' === $fields) {
                        $ret[$myrow[$this->keyName]] = $obj;
                    } else {
                        $ret[] = $obj;
                    }
                } else {
                    $row     = [];
                    $vars    = $obj->getVars();
                    $tbl_tmp = array_keys($vars);
                    foreach ($tbl_tmp as $i) {
                        $row[$i] = $obj->getVar($i);
                    }
                    $ret[$myrow[$this->keyName]] = $row;
                }
            }
            unset($obj);
        }

        return $ret;
    }



    /**
     * Retourne des éléments selon leur ID
     *
     * @param  array $ids Les ID des éléments à retrouver
     * @return array Tableau d'objets (clé = id key name)
     */
    public function getItemsFromIds($ids)
    {
        $ret = [];
        if (is_array($ids) && count($ids) > 0) {
            $criteria = new \Criteria($this->keyName, '(' . implode(',', $ids) . ')', 'IN');
            $ret      = $this->getObjects($criteria, true);
        }

        return $ret;
    }


    /**
     * Retourne le total d'un champ
     *
     * @param  string          $field    Le champ dont on veut calculer le total
     * @param  \CriteriaElement $criteria {@link CriteriaElement} to match
     * @return integer le total
     */
    public function getSum($field, $criteria = null)
    {
        $limit = $start = 0;
        //require_once __DIR__ . '/lite.php';

        $sql = 'SELECT Sum(' . $field . ') as cpt FROM ' . $this->table;
        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ('' !== $criteria->groupby) {
                $sql .= $criteria->getGroupby();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        //$Cache_Lite = new oledrion_Cache_Lite($this->cacheOptions);
        $id = $this->_getIdForCache($sql, $start, $limit);
        //$cacheData = $Cache_Lite->get($id);
        //if ($cacheData === false) {
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            $ret = 0;

            //$Cache_Lite->save($ret);
            return $ret;
        }
        $row   = $this->db->fetchArray($result);
        $count = $row['cpt'];

        //$Cache_Lite->save($count);
        return $count;
        //} else {
        //  return $cacheData;
        // }
    }


    /**
     * Quickly insert a record like this $myobjectHandler->quickInsert('field1' => field1value, 'field2' => $field2value)
     *
     * @param  array $vars  Array containing the fields name and value
     * @param  bool  $force whether to force the query execution despite security settings
     * @return bool  @link insert's value
     */
    public function quickInsert($vars = null, $force = true)
    {
        $object = $this->create(true);
        $object->setVars($vars);
        $retval = $this->insert($object, $force);
        unset($object);

        // Clear cache
        $this->forceCacheClean();

        return $retval;
    }



    //  check if target object is attempting to use duplicated info

    /**
     * @param         $obj
     * @param  string $field
     * @param  string $error
     * @return bool
     */
    public function isDuplicated($obj, $field = '', $error = '')
    {
        if (empty($field)) {
            return false;
        }
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria($field, $obj->getVar($field)));
        //  one more condition if target object exisits in database
        if (!$obj->isNew()) {
            $criteria->add(new \Criteria($this->_key, $obj->getVar($this->_key), '!='));
        }
        if ($this->getCount($criteria)) {
            $obj->setErrors($error);

            return true;
        }

        return false;
    }



    /**
     * Compare two objects and returns, in an array, the differences
     *
     * @param  \XoopsObject $old_object The first object to compare
     * @param  \XoopsObject $new_object The new object
     * @return array       differences    key = fieldname, value = array('old_value', 'new_value')
     */
    public function compareObjects($old_object, $new_object)
    {
        $ret       = [];
        $vars_name = array_keys($old_object->getVars());
        foreach ($vars_name as $one_var) {
            if ($old_object->getVar($one_var, 'f') == $new_object->getVar($one_var, 'f')) {
            } else {
                $ret[$one_var] = [$old_object->getVar($one_var), $new_object->getVar($one_var)];
            }
        }

        return $ret;
    }

    /**
     * Get distincted values of a field in the table
     *
     * @param  string          $field    Field's name
     * @param  \CriteriaElement $criteria {@link CriteriaElement} conditions to be met
     * @param  string          $format   Format in wich we want the datas
     * @return array  containing the distinct values
     */
    public function getDistincts($field, $criteria = null, $format = 's')
    {
        //require_once __DIR__ . '/lite.php';
        $limit = $start = 0;
        $sql   = 'SELECT ' . $this->keyName . ', ' . $field . ' FROM ' . $this->table;
        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
            $sql   .= ' ' . $criteria->renderWhere();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $sql .= ' GROUP BY ' . $field . ' ORDER BY ' . $field;

        //$Cache_Lite = new oledrion_Cache_Lite($this->cacheOptions);
        $id = $this->_getIdForCache($sql, $start, $limit);
        //$cacheData = $Cache_Lite->get($id);
        //if ($cacheData === false) {
        $result = $this->db->query($sql, $limit, $start);
        $ret    = [];
        $obj    = new $this->className();
        while ($myrow = $this->db->fetchArray($result)) {
            $obj->setVar($field, $myrow[$field]);
            $ret[$myrow[$this->keyName]] = $obj->getVar($field, $format);
        }

        //$Cache_Lite->save($ret);
        return $ret;
        //} else {
        //return $cacheData;
        // }
    }

    /**
     * A generic shortcut to getObjects
     *
     * @author Herve Thouzard - Instant Zero
     *
     * @param  integer $start   Starting position
     * @param  integer $limit   Maximum count of elements to return
     * @param  string  $sort    Field to use for the sort
     * @param  string  $order   Sort order
     * @param  boolean $idAsKey Do we have to return an array whoses keys are the record's ID ?
     * @return array   Array of current objects
     */
    public function getItems($start = 0, $limit = 0, $sort = '', $order = 'ASC', $idAsKey = true)
    {
        if ('' === trim($order)) {
            if (isset($this->identifierName) && '' !== trim($this->identifierName)) {
                $order = $this->identifierName;
            } else {
                $order = $this->keyName;
            }
        }
        $items   = [];
        $critere = new \Criteria($this->keyName, 0, '<>');
        $critere->setLimit($limit);
        $critere->setStart($start);
        $critere->setSort($sort);
        $critere->setOrder($order);
        $items = $this->getObjects($critere, $idAsKey);

        return $items;
    }

    /**
     * Forces the cache to be cleaned
     */
    public function forceCacheClean()
    {
        //require_once __DIR__ . '/lite.php';
        //$Cache_Lite = new oledrion_Cache_Lite($this->cacheOptions);
        //$Cache_Lite->clean();
    }
}
