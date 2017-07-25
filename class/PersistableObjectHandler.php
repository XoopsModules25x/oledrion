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
// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

class Oledrion_Object extends XoopsObject
{
    /**
     * @param  string $format
     * @return array
     */
    public function toArray($format = 's')
    {
        $ret = array();
        foreach ($this->vars as $k => $v) {
            $ret[$k] = $this->getVar($k, $format);
        }

        return $ret;
    }

    // TODO: Rajouter une méthode intsert() et delete()

    /**
     * Permet de valoriser un champ de la table comme si c'était une propriété de la classe
     *
     * @example $enregistrement->nom_du_champ = 'ma chaine'
     *
     * @param  string $key   Le nom du champ à traiter
     * @param  mixed  $value La valeur à lui attribuer
     * @return void
     */
    public function __set($key, $value)
    {
        return $this->setVar($key, $value);
    }

    /**
     * Permet d'accéder aux champs de la table comme à des propriétés de la classe
     *
     * @example echo $enregistrement->nom_du_champ;
     *
     * @param  string $key Le nom du champ que l'on souhaite récupérer
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getVar($key);
    }
}

/**
 * Persistable Object Handler class.
 * This class is responsible for providing data access mechanisms to the data source
 * of derived class objects. Original Author : Mithrandir
 *
 */
class Oledrion_XoopsPersistableObjectHandler extends XoopsObjectHandler
{
    /**#@+
     * Information about the class, the handler is managing
     *
     * @var string
     */
    public $table;
    protected $keyName;
    protected $className;
    protected $identifierName;
    protected $cacheOptions = array();

    /**#@-*/

