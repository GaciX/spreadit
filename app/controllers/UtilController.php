<?php

class UtilController extends BaseController
{
    public static function prettySubstr($data, $max_length=70)
    {
        $data = trim($data);

        if(strlen($data) > $max_length) {
            $data = wordwrap($data, $max_length);
            $data = explode("\n", $data);
            $data = array_shift($data);
        }
        if(strlen($data) > $max_length) {
            $data = substr($data, 0, $max_length);
        }

        return $data;
    }

    public static function prettyUrl($url, $max_length=70)
    {
        $url = self::prettySubstr($url, $max_length);
        
        //from: http://stackoverflow.com/a/7568253
        $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
        $url = trim($url, "-");
        $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
        $url = strtolower($url);
        $url = preg_replace('~[^-a-z0-9_]+~', '', $url);

        return $url;
    }

    public static function prettyAgo($tm, $rcs = 0)
    {
        $cur_tm = time(); $dif = $cur_tm-$tm;
        $pds = array('second','minute','hour','day','week','month','year','decade');
        $lngh = array(1,60,3600,86400,604800,2630880,31570560,315705600);
        for($v = sizeof($lngh)-1; ($v >= 0)&&(($no = $dif/$lngh[$v])<=1); $v--); if($v < 0) $v = 0; $_tm = $cur_tm-($dif%$lngh[$v]);

        $no = floor($no); if($no <> 1) $pds[$v] .='s'; $x=sprintf("%d %s ",$no,$pds[$v]);
        if(($rcs == 1)&&($v >= 1)&&(($cur_tm-$_tm) > 0)) $x .= time_ago($_tm);
        return $x;
    }
}
