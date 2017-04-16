<?php
include ('/usr/local/http2/htdocs/redis_weibo/lib.php');
$Redis = connect();

$sql = "insert into redis_weibo_content (postid,userid,username,time,content) values ";

$i = 0;
while ($Redis->llen('global:store') && $i++ < 1000) {
	$postid = $Redis->lpop('global:store');
	$post = $Redis->hmget('post:postid:'.$postid, array('userid','username','time','content'));
	$sql .= "($postid,".$post['userid'] .",'".$post['username']."','".$post['time']."','".$post['content']."'),";
}

if ($i == 0) {
  jump('home.php','没数据！');
}

$sql = substr($sql, 0,-1);
// echo $sql;

$con = mysql_connect('127.0.0.1', 'root','123456');
mysql_query('use mysql_test', $con);
mysql_query('set names utf8', $con);
mysql_query($sql, $con);

echo 'ok~~~~~~~~~~~~~';
?>
