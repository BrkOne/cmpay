<?php

/**
 *  +----------------------------------------------------------------------
 *  | 草帽支付系统 [ WE CAN DO IT JUST THINK ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2018 http://www.iredcap.cn All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Licensed ( https://www.apache.org/licenses/LICENSE-2.0 )
 *  +----------------------------------------------------------------------
 *  | Author: Brian Waring <BrianWaring98@gmail.com>
 *  +----------------------------------------------------------------------
 */

namespace app\common\model;


class Balance extends BaseModel
{
    /**
     * 获取商户资产
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $uid
     * @return Balance|null
     * @throws \think\exception\DbException
     */
    public function getUserBalance($uid){
        return self::get(['uid'=>$uid]);
    }
}