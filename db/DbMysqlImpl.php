<?php
/**
 * Created by PhpStorm.
 * User: beibei
 * Date: 2015/5/5
 * Time: 8:51
 */
include(dirname(__FILE__) . '/DbProviderInterface.php');

class DbMysqlImpl implements DbProviderInterface
{
    /**
     * @var 单例对象
     */
    private static $_instance;

    /**
     * @var PDO句柄资源
     */
    private $_db;

    /**
     * @var 最终执行的statement object
     */
    private $_stmt;

    /**
     * @var int
     * 受影响的行数
     */
    private $_rowCount = 0;

    /**
     * @var array
     * sql拼接依赖字段
     */
    private $_sqlQuery = array(
        'SELECT' => array(),
        'FROM' => array(),
        'WHERE' => array(),
        'GROUP' => array(),
        'HAVING' => array(),
        'ORDER' => array(),
        'LIMIT' => '',
        'OFFSET' => '',
    );

    /**
     * @var array
     * sql关联依赖字段
     */
    private $_joinTypes = array(
        'INNER JOIN' => '',
        'LEFT JOIN' => '',
        'RIGHT JOIN' => '',
    );

    /**
     * @var string
     * 最终拼接之后的sql语句
     */
    private $_querySql = '';

    /**
     * mysql默认适配端口
     */
    const DEFAULT_ADAPTER_PORT = 3306;

    /**
     * 数据库默认适配器
     */
    const DEFAULT_ADAPTER_TYPE = 'mysql';

    /**
     * @param array
     *$dbConfig=array(
     * 'adapter'=>'mysql',
     * 'dbname'=>'user',
     * 'host'=>'localhost',
     * 'port'=>'3306',
     * 'charset'=>'UTF8',
     * 'username'=>'root',
     * 'password'=>'root'
     * );
     * @throws Exception
     */
    private function __construct(array $dbConfig = null)
    {
        if (empty($dbConfig)) {
            $dbConfigFile = dirname(dirname(__FILE__)) . '/config/db.php';
            if (file_exists($dbConfigFile)) {
                $dbConfig = require_once($dbConfigFile);
            } else {
                throw new Exception("数据源配置文件不存在");
            }
        }

        $dsn = $this->_createDsn($dbConfig);
        try {
            $this->_db = new PDO($dsn, $dbConfig['username'], $dbConfig['password']);
        } catch (Exception $ex) {
            throw new Exception('创建PDO失败');
        }

        $this->_db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->_db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->_db->exec('SET NAMES ' . $dbConfig['charset']);
    }

