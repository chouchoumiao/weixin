<?php
/**
 * Created by PhpStorm.
 * User: wb-wjy227944
 * Date: 2016/10/13
 * Time: 16:40
 */

    /**
     * 判断字符某个位置是中文字符的左半部分还是右半部分，或不是中文
     * 返回 0表示在中文3号位置，1表示在中文1号位置，2表示在2号位置,-1表示不是中文
     * @return int
     * @param string $str 开始位置
     * @param int $location 位置
     */

    function is_cn_utf8_str($str, $location)
    {
        $cut_location=$location+1;
        $length = $location;
        if($location < 0 )return 1;
        if(ord($str{$length}) <= 127)return -1;

        for($i = $length ;$i >=0; $i--)
        {

            if(ord($str{$i}) <= 127)
            {
                $cut_location=$location-$i;
                break;
            }

        }
        return $cut_location%3 ==0 ? 3 : $cut_location%3 ;
    }

    /**
     * 处理截取中文字符串的操作
     * @return string
     * @param string $str 要处理的字符
     * 		  string $start 开始位置
     *        string $offset 偏移量
     *        string $t_str 字符结果尾部增加的字符串，默认为空
     *        boolen $ignore $start位置上如果是中文的某个字后半部分是否忽略该字符，默认true
     */
    function substr_cn_utf8($str, $start, $offset, $t_str = '', $ignore = true)
    {
        $length  = strlen($str);
        if ($length <=  $offset && $start == 0)
        {
            return $str;
        }
        if ($start > $length)
        {
            return $str;
        }
        $r_str     = "";
        for ($i = $start; $i < ($start + $offset); $i++)
        {
            if (ord($str{$i}) > 127)
            {
                if ($i == $start)  //检测头一个字符的时候，是否需要忽略半个中文
                {
                    $cut_length = string::is_cn_utf8_str($str, $i);

                    if ( $cut_length!= -1)
                    {
                        if ($ignore && ($cut_length==2 || $cut_length==3 ))
                        {
                            $i=$i+(3-$cut_length);

                            continue;
                        }
                        else
                        {
                            $i=$i - $cut_length + 1;

                            $r_str .= $str{($i)}.$str{++$i}.$str{++$i};


                        }
                    }
                    else
                    {
                        $r_str .= $str{$i};
                    }

                }
                else
                {

                    $r_str .= $str{$i}.$str{++$i}.$str{++$i};
                }
            }
            else
            {

                $r_str .= $str{$i};
                continue;
            }

        }

        return $r_str . $t_str;
        //return preg_replace("/(&)(#\d{5};)/e", "string::un_html_callback('\\1', '\\2')", $r_str . $t_str);

    }

    function substr_cn($str, $start, $offset, $t_str = '', $ignore = true)
    {
        $length  = strlen($str);
        if ($length <=  $offset && $start == 0)
        {
            return $str;
        }
        if ($start > $length)
        {
            return $str;
        }
        $r_str     = "";
        for ($i = $start; $i < ($start + $offset); $i++)
        {
            if (ord($str{$i}) > 127)
            {
                if ($i == $start)  //检测头一个字符的时候，是否需要忽略半个中文
                {
                    if (string::is_cn_str($str, $i) == 1)
                    {
                        if ($ignore)
                        {
                            continue;
                        }
                        else
                        {
                            $r_str .= $str{($i - 1)}.$str{$i};
                        }
                    }
                    else
                    {
                        $r_str .= $str{$i}.$str{++$i};
                    }
                }
                else
                {
                    $r_str .= $str{$i}.$str{++$i};
                }
            }
            else
            {
                $r_str .= $str{$i};
                continue;
            }
        }
        return $r_str . $t_str;
        //return preg_replace("/(&)(#\d{5};)/e", "string::un_html_callback('\\1', '\\2')", $r_str . $t_str);

    }

    /**
    function un_html_callback($a, $b){
    if ($b){
    return $a. $b;
    }
    return '&amp;';
    }
     **/

    //-- 判断字符串是否含有非法字符 -------
    function check_badchar($str, $allowSpace = false)
    {
        if ($allowSpace)
            return preg_match ("/[><,.\][{}?\/+=|\\\'\":;~!@#*$%^&()`\t\r\n-]/i", $str) == 0 ? true : false;
        else
            return preg_match ("/[><,.\][{}?\/+=|\\\'\":;~!@#*$%^&()` \t\r\n-]/i", $str) == 0 ? true : false;
    }

    /**
     * 判断字符某个位置是中文字符的左半部分还是右半部分，或不是中文
     * 返回 1 是左边 0 不是中文 -1是右边
     * @return int
     * @param string $str 开始位置
     * @param int $location 位置
     */

    function is_cn_str($str, $location)
    {
        $result	= 1;
        $i		= $location;
        while(ord($str{$i}) > 127 && $i >= 0)
        {
            $result *= -1;
            $i --;
        }

        if($i == $location)
        {
            $result = 0;
        }
        return $result;
    }

    /**
     * 判断字符是否全是中文字符组成
     * 2 全是 1部分是 0没有中文
     * @return boolean
     * @param string $str 要判断的字符串
     */

    function chk_cn_str($str)
    {
        $result = 0;
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++)
        {
            if (ord($str{$i}) > 127)
            {
                $result ++;
                $i ++;
            }
            elseif ($result)
            {
                $result = 1;
                break;
            }
        }
        if ($result > 1)
        {
            $result = 2;
        }
        return $result;
    }

    /**
     * 判断邮件地址的正确性
     * @return boolean
     * @param string $mail 邮件地址
     */

    function is_mail($mail)
    {
        //return preg_match("/^[a-z0-9_\-\.]+@[a-z0-9_]+\.[a-z0-9_\.]+$/i" , $mail);
        return preg_match('/^[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)*@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+){1,4}$/', $mail) ? true : false;
    }

    /**
     * 验证输入的手机号码
     *
     * @access  public
     * @param   string      $user_mobile      需要验证的手机号码
     *
     * @return bool
     */
    function is_mobile($mobile)
    {

        return preg_match("/^((\(\d{2,3}\))|(\d{3}\-))?1(3|5|8|9)\d{9}$/", $mobile)? true : false;
    }




    /**
     * 判断App的CallbackURL是否合法（可以包含端口号）
     * @return boolean
     * @param string $url URL地址
     */

    function is_callback_url($url)
    {
        return  preg_match("/(ht|f)tp(s?):\/\/([\w-]+\.)+[\w-]+(\/[\w-.\/?%&=]*)?/i" , $url);
    }

    /**
     * 判断URL是否以http(s):// ftp://格式开始的地址
     * @return boolean
     * @param string $url URL地址
     */

    function is_http_url($url)
    {
        return  preg_match("/^(https?|ftp):\/\/([\w-]+\.)+[\w-]+(\/[\w;\/?:@&=+$,# _.!~*'\"()%-]*)?$/i" , $url);
        //return preg_match("/^(http(s)|ftp):\/\/[a-z0-9\.\/_-]*?$/i" , $url);
    }

    /**
     * 允许中文
     */
    function is_url($url)
    {
        //return  preg_match("/^(https?|ftp|mms|mmsu|mmst|rtsp):\/\/([\w-]+\.)+[\w-]+(\/[\w;\/?:@&=+$,# _.!~*'\"()%-]*)?$/i" , $url);
        //return  preg_match("/^(https?|ftp|mms|mmsu|mmst|rtsp):\/\/([\w-]+\.)+[\w-]+(\/[^ \t\r\n{}\[\]`^<>\\\\]*)?$/i" , $url);
        //return preg_match("/^(http(s)|ftp):\/\/[a-z0-9\.\/_-]*?$/i" , $url);
        return preg_match("/^(https?|ftp|mms|mmsu|mmst|rtsp):\/\/([\w-]+\.)+[\w-]+(:\d{1,9}+)?(\/[^ \t\r\n{}\[\]`^<>\\\\]*)?$/i" , $url);

    }

    /**
     * 判断URL是否是正确的音乐地址
     * @return boolean
     * @param string $url URL地址
     */

    function is_music_url($url)
    {
        return preg_match("/^(https?|ftp|mms|mmsu|mmst|rtsp):\/\/([\w-]+\.)+[\w-]+(:\d{1,9}+)?(\/[^ \t\r\n{}\[\]`^<>\\\\]*)?$/i" , $url);
        //return preg_match("/^(https?|ftp|mms|mmsu|mmst|rtsp):\/\/([\w-]+\.)+[\w-]+(\/[^ \t\r\n{}\[\]`^<>\\\\]*)?$/i" , $url);
        //return  preg_match("/^(https?|ftp|mms|mmsu|mmst|rtsp):\/\/([\w-]+\.)+[\w-]+(\/[\w;\/?:@&=+$,# _.!~*'\"()%-]*)?$/i" , $url);
        //return preg_match("/^(http(s)|ftp):\/\/[a-z0-9\.\/_-]*?$/i" , $url);
    }



    /**
     * 过滤字符串中的特殊字符
     * @return string
     * @param string $str 需要过滤的字符
     * @param string $filtStr 需要过滤字符的数组（下标为需要过滤的字符，值为过滤后的字符）
     * @param boolen $regexp 是否进行正则表达试进行替换，默认false
     */

    function filt_string($str, $filtStr, $regexp = false)
    {
        if (!is_array($filtStr))
        {
            return $str;
        }
        $search		= array_keys($filtStr);
        $replace	= array_values($filtStr);

        if ($regexp)
        {
            return preg_replace($search, $replace, $str);
        }
        else
        {
            return str_replace($search, $replace, $str);
        }
    }

    /**
     * 过滤字符串中的HTML标记 < >
     * @return string
     * @param string $str 需要过滤的字符
     */

    function un_html($str)
    {
        $s	= array(
            "&"     => "&amp;",
            "<"	=> "&lt;",
            ">"	=> "&gt;",
            "\n"	=> "<br>",
            "\t"	=> "&nbsp;&nbsp;&nbsp;&nbsp;",
            "\r"	=> "",
            " "	=> "&nbsp;",
            "\""	=> "&quot;",
            "'"	=> "&#039;",
        );
        //$str = string::esc_korea_change($str);
        $str = strtr($str, $s);
        //$str = string::esc_korea_restore($str);
        return $str;
    }

    /**
     * 过滤字符串的特殊字符，以便把数据存入mysql数据库
     */
    function esc_mysql($str)
    {
        return mysql_escape_string($str);
    }

    /**
     * 过滤字符串的特殊字符，以便把数据输出到页面做编辑显示
     */
    function esc_edit_html($str)
    {
        $s	= array(
            //"&"     => "&amp;",
            "<"		=> "&lt;",
            ">"		=> "&gt;",
            "\""	=> "&quot;",
            "'"		=> "&#039;",
        );
        $str = string::esc_korea_change($str);
        $str = strtr($str, $s);
        $str = string::esc_korea_restore($str);
        return $str;
    }


    /**
     * 过滤字符串的特殊字符，以便把数据输出到页面做输出显示
     */
    function esc_show_html($str)
    {
        $s	= array(
            "&"     => "&amp;",
            "<"		=> "&lt;",
            ">"		=> "&gt;",
            "\n"	=> "<br>",
            "\t"	=> "&nbsp;&nbsp;&nbsp;&nbsp;",
            "\r"	=> "",
            " "		=> "&nbsp;",
            "\""	=> "&quot;",
            "'"		=> "&#039;",
        );


        $str = string::esc_korea_change($str);
        $str = strtr($str, $s);
        $str = string::esc_korea_restore($str);
        return $str;
    }


    function esc_ascii($str)
    {
        $esc_ascii_table = array(
            chr(0),chr(1), chr(2),chr(3),chr(4),chr(5),chr(6),chr(7),chr(8),
            chr(11),chr(12),chr(14),chr(15),chr(16),chr(17),chr(18),chr(19),
            chr(20),chr(21),chr(22),chr(23),chr(24),chr(25),chr(26),chr(27),chr(28),
            chr(29),chr(30),chr(31)
        );


        $str = str_replace($esc_ascii_table, '', $str);
        return $str;
    }

    function esc_user_input($str)
    {
        //$str = iconv("utf-8", "gb2312", $str);
        $str = iconv("utf-8", "gbk//IGNORE", $str);
        // 过滤非法词汇

        // 过滤非法ASCII字符串
        $str = string::esc_ascii($str);

        // 过滤SQL语句
        //$str = string::esc_mysql($str);


        return $str;
    }

    /**
     * 过滤字符串中的<script ...>....</script>
     * @return string
     * @param string $str 需要过滤的字符
     */

    function un_script_code($str)
    {
        $s			= array();
        $s["/<script[^>]*?>.*?<\/script>/si"] = "";
        return string::filt_string($str, $s, true);
    }

    /**
     * 把HTML代码转化ducument.write输出的内容
     * @return string
     * @param string $html 需要处理的HTML代码
     */

    function html2script($html)
    {
        //需要进行转义的字符
        $s			= array();
        $s["\\"]	= "\\\\";
        $s["\""]	= "\\\"";
        $s["'"]		= "\\'";
        $html = string::filt_string($html, $s);
        $html = implode("\\\r\n", explode("\r\n", $html));

        return "document.write(\"\\\r\n" . $html . "\\\r\n\");";
    }

    // 转义js输出，返回合法的js字符串
    function js_esc($str)
    {
        $s_tag = array("\\", "\"", "/", "\r", "\n");
        $r_tag = array("\\\\", "\\\"", "\/", "\\r", "\\n");
        $str = str_replace($s_tag, $r_tag, $str);

        return $str;
    }

    /**
     * 把ducument.write输出的内容转化成HTML代码(必须是html2script函数进行转化的结果)
     * @return string
     * @param string $jsCode 需要处理的JS代码
     */

    function script2html($jsCode)
    {
        $html = explode("\\\r\n", $jsCode);
        array_shift($html);		//去掉数组开头单元
        array_pop($html);		//去掉数组末尾单元
        return implode("\r\n", $html);
    }

    function length($str)
    {
        $str = preg_replace("/&(#\d{5});/", "__", $str);
        return strlen($str);
    }



