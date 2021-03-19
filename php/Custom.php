<?php
namespace app\components;

use yii;

//常用功能集合
class Custom{

    /**
     * 下载网络资源(图片)
     * @param $url
     * @param $saveName
     */
    public static function getSource($url, $saveName){

        $savePath = dirname($saveName);
        if(!file_exists($savePath)){
            create_dirs($savePath);
        }

        //CURL获取网络资源
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt ( $ch, CURLOPT_URL, $url );
        ob_start ();
        curl_exec ( $ch );
        $return_content = ob_get_contents ();
        ob_end_clean ();

        file_put_contents($saveName,$return_content);

    }


    /**
     * 相邻两个单词是英文或者数字的中间加空格
     * @return string
     */
    public static function concatWords(){
        $pattern="/^[A-Za-z0-9\s]+$/";

        $num=func_num_args();
        if($num<2)  trigger_error('concatWords需至少有两个参数', E_USER_ERROR);

        $args=func_get_args();

        $str='';
        for($i=0;$i<=$num;$i++){
            if(preg_match($pattern,$str) && preg_match($pattern, $args[$i])){
                $wrap=' ';
            }else{
                $wrap='';
            }
            $str.=$wrap.$args[$i];
        }
        return $str;
    }

    /**
     * 汉字转拼音
     * @param $_String
     * @param string $_Code
     * @return mixed
     */
    public static function Pinyin($_String, $_Code='UTF8'){ //GBK页面可改为gb2312，其他随意填写为UTF8
        $_String=preg_replace("/[A-Za-z0-9]+/", '', $_String);

        $_DataKey = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha".
            "|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|".
            "cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er".
            "|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui".
            "|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang".
            "|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang".
            "|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue".
            "|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne".
            "|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen".
            "|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang".
            "|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|".
            "she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|".
            "tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu".
            "|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you".
            "|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|".
            "zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo";
        $_DataValue = "-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990".
            "|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725".
            "|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263".
            "|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003".
            "|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697".
            "|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211".
            "|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922".
            "|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468".
            "|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664".
            "|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407".
            "|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959".
            "|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652".
            "|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369".
            "|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128".
            "|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914".
            "|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645".
            "|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149".
            "|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087".
            "|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658".
            "|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340".
            "|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888".
            "|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585".
            "|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847".
            "|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055".
            "|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780".
            "|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274".
            "|-10270|-10262|-10260|-10256|-10254";
        $_TDataKey   = explode('|', $_DataKey);
        $_TDataValue = explode('|', $_DataValue);
        $_Data = array_combine($_TDataKey, $_TDataValue);
        arsort($_Data);
        reset($_Data);
        if($_Code!= 'gb2312') $_String = self::_U2_Utf8_Gb($_String);
        $_Res = '';
        for($i=0; $i<strlen($_String); $i++) {
            $_P = ord(substr($_String, $i, 1));
            if($_P>160) {
                $_Q = ord(substr($_String, ++$i, 1)); $_P = $_P*256 + $_Q - 65536;
            }
            $_Res .= self::_Pinyin($_P, $_Data);
        }
        return preg_replace("/[^a-z0-9]*/", '', $_Res);
    }

    private static function _Pinyin($_Num, $_Data){
        if($_Num>0 && $_Num<160 ){
            return chr($_Num);
        }elseif($_Num<-20319 || $_Num>-10247){
            return '';
        }else{
            foreach($_Data as $k=>$v){
                if($v<=$_Num) break;
            }
            return $k;
        }
    }

    private static function _U2_Utf8_Gb($_C){
        $_String = '';
        if($_C < 0x80){
            $_String .= $_C;
        }elseif($_C < 0x800) {
            $_String .= chr(0xC0 | $_C>>6);
            $_String .= chr(0x80 | $_C & 0x3F);
        }elseif($_C < 0x10000){
            $_String .= chr(0xE0 | $_C>>12);
            $_String .= chr(0x80 | $_C>>6 & 0x3F);
            $_String .= chr(0x80 | $_C & 0x3F);
        }elseif($_C < 0x200000) {
            $_String .= chr(0xF0 | $_C>>18);
            $_String .= chr(0x80 | $_C>>12 & 0x3F);
            $_String .= chr(0x80 | $_C>>6 & 0x3F);
            $_String .= chr(0x80 | $_C & 0x3F);
        }
        return mb_convert_encoding( $_String,'GB2312','UTF-8');
    }


    public static function jsonResponseCallback($mixed, $callback) {
        header('Content-type: application/json');
        die(sprintf('%s(%s);', $callback, json_encode($mixed)));
    }


