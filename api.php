<?php
if(isset($_GET['sid'])){
	session_id($_GET['sid']);
}
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

$access_token = 'EAAEshl2a5DQBAMq02KP4CKmneFZBkzZCuqVixluZCfy88039CstgRsaXzPcvsKoA1ZAnPOCwY8yhDi3QLucbiUakACnjiHnjmb1fFMihGCyKmwbo9tHawp5JvuHzXRZAz63dIERmUMZCh3GVNNUkealK3EO2S8E8lST5zdU3hsPUmDJUT2xoI45FwFSr0EWgYZD';
$page_id='mytestpageb';
$pageFeed = 'https://graph.facebook.com/'.$page_id.'/posts?fields=message,created_time,full_picture,from&pretty=1&limit=4&date_format=U&access_token='.$access_token;

//Make GET request
function getUrl($url){
	$getFeed = curl_init($url);
	curl_setopt($getFeed, CURLOPT_RETURNTRANSFER, 1);
	$server_output = curl_exec ($getFeed);
	if($server_output === false){
		header("HTTP/1.1 500 Internal Server Error");
		die('{"error": true, "code":5000}');
	}
	$r = json_decode($server_output);
	//Check if there is an error object from Facebook API
	if($r->error){
		header("HTTP/1.1 500 Internal Server Error");
		die('{"error": true, "code":5001}');
	}
	return $r;
}

//Make response
/*
Return:
	data = Post objects
	next = Boolean, True: there are more posts, False: there are no more posts.
	sid = Session ID, used for load more function.
*/
function makeResponse($serverRes){
	$next = false;
	if (isset($serverRes->paging->next)){
		$next=true;
		$_SESSION["nextUrl"] = $serverRes->paging->next;
	}
	$_SESSION["next"] = $next;
	return array('data' => $serverRes->data, 'next' => $next, 'sid' => session_id());
}

//Check if paramert next is set and its value = true
if(isset($_GET["next"]) && $_GET["next"] == 'true'){
	//check if Session var next is set, if its set fetch posts from nextUrl.
	if($_SESSION["next"]){
			echo json_encode (makeResponse(getUrl($_SESSION["nextUrl"])), JSON_FORCE_OBJECT | JSON_UNESCAPED_SLASHES);	
	}
	else {
		header("HTTP/1.1 500 Internal Server Error");
		echo'{"error": true, "code":5002}';			
	}	
}
else {
	echo json_encode (makeResponse(getUrl($pageFeed)), JSON_FORCE_OBJECT | JSON_UNESCAPED_SLASHES);
}

?>