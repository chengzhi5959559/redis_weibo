<?php 
include 'lib.php';
if (isLogin() !== false) {
  jump('home.php', '您已登录！');
}

$Redis = connect();
$username = $_POST['username'];
$password = $_POST['password'];

if (empty($username) || empty($password)) {
	jump('index.php', '请输入完整登录信息！');

}

$userid = $Redis->get('user:username:'.$username.':userid');

$realPass = $Redis->get('user:userid:'.$userid.':password');

if (!$userid) {
  jump('index.php', '用户不存在！');
}

if ($realPass != $password) {
    jump('index.php', '密码不正确！');
 }

setcookie('userid', $userid);
setcookie('username', $username);
jump('home.php', '登录成功！');
 ?>
