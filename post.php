<?php 
include '/usr/local/http2/htdocs/redis_weibo/lib.php';
if (isLogin() === false ) {
  jump('home.php', '请登录！');
}
$user = isLogin();

$Redis = connect();

$content = $_POST['content'];

if (empty($content)) {
  jump('home.php', '内容不能为空！');
}

/* 拉模型 pull*/
$postid = $Redis->incr('global:postid');
/*$Redis->set('post:postid:'.$postid.':userid', $user['userid']);
$Redis->set('post:postid:'.$postid.':time', time());
$Redis->set('post:postid:'.$postid.':content', $content);*/

$Redis->hmset('post:postid:'.$postid, array('userid'=>$user['userid'], 'username'=>$user['username'], 'time'=>time(),'content'=>$content));

/* 把自己发的微博维护一个有序集合里，只取最新的20条，供粉丝拉取 */
$Redis->zadd('starpost:userid:'. $user['userid'], $postid, $postid);
if ($Redis->zcard('starpost:userid:'. $user['userid']) > 20) {
    $Redis->zremrangebyrank('starpost:userid:'. $user['userid'], 0, 0);
}

/* 把自己发的微博维护一个链表里，1000条，自己查看 ， 1000个外的旧数据放MySQL */

$Redis->lpush('mypost:userid:'.$user['userid'], $postid);
if ($Redis->lLen('mypost:userid:'.$user['userid']) > 10) {
  $Redis->rpoplpush('mypost:userid:'.$user['userid'], 'global:store');
}



jump('home.php', '发布成功！');
?>
