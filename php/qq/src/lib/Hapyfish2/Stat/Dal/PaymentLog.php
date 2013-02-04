<?php


class Hapyfish2_Stat_Dal_PaymentLog
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Stat_Dal_PaymentLog
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getPaymentLogTableName($id)
    {
    	return 'alchemy_user_paylog_' . $id;
    }
    
    public function getPaymentLogData($dbId, $tbId, $begin, $end)
    {
    	$tbname = $this->getPaymentLogTableName($tbId);
    	$sql = "SELECT uid,amount,gold,user_level,create_time FROM $tbname WHERE create_time>=:begin AND create_time<:end";

        $db = Hapyfish2_Db_FactoryTool::getDB($dbId);
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('begin' => $begin, 'end' => $end));
    }

    public function getGoldTableName($tbId)
    {
    	return 'alchemy_user_log_consume_gem_' . $tbId;
    }
    
    public function getGold($dbId, $tbId, $begin, $end)
    {
    	$tbname = $this->getGoldTableName($tbId);
    	$sql = "SELECT SUM(cost) AS allcost FROM $tbname WHERE create_time>=:begin AND create_time<:end ";
        $db = Hapyfish2_Db_FactoryTool::getDB($dbId);
        $rdb = $db['r'];
    	
        return $rdb->fetchOne($sql, array('begin' => $begin, 'end' => $end));
    }
    
}