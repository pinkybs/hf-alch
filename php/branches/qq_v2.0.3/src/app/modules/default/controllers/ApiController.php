<?php

/**
 * Alchemy api controller
 *
 * @copyright  Copyright (c) 2010 HapyFish
 * @create      2012/02    Nick
 */
class ApiController extends Hapyfish2_Controller_Action_Api {

    /**
     * init swf
     *
     */
    public function initswfAction() {
        include_once(CONFIG_DIR . '/swfconfig.php');
        $apiBasicVer = Hapyfish2_Alchemy_Cache_Basic::getBasicVersion();
        $swfConfig['interfaces']['loadstatic'] .= '?v=' . $apiBasicVer;
        $swfConfig['interfaces']['GiftGetActInitStatic'] .= '?v=' . $apiBasicVer;
        $swfConfig['interfaces']['TrainingCampInitStatic'] .= '?v=' . $apiBasicVer;
        if (defined('ENABLE_STATIC_GZ') && ENABLE_STATIC_GZ) {
            $swfConfig['interfaces']['loadstatic'].= '&gz=1&org=hf';
            $swfConfig['interfaces']['TrainingCampInitStatic'].= '&gz=1&org=hf';
        }
        $this->echoResult($swfConfig);
    }

    /**
     * init swf for watch
     *
     */
    public function initswfwatchAction() {
        include_once(CONFIG_DIR . '/swfconfig.php');
        $apiBasicVer = Hapyfish2_Alchemy_Cache_Basic::getBasicVersion();
        $swfConfig['interfaces']['loadstatic'] .= '?v=' . $apiBasicVer;
        $swfConfig['interfaces']['GiftGetActInitStatic'] .= '?v=' . $apiBasicVer;
        $swfConfig['interfaces']['TrainingCampInitStatic'] .= '?v=' . $apiBasicVer;

        $info = $swfConfig['initSwf'];
        $initswf = array();
        foreach ( $info as $v ) {
        	if ( $v == 'v2012121901/HappyMagic.swf?v=20120080301' ) {
        		$initswf[] = 'debug/v2012121903/HappyMagic.swf?v=20120080301';
        	}
			else if ( $v == 'guide.swf?v=2012081701' ) {
        		$initswf[] = 'debug/v2012121903/guide.swf?v=2012081701';
			}
        	else {
        		$initswf[] = $v;
        	}
        }
        $swfConfig['initSwf'] = $initswf;

        if (defined('ENABLE_STATIC_GZ') && ENABLE_STATIC_GZ) {
            $swfConfig['interfaces']['loadstatic'].= '&gz=1&org=hf';
            $swfConfig['interfaces']['TrainingCampInitStatic'].= '&gz=1&org=hf';
        }
        $this->echoResult($swfConfig);
    }