//获取IP地址
function GetIP()
{
    $unknown = 'unknown';
    if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)){
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    /**
     * 处理多层代理的情况
     * 或者使用正则方式：$ip = <a href="https://www.baidu.com/s?wd=preg_match&tn=44039180_cpr&fenlei=mv6quAkxTZn0IZRqIHckPjm4nH00T1d9mHf1m1Pbm1bsnj61PHcs0AP8IA3qPjfsn1bkrjKxmLKz0ZNzUjdCIZwsrBtEXh9GuA7EQhF9pywdQhPEUiqkIyN1IA-EUBt1PWDvnWRdPWcvn10drjD3PHc" target="_blank" class="baidu-highlight">preg_match</a>("/[\d\.]{7,15}/", $ip, $matches) ? $matches[0] : $unknown;
     */
    //修改追加变量来接受，去除警告 20160312
    $tmp1 = strpos($ip, ',');
    if (false !== $tmp1) {
        $tmp2 = explode(',', $ip);
        $ip = reset($tmp2);
    }
    return $ip;
}

//翻页

//分页
function multi($num, $perpage, $curpage, $mpurl, $ajax=0, $ajax_f='',$flag='') {


    $page = 5;
    $multipage = '';
    $mpurl .= strpos($mpurl, '?') ? '&' : '?';
    $realpages = 1;
    if($num > $perpage) {
        $offset = 2;
        $realpages = @ceil($num / $perpage);
        $pages = $realpages;
        if($page > $pages) {
            $from = 1;
            $to = $pages;
        } else {
            $from = $curpage - $offset;
            $to = $from + $page - 1;
            if($from < 1) {
                $to = $curpage + 1 - $from;
                $from = 1;
                if($to - $from < $page) {
                    $to = $page;
                }
            } elseif($to > $pages) {
                $from = $pages - $page + 1;
                $to = $pages;
            }
        }
        $multipage = '';
        if($curpage - $offset > 1 && $pages > $page) {
            $multipage .= "<a ";
            if($ajax) {
                $multipage .= "href=\"javascript:{$ajax_f}($flag,1);\"";
            } else {
                $multipage .= "href=\"{$mpurl}page=1{$urlplus}\"";
            }
            $multipage .= " class=\"first\">首</a>";
        }
        if($curpage > 1) {
            $multipage .= "<a ";
            if($ajax) {
                $multipage .= "href=\"javascript:{$ajax_f}($flag,".($curpage-1).");\" ";
            } else {
                $multipage .= "href=\"{$mpurl}page=".($curpage-1)."$urlplus\"";
            }
            $multipage .= " class=\"prev\">&lt;&lt; </a>";
        }
        for($i = $from; $i <= $to; $i++) {
            if($i == $curpage) {
                $multipage .= '<a href="###" class="cur">'.$i.'</strong>';
            } else {
                $multipage .= "<a ";
                if($ajax) {
                    $multipage .= "href=\"javascript:{$ajax_f}($flag,$i);\" ";
                } else {
                    $multipage .= "href=\"{$mpurl}page=$i{$urlplus}\"";
                }
                $multipage .= ">&nbsp$i&nbsp</a>";
            }
        }
        if($curpage < $pages) {
            $multipage .= "<a ";
            if($ajax) {
                $multipage .= "href=\"javascript:{$ajax_f}($flag,".($curpage+1).");\" ";
            } else {
                $multipage .= "href=\"{$mpurl}page=".($curpage+1)."{$urlplus}\"";
            }
            $multipage .= " class=\"next\"> &gt;&gt;</a>";
        }
        if($to < $pages) {
            $multipage .= "<a ";
            if($ajax) {
                $multipage .= "href=\"javascript:{$ajax_f}($flag,$pages);\" ";
            } else {
                $multipage .= "href=\"{$mpurl}page=$pages{$urlplus}\"";
            }
            $multipage .= " class=\"last\">尾</a>";
        }
        if($multipage) {
            //$multipage = '<em>&nbsp;'.$num.'&nbsp;</em>'.$multipage;
        }
    }
    return $multipage;
}

