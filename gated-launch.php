<?php

public static function isShowReinvestment($property, $uid)
{
    if ($property->getIsRookie()) {
        return false;
    }

    // 内部人员uid
    $qnnStaff = Yii::app()->getCache()->get('reinvestment_qnn_staff_uids');
    if ($qnnStaff) {
        $staffArr = explode(',', $qnnStaff);
        if (in_array($uid, $staffArr)) {
            return true;
        }
    }

    // 当reinvestment_fanli_block有值的时候，返利不开放
    if (Yii::app()->getCache()->get('reinvestment_fanli_block') && UserChannelService::isFanli()) {
        return false;
    }

    // 从缓存获取开放比例，获取失败则全部开放
    // 设置方法，缓存key=reinvestment_uid_divide，value=数字
    // 获取失败，全部开放
    // 当数字<=0的时候，全部关闭
    // 当数字==1的时候，开放10%（其实就是用户尾号为0的）
    // ...
    // 当数字>=10的时候，全量打开（其实就是用户尾号全部都小于10）
    $uidDivide = Yii::app()->getCache()->get('reinvestment_uid_divide');
    if ($uidDivide === false) {
        return true;
    }

    $uidDivide = intval($uidDivide);
    $uidTail = intval(substr($uid, -1));
    if ($uidTail < $uidDivide) {
        return true;
    } else {
        return false;
    }
}
