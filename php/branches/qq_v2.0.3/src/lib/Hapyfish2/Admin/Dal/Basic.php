<?php

class Hapyfish2_Admin_Dal_Basic
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Admin_Dal_Basic
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getBasicList($tbName)
    {
        $db = Hapyfish2_Db_Factory::getBasicDB('db_0');
        $rdb = $db['r'];
    	$sql = "SELECT * FROM $tbName ";
        return $rdb->fetchAll($sql);
    }


    public function addInfo($tbName, $info)
    {
    	$db = Hapyfish2_Db_Factory::getBasicDB('db_0');
        $wdb = $db['w'];

        $aryCols = array();
        $aryColsP = array();
        $aryVals = array();
        $aryColVal = array();
        $param = array();
        foreach ($info as $key=>$val) {
            $aryCols[] = '`'.$key.'`';
            $aryColsP[] = ':'.$key;
            //$aryVals[] = $wdb->quote($val);
            //$aryColVal[] = $key . '=' . $wdb->quote($val);
            $aryColVal[] = '`'.$key.'`' . '=:' . $key;
            $param[$key] = $val;
        }

        $strCols = implode(',', $aryCols);
        $strColsP = implode(',', $aryColsP);
        $strColVal = implode(',', $aryColVal);

        $sql = "INSERT INTO $tbName (" . $strCols . ") VALUES"
              . '(' . $strColsP . ')'
              . ' ON DUPLICATE KEY UPDATE '
              . $strColVal;
//info_log($sql, 'aa');
//info_log(json_encode($param), 'aa');
        return $wdb->query($sql, $param);
    }

    public function update($tbName, $keyField, $keyValue, $info)
    {
        $db = Hapyfish2_Db_Factory::getBasicDB('db_0');
        $wdb = $db['w'];

    	$keyValue = $wdb->quote($keyValue);
    	$where = "$keyField=$keyValue";
        return $wdb->update($tbName, $info, $where);
    }

    public function deleteInfo($tbName, $field, $selVal)
    {
        $db = Hapyfish2_Db_Factory::getBasicDB('db_0');
        $wdb = $db['w'];

        $sql = "DELETE FROM $tbName WHERE $field=$selVal";
        return $wdb->query($sql);
    }

}