//判断输入data是否为空
function strIsNull($data,$message)
{
    if($data==='')
    {
        echo "<script>alert('$message');history.back();</Script>";
        exit;

    }
}

//判断输入data是否为空，已经是否数字
function isNum($data,$message1,$message2)
{
    if($data === '')
    {
        echo "<script>alert('$message1');history.back();</Script>";
        exit;

    }else if(!is_numeric($data)){
        echo "<script>alert('$message2');history.back();</Script>";
        exit;
    }
}


//判断输入data是否是fromdata和todata范围内的数据  -by 20150320
function isThisRangeNum($data,$fromdata,$todata,$message)
{
    //echo "<script>alert('$message');history.back();</Script>";
    if(($data < $fromdata)||($data > $todata))
    {
        echo "<script>alert('必须是'+$fromdata+'到'+$todata+'范围内的数字');history.back();</Script>";
        exit;
    }
}

//比较date1，date2相差的天数
function dateDiffer($date1,$date2,$message)
{
    if((strtotime($date1) - strtotime($date2))/86400 < 0){
        echo "<script>alert('$message');history.back();</Script>";
        exit;
    }
}

//判断date是否为日期格式
function isDateOrNot($data,$message)
{
    if(!isdate($data))
    {
        echo "<script>alert('$message');history.back();</Script>";
        exit;

    }
}
//判断日期格式子函数
function isdate($str,$format="Y-m-d"){
    $strArr = explode("-",$str);
    if(empty($strArr)){
        return false;
    }
    foreach($strArr as $val){
        if(strlen($val)<2){
            $val="0".$val;
        }
        $newArr[]=$val;
    }
    $str =implode("-",$newArr);
    $unixTime=strtotime($str);
    $checkDate= date($format,$unixTime);
    if($checkDate==$str)
        return true;
    else
        return false;
}