    /*
     * 生成baidu短链接 接口
     * author huangdi
     */
    public static function getTinyurl($url){
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,"http://dwz.cn/create.php");
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $data=array('url'=>$url);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        $strRes=curl_exec($ch);
        curl_close($ch);
        $arrResponse=json_decode($strRes,true);
        if($arrResponse['status']==0)
        {
            /** tinyurl */
            return $arrResponse['tinyurl'];
        }
        /**错误处理*/
        return 'http://dwz.cn/f7I67';
    }

    /**
     * 简单的生成带数字和字母的随机字符串
     *
     * @param int $length 字符串长度,默认长度为16
     * @param bool $case_sensitive 默认值为true . true是区分大小写, false是不区分大小写
     * @return string
     * @author LvJianhua v2014/06/23
     */
    public static function randomString($length = 16, $case_sensitive = true)
    {
        $str = $case_sensitive ? '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' : '0123456789abcdefghijklmnopqrstuvwxyz';

        return substr(str_shuffle(str_repeat($str, 15)), 0, $length);
    }

    /**
     * 获取相对时间
     * 1分钟以内的 显示刚刚
     * 1小时以内的 显示xx分钟前
     * 1天之内的 显示今天 xx:xx
     * 今天之前的 显示 月-日 xx:xx
     * @param int $time
     * @return int
     * @author SunKai 2013/12/09
     * @modify LvJianHua v2014/09/18
     */
    public static function getRtime($time, $timeFormat='m月d日 H:i')
    {
        //获取当前时间
        $currentTimestamp = time();

        //获取相对时间
        $relativeTimestamp = $currentTimestamp - $time;

        //获取今天的零点时间的时间戳
        $todayBeginTimestamp = strtotime('today');

        //昨天的零点时间的时间戳
        $yestodayBeginTimestamp = strtotime('yesterday');

        if ($todayBeginTimestamp <= $time  && $time <= $currentTimestamp ) {
            if ($relativeTimestamp >= 3600) {
                return round($relativeTimestamp / 3600) . '小时前';
            }else if ($relativeTimestamp >= 60) {
                return round($relativeTimestamp / 60) . '分钟前';
            } elseif (0 < $relativeTimestamp && $relativeTimestamp < 60) {
                return  $relativeTimestamp . '秒前';
            }else{
                return '刚刚';
            }
        } elseif ($yestodayBeginTimestamp <= $time && $time < $todayBeginTimestamp) {
            return '昨天 ' . date('H:i', $time);
        } else {
            return date($timeFormat, $time);
        }
    }

    /**
     * 后台使用时间格式化函数
     * @param $time
     * @return bool|string
     */
    public static function FormatTime($time)
    {
        //获取当前时间
        $currentTimestamp = time();

        //获取相对时间
        $relativeTimestamp = $currentTimestamp - $time;

        //获取今天的零点时间的时间戳
        $todayBeginTimestamp = strtotime('today');
        //昨天的零点时间的时间戳
        $yestodayBeginTimestamp = strtotime('yesterday');


        if ($todayBeginTimestamp <= $time  && $time <= $currentTimestamp ) {
            if ($relativeTimestamp >= 3600) {
                return '今天 ' . date('H:i', $time);
            }else if ($relativeTimestamp >= 60) {
                return round($relativeTimestamp / 60) . '分钟前';
            } elseif (0 < $relativeTimestamp && $relativeTimestamp < 60) {
                return  $relativeTimestamp . '秒前';
            }else{
                return '刚刚';
            }
        } elseif ($yestodayBeginTimestamp <= $time && $time < $todayBeginTimestamp) {
            return '昨天 ' . date('H:i', $time);
        } else {
            return date('m月d日 H:i', $time);
        }
    }


    /**
     * 中文算两个字符 英文算一个, 算出的结果是为英文的个数为基准
     * @param $str
     * @return float
     * @modify LvJianHua v2014/10/22
     */
    public static function getWordsLength($str)
    {
        return  (strlen($str) + mb_strlen($str, 'UTF-8')) / 2;
    }

    /**
     * 截取字符串
     * @param  [type] $string [description]
     * @param  [type] $length [description]
     * @return [type]         [description]
     */
    public static function cutstr($string, $length,$last=false) {
        preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $string, $info);
        // var_dump($info);exit();
        $j = 0;
        $wordscut='';
        for($i=0; $i<count($info[0]); $i++) {
            $wordscut .= $info[0][$i];
            $j = ord($info[0][$i]) > 127 ? $j + 2 : $j + 1;
            if ($j > $length - 3) {
                if($last){
                    return $wordscut;
                }
                return $wordscut." ...";
            }
        }
        return join('', $info[0]);
    }

    /**
     *
     * @param $data array
     * @param $fileFullPath string
     * @param $excelHeaders
     * @author LvJianHua v2014/12/08
     */
    public static function ExportExcel($data, $fileFullPath, $excelHeaders, $colStart = 'A')
    {


        $objExcel = new \PHPExcel();
        $objExcel->setActiveSheetIndex(0);

        //行号
        $lineNo = 1;

        $colStart = ord($colStart);

        $i = 0;
        foreach ($excelHeaders as $excelHeader) {
            $columnID = self::getAbc($i);// chr($colStart + $i);
            $objExcel->getActiveSheet()->setCellValue($columnID . $lineNo, $excelHeader);
            $objExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            $i++;
        }

        foreach ($data as $position => $val) {
            $objExcel->getActiveSheet()->setCellValue($position, $val);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$fileFullPath.'"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    /**
     * 去除样式信息
     * @param  [type] $content [description]
     * @return [type]          [description]
     */
    public static function removeStyle($content){
        $content = preg_replace("/style=.+?['|\"]/i",'',$content);//去除样式
        $content = preg_replace("/class=.+?['|\"]/i",'',$content);//去除样式
        $content = preg_replace("/id=.+?['|\"]/i",'',$content);//去除样式
        $content = preg_replace("/lang=.+?['|\"]/i",'',$content);//去除样式
        $content = preg_replace("/width=.+?['|\"]/i",'',$content);//去除样式
        $content = preg_replace("/height=.+?['|\"]/i",'',$content);//去除样式
        $content = preg_replace("/border=.+?['|\"]/i",'',$content);//去除样式
        $content = preg_replace("/face=.+?['|\"]/i",'',$content);//去除样式
        $content = preg_replace("/face=.+?['|\"]/",'',$content);//去除样式 只允许小写 正则匹配没有带 i 参数
        return $content;
    }


    /**
     * 导出CSV文件
     * @param array $data        数据
     * @param array $header_data 首行数据
     * @param string $file_name  文件名称
     * @return string
     */
    public static function exportCsv($data = [], $header_data = [], $file_name = ''){
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$file_name);
        header('Cache-Control: max-age=0');
        $fp = fopen('php://output', 'a');
        if (!empty($header_data)) {
            foreach ($header_data as $key => $value) {
                $header_data[$key] = iconv('utf-8', 'gbk//IGNORE', $value);
            }
            fputcsv($fp, $header_data);
        }
        $num = 0;
        //每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 100000;
        //逐行取出数据，不浪费内存
        $count = count($data);
        $preg = array('/ر/');
        if ($count > 0) {
            for ($i = 0; $i < $count; $i++) {
                $num++;
                //刷新一下输出buffer，防止由于数据过多造成问题
                if ($limit == $num) {
                    ob_flush();
                    flush();
                    $num = 0;
                }
                $row = $data[$i];
                foreach ($row as $key => $value) {
                    $row[$key] = iconv('utf-8//IGNORE', 'gbk//IGNORE', preg_replace($preg,"*",$value));
                }
                fputcsv($fp, $row);
            }
        }
        fclose($fp);
    }

    //JSON格式输出
    public static function jsonResponse($mixed , $option=null) {
        header("Cache-Control: no-cache");
        header("Pragma: no-cache");
        header('Content-type: application/json');
        if($option){
            echo (json_encode($mixed , $option));
            exit;
        }
        echo (yii\helpers\Json::encode($mixed));
        exit;
    }

    public static function arrayValToPinyin($list){
        if ($list) {
            foreach($list as $key => $val) {
                //Custom::Pinyin()只可转换中文，对于非中文字符，都是返回''
                $words = Custom::Pinyin($val);
                if ($words && ord($val) > 128) {  //首字是中文
                    $pinyin = substr($words, 0, 1);
                } else {
                    $pinyin = substr($val, 0, 1);
                }
                if(!preg_match('/^[A-Za-z0-9]+$/',ucwords($pinyin))){
                    $pinyin = '0';
                }
                $list[$key] = ucwords($pinyin) . ' ' .$val;
            }
            asort($list);
        }
        return $list;
    }

    public static function carNumber($license){


        //替换一些傻傻的英文写错的字符
        $license = str_replace('Ⅴ','V',$license); //第一个v其实是希腊字母4
        $license = str_replace('Ⅰ','I',$license); //第一个I其实是希腊字母1
        $license = str_replace('贑','赣',$license);
        $license = str_replace('翼','冀',$license);
        $license = str_replace('粵','粤',$license);
        $license = str_replace('Ⅹ','X',$license); //这个X是个乘号
        $license = str_replace('ⅹ','X',$license); //这个ⅹ是个乘号

        //全角转换为半角
        $license = self::convertStrType($license,'TOSBC');

        //去掉所有除中文、英文、数字之外的字符
        $license = self::match_chinese($license);

        //所有应为转为大写
        $license = strtoupper($license);

        //正则
        $regularCard = "/([京津冀晋蒙辽吉黑沪苏浙皖闽赣鲁豫鄂湘粤桂琼川贵云渝藏陕甘青宁新使]{1}[A-Z]{1}[0-9a-zA-Z]{5})|(\/)$/u";
        $regularGua = "/([京津冀晋蒙辽吉黑沪苏浙皖闽赣鲁豫鄂湘粤桂琼川贵云渝藏陕甘青宁新使]{1}[A-Z]{1}[0-9a-zA-Z]{4}挂)|(\/)$/u";

        $license = str_replace(' ','',$license);
        preg_match($regularCard, $license, $matchCard);
        preg_match($regularGua, $license, $matchGua);
        $cardMatch = [
            'card'=>'',
            'gua'=>''
        ];
        if (isset($matchCard[0]))
            $cardMatch['card'] = $matchCard[0];
        if (isset($matchGua[0]))
            $cardMatch['gua'] = $matchGua[0];

        return $cardMatch;
    }

    public static function match_chinese($chars,$encoding='utf8')
    {

        $pattern =($encoding=='utf8')?'/[\x{4e00}-\x{9fa5}a-zA-Z0-9]/u':'/[\x80-\xFF]/';

        preg_match_all($pattern,$chars,$result);

        $temp =join('',$result[0]);

        return $temp;

    }

    public static function convertStrType($str, $type) {
        $dbc = array(
            '０' , '１' , '２' , '３' , '４' ,
            '５' , '６' , '７' , '８' , '９' ,
            'Ａ' , 'Ｂ' , 'Ｃ' , 'Ｄ' , 'Ｅ' ,
            'Ｆ' , 'Ｇ' , 'Ｈ' , 'Ｉ' , 'Ｊ' ,
            'Ｋ' , 'Ｌ' , 'Ｍ' , 'Ｎ' , 'Ｏ' ,
            'Ｐ' , 'Ｑ' , 'Ｒ' , 'Ｓ' , 'Ｔ' ,
            'Ｕ' , 'Ｖ' , 'Ｗ' , 'Ｘ' , 'Ｙ' ,
            'Ｚ' , 'ａ' , 'ｂ' , 'ｃ' , 'ｄ' ,
            'ｅ' , 'ｆ' , 'ｇ' , 'ｈ' , 'ｉ' ,
            'ｊ' , 'ｋ' , 'ｌ' , 'ｍ' , 'ｎ' ,
            'ｏ' , 'ｐ' , 'ｑ' , 'ｒ' , 'ｓ' ,
            'ｔ' , 'ｕ' , 'ｖ' , 'ｗ' , 'ｘ' ,
            'ｙ' , 'ｚ' , '－' , '　' , '：' ,
            '．' , '，' , '／' , '％' , '＃' ,
            '！' , '＠' , '＆' , '（' , '）' ,
            '＜' , '＞' , '＂' , '＇' , '？' ,
            '［' , '］' , '｛' , '｝' , '＼' ,
            '｜' , '＋' , '＝' , '＿' , '＾' ,
            '￥' , '￣' , '｀'
        );
        $sbc = array( //半角
            '0', '1', '2', '3', '4',
            '5', '6', '7', '8', '9',
            'A', 'B', 'C', 'D', 'E',
            'F', 'G', 'H', 'I', 'J',
            'K', 'L', 'M', 'N', 'O',
            'P', 'Q', 'R', 'S', 'T',
            'U', 'V', 'W', 'X', 'Y',
            'Z', 'a', 'b', 'c', 'd',
            'e', 'f', 'g', 'h', 'i',
            'j', 'k', 'l', 'm', 'n',
            'o', 'p', 'q', 'r', 's',
            't', 'u', 'v', 'w', 'x',
            'y', 'z', '-', ' ', ':',
            '.', ',', '/', '%', '#',
            '!', '@', '&', '(', ')',
            '<', '>', '"', '\'','?',
            '[', ']', '{', '}', '\\',
            '|', '+', '=', '_', '^',
            '¥','~', '`'
        );
        if($type == "TODBC"){
            return str_replace( $sbc, $dbc, $str ); //半角到全角
        }elseif($type == "TOSBC"){
            return str_replace( $dbc, $sbc, $str ); //全角到半角
        }else{
            return false;
        }
    }

}
