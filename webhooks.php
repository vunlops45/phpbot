<?php 

require "vendor/autoload.php";
// include "admin/config.php";
require_once('vendor/linecorp/line-bot-sdk/line-bot-sdk-tiny/LINEBotTiny.php');

$access_token = "Yj5DQD1eT6DmR1/NScA39PtOfKYcxNntn+oPl1+HOWozB6FkvBRTWhTV3Dn7ABJQ7hOQ+bFrUWQ8hUU9KR6Dtov9o5UrrteKJx3uwnyVerfXC5BXUcX4ax1E/s3Ctde4SKzQIhca8Z/0HPHU6/cO9wdB04t89/1O/w1cDnyilFU=";

$content = file_get_contents('php://input');
$events = json_decode($content, true);


if (!is_null($events['events'])) {
	foreach ($events['events'] as $event) {
	
		if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
			
			error_log($event['message']['text']);
			$text = $event['message']['text'];
			$replyToken = $event['replyToken'];
			## เปิดสำหรับใช้่งาน mysql message
			// $text = searchMessage($text ,$conn);
			// $messages = setText($text);
			$messages = setFlex();
			sentToLine( $replyToken , $access_token  , $messages );
		}
	}
}


function setText( $text){
	$messages = [
		'type' => 'text',
		'text' => $text
	];
	return $messages;
}

function setFlex(){
	$message = '{
		"type": "flex",
		"altText": "Flex Message",
		"contents": {
		  "type": "bubble",
		  "direction": "ltr",
		  "header": {
			"type": "box",
			"layout": "vertical",
			"contents": [
			  {
				"type": "text",
				"text": "Header",
				"align": "center"
			  }
			]
		  },
		  "hero": {
			"type": "image",
			"url": "https://developers.line.biz/assets/images/services/bot-designer-icon.png",
			"size": "full",
			"aspectRatio": "1.51:1",
			"aspectMode": "fit"
		  },
		  "body": {
			"type": "box",
			"layout": "vertical",
			"contents": [
			  {
				"type": "text",
				"text": "Body",
				"align": "center"
			  }
			]
		  }
		}
	  }';
	return $message;
}

function searchMessage($text , $conn){
	$sql = "SELECT * FROM data where keyword='".$text."' ";
	$result = $conn->query($sql);
	
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$message = $row['intent'];
		}
	} else {
		$message = "ไม่เข้าใจอ่ะ";
	}
	$conn->close();
	return $message;
}

function sentToLine($replyToken , $access_token  , $messages ){
	error_log("send");
	$url = 'https://api.line.me/v2/bot/message/reply';
	
	$data = '{
		"replyToken" : "'. $replyToken .'" ,
		"messages" : ['. $messages .']
	}';
	$post = $data;

	error_log($post);
	$headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$result = curl_exec($ch);
	curl_close($ch);
	echo $result . "\r\n";
	error_log($result);
	error_log("send ok");
}