    /**
     * 初始化静态信息
     */
    public function initstaticAction() {
        header('Cache-Control: max-age=31104000');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31104000) . ' GMT');
        $gz = $this->_request->getParam('gz', 0);
        if ($gz == 1) {
            header('Content-Type: application/octet-stream');
            echo Hapyfish2_Alchemy_Bll_BasicInfo::getInitVoData('1.0', true);
        } else {
            echo Hapyfish2_Alchemy_Bll_BasicInfo::getInitVoData();
        }
        exit;
    }

    public function initavatarAction() {
        $uid = $this->uid;
        $avatarId = (int) $this->_request->getParam('avatarId');
        $status = Hapyfish2_Alchemy_Bll_User::initAvatar($uid, $avatarId);
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    public function inituserinfoAction() {
        $uid = $this->uid;

        //临时补偿
        Hapyfish2_Alchemy_Bll_Recoup::recoupUser($uid);
        $data = array();

        //连续登录信息 login time upd
        $loginInfo = Hapyfish2_Alchemy_Bll_User::updateLoginTime($uid);

//    	//读取所有订单(已接，未接)，并更新满意度
//		$orders = Hapyfish2_Alchemy_Bll_Order::getOrderList($uid);

		//get user info
        $data['user'] = Hapyfish2_Alchemy_Bll_User::getUserInit($uid);
        $data['items'] = Hapyfish2_Alchemy_Bll_Bag::getAll($uid);

        $data['mixs'] = Hapyfish2_Alchemy_HFC_Mix::getUserMix($uid);
        $data['curMixs'] = Hapyfish2_Alchemy_HFC_Furnace::getCurMixs($uid);
        $data['funrnaceGridInfo'] = Hapyfish2_Alchemy_Bll_Mix::getFurnaceNumList($uid);
        $data['illustrations'] = Hapyfish2_Alchemy_Bll_Illustrations::getUserIllustrations($uid);

        //世界地图
        $hasNewWorld = false;
        $data['curOpenScene'] = Hapyfish2_Alchemy_Bll_WorldMap::getOpenWorldMap($uid, $hasNewWorld);
        $data['isnewworldmap'] = $hasNewWorld ? 1 : 0;

        if (PLATFORM == 'sinaweibo') {
            $data['acts'] = Hapyfish2_Alchemy_Bll_Act_Sinawb::get($uid);
        } else if (PLATFORM == 'pengyou' || PLATFORM == 'qzone') {
            $data['acts'] = Hapyfish2_Alchemy_Bll_Act_Qq::get($uid);
        } else if (PLATFORM == 'kaixin') {
            $data['acts'] = Hapyfish2_Alchemy_Bll_Act_Kaixin::get($uid);
        } else {
            $data['acts'] = Hapyfish2_Alchemy_Bll_Act::get($uid);
        }

        //佣兵与主角数据
        $homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
        $data['roles'] = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);

        //任务数据
        $data['tasks'] = Hapyfish2_Alchemy_Bll_Task::getCurTaskList($uid);
        $data['systime'] = time();

		//任务数据
		$data['tasks'] = Hapyfish2_Alchemy_Bll_Task::getCurTaskList($uid);
		$data['systime'] = time();

        //订单数据
//		$data['orders'] = $orders;
        $vip = new Hapyfish2_Alchemy_Bll_Vip();
        $data['vipInfo'] = $vip->getInfo($uid);
//		$data['autoUseList'] = Hapyfish2_Alchemy_HFC_Goods::getUserAuto($uid);
		$data['bloodBankList'] = Hapyfish2_Alchemy_HFC_Goods::getBloodVo($uid);
		//第一次进入游戏播放剧情
		/* $storyVo = Hapyfish2_Alchemy_Bll_Story::startStory($uid, 11);
		if ( $storyVo != -200) {
			$data['story'] = $storyVo;
		} */
		
		//副本传送门数据
		$data['transportDataList'] = Hapyfish2_Alchemy_Bll_MapCopy::getTransportList($uid);
		$userRefresh = Hapyfish2_Alchemy_HFC_MapCopy::getUserRefreshMap($uid);
		$cost = 8*pow(2,$userRefresh['num']);
		$data['refreshDungeonCost'] = $cost;
		$data['linePowerClass'] = Hapyfish2_Alchemy_Bll_VipWelfare::getSpArr($uid);
		//cd列表
		$cdList = Hapyfish2_Alchemy_Bll_Cdtime::getCdList($uid);
		$data['cdList'] = $cdList;
		
		//建筑升级时间-结束时间点
		$buildingCdTm = Hapyfish2_Alchemy_Cache_User::getBuildingCdTm($uid);
		$data['spBuildUpgradeCd'] = $buildingCdTm;

		//今天已经使用的免费传送次数
		$todayUsedFreeCnt = Hapyfish2_Alchemy_Cache_User::getTodayUsedFreeTransportCnt($uid);
		$data['vipTransportTimesUsed'] = $todayUsedFreeCnt;
		$data['waterDungeon100'] = Hapyfish2_Alchemy_Bll_Helltower::abyssOpenStatus($uid);
		$userAbyss = Hapyfish2_Alchemy_Cache_Helltower::getUserAbyssStatus($uid);
		$data['abyssDungeonOpen'] = $userAbyss['open'];
    	$result = $data;
    	
        $this->echoResult($result);
    }

    public function getusermixAction() {
        $uid = $this->uid;
        $result = Hapyfish2_Alchemy_HFC_Mix::getUserMix($uid);

        $this->echoResult($result);
    }

    /**
     * 购买物品-单物品购买
     */
    public function buyitemAction() {
        $uid = $this->uid;
        $cid = $this->_request->getParam('cid');
        $num = $this->_request->getParam('num');


        if (PLATFORM == 'qzone' || PLATFORM == 'pengyou') {
            $status = Hapyfish2_Alchemy_Bll_Shop::buyOneItemQq($uid, $cid, $num);
        } else {
            $status = Hapyfish2_Alchemy_Bll_Shop::buyOneItem($uid, $cid, $num);
        }
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    /**
     * 购买物品-多物品购买
     */
    public function buyitemsAction() {
        $uid = $this->uid;
        $items = $this->_request->getParam('items');
        $items = json_decode($items, true);
        if (PLATFORM == 'qzone' || PLATFORM == 'pengyou') {
            $status = Hapyfish2_Alchemy_Bll_Shop::buyItemsQq($uid, $items);
        } else {
            $status = Hapyfish2_Alchemy_Bll_Shop::buyItems($uid, $items);
        }
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    /**
     * 售出物品-多物品出售
     * items:[	[141,1], //非装备 第一位是CID 第二位是NUM
     * 	        [1000000161,1], //装备 第一位是ID 第二位是NUM
     *       ]
     */
    public function saleitemsAction() {
        $uid = $this->uid;
        $items = $this->_request->getParam('items');
        $items = json_decode($items, true);

        $status = Hapyfish2_Alchemy_Bll_Shop::saleItems($uid, $items);
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    /**
     * 查询用户图鉴信息
     */
    public function getillustrationsAction() {
        $uid = $this->uid;

        $result = Hapyfish2_Alchemy_Bll_Illustrations::getUserIllustrations($uid);

        $this->echoResult($result);
    }

    /**
     * 添加图鉴信息
     */
    public function addillustrationsAction() {
        $uid = $this->uid;
        $cid = $this->_request->getParam('cid');

        $result = Hapyfish2_Alchemy_Bll_Illustrations::addUserIllustrations($uid, $cid);

        $this->echoResult($result);
    }

    /**
     * 查阅新图鉴
     */
    public function readnewillustrationAction() {
        $uid = $this->uid;
        $id = $this->_request->getParam('id');

        $result = Hapyfish2_Alchemy_Bll_Illustrations::readUserIllustration($uid, $id);

        $this->echoResult($result);
    }

    /**
     * 阅读对白
     */
    public function readdialogAction() {
        $uid = $this->uid;
        $npcId = (int) $this->_request->getParam('npcId');
        $chatId = (int) $this->_request->getParam('chatId');

        $status = Hapyfish2_Alchemy_Bll_Story::readDialog($uid, $chatId, $npcId);

        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    public function usecardAction() {
        $cid = $this->_request->getParam('cid');
        $num = $this->_request->getParam('num', 1);
        $roleId = $this->_request->getParam('roleid');

        $status = Hapyfish2_Alchemy_Bll_Card::useCard($this->uid, $cid, $roleId, $num);

        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    /**
     * 好友信息
     */
    public function getfriendsAction() {
        $pageIndex = $this->_request->getParam('pageIndex', 1);
        $pageSize = $this->_request->getParam('pageSize', 20);

        $status = Hapyfish2_Alchemy_Bll_Friend::getRankList($this->uid, $pageIndex, $pageSize);

        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    /* 战斗 */

    /**
     * 初始化好友援助信息
     */
    public function initfightassistAction() {
        $uid = $this->uid;
        $info = Hapyfish2_Alchemy_Bll_Fight::getFriendAssistVo($uid);
        if ($info) {
            Hapyfish2_Alchemy_Cache_Fight::setFightFriendAssistInfo($uid, $info);
        }
        header("Cache-Control: no-store, no-cache, must-revalidate");
        echo json_encode($info);
        exit;
    }

    /**
     * 战斗开始
     */
    public function beginfightAction() {
        $uid = $this->uid;
        $id = $this->_request->getParam('monsterId');

        $status = Hapyfish2_Alchemy_Bll_Fight::regFight($uid, $id);
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    /**
     * 战斗结束
     */
    public function endfightAction() {
        $uid = $this->uid;
        $rst = $this->_request->getParam('rst');
        $log = $this->_request->getParam('log');
        $skip = $this->_request->getParam('isJump', 0);
        if ($skip == 1) {
            $vip = new Hapyfish2_Alchemy_Bll_Vip();
            $jump = $vip->vipSkip($uid);
        }
        if ($jump < 0) {
            $this->echoError($jump);
        }
        $status = -200;
        if ($rst) {
            $info = json_decode($rst, true);
            $id = $info['id'];
            $ver = $info['v'];
            $ftRst = $info['type'];
            $aryAct = $info['data'];

            if (in_array($uid, array(10010,10110,10014))) {
                info_log_fight('rst=' . $rst, 'proc-' . $uid . '-' . $id . '-act');
                if ($log) {
                    info_log_fight("\n" . $log, 'proc-' . $uid . '-' . $id . '-as');
                    $strAct = '';
                    foreach ($aryAct as $act) {
                        $strAct .= json_encode($act) . "\n";
                    }
                    info_log_fight("\n" . $strAct, 'proc-' . $uid . '-' . $id . '-act');
                }
            }
            $status = Hapyfish2_Alchemy_Bll_Fight::completeFight($uid, $id, $aryAct, $ftRst);
        }

        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    /* 探险 */

    /**
     * 回主场景 or 去好友主场景
     */
    public function gohomeAction() {
        $uid = $this->uid;
        $fid = (int) $this->_request->getParam('fid');
        $status = Hapyfish2_Alchemy_Bll_Scene::goHomeScene($uid, $fid);
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    /**
     * 静态地图副本数据
     */
    public function mapstaticAction() {
        header("Cache-Control: max-age=2592000");
        $mapId = (int) $this->_request->getParam('id');
        echo Hapyfish2_Alchemy_Bll_BasicInfo::getMapStaticData($mapId);
        exit;
    }

    /**
     * 进入副本
     */
    public function entermapAction() {
        $uid = $this->uid;
        $visitType = $this->_request->getParam('visitType');
        $fid = (int) $this->_request->getParam('fid');
        $mapId = (int) $this->_request->getParam('sceneId');
        $portalId = (int) $this->_request->getParam('portalId');
        $storyId = (int) $this->_request->getParam('storyId');
        $transport = (int) $this->_request->getParam('transport', null);

        if ($fid && $fid != $uid) {
            $status = Hapyfish2_Alchemy_Bll_MapCopy::enterFriendMap($uid, $fid, $mapId, $visitType);
        } else {
            if ($mapId < 100) {
                $status = Hapyfish2_Alchemy_Bll_MapCopy::enterHomeOrVila($uid, $mapId);
            } else {
                $status = Hapyfish2_Alchemy_Bll_MapCopy::enterMap($uid, $mapId, $portalId, $storyId, $transport);
            }
        }
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    /**
     * 采集
     */
    public function gatherAction() {
        $uid = $this->uid;
        $id = (int) $this->_request->getParam('id');

        $status = Hapyfish2_Alchemy_Bll_MapCopy::hitMine($uid, $id);
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    /* 交互 */

    /**
     *
     * 开启保护
     */
    public function openprotectAction() {
        $uid = $this->uid;

        $status = Hapyfish2_Alchemy_Bll_FightOccupy::openProtect($uid);
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    /**
     *
     * 收税
     */
    public function collecttaxAction() {
        $uid = $this->uid;
        $fid = $this->_request->getParam('fid');
        $status = Hapyfish2_Alchemy_Bll_FightOccupy::collectTax($uid, $fid);
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    /**
     *
     * 侵略准备
     */
    public function invadereadyAction() {
        $uid = $this->uid;
        $fid = $this->_request->getParam('fid');
        $mode = (int) $this->_request->getParam('mode'); //$mode 1-侵略aggress 2-反抗gainst 3-援助succor
        if ($mode == 1) {
            $status = Hapyfish2_Alchemy_Bll_FightOccupy::aggress($uid, $fid);
        } else if ($mode == 2) {
            $status = Hapyfish2_Alchemy_Bll_FightOccupy::gainst($uid);
        } else if ($mode == 3) {
            $status = Hapyfish2_Alchemy_Bll_FightOccupy::succor($uid, $fid);
        }

        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    /**
     *
     * 侵略
     */
    public function invadeAction() {
        $uid = $this->uid;
        $fid = $this->_request->getParam('fid');
        $houseId = (int) $this->_request->getParam('buildId');
        $mode = (int) $this->_request->getParam('mode'); //$mode 1-侵略aggress 2-反抗gainst 3-援助succor
        $num = (int) $this->_request->getParam('num');

        $key = 'lock:invade:' . $fid;
        $lock = Hapyfish2_Cache_Factory::getLock($fid);
        //get lock
        $ok = $lock->lock($key);
        if (!$ok) {
            $this->echoError(-615);
        }

        $status = Hapyfish2_Alchemy_Bll_FightOccupy::startLotto($uid, $fid, $houseId, $mode, $num);

        //release lock
        $lock->unlock($key);

        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    /**
     * 播放指定剧情
     */
    public function showstoryAction() {
        $uid = $this->uid;
        $id = $this->_request->getParam('id');

        $data = array();
        $storyVo = Hapyfish2_Alchemy_Bll_Story::startStory($uid, $id, true);

        $this->flush();
    }

    public function initspecialupgradeAction() {
        header("Cache-Control: max-age=2592000");
        $result = Hapyfish2_Alchemy_Bll_BasicInfo::getInitSpecialUpgrade();

        $this->echoResult($result);
    }

    public function initroleupgradeAction() {
        header("Cache-Control: max-age=2592000");
        $result = Hapyfish2_Alchemy_Bll_BasicInfo::getInitRoleUpgrade();

        $this->echoResult($result);
    }

    /**
     * 特殊建筑升级（酒馆，自宅，铁匠铺）
     */
    public function specialupgradeAction() {
        $uid = $this->uid;
        $id = $this->_request->getParam('id');

        $status = Hapyfish2_Alchemy_Bll_Upgrade::specialUpgrade($uid, $id);
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    /**
     * 快速结束建筑升级cd时间
     */
    public function removebuildingcdAction() {
        $uid = $this->uid;

        $status = Hapyfish2_Alchemy_Bll_Upgrade::removeBuildingCd($uid);
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    /**
     * read feed
     *
     */
    public function readfeedAction() {
        $pageIndex = $this->_request->getParam('pageIndex', 1);
        $pageSize = $this->_request->getParam('pageSize', 50);
        $feedList = Hapyfish2_Alchemy_Bll_Feed::getFeed($this->uid, $pageIndex, $pageSize);

        $data['diaryList'] = $feedList;
        $this->echoResult($data);
    }

    /**
     * 好友家加酒
     */
    public function addwineAction() {
        return;
        /* $uid = $this->uid;
          $fid = $this->_request->getParam('fid');

          $status = Hapyfish2_Alchemy_Bll_Mercenary::addWine($uid, $fid);
          if ($status < 0) {
          $this->echoError($status);
          }
          $this->flush(); */
    }

    /**
     * 修理装备
     */
    public function repairweaponAction() {
        $uid = $this->uid;
        $wid = $this->_request->getParam('wid', 0);
        $type = $this->_request->getParam('type');

        $status = Hapyfish2_Alchemy_Bll_Repair::repairWeapon($uid, $wid, $type);
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    /**
     * 更新当前新手引导的小索引
     */
    public function updatehelpAction() {
        $uid = $this->uid;
        $idx = $this->_request->getParam('idx');

        $status = Hapyfish2_Alchemy_Bll_Help::updateHelp($uid, $idx);
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    /**
     * 完成当前引导步骤
     */
    public function completehelpAction() {
        $uid = $this->uid;

        $status = Hapyfish2_Alchemy_Bll_Help::completeHelp($uid);
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    /**
     * 解锁一个功能按钮
     */
    public function unlockfuncAction() {
        $uid = $this->uid;
        $func = $this->_request->getParam('func');

        /* $status = Hapyfish2_Alchemy_Bll_Help::unlockFunc($uid, $func);
          if ($status < 0) {
          $this->echoError($status);
          } */
        $this->flush();
    }

    public function setautoAction() {
        $uid = $this->uid;
        $id = $this->_request->getParam('itemId');
        $type = $this->_request->getParam('type');
        $status = Hapyfish2_Alchemy_HFC_Goods::updateUserAuto($uid, $id, $type);
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    public function strengthenequipmentAction() {
        $uid = $this->uid;
        $roleId = $this->_request->getParam('roleId', 0);
        $wid = $this->_request->getParam('wid');
        $type = $this->_request->getParam('type', 0);
        $status = Hapyfish2_Alchemy_Bll_Weapons::strengthenequipment($uid, $roleId, $wid, $type);
        if ($status < 0 && $status != -1) {
            $this->echoError($status);
        }
        $this->flush();
    }

    public function initstrengthenequipmentAction() {
        $uid = $this->uid;
        $data = Hapyfish2_Alchemy_Bll_Weapons::initStrengthenEquipment($uid);
        $this->echoResult($data);
    }

    /**
     * 领取VIP每日奖励
     */
    public function getvipdailyawardAction() {
        $uid = $this->uid;
        $status = Hapyfish2_Alchemy_Bll_Vip::getVipDialyAward($uid);
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    /**
     * VIP每日奖励静态信息
     */
    public function initvipdailyawardAction() {
        $uid = $this->uid;
        $status = Hapyfish2_Alchemy_Bll_Vip::initVipDailyAward($uid);
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    /**
     * 检查是否已领取VIP每日奖励
     */
    public function checkvipdailyawardAction() {
        $uid = $this->uid;
        $status = Hapyfish2_Alchemy_Bll_Vip::checkVipDailyAward($uid);
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    public function getidentityAction() {
        $uid = $this->uid;
        $identity = Hapyfish2_Platform_Bll_User::getIdentity($uid);
        $data['result']['status'] = 1;
        $data['sinaAntiState'] = $identity['sinaAntiState'];
        $data['antiTime'] = $identity['antiTime'];
        $this->echoResult($data);
    }

    public function addspAction() {
        $uid = $this->uid;
        $type = $this->_request->getParam('type');
        $status = Hapyfish2_Alchemy_Bll_VipWelfare::addSp($uid, $type);
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    public function refreshmapAction() {
        $uid = $this->uid;
        $group = $this->_request->getParam('group');
        $status = Hapyfish2_Alchemy_Bll_MapCopy::refreshMapCopy($uid, $group);
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    public function openfriendboxAction() {
        $uid = $this->uid;
        $status = Hapyfish2_Alchemy_Bll_EventGift::openFriendBox($uid);
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    public function initfriendboxAction() {
        $uid = $this->uid;
        $status = Hapyfish2_Alchemy_Bll_EventGift::InitBox($uid);
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    public function initvipguessAction() {
        $uid = $this->uid;
        $info = Hapyfish2_Alchemy_Event_Bll_VipGuess::initVipGuess($uid);
        $data['result']['status'] = 1;
        $data['vipGuessVo'] = $info;
        $this->echoResult($data);
    }

    public function guesspriceAction() {
        $uid = $this->uid;
        $price = $this->_request->getParam('num');
        $status = Hapyfish2_Alchemy_Event_Bll_VipGuess::guessPrice($uid, $price);
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    public function getguessawardAction() {
        $uid = $this->uid;
        $status = Hapyfish2_Alchemy_Event_Bll_VipGuess::getGuessAward($uid);
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    public function clearstrcooltimeAction() {
        $uid = $this->uid;
        $status = Hapyfish2_Alchemy_Bll_Weapons::clearCoolTime($uid);
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    public function dmAction() {
        $uid = $this->uid;
        $status = Hapyfish2_Alchemy_Bll_User::getUserDmStatus($uid);
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }

    public function initdmAction() {
        $uid = $this->uid;
        $data = Hapyfish2_Alchemy_Cache_Dm::getDmConfig();
        $initConfig['dmVo'] = json_decode($data['dm'], true);
        $initConfig['dmvBtnVo'] = json_decode($data['btn'], true);
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'dmVo', $initConfig['dmVo']);
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'dmvBtnVo', $initConfig['dmvBtnVo']);
        $this->flush();
    }
    
    public function initspecialshopAction()
    {
    	$uid = $this->uid;
    	$type = $this->_request->getParam('type');
        $status = Hapyfish2_Alchemy_Bll_SpecialShop::initSpecialShop($uid, $type);
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }
    
    public function buyspecialshopAction()
    {
    	$uid = $this->uid;
    	$type = $this->_request->getParam('type');
        $status = Hapyfish2_Alchemy_Bll_SpecialShop::buyQQSpecialShop($uid, $type);
        if ($status < 0) {
            $this->echoError($status);
        }
        $this->flush();
    }
    
    public function buildupgradebasicAction()
    {
    	header("Cache-Control: max-age=2592000");
    	$result = Hapyfish2_Alchemy_Bll_BasicInfo::getBuildUpgradeBasic();
    	
    	$this->echoResult($result);
    }

    /**
     * 获取QQ黄钻每日奖励
     */
    public function getqqylwvipdailygiftAction()
    {
    	$uid = $this->uid;
    	$status = Hapyfish2_Alchemy_Bll_QQYellowVip::getDailyGift($uid);
    	if($status < 0){
    		$this->echoError($status);
    	}
    	$this->flush();
    }
    
    /**
     * 是否已经领取QQ黄钻每日礼包
     */
    public function hasqqylwvipdailygiftAction()
    {
    	$uid = $this->uid;
    	$status = Hapyfish2_Alchemy_Bll_QQYellowVip::hasDailyGift($uid);
    	if($status < 0){
    		$this->echoError($status);
    	}
    	$this->flush();
    }
    
    /**
     * 是否可以领取QQ黄钻新手礼包
     */
    public function hasqqylwvipguidegiftAction()
    {
    	$uid = $this->uid;
    	$status = Hapyfish2_Alchemy_Bll_QQYellowVip::hasGuideGift($uid);
    	if($status < 0){
    		$this->echoError($status);
    	}
    	$this->flush();
    }
    
    /**
     * 领取QQ黄钻新手礼包
     */
    public function getqqylwvipguidegiftAction()
    {
    	$uid = $this->uid;
    	$status = Hapyfish2_Alchemy_Bll_QQYellowVip::getGuideGift($uid);
    	if($status < 0){
    		$this->echoError($status);
    	}
    	$this->flush();
    }
    
}
