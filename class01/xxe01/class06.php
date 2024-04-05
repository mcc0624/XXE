<?php

error_reporting(0);
libxml_disable_entity_loader(false);
$xml = file_get_contents('php://input');
if(isset($xml)){
    $dom = new DOMDocument();
    $dom->loadXML($xml, LIBXML_NOENT | LIBXML_DTDLOAD);
    $creds = simplexml_import_dom($dom);
    $benben = $creds->admin;
    echo admin;
}
highlight_file(__FILE__);
?>