<?php
use PDO;
class mailClass{
/* send mail */

public function sendMail($sender_id,$receiver_id,$title,$body){
	try{
		$db = getDB();
		
		/***********************************************/
		$stmt = $db->prepare("SELECT id FROM users WHERE email=:sender_id"); 
		$stmt->bindParam("sender_id", $sender_id,PDO::PARAM_STR) ;
		$stmt->execute();
		$count=$stmt->rowCount();
		/************************************************************************/
		if($count>0){
			$sender_id=returnField("users","email",$sender_id,"id");
			
			$stmt = $db->prepare("SELECT stat FROM friends WHERE (request_id=:sender_id AND response_id=:receiver_id) OR (response_id=:sender_id AND request_id=:receiver_id)"); 
			$stmt->bindParam("sender_id", $sender_id,PDO::PARAM_STR) ;
			$stmt->bindParam("receiver_id", $receiver_id,PDO::PARAM_STR) ;
			$stmt->execute();
			$countFriend=$stmt->rowCount();
			
			if($countFriend>0){
				$stmt = $db->prepare("INSERT INTO messages(sender_id,receiver_id,title,body) VALUES (:sender_id,:receiver_id,:title,:body)");
				$stmt->bindParam("sender_id", $sender_id,PDO::PARAM_STR) ;
				$stmt->bindParam("receiver_id", $receiver_id,PDO::PARAM_STR) ;
				$stmt->bindParam("title", $title,PDO::PARAM_STR) ;
				$stmt->bindParam("body", $body,PDO::PARAM_STR) ;
				$stmt->execute();
				$id=$db->lastInsertId(); // Last inserted row id
				$response =1;
				/*$target_dir="images/";
				if($profile_pic!=""){
					$target_file = $target_dir . $profile_pic;
					if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
					}
				}*/
			}
			else{
				$response=0;
			}
		}
		else{
			$response=-1;
		}
		
		$db = null;
		return $response;
	
	} 
	catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}
/*****************************************************************************************************************/
public function addContact($request_id,$response_id){
	try{
		$db = getDB();			
		$stmt = $db->prepare("INSERT INTO friends(request_id,response_id) VALUES (:request_id,:response_id)");
		$stmt->bindParam("request_id", $request_id,PDO::PARAM_STR) ;
		$stmt->bindParam("response_id", $response_id,PDO::PARAM_STR) ;
		$stmt->execute();
		$id=$db->lastInsertId(); // Last inserted row id
		$db = null;
		return true;
	
	} 
	catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

/*****************************************************************************************/
}
?>