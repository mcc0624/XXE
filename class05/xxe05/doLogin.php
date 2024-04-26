<?php

error_reporting(0);
$USERNAME = 'admin'; //账号
$PASSWORD = 'admin'; //密码
$result = null;

libxml_disable_entity_loader(false);
$preg='/file:\/\/|http:\/\/|php:\/\//gmi';
$xmlfile = file_get_contents('php://input');
if(!preg_match_all($preg,$xmlfile,$res)) {
    $xmlfile = file_get_contents('php://input');
    $bef="/\<\!DOC.*expect:\/\//mi";
    $end="/\>]>/m";
    $cmd1=preg_replace($bef,'',$xmlfile);
    $cmd2=preg_replace($end,'',$cmd1);
    $cmd3=preg_replace('/\"|\'/m','',$cmd2);
    $cmd=preg_replace('/<user>.*<\/user>/mi','',$cmd3);
    $pattern = '/["\{\}|\\<>: ]/';

    $preg1="/\<\!DOCTYPE.*\]\>/mi";
    $xmlfile = preg_replace($preg1,'',$xmlfile);
    $xmlfile = preg_replace('/\&.*\;/i','user',$xmlfile);


    try {
        $dom = new DOMDocument();
        $dom->loadXML($xmlfile, LIBXML_NOENT | LIBXML_DTDLOAD);
        $creds = simplexml_import_dom($dom);

        $username = $creds->username;
        $password = $creds->password;

        if($username == $USERNAME && $password == $PASSWORD){
            $username='admin';
            $password='admin';
        }else{
            if (preg_match($pattern,$cmd)){
                echo "DOMDocument::loadXML(): Invalid URI: expect://echo BLAH in Entity, line: 40";
            } else {
                $username=shell_exec($cmd);
            }
        }

        if ($username == $USERNAME && $password == $PASSWORD) {
            $result = sprintf("<result><code>%d</code><msg>%s</msg></result>", 1, $username);
        } else {
            $result = sprintf("<result><code>%d</code><msg>%s</msg></result>", 0, $username);
        }
    } catch (Exception $e) {
        $result = sprintf("<result><code>%d</code><msg>%s</msg></result>", 3, $e->getMessage());
    }

    header('Content-Type: text/html; charset=utf-8');
    echo $result;
}else{
    echo "非法输入！";
}
?>