//判断是否是会员，不是则跳转到注册页面
function isVipByOpenid($openid,$weixinID,$nowUrl){

    if(!vipInfo($openid,$weixinID)){
        $url  =  "http://".$_SERVER['HTTP_HOST']."/APP/01_vipCenter/VipBD.php?openid=".$openid."&weixinID=".$weixinID."&url=".$nowUrl;

        echo "<script> location = '$url'</script>";
        exit;
    }
}

//生成token函数
function getToken( $len = 32, $md5 = true ) {
    # Seed random number generator
    # Only needed for PHP versions prior to 4.2
    mt_srand( (double)microtime()*1000000 );
    # Array of characters, adjust as desired
    $chars = array(
        'Q', '@', '8', 'y', '%', '^', '5', 'Z', '(', 'G', '_', 'O', '`',
        'S', '-', 'N', '<', 'D', '{', '}', '[', ']', 'h', ';', 'W', '.',
        '/', '|', ':', '1', 'E', 'L', '4', '&', '6', '7', '#', '9', 'a',
        'A', 'b', 'B', '~', 'C', 'd', '>', 'e', '2', 'f', 'P', 'g', ')',
        '?', 'H', 'i', 'X', 'U', 'J', 'k', 'r', 'l', '3', 't', 'M', 'n',
        '=', 'o', '+', 'p', 'F', 'q', '!', 'K', 'R', 's', 'c', 'm', 'T',
        'v', 'j', 'u', 'V', 'w', ',', 'x', 'I', '$', 'Y', 'z', '*'
    );
    # Array indice friendly number of chars;
    $numChars = count($chars) - 1;
    $token = '';
    # Create random token at the specified length
    for ( $i=0; $i<$len; $i++ )
        $token .= $chars[ mt_rand(0, $numChars) ];
    # Should token be run through md5?
    if ( $md5 ) {
        # Number of 32 char chunks
        $chunks = ceil( strlen($token) / 32 ); $md5token = '';
        # Run each chunk through md5
        for ( $i=1; $i<=$chunks; $i++ )
            $md5token .= md5( substr($token, $i * 32 - 32, 32) );
        # Trim the token
        $token = substr($md5token, 0, $len);
    }
    return $token;
}

