<?php 
/* 主页 */
include './lib.php';
if (isLogin() === false ) {
  jump('home.php', '请登录！');
}
$user = isLogin();
$Redis = connect();
/*$Redis->ltrim('recivepost:'.$user['userid'], 0,49);
$newpost = $Redis->sort('recivepost:'.$user['userid'], array('sort'=>'desc'));
//print_r($newpost);*/
/* 获取我关注的人 */
$star = $Redis->sMembers('following:'. $user['userid']);
$star[] = $user['userid'];

//print_r($star); exit();
$lastpull = $Redis->get('lastpull:userid:'.$user['userid']);
if (!$lastpull) {
   $lastpull = 0;
}

/* 拉取最新微博 */
$lastlist = array();
foreach ($star as $s) {
   $lastlist = array_merge($lastlist, $Redis->zrangebyscore('starpost:userid:'.$s, $lastpull+1, 1<<32-1));
}

sort($lastlist, SORT_NUMERIC);

if (!empty($lastlist)) {
   $Redis->set('lastpull:userid:'.$user['userid'], end($lastlist));
}

foreach ($lastlist as $l) {
	$Redis->lpush('recivepost:'. $user['userid'], $l);
}



/* 1000条微博数据 */
$Redis->ltrim('recivepost:'.$user['userid'], 0,999);
$newpost = $Redis->sort('recivepost:'.$user['userid'], array('sort'=>'desc'));

$datalist = array();
foreach ($newpost as $postid) {
  $datalist[] = $Redis->hmget('post:postid:'.$postid, array('userid','username','time','content'));
}

//print_r($datalist); exit();

$myfans = $Redis->sCard('follower:'. $user['userid']);
$mystart = $Redis->sCard('following:'. $user['userid']);
 ?>
<?php include 'head.php'; ?>
<div id="postform">
<form method="POST" action="post.php">
<?php echo $user['username']; ?>, 有啥感想?
<br>
<table>
<tr><td><textarea cols="70" rows="3" name="content"></textarea></td></tr>
<tr><td align="right"><input type="submit" name="doit" value="Update"></td></tr>
</table>
</form>
<div id="homeinfobox">
<?php echo $myfans; ?> 粉丝<br>
<?php echo $mystart; ?> 关注<br>
</div>
</div>
<?php foreach ($datalist as $data) { ?>
<div class="post">
<a class="username" href="profile.php?u=<?php echo $data['username']; ?>"><?php echo $data['username']; ?></a> <?php echo $data['content']; ?><br>
<i> <?php echo formatTime($data['time']); ?> 前 通过 web发布</i>
</div>
<?php }; ?>
<?php include './foot.php' ?>
