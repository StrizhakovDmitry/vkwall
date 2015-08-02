<?php
header('content-type: application/json; charset=utf-8');

$a = array (
"access_token"=>"535eac497d30a6c4fdb9fe736845b4bcc56e0d68a9c5175a1e6371159c009d1d7b472fd0129c72137ce01",
"expires_in"=>86400,
"user_id"=>94423672
);
$b = json_encode($a);
$c='{"access_token":"3622a473c6db85174dc49e31a90df80fbb314b2b3664036d4b42c6ec0c6d2abd5400b1f486e7f256eb02c"
,"expires_in":86396,"user_id":94423672}';
echo "alert(123)";














?>