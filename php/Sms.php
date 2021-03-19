<?php


namespace app\components;

use Yii;
use yii\base\Component;
use yii\redis\Connection;

class Sms extends Component
{
    public $error = '';
    public $codeExpire = 60*5;//验证码消息的有效期
    public $sendLimit = 30;//发送间隔 ，单位秒
    public $ipMaxSmsNum = 50;//ip每日最大发送量
    public $daySendMaxNum = 20;//每日最大发送数量
    //白名单
    public $whiteList = [
        'mobile'    =>  [],
        'ip'    =>  [],
    ];


    /**
     * 发送普通短信
     * @param $mobile
     * @param $content
     * @param $ip
     * @return bool
     */
    public function sendText($mobile , $content , $ip){
        return $this->getSendHandle()->sendText($mobile , $content , $ip);
    }

    /**
     * 发送验证码
     * @param $mobile 手机号
     * @param $ip 客户端IP
     * @return bool
     */
    public function sendCode($mobile , $ip){

        if (!$this->verifySendSms($mobile , $ip))
            return false;

        $code = mt_rand(10000,99999);
        $min = $this->codeExpire/60;
        $content = "您的验证码是 %s，在{$min}分钟内有效。如非本人操作请忽略本短信。";
        $content = sprintf($content, $code);
        $sendRes = $this->getSendHandle()->sendCode($mobile , $content , $ip);
        if (!$sendRes){
            $this->error = '短信发送失败，请重试';
            return false;
        }

        //验证码放入redis 5分钟后失效
        $mobileKey = $this->getCodeKey($mobile);
        Yii::$app->redis->setex($mobileKey ,$this->codeExpire, $code);
        //加入redis完成

        $this->afterSendCodeSuccess($mobile , $ip);
        return true;

    }


    /**
     * 发送成功后的操作。
     * @param $mobile
     * @param $ip
     */
    public function afterSendCodeSuccess($mobile , $ip){
        $end = mktime(23,59,59,date("m"),date("d"),date("Y"));

        /** @var Connection $redis */
        $redis = Yii::$app->redis;
        //添加间隔验证。
        $limitKey = $this->getLimitKey($mobile);
        $redis->setex($limitKey ,$this->sendLimit, 1);
        //ip发送量增加
        $keyIp = $this->getIpKey($ip);
        $redis->incrby($keyIp , 1);
        if ($redis->ttl($keyIp) == -1) {
            //加round是防止同一时间失效
            $redis->expireat($keyIp , $end+(rand(60,60*10)));
        }
        //当日发送量增加
        $key = $this->getSendNumKey($mobile);
        $redis->incrby($key , 1);
        if ($redis->ttl($key) == -1){
            $redis->expireat($key , $end+(rand(60,60*10)));
        }
    }

    /**
     * 验证验证码
     * @param $mobile 手机号
     * @param $inputCode 输入的验证码
     * @param bool $del 验证后是否删除该验证码
     * @return bool
     */
    public function verifyCode($mobile , $inputCode , $del = false){
        $redisCode = $this->getCode($mobile);
        if ($redisCode == $inputCode){
            if ($del){
                $this->delMobileCode($mobile);
            }
            return true;
        }
        return false;
    }

    /**
     * 获取手机的验证码
     * @param $mobile
     * @return mixed
     */
    public function getCode($mobile){
        $mobileKey = $this->getCodeKey($mobile);
        $redis = Yii::$app->redis;
        return $redis->get($mobileKey);
    }


    /**
     * @return ChanzorSms
     */
    public function getSendHandle(){
        return new ChanzorSms();
    }

    /**
     * 验证是否可以发送
     * @param $mobile
     * @param $ip
     * @return bool
     */
    public function verifySendSms($mobile , $ip){
        if(!SimpleValidator::mobile($mobile)){
            $this->error = '手机号码不合法';
            return false;
        }
        if(!$this->verifyIntervalTime($mobile)){
            $this->error = '发送频率太快了，请稍后再试';
            return false;
        }
        if (!$this->verifyIP($ip)) {
            $this->error = '这个IP发送过多';
            return false;
        }
        //今天的发送次数
        if (!$this->verifyTodaySendNum($mobile)) {
            $this->error = '您今天发送的短信已经超过限制，请明日再试';
            return false;
        }
        return true;
    }

    /**
     * 检查发送间隔
     * @param $mobile
     * @return bool
     */
    public function verifyIntervalTime($mobile){
        //白名单不检验
        if (in_array($mobile , $this->whiteList['mobile'])){
            return true;
        }
        //检查是否相隔60秒后发送
        $limitKey = $this->getLimitKey($mobile);
        $smsSendLimit = Yii::$app->redis->get($limitKey);
        if ($smsSendLimit) {
            return false;
        }
        return true;
    }

    /**
     * 检查当日ip发送总数
     * @param $ip
     * @return bool
     */
    public function verifyIp($ip){
        //白名单不检验
        if (in_array($ip , $this->whiteList['ip'])){
            return true;
        }
        $keyIp = $this->getIpKey($ip);
        $smsSendNum = Yii::$app->redis->get($keyIp);
        if (empty($smsSendNum) || $smsSendNum < $this->ipMaxSmsNum) {
            return true;
        }
        return false;
    }


    /**
     * 验证当日发送短信数量
     * @param $mobile
     * @return bool
     */
    public function verifyTodaySendNum($mobile){
        //白名单不检验
        if (in_array($mobile , $this->whiteList['mobile'])){
            return true;
        }
        $key = $this->getSendNumKey($mobile);
        $smsSendNum = Yii::$app->redis->get($key);
        if (empty($smsSendNum) || $smsSendNum < $this->daySendMaxNum) {
            return true;
        }
        return false;
    }


    /**
     * 删除手机验证码
     * @param $mobile
     */
    public function delMobileCode($mobile){
        $mobileKey = $this->getCodeKey($mobile);
        return Yii::$app->redis->del($mobileKey);
    }

    /**
     * 获取验证码的key
     * @param $mobile
     * @return string
     */
    public function getCodeKey($mobile){
        return "mobile:sms_code:".$mobile;
    }
    /**
     * 获取间隔时间的KEY
     * @param $mobile
     * @return string
     */
    public function getLimitKey($mobile){
        return  "mobile:sms_send_limit:" . $mobile;
    }

    /**
     * 获取当日ip验证的key
     * @param $ip
     * @return string
     */
    public function getIpKey($ip){
        return "mobile:sms_ip_num:".$ip.":".date('Ymd');
    }


    /**
     * 获取当日发送数量的key
     * @param $mobile
     * @return string
     */
    public function getSendNumKey($mobile){
        return "mobile:sms_send_num:".$mobile.":".date('Ymd');
    }


    /**
     * 获取手机号下一次发送的允许的时间（在xx秒后）
     * @param $mobile
     * @return int
     */
    public function getMobileNextSendTime($mobile){
        return Yii::$app->redis->ttl($this->getLimitKey($mobile));
    }

    /**
     * 解除ip或者手机号的发送限制
     * @param $mobile
     * @param $ip
     */
    public function clearSendLimit($mobile , $ip){
        /** @var Connection $redis */
        $redis = Yii::$app->redis;
        $redis->del($this->getIpKey($ip) , $this->getSendNumKey($mobile) , $this->getLimitKey($mobile));
        return true;
    }

}