    /**
     * Constructor - called from child classes
     * @param object|XoopsDatabase $db           {@link XoopsDatabase}
     *                                           object
     * @param string               $tablename    Name of database table
     * @param string               $classname    Name of Class, this handler is managing
     * @param string               $keyname      Name of the property, holding the key
     * @param string               $idenfierName Name of the property, holding the label
     * @param array                $cacheOptions Optional, options for the cache
     */
    public function __construct(XoopsDatabase $db, $tablename, $classname, $keyname, $idenfierName = '', $cacheOptions = null)
    {
        //require_once __DIR__ . '/../include/common.php';
        require_once XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
        //        $this->XoopsObjectHandler($db);
        parent::__construct($db);
        $this->table     = $db->prefix($tablename);
        $this->keyName   = $keyname;
        $this->className = $classname;
        if (trim($idenfierName) != '') {
            $this->identifierName = $idenfierName;
        }
        // To diable cache, add this line after the first one : 'caching' => false,
        if (null === $cacheOptions) {
            $this->setCachingOptions(array(
                                         'cacheDir'               => OLEDRION_CACHE_PATH,
                                         'lifeTime'               => null,
                                         'automaticSerialization' => true,
                                         'fileNameProtection'     => false
                                     ));
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
     * create a new object
     *
     * @param bool $isNew Flag the new objects as "new"?
     *
     * @return object
     */
    public function create($isNew = true)
    {
        $obj = new $this->className();
        if ($isNew === true) {
            $obj->setNew();
        }

        return $obj;
    }

    /**
     * retrieve an object
     *
     * @param  mixed $id        ID of the object - or array of ids for joint keys. Joint keys MUST be given in the same order as in the constructor
     * @param  bool  $as_object whether to return an object or an array
     * @return mixed reference to the object, FALSE if failed
     */
    public function get($id, $as_object = true)
    {
        if (is_array($this->keyName)) {
            $criteria = new CriteriaCompo();
            $vnb      = count($this->keyName);
            for ($i = 0; $i < $vnb; ++$i) {
                $criteria->add(new Criteria($this->keyName[$i], (int)$id[$i]));
            }
        } else {
            $criteria = new Criteria($this->keyName, (int)$id);
        }
        $criteria->setLimit(1);
        $obj_array = $this->getObjects($criteria, false, $as_object);
        if (count($obj_array) != 1) {
            $ret = null;
        } else {
            $ret = $obj_array[0];
        }

        return $ret;
    }

    /**
     * retrieve objects from the database
     *
     * @param object  $criteria  {@link CriteriaElement} conditions to be met
     * @param bool    $id_as_key use the ID as key for the array?
     * @param bool    $as_object return an array of objects?
     *
     * @param  string $fields
     * @param  bool   $autoSort
     * @return array
     */
    public function getObjects(
        $criteria = null,
        $id_as_key = false,
        $as_object = true,
        $fields = '*',
        $autoSort = true
    ) {
        //require_once __DIR__ . '/lite.php';
        $ret   = array();
        $limit = $start = 0;
        $sql   = 'SELECT ' . $fields . ' FROM ' . $this->table;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ($criteria->groupby != '') {
                $sql .= $criteria->getGroupby();
            }
            if ($criteria->getSort() != '') {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            } elseif ($this->identifierName != '' && $autoSort) {
                $sql .= ' ORDER BY ' . $this->identifierName;
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
            return $ret;
        }
        $ret = $this->convertResultSet($result, $id_as_key, $as_object, $fields);

        //$Cache_Lite->save($ret);
        return $ret;
        //} else {
        //return $cacheData;
        //}
    }

    /**
     * Convert a database resultset to a returnable array
     *
     * @param object  $result    database resultset
     * @param boolean $id_as_key - should NOT be used with joint keys
     * @param boolean $as_object
     * @param string  $fields    Requested fields from the query
     *
     * @return array
     */
    protected function convertResultSet($result, $id_as_key = false, $as_object = true, $fields = '*')
    {
        $ret = array();
        while ($myrow = $this->db->fetchArray($result)) {
            $obj = $this->create(false);
            $obj->assignVars($myrow);
            if (!$id_as_key) {
                if ($as_object) {
                    $ret[] = $obj;
                } else {
                    $row     = array();
                    $vars    = $obj->getVars();
                    $tbl_tmp = array_keys($vars);
                    foreach ($tbl_tmp as $i) {
                        $row[$i] = $obj->getVar($i);
                    }
                    $ret[] = $row;
                }
            } else {
                if ($as_object) {
                    if ($fields == '*') {
                        $ret[$myrow[$this->keyName]] = $obj;
                    } else {
                        $ret[] = $obj;
                    }
                } else {
                    $row     = array();
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
     * get IDs of objects matching a condition
     *
     * @param  object $criteria {@link CriteriaElement} to match
     * @return array  of object IDs
     */
    public function getIds($criteria = null)
    {
        //require_once __DIR__ . '/lite.php';
        $limit = $start = 0;

        //$Cache_Lite = new oledrion_Cache_Lite($this->cacheOptions);
        $sql = 'SELECT ' . $this->keyName . ' FROM ' . $this->table;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ($criteria->groupby != '') {
                $sql .= $criteria->getGroupby();
            }
            if ($criteria->getSort() != '') {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            } elseif ($this->identifierName != '') {
                $sql .= ' ORDER BY ' . $this->identifierName;
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }

        $id = $this->_getIdForCache($sql, $start, $limit);
        //$cacheData = $Cache_Lite->get($id);
        //if ($cacheData === false) {
        $result = $this->db->query($sql, $limit, $start);
        $ret    = array();
        while ($myrow = $this->db->fetchArray($result)) {
            $ret[] = $myrow[$this->keyName];
        }

        //$Cache_Lite->save($ret);
        return $ret;
        //} else {
        //  return $cacheData;
        //}
    }

    /**
     * Retrieve a list of objects as arrays - DON'T USE WITH JOINT KEYS
     *
     * @param  object $criteria {@link CriteriaElement} conditions to be met
     * @return array
     */
    public function getList($criteria = null)
    {
        //require_once __DIR__ . '/lite.php';
        $limit = $start = 0;
        //$Cache_Lite = new oledrion_Cache_Lite($this->cacheOptions);

        $ret = array();

        $sql = 'SELECT ' . $this->keyName;
        if (!empty($this->identifierName)) {
            $sql .= ', ' . $this->identifierName;
        }
        $sql .= ' FROM ' . $this->table;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ($criteria->groupby != '') {
                $sql .= $criteria->getGroupby();
            }
            if ($criteria->getSort() != '') {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            } elseif ($this->identifierName != '') {
                $sql .= ' ORDER BY ' . $this->identifierName;
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }

        $id = $this->_getIdForCache($sql, $start, $limit);
        // $cacheData = $Cache_Lite->get($id);
        //if ($cacheData === false) {
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            //$Cache_Lite->save($ret);
            return $ret;
        }

        $myts = MyTextSanitizer::getInstance();
        while ($myrow = $this->db->fetchArray($result)) {
            // identifiers should be textboxes, so sanitize them like that
            $ret[$myrow[$this->keyName]] = empty($this->identifierName) ? 1 : $myts->htmlSpecialChars($myrow[$this->identifierName]);
        }

        //$Cache_Lite->save($ret);
        return $ret;
        //} else {
        //return $cacheData;
        // }
    }

    /**
     * Retourne des éléments selon leur ID
     *
     * @param  array $ids Les ID des éléments à retrouver
     * @return array Tableau d'objets (clé = id key name)
     */
    public function getItemsFromIds($ids)
    {
        $ret = array();
        if (is_array($ids) && count($ids) > 0) {
            $criteria = new Criteria($this->keyName, '(' . implode(',', $ids) . ')', 'IN');
            $ret      = $this->getObjects($criteria, true);
        }

        return $ret;
    }

    /**
     * count objects matching a condition
     *
     * @param  object $criteria {@link CriteriaElement} to match
     * @return int    count of objects
     */
    public function getCount($criteria = null)
    {
        $field   = '';
        $groupby = false;
        $limit   = $start = 0;
        //require_once __DIR__ . '/lite.php';

        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            if ($criteria->groupby != '') {
                $groupby = true;
                $field   = $criteria->groupby . ', '; //Not entirely secure unless you KNOW that no criteria's groupby clause is going to be mis-used
            }
        }
        $sql = 'SELECT ' . $field . 'COUNT(*) FROM ' . $this->table;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ($criteria->groupby != '') {
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
            $Cache_Lite->save($ret);

            return $ret;
        }
        if ($groupby === false) {
            list($count) = $this->db->fetchRow($result);

            //$Cache_Lite->save($count);
            return $count;
        } else {
            $ret = array();
            while (list($id, $count) = $this->db->fetchRow($result)) {
                $ret[$id] = $count;
            }

            //  $Cache_Lite->save($ret);
            return $ret;
        }
        //} else {
        //return $cacheData;
        //}
    }

    /**
     * Retourne le total d'un champ
     *
     * @param  string $field    Le champ dont on veut calculer le total
     * @param  object $criteria {@link CriteriaElement} to match
     * @return integer le total
     */
    public function getSum($field, $criteria = null)
    {
        $limit = $start = 0;
        //require_once __DIR__ . '/lite.php';

        $sql = 'SELECT Sum(' . $field . ') as cpt FROM ' . $this->table;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ($criteria->groupby != '') {
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
     * delete an object from the database
     *
     * @param  XoopsObject $obj reference to the object to delete
     * @param  bool        $force
     * @return bool   FALSE if failed.
     */
    public function delete(XoopsObject $obj, $force = false)
    {
        if (is_array($this->keyName)) {
            $clause = array();
            $vnb    = count($this->keyName);
            for ($i = 0; $i < $vnb; ++$i) {
                $clause[] = $this->keyName[$i] . ' = ' . $obj->getVar($this->keyName[$i]);
            }
            $whereclause = implode(' AND ', $clause);
        } else {
            $whereclause = $this->keyName . ' = ' . $obj->getVar($this->keyName);
        }
        $sql = 'DELETE FROM ' . $this->table . ' WHERE ' . $whereclause;
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        // Clear cache
        $this->forceCacheClean();

        if (!$result) {
            return false;
        }

        return true;
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

    /**
     * insert a new object in the database
     *
     * @param  XoopsObject $obj         reference to the object
     * @param  bool        $force       whether to force the query execution despite security settings
     * @param  bool        $checkObject check if the object is dirty and clean the attributes
     * @return bool   FALSE if failed, TRUE if already present and unchanged or successful
     */

    public function insert(XoopsObject $obj, $force = false, $checkObject = true)
    {
        if ($checkObject !== false) {
            if (!is_object($obj)) {
                trigger_error('Error, not object');

                return false;
            }
            /**
             * @TODO: Change to if (!(class_exists($this->className) && $obj instanceof $this->className)) when going fully PHP5
             */
            if (!is_a($obj, $this->className)) {
                $obj->setErrors(get_class($obj) . ' Differs from ' . $this->className);

                return false;
            }
            if (!$obj->isDirty()) {
                $obj->setErrors('Not dirty'); //will usually not be outputted as errors are not displayed when the method returns true, but it can be helpful when troubleshooting code - Mith

                return true;
            }
        }
        if (!$obj->cleanVars()) {
            foreach ($obj->getErrors() as $oneerror) {
                trigger_error($oneerror);
            }

            return false;
        }
        foreach ($obj->cleanVars as $k => $v) {
            if ($obj->vars[$k]['data_type'] == XOBJ_DTYPE_INT) {
                $cleanvars[$k] = (int)$v;
            } elseif (is_array($v)) {
                $cleanvars[$k] = $this->db->quoteString(implode(',', $v));
            } else {
                $cleanvars[$k] = $this->db->quoteString($v);
            }
        }
        if (isset($cleanvars['dohtml'])) { // Modification Hervé to be able to use dohtml
            unset($cleanvars['dohtml']);
        }
        if ($obj->isNew()) {
            if (!is_array($this->keyName)) {
                if ($cleanvars[$this->keyName] < 1) {
                    $cleanvars[$this->keyName] = $this->db->genId($this->table . '_' . $this->keyName . '_seq');
                }
            }
            $sql = 'INSERT INTO ' . $this->table . ' (' . implode(',', array_keys($cleanvars)) . ') VALUES (' . implode(',', array_values($cleanvars)) . ')';
        } else {
            $sql = 'UPDATE ' . $this->table . ' SET';
            foreach ($cleanvars as $key => $value) {
                if ((!is_array($this->keyName) && $key == $this->keyName)
                    || (is_array($this->keyName)
                        && in_array($key, $this->keyName))) {
                    continue;
                }
                if (isset($notfirst)) {
                    $sql .= ',';
                }
                $sql      .= ' ' . $key . ' = ' . $value;
                $notfirst = true;
            }
            if (is_array($this->keyName)) {
                $whereclause = '';
                $vnb         = count($this->keyName);
                for ($i = 0; $i < $vnb; ++$i) {
                    if ($i > 0) {
                        $whereclause .= ' AND ';
                    }
                    $whereclause .= $this->keyName[$i] . ' = ' . $obj->getVar($this->keyName[$i]);
                }
            } else {
                $whereclause = $this->keyName . ' = ' . $obj->getVar($this->keyName);
            }
            $sql .= ' WHERE ' . $whereclause;
        }

        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }

        // Clear cache
        $this->forceCacheClean();

        if (!$result) {
            return false;
        }
        if ($obj->isNew() && !is_array($this->keyName)) {
            $obj->assignVar($this->keyName, $this->db->getInsertId());
        }

        return true;
    }

    /**
     * Change a value for objects with a certain criteria
     *
     * @param string $fieldname  Name of the field
     * @param string $fieldvalue Value to write
     * @param object $criteria   {@link CriteriaElement}
     *
     * @param  bool  $force
     * @return bool
     */
    public function updateAll($fieldname, $fieldvalue, $criteria = null, $force = false)
    {
        $set_clause = $fieldname . ' = ';
        if (is_numeric($fieldvalue)) {
            $set_clause .= $fieldvalue;
        } elseif (is_array($fieldvalue)) {
            $set_clause .= $this->db->quoteString(implode(',', $fieldvalue));
        } else {
            $set_clause .= $this->db->quoteString($fieldvalue);
        }
        $sql = 'UPDATE ' . $this->table . ' SET ' . $set_clause;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if ($force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }

        // Clear cache
        $this->forceCacheClean();

        if (!$result) {
            return false;
        }

        return true;
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
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria($field, $obj->getVar($field)));
        //  one more condition if target object exisits in database
        if (!$obj->isNew()) {
            $criteria->add(new Criteria($this->_key, $obj->getVar($this->_key), '!='));
        }
        if ($this->getCount($criteria)) {
            $obj->setErrors($error);

            return true;
        }

        return false;
    }

    /**
     * delete all objects meeting the conditions
     *
     * @param  object $criteria {@link CriteriaElement} with conditions to meet
     * @return bool
     */
    public function deleteAll($criteria = null)
    {
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql = 'DELETE FROM ' . $this->table;
            $sql .= ' ' . $criteria->renderWhere();
            if (!$this->db->queryF($sql)) {
                return false;
            }
            $rows = $this->db->getAffectedRows();

            // Clear cache
            $this->forceCacheClean();

            return $rows > 0 ? $rows : true;
        }

        return false;
    }

    /**
     * Compare two objects and returns, in an array, the differences
     *
     * @param  XoopsObject $old_object The first object to compare
     * @param  XoopsObject $new_object The new object
     * @return array       differences    key = fieldname, value = array('old_value', 'new_value')
     */
    public function compareObjects($old_object, $new_object)
    {
        $ret       = array();
        $vars_name = array_keys($old_object->getVars());
        foreach ($vars_name as $one_var) {
            if ($old_object->getVar($one_var, 'f') == $new_object->getVar($one_var, 'f')) {
            } else {
                $ret[$one_var] = array($old_object->getVar($one_var), $new_object->getVar($one_var));
            }
        }

        return $ret;
    }

    /**
     * Get distincted values of a field in the table
     *
     * @param  string $field    Field's name
     * @param  object $criteria {@link CriteriaElement} conditions to be met
     * @param  string $format   Format in wich we want the datas
     * @return array  containing the distinct values
     */
    public function getDistincts($field, $criteria = null, $format = 's')
    {
        //require_once __DIR__ . '/lite.php';
        $limit = $start = 0;
        $sql   = 'SELECT ' . $this->keyName . ', ' . $field . ' FROM ' . $this->table;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
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
        $ret    = array();
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
        if (trim($order) == '') {
            if (isset($this->identifierName) && trim($this->identifierName) != '') {
                $order = $this->identifierName;
            } else {
                $order = $this->keyName;
            }
        }
        $items   = array();
        $critere = new Criteria($this->keyName, 0, '<>');
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