    /**
     * @param null $config
     * @return DbMysqlImpl
     * 获取Db单例
     */
    public static function getInstance($config = null)
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self($config);
        }

        return self::$_instance;
    }

    /**
     * 防止深拷贝产生新的对象
     */
    public function __clone() {}

    /**
     * 释放数据库连接
     */
    public static function unsetInstance()
    {
        self::$_instance = null;
    }


    /**
     * @param array $dbConfig
     * @return string
     * @throws Exception
     * 创建数据源，提供给PDO构造参数
     */
    private function _createDsn(array $dbConfig)
    {
        $dsn = '';
        if (!isset($dbConfig['adapter']) || !$dbConfig['adapter'])  {
            $dsn .= self::DEFAULT_ADAPTER_TYPE . ':';
        } else {
            $dsn .= trim($dbConfig['adapter']) . ':';
        }
        if (!isset($dbConfig['dbname']) || (!$dbConfig['dbname'])) {
            throw new Exception('未配置dbname参数');
        } else {
            $dsn .= 'dbname=' . trim($dbConfig['dbname']) . ';';
        }
        if (!isset($dbConfig['host']) || (!$dbConfig['host'])) {
            throw new Exception('未配置host参数');
        } else {
            $dsn .= 'host=' . trim($dbConfig['host']);
            if (!isset($dbConfig['port']) || (!$dbConfig['port'])) {
                $dbConfig['port'] = self::DEFAULT_ADAPTER_PORT;
            }
            $dsn .= ';port=' . trim($dbConfig['port']);
        }
        if (isset($dbConfig['charset']) || (!$dbConfig['charset'])) {
            $dsn .= ';charset=' . trim($dbConfig['charset']);
        }
        return $dsn;
    }


    /**
     * @return int
     * 返回最后一次sql执行受影响的行数
     */
    public function getRowCount()
    {
        return $this->_rowCount;
    }

    /**
     * @param $tableName
     * @param $columns
     * @throws Exception
     * insert('user',array('id'=>1,'name'=>'bob'))
     */
    public function insert($tableName, array $columns)
    {
        $cols = array();
        $placeholder = array();
        foreach ($columns as $k => $v) {
            $cols[] = $k;
            $placeholder[] = '?';
        }

        $sql = 'INSERT INTO ' . $tableName . ' (' . implode(', ', $cols)
            . ') VALUES (' . implode(', ', $placeholder) . ')';
        try {
            $stmt = $this->_db->prepare($sql);
            $this->bindValue($stmt, $columns);
            $this->_stmt->execute();
            $this->_rowCount = $this->_stmt->rowCount();
            $this->_stmt->closeCursor();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * @param $tableName
     * @param array $columns
     * @param array $where
     * @throws Exception
     * update("user",array('name'=>'hello,world'),array('id'=>1,'name'=>'xxoo'))
     */
    public function update($tableName, array $columns, array $where = null)
    {
        if (!$columns) {
            throw new Exception("columns 参数为空");
        }

        $set = array();
        $bindValue = array();
        foreach ($columns as $k => $v) {
            $set[] = $k . ' = ?';
            $bindValue[] = trim($v);
        }
        try {
            $sql = 'UPDATE ' . $tableName . ' SET ' . implode(', ', $set);
            $this->where($where);
            $sql .= $this->getWhere();
            $value = array_merge($bindValue, $this->_sqlQuery['WHERE']);
            $stmt = $this->_db->prepare($sql);
            $this->bindValue($stmt, $value);
            $this->_stmt->execute();
            $this->_rowCount = $this->_stmt->rowCount();
            $this->_stmt->closeCursor();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }

        $this->_unsetSqlQuery();
    }


    /**
     * @param $tableName
     * @param null $where
     * @throws Exception
     * delete('user',array('id'=>1))
     */
    public function delete($tableName, $where = null)
    {
        $sql = 'DELETE FROM ' . $tableName;

        try {
            $this->where($where);
            $sql .= $this->getWhere();
            $stmt = $this->_db->prepare($sql);
            $this->bindValue($stmt, $this->_sqlQuery['WHERE']);
            $this->_stmt->execute();
            $this->_rowCount = $this->_stmt->rowCount();
            $this->_stmt->closeCursor();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }

        $this->_unsetSqlQuery();
    }


    /**
     * @param mixed $columns
     * @return $this
     * $this->select(array('id','name')) => select id name
     */
    public function select($columns = '*')
    {
        if (!empty($this->_sqlQuery['SELECT'])) {
            $this->_sqlQuery['SELECT'] = '';
        }
        if (is_string($columns)) {
            $this->_sqlQuery['SELECT'] = $columns;
        } elseif (is_array($columns)) {
            $this->_sqlQuery['SELECT'] = implode(', ', $columns);
        }
        return $this;
    }


    /**
     * @param $tableName
     * @return $this
     * $this->from(admin) => from admin
     */
    public function from($tableName)
    {
        if (!empty($this->_sqlQuery['FROM'])) {
            $this->_sqlQuery['FROM'] = '';
        }
        $this->_sqlQuery['FROM'] = $tableName;
        return $this;
    }

    public function join($tableName, $on)
    {
        $this->_joinTypes['INNER JOIN'][] = $tableName . ' ON ' . $on;

        return $this;
    }

    public function leftJoin($tableName, $on)
    {
        $this->_joinTypes['LEFT JOIN'][] = $tableName . ' ON ' . $on;

        return $this;
    }

    public function rightJoin($tableName, $on)
    {
        $this->_joinTypes['RIGHT JOIN'][] = $tableName . ' ON ' . $on;

        return $this;
    }

    public function getJoinStr()
    {
        $joinStr = '';
        if (!empty($this->_joinTypes['JOIN'])) {
            $join = '';
            if (is_array($this->_joinTypes['JOIN'])) {
                foreach ($this->_joinTypes['JOIN'] as $k => $v) {
                    $join .= ' JOIN ' . $v;
                }
            } else {
                $join .= ' JOIN ' . $this->_joinTypes['JOIN'];
            }
            $joinStr .= $join;
        }
        if (!empty($this->_joinTypes['LEFT JOIN'])) {
            $leftJoin = '';
            if (is_array($this->_joinTypes['LEFT JOIN'])) {
                foreach ($this->_joinTypes['LEFT JOIN'] as $k => $v) {
                    $leftJoin .= ' LEFT JOIN ' . $v;
                }
            } else {
                $leftJoin .= ' LEFT JOIN ' . $this->_joinTypes['LEFT JOIN'];
            }
            $joinStr .= $leftJoin;
        }
        if (!empty($this->_joinTypes['RIGHT JOIN'])) {
            $rightJoin = '';
            if (is_array($this->_joinTypes['RIGHT JOIN'])) {
                foreach ($this->_joinTypes['RIGHT JOIN'] as $k => $v) {
                    $rightJoin .= ' RIGHT JOIN ' . $v;
                }
            } else {
                $rightJoin .= ' RIGHT JOIN ' . $this->_joinTypes['RIGHT JOIN'];
            }
            $joinStr .= $rightJoin;
        }

        return $joinStr;
    }


    /**
     * @param array $conditions
     * @return $this
     */
    public function where(array $conditions)
    {
        $where = array();
        foreach ($conditions as $key => $value) {
            $where[$key . ' ?'] = $value;
        }

        $this->_sqlQuery['WHERE'] = array_merge($this->_sqlQuery['WHERE'], $where);
        return $this;
    }

    public function having(array $conditions)
    {
        $having = array();
        foreach ($conditions as $key => $value) {
            $having[$key . ' ?'] = $value;
        }

        $this->_sqlQuery['HAVING'] = array_merge($this->_sqlQuery['HAVING'], $having);
        return $this;
    }

    public function getWhere()
    {
        $where = '';
        if (!$this->_sqlQuery['WHERE']) {
            return $where;
        }

        $keys = array_keys($this->_sqlQuery['WHERE']);
        $where .= ' WHERE ' . implode(' AND ', $keys);

        return $where;
    }

    public function getHaving()
    {
        $having = '';
        if (!$this->_sqlQuery['HAVING']) {
            return $having;
        }

        $keys = array_keys($this->_sqlQuery['HAVING']);
        $having .= ' HAVING ' . implode(' AND ', $keys);

        return $having;
    }

    public function group($group)
    {
        $this->_sqlQuery['GROUP'] = $group;

        return $this;
    }

    public function order($order)
    {
        $this->_sqlQuery['ORDER'] = $order;

        return $this;
    }

    public function limit($limit)
    {
        $this->_sqlQuery['LIMIT'] = intval($limit);

        return $this;
    }

    public function offset($offset)
    {
        $this->_sqlQuery['OFFSET'] = intval($offset);

        return $this;
    }


    /**
     * @throws Exception
     * 构造select查询语句
     */
    public function buildQuery()
    {
        $sql = 'SELECT ';
        $sql .= !empty($this->_sqlQuery['SELECT']) ? $this->_sqlQuery['SELECT'] : '*';
        if (empty($this->_sqlQuery['FROM'])) {
            throw new Exception("FROM 参数为空");
        } else {
            $sql .= ' FROM ' . $this->_sqlQuery['FROM'];
        }
        $sql .= $this->getJoinStr();
        $sql .= $this->getWhere();
        $sql .= !empty($this->_sqlQuery['GROUP']) ? ' GROUP BY ' . $this->_sqlQuery['GROUP'] : '';
        $sql .= $this->getHaving();
        $sql .= !empty($this->_sqlQuery['ORDER']) ? ' ORDER BY ' . $this->_sqlQuery['ORDER'] : '';
        $sql .= $this->_sqlQuery['LIMIT'] > 0 ? ' LIMIT ' .$this->_sqlQuery['LIMIT'] : '';
        $sql .= $this->_sqlQuery['OFFSET'] > 0 ? ' OFFSET ' .$this->_sqlQuery['OFFSET'] : '';
        $this->_querySql = $sql;
        echo $sql;
    }

    /**
     * 查询之后所有字段清空处理
     */
    public function _unsetSqlQuery()
    {
        $this->_sqlQuery = array(
            'SELECT' => array(),
            'FROM' => array(),
            'WHERE' => array(),
            'GROUP' => array(),
            'HAVING' => array(),
            'ORDER' => array(),
            'LIMIT' => '',
            'OFFSET' => '',
        );

        $this->_joinTypes = array(
            'INNER JOIN' => '',
            'LEFT JOIN' => '',
            'RIGHT JOIN' => '',
        );
    }

    public function fetchAll($tableName, $columns = '*', array $where = array(), $order = null, $limit = null, $offset = null)
    {
        if ($this->_stmt) {
            $this->_stmt = null;
        }

        try {
            if ($this->_querySql) {
                $this->_unsetSqlQuery();
            }
            $this->select($columns)->from($tableName)->where($where)->order($order)->limit($limit)->offset($offset);
            $this->buildQuery();
            $stmt = $this->_db->prepare($this->_querySql);
            $this->bindValue($stmt, $this->_sqlQuery['WHERE']);
            $this->_stmt->execute();
            $this->_unsetSqlQuery();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }

        $rs = $this->_stmt->fetchAll();
        $this->_stmt->closeCursor();

        return $rs;
    }

    public function fetchRow($tableName, $columns = '*', array $where = array(), $order = null)
    {
        if ($this->_stmt) {
            $this->_stmt = null;
        }

        try {
            if ($this->_querySql) {
                $this->_unsetSqlQuery();
            }
            $this->select($columns)->from($tableName)->where($where)->order($order)->limit(1);
            $this->buildQuery();
            $stmt = $this->_db->prepare($this->_querySql);
            $this->bindValue($stmt, $this->_sqlQuery['WHERE']);
            $this->_stmt->execute();
            $this->_unsetSqlQuery();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }

        $rs = $this->_stmt->fetch();
        $this->_stmt->closeCursor();

        return $rs;
    }

    public function fetchCount($tableName, $columns = '*', array $where = array())
    {
        if ($this->_stmt) {
            $this->_stmt = null;
        }

        try {
            if ($this->_querySql) {
                $this->_unsetSqlQuery();
            }

            $this->select('count(' . $columns . ') AS num')->from($tableName)->where($where);
            $this->buildQuery();
            $stmt = $this->_db->prepare($this->_querySql);
            $this->bindValue($stmt, $this->_sqlQuery['WHERE']);
            $this->_stmt->execute();
            $this->_rowCount = $this->_stmt->rowCount();
            $res = $this->_stmt->fetch();
            $this->_stmt->closeCursor();
            $this->_unsetSqlQuery();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }

        return $res['num'];
    }

    /**
     * @param null $sql
     * @return PDOStatement
     * 提供原生拼接方式执行sql语句
     */
    public function query($sql = null)
    {
        return $this->_db->query($sql);
    }

    public function queryAll()
    {
        if ($this->_stmt) {
            $this->_stmt = null;
        }

        try {
            $this->buildQuery();
            $stmt = $this->_db->prepare($this->_querySql);
            $mergeArr = array_merge($this->_sqlQuery['WHERE'],$this->_sqlQuery['HAVING']);
            if($mergeArr) {
                $this->bindValue($stmt,$mergeArr);
            }
            $this->_stmt->execute();
            $this->_rowCount = $this->_stmt->rowCount();
            $this->_unsetSqlQuery();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
        $rs = $this->_stmt->fetchAll();
        $this->_stmt->closeCursor();

        return $rs;
    }

    public function queryRow()
    {
        if ($this->_stmt) {
            $this->_stmt = null;
        }
        try {
            $this->buildQuery();
            $stmt = $this->_db->prepare($this->_querySql);
            $mergeArr = array_merge($this->_sqlQuery['WHERE'],$this->_sqlQuery['HAVING']);
            if($mergeArr) {
                $this->bindValue($stmt,$mergeArr);
            }
            $this->_stmt->execute();
            $this->_unsetSqlQuery();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }

        $rs = $this->_stmt->fetch();
        $this->_stmt->closeCursor();

        return $rs;
    }

    /**
     * 返回执行的sql语句
     */
    public function getPrepareSql()
    {
        return $this->_querySql;
    }

    /**
     * @return string
     * 返回最后一次插入id
     */
    public function lastInsertId()
    {
        return $this->_db->lastInsertId();
    }

    /**
     * @param PDOStatement $stmt
     * @param array $data
     * 将占位符替换为具体value
     */
    public function bindValue(PDOStatement $stmt, array $data)
    {
        //预编译占位符默认从1开始计数
        $i = 1;
        foreach ($data as $key => $value) {
            $dataType = $this->getBindValueDataType($value);
            $stmt->bindValue($i++, $value, $dataType);
        }

        $this->_stmt = $stmt;
    }

    /**
     * @param $value
     * @return int
     * 返回占位符预编译数据类型
     */
    public function getBindValueDataType($value)
    {
        if (is_int($value)) {
            $param = PDO::PARAM_INT;
        } elseif (is_bool($value)) {
            $param = PDO::PARAM_BOOL;
        } elseif (is_null($value)) {
            $param = PDO::PARAM_NULL;
        } elseif (is_string($value)) {
            $param = PDO::PARAM_STR;
        } else {
            $param = PDO::PARAM_STR;
        }
        return $param;
    }
}
$db = DbMysqlImpl::getInstance();
$db->query("select * from user WHERE id=".$_GET['id']);
//$db->select("name")->from('user')->where(array('id >'=>1))->queryAll();;
//$db->insert('user',array('id'=>3,'name'=>'firebroo'));
//print_r($db->select()->from('user AS admin')->where(array('id >='=>1,'id <'=>100))->order("name")->limit(1)->offset(1)->queryAll());
//$db->delete('user',array('id ='=>1));
//$db->update('user',array('name '=>'dogman'),array('id >'=>1000));