/**
 * 输出HTML的Warning
 * @param $msg
 */
function echoWarning($msg){
    echo '<html>
        <head>
        <link href="http://apps.bdimg.com/libs/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0" />
        <style>
        .wrap{margin:0 auto;width:80%;text-align: center;}
        </style>
        </head>
        <body>
            <div class="wrap">
            	<br/>
                <div id="myMsg" class="alert alert-warning">'.$msg.'</div>
            </div>
        </body>
    </html>';
}

/**
 * 输出HTML的Info
 * @param $msg
 */
function echoInfo($msg){
    echo '<html>
        <head>
        <link href="http://apps.bdimg.com/libs/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0" />
        <style>
        .wrap{margin:0 auto;width:80%;text-align: center;}
        </style>
        </head>
        <body>
            <div class="wrap">
                <br/>
                <div id="myMsg" class="alert alert-success">'.$msg.'</div>
            </div>
        </body>
    </html>';
}

/**
 *  进入会员绑定按钮
 */
//function linkToVipBD($url){
//
//    echo '<html>
//        <head>
//        <link href="http://apps.bdimg.com/libs/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
//        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0" />
//        <style>
//        .wrap{margin:0 auto;width:80%;text-align: center;}
//        </style>
//        </head>
//        <body>
//            <div class="wrap">
//                <br/>
//                <a href='.$url.' class="btn btn-success" role="button">赶紧注册会员吧</a>
//            </div>
//        </body>
//
//    </html>';
//}

