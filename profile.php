<?php 
/* 个人中心 */
include './lib.php';
if (isLogin() === false ) {
  jump('home.php', '请登录！');
}
$user = isLogin();

$Redis = connect();

$uname = $_GET['u'];

if (!isset($uname)) {
  jump('timeline.php', '参数错误！');
}

$prouid = $Redis->get('user:username:'.$uname.':userid');
if (!$prouid) {
	jump('timeline.php', '非法用户！');
}
$isf = $Redis->sismember('following:'.$user['userid'], $prouid);
$isfStatus = $isf ? '0' : '1' ;
$isword = $isf ? '取消关注' : '关注他' ;


?>



<?php include 'head.php'; ?>
<h2 class="username"><?php echo $uname; ?></h2>
<a href="follow.php?uid=<?php echo $prouid; ?>&f=<?php echo $isfStatus ?>" class="button"><?php echo $isword; ?></a>

<div class="post">
<a class="username" href="profile.php?u=test">test</a> 
world<br>
<i>11 分钟前 通过 web发布</i>
</div>

<div class="post">
<a class="username" href="profile.php?u=test">test</a>
hello<br>
<i>22 分钟前 通过 web发布</i>
</div>

<?php include 'foot.php'; ?>
