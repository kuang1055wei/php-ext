<?php
/**
 * 重复内容过滤器
 * 该布隆过滤器总位数为2^32位, 判断条数为2^30条. hash函数最优为3个.(能够容忍最多的hash函数个数)
 * 使用的三个hash函数为
 * BKDR, SDBM, JSHash
 *
 * 注意, 在存储的数据量到2^30条时候, 误判率会急剧增加, 因此需要定时判断过滤器中的位为1的的数量是否超过50%, 超过则需要清空.
 */
class BloomFilterRedis
{
    /**
     * 表示判断重复内容的过滤器
     * @var string
     */
    protected $bucket = 'rptc';

    protected $hashFunction = array('BKDRHash', 'SDBMHash', 'JSHash');

    /**
     * @var Redis
     */
    private $Redis;
    /**
     * @var BloomFilterHash
     */
    private $Hash;

    public function __construct($redis , $bucket)
    {
        if (!$this->bucket || !$this->hashFunction) {
            throw new Exception("需要定义bucket和hashFunction", 1);
        }
        $this->Hash = new BloomFilterHash;
        $this->Redis = $redis; //假设这里你已经连接好了
        $this->bucket = $bucket;
    }

    /**
     * 添加到集合中
     */
    public function add($string)
    {
        $pipe = $this->Redis->multi();
        foreach ($this->hashFunction as $function) {
            $hash = $this->Hash->$function($string);
            $pipe->setBit($this->bucket, $hash, 1);
        }
        return $pipe->exec();
    }

    /**
     * 查询是否存在, 如果曾经写入过，必定回true，如果没写入过，有一定几率会误判为存在
     * @param $string
     *
     * @return bool true:存在。false:不存在
     */
    public function exists($string)
    {
        $pipe = $this->Redis->multi();
        $len = strlen($string);
        foreach ($this->hashFunction as $function) {
            $hash = $this->Hash->$function($string, $len);
            $pipe = $pipe->getBit($this->bucket, $hash);
        }
        $res = $pipe->exec();
        foreach ($res as $bit) {
            if ($bit == 0) {
                return false;
            }
        }
        return true;
    }
}