/**
 * 对变量进行 JSON 编码
 * @param $value
 * @return mixed|string
 */
function getPreg_replace( $value)
{
    if ( version_compare( PHP_VERSION,'5.4.0','<'))
    {
        $str = json_encode( $value);
        $str =  preg_replace_callback(
            "#\\\u([0-9a-f]{4})#i",
            function( $matchs)
            {
                return  iconv('UCS-2BE', 'UTF-8',  pack('H4',  $matchs[1]));
            },
            $str
        );
        return  $str;
    }
    else
    {
        return json_encode( $value, JSON_UNESCAPED_UNICODE);
    }
}
//用于json文字转化
//function getPreg_replace($arr){
//    return preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", json_encode($arr));
//}

//URL地址参数加密
function myURLEncode($str){
    return base64_encode(base64_encode($str));
}

//URL地址参数解密
function myURLDecode($str){
    return base64_decode(base64_decode($str));
}

//传入参数 长度判断
function isParameterOK($p,$len){
    if(!isset($p) || strlen($p) != $len){
        echo "传入参数不正确，请确认！";
        exit;
    }
}

/**
 * 传入参数OpenID和WeixinID 长度判断
 * openid固定长：28,weixinID固定长：2
 * @param $openid
 * @param $weixinID
 */
function isOpenIDWeixinIDOK($openid,$weixinID){
    if(!isset($openid)
        || !isset($weixinID)
        || strlen(base64_decode($openid)) != 28
        || strlen(base64_decode($weixinID)) != 2){
        echo '<html>
                <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                <link href="http://apps.bdimg.com/libs/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
                <link href="//cdn.bootcss.com/flat-ui/2.2.2/css/flat-ui.min.css" rel="stylesheet">
                <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0" /> 
                <style>
                .wrap{
                    position: absolute;
                    width:90%;
                    height:200px;
                    left:50%;
                    top:50%;
                    margin-left:-45%;
                    margin-top:-100px;
                    text-align:center;
                } 
                </style>
                </head>
                <body>
                    <div class="wrap">
                        <div id="myMsg" class="alert alert-danger"><h3>参数错误</h3></div>
                    </div>
                </body>
            </html>';
        exit;
    }
}


