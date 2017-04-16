<?php 
/* 关注操作 */
include './lib.php';
if (isLogin() === false ) {
  jump('home.php', '请登录！');
}
$user = isLogin();
$Redis = connect();

$uid = $_GET['uid'];
$f = $_GET['f'];
$uname = $Redis->get('user:userid:'.$uid.':username');
$url = 'profile.php?u='.$uname;
if ($user['userid'] == $uid) {
	
    jump($url, '不能关注自己！');
}

if ($f) {
  $Redis->sadd('following:'.$user['userid'], $uid);
  $Redis->sadd('follower:'.$uid, $user['userid']);
  jump($url, '关注成功！');
} else {
  $Redis->srem('following:'.$user['userid'], $uid);
  $Redis->srem('follower:'.$uid, $user['userid']);
  jump($url, '取消关注成功！');
}

 ?>