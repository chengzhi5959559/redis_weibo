<?php
/**
 * 公共函数
 */
//error_reporting('E_ALL ~ notice');
header("Content-type: text/html; charset=utf-8"); 
function connect() {
   static $Redis = null;
   if (is_null($Redis)) {
      $Redis = new Redis();
      $Redis->connect('127.0.0.1', '6379');
      return $Redis;
   }
   
   return $Redis;
}

function jump($url, $message) {
   echo '<script>
        alert("'.$message.'");
        window.location.href="'.$url.'";
       </script>';

}

function isLogin () {
  if (!$_COOKIE['userid'] || !$_COOKIE['username']) {
     return false;
  }
  
  return array('userid'=>$_COOKIE['userid'], 'username'=>$_COOKIE['username']);

}

/* 格式化时间函数*/

function formatTime($timestamp) {
   $sec = time() - $timestamp;
   if ($sec >= 86400) {
     return floor($sec/86400) . '天';
   }else if ($sec >= 3600) {
   	 return floor($sec/3600) . '时';
   }else if ($sec >= 60) {
     return floor($sec/60) . '分';
   }else {
     return $sec . '秒';
   }

}


?>