/**
 * 防注入函数追加 20151130
 */

//PHP整站防注入程序，需要在公共文件中require_once本文件

//判断magic_quotes_gpc状态

if (@get_magic_quotes_gpc ()) {
    $_GET = sec ( $_GET );
    $_POST = sec ( $_POST );
    $_COOKIE = sec ( $_COOKIE );
    $_FILES = sec ( $_FILES );
}

$_SERVER = sec ( $_SERVER );

function sec(&$array) {

    //如果是数组，遍历数组，递归调用

    if (is_array ( $array )) {
        foreach ( $array as $k => $v )
            $array [$k] = sec ( $v );
    }else if (is_string ( $array )){

        //使用addslashes函数来处理
        $array = addslashes ( $array );
    } else if (is_numeric ( $array )) {
        $array = intval ( $array );
    }
    return $array;

}

//整型过滤函数

function num_check($id) {

    if (! $id) {
        die ( '参数不能为空!' );
    } //是否为空的判断

    else if (inject_check ( $id )) {
        die ( '非法参数' );
    } //注入判断
    else if (! is_numetic ( $id )) {
        die ( '非法参数' );
    }

    //数字判断
    $id = intval ( $id );
    //整型化
    return $id;

}

//字符过滤函数
function str_check($str) {
    if (inject_check ( $str )) {
        die ( '非法参数' );
    }
    //注入判断
    $str = htmlspecialchars ( $str );
    //转换html
    return $str;
}

function search_check($str) {

    $str = str_replace ( "_", "_", $str );

//把"_"过滤掉

    $str = str_replace ( "%", "%", $str );

//把"%"过滤掉

    $str = htmlspecialchars ( $str );

//转换html

    return $str;

}

//表单过滤函数

function post_check($str, $min, $max) {

    if (isset ( $min ) && strlen ( $str ) < $min) {

        die ( '最少$min字节' );

    } else if (isset ( $max ) && strlen ( $str ) > $max) {

        die ( '最多$max字节' );

    }

    return stripslashes_array ( $str );

}

//防注入函数
function inject_check($sql_str) {

    return eregi ( "select|inert|update|delete|'|/*|*|../|./|UNION|into|load_file|outfile", $sql_str );

}

function stripslashes_array(&$array) {

    if (is_array ( $array )) {
        foreach ( $array as $k => $v ) {
            $array [$k] = stripslashes_array ( $v );
        }

    }else if (is_string ( $array )) {
        $array = stripslashes ( $array );
    }
    return $array;

}
//将获取的openid和weixinID保存到KVDB中 20151214 ↓↓↓↓↓↓↓↓↓↓↓↓↓
function setKVDB($key,$value){

    $kv = new SaeKV();
    $kv->init(); //访问授权应用的数据

    if($kv->get($key) == null)
    {
        $kv->add($key,$value);
    }else{
        $kv->set($key,$value);
    }
}
function getKVDB($key){

    $kv = new SaeKV();
    $kv->init("accesskey"); //访问授权应用的数据

    return $kv->get($key);
}

/**
 * 追加推荐得iphone活动的功能 20151215
 * 1.根据取得的IP地址获取城市名称
 */
//需追加 20151216
function getCity(){
    
    $content = file_get_contents("http://ip.taobao.com/service/getIpInfo.php?ip=".GetIP());
    $json = json_decode($content,JSON_UNESCAPED_UNICODE);
    return $json['data']['region'];//按层级关系提取address数据
}

//判断是否使用微信自带的浏览器进行访问
function is_weixin(){
    $useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
    if ( strpos($useragent, 'MicroMessenger') !== false || strpos($useragent, 'Windows Phone') !== false ) {
        return true;
    }
    return false;
}
