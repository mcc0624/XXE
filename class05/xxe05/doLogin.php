<?php

error_reporting(0);
$USERNAME = 'admin'; //账号
$PASSWORD = 'admin'; //密码
$result = null;

function validateEntityConsistency($xmlfile) {
    // 步骤1：提取ENTITY定义名称
    preg_match('/<!ENTITY\s+(\w+)\s+SYSTEM\s+".+?"/i', $xmlfile, $entityMatches);
    if (empty($entityMatches)) return false;

    // 步骤2：提取username节点的实体引用
    preg_match('/<username>([^<]+)<\/username>/i', $xmlfile, $usageMatches);
    if (empty($usageMatches)) return false;

    // 步骤3：双重验证机制
    $definedEntity = $entityMatches;
    $usedReference = $usageMatches;

    return (
        // 格式验证：必须包含实体引用符号
        (strpos($usedReference[1], '&') === 0) &&
        (substr($usedReference[1], -1) === ';') &&
        // 内容验证：去除符号后与定义一致
        (trim($usedReference[1], '&;') === $definedEntity[1])
    );
}

libxml_disable_entity_loader(false);
$preg='/file:\/\/|http:\/\/|php:\/\//gmi';
$xmlfile = file_get_contents('php://input');
if(!preg_match_all($preg,$xmlfile,$res)) {
    $xmlfile = file_get_contents('php://input');
    $bef="/\<\!DOC.*expect:\/\//mi";
    $end="/\>]>/m";

    if (preg_match( $bef, $xmlfile)) {
        $cmd1 = preg_replace($bef, '', $xmlfile);
        $cmd2 = preg_replace($end, '', $cmd1);
        $cmd3 = preg_replace('/\"|\'/m', '', $cmd2);
        $cmd = trim(preg_replace('/<user>.*<\/user>/mi', '', $cmd3));
        $pattern = '/["\{\}|\\<>: ]/';

        $preg1 = "/\<\!DOCTYPE.*\]\>/mi";
        $xmlfile1 = preg_replace($preg1, '', $xmlfile);
        $xmlfile2 = preg_replace('/\&.*\;/i', 'user', $xmlfile1);
    }else{
        $cmd = $xmlfile2;
    }


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
                if (validateEntityConsistency($xmlfile)&&preg_match($bef, $xmlfile)) {
                    $username = shell_exec($cmd);

                }
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