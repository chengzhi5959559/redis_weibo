<?php
include './lib.php';
if (isLogin() !== false) {
  jump('home.php', '您已登录！');
}

$Redis = connect();

$username = $_POST['username'];
$password = $_POST['password'];
$password2 = $_POST['password2'];

if (empty($username) || empty($password) || empty($password2)) {
   jump('index.php', '请输入完整注册信息！');

}

if ($password != $password2) {
   jump('index.php', '两次密码输入不一致！');
}

if ($Redis->get('user:username:'.$username.'userid')) {
   jump('index.php', '该用户已存在！');
}

$userid = $Redis->incr('global:userid');

$Redis->set('user:userid:'.$userid.':username', $username);
$Redis->set('user:userid:'.$userid.':password', $password);
$Redis->set('user:username:'.$username.':userid', $userid);

/* 维护一个链表 ，保存最新的50个用户 */
$Redis->lpush('newuserlink', $userid);
$Redis->ltrim('newuserlink', 0, 49);
/* 增加登录标识 */
setcookie('userid', $userid);
setcookie('username', $username);

jump('index.php', '恭喜你，注册成功！');
 ?>