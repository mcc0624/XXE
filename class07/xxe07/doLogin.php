<?php

error_reporting(0);
$USERNAME = 'admin'; //账号
$PASSWORD = 'admin'; //密码
$result = null;

libxml_disable_entity_loader(false);
$xmlfile = file_get_contents('php://input');
$preg='/<!DOCYPT/i';
$xmlfile = file_get_contents('php://input');
if(!preg_match_all($preg,$xmlfile,$res)) {
    try {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xmlfile, LIBXML_NOENT | LIBXML_DTDLOAD);
        $creds = simplexml_import_dom($dom);
        $dom->xinclude();

        $username = $creds->username;
        $password = $creds->password;
        if (preg_match('/<username>/i', $dom->saveXML(), $res)) {
            if ($username == $USERNAME && $password == $PASSWORD) {
                $result = sprintf("<result><code>%d</code><msg>%s</msg></result>", 1, $username);
            } else {
                $result = sprintf("<result><code>%d</code><msg>%s</msg></result>", 0, $username);
            }
        } else {
            echo $dom->saveXML();
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