<?php
	function userRecord($id){
		$data=array();
		$db = getDB();
		$st = $db->prepare("SELECT firstname,lastname FROM users WHERE id=:id");
		$st->bindParam("id", $id,PDO::FETCH_NUM);
		$st->execute();
		$count=$st->rowCount();
		if($count>=0){
			$data=$st->fetch(PDO::FETCH_OBJ);
		}
		$db = null;
		return $data;
	}
	function inbox_list($receiver_id,$count,$sort){
		$data=array();
		$db = getDB();
		$st = $db->prepare("SELECT * FROM messages WHERE receiver_id=:receiver_id  ORDER  BY date desc  LIMIT ".$count);
		if($sort == "sender"){
			$st = $db->prepare("SELECT * FROM messages as M,USERS as U WHERE M.receiver_id=:receiver_id and M.sender_id = U.id
 								ORDER  BY U.firstname asc  LIMIT ".$count);
		}elseif($sort == "attach"){
			//not test yet
            $st = $db->prepare("SELECT * FROM messages WHERE receiver_id=:receiver_id  ORDER  BY attachment desc  LIMIT ".$count);
		}
		$st->bindParam("receiver_id", $receiver_id,PDO::PARAM_STR);
		$st->execute();
		$counter=$st->rowCount();
		
		if($counter>=0){
			$data=$st->fetchall(PDO::FETCH_ASSOC);
		}
		$db = null;

		return $data;

		
	}

	function sent_list($sender_id,$count,$sort){
		$data=array();
		$db = getDB();
		$st = $db->prepare("SELECT * FROM messages WHERE sender_id=:sender_id  ORDER  BY date desc  LIMIT ".$count);
		if($sort == "sender"){
			$st = $db->prepare("SELECT * FROM messages as M,USERS as U WHERE M.sender_id=:sender_id and M.sender_id = U.id
 								ORDER  BY U.firstname asc  LIMIT ".$count);
		}elseif($sort == "attach"){
            //not test yet
            $st = $db->prepare("SELECT * FROM messages WHERE sender_id=:sender_id  ORDER  BY attachment desc  LIMIT ".$count);
		}
		$st->bindParam("sender_id", $sender_id,PDO::PARAM_STR);
		$st->execute();
		$counter=$st->rowCount();

		if($counter>=0){
			$data=$st->fetchall(PDO::FETCH_ASSOC);
		}
		$db = null;
		return $data;

	}
	function flname($id){
		$db = getDB();
		$st = $db->prepare("SELECT firstname,lastname FROM users WHERE id=:id");
		$st->bindParam("id", $id,PDO::FETCH_NUM);
		$st->execute();
		$count=$st->rowCount();
		if($count>=0){
			$data=$st->fetch(PDO::FETCH_ASSOC);
		}
		$db = null;
		return $data["firstname"]. " " . $data["lastname"];
	}

	/*******/
	function returnField($table,$fieldname,$field,$value){
		$db = getDB();
		$st = $db->prepare("SELECT * FROM ".$table." WHERE ".$fieldname."=:".$fieldname."");
		$st->bindParam($fieldname, $field,PDO::FETCH_NUM);
		$st->execute();
		$count=$st->rowCount();
		if($count>=0){
			$data=$st->fetch(PDO::FETCH_ASSOC);
		}
		$db = null;
		return $data[$value];
	}

	function user_list($id){
		$data=array();
		$db = getDB();
		$st = $db->prepare("SELECT * FROM users WHERE  id<>".$id." ");
		//$st->bindParam("receiver_id", $receiver_id,PDO::PARAM_STR);
		$st->execute();
		$counter=$st->rowCount();

		if($counter>=0){
			$data=$st->fetchall(PDO::FETCH_ASSOC);
		}
		$db = null;
		return $data;

	}
	function friend_list($id){
		$data=array();
		$db = getDB();
		$st = $db->prepare("SELECT users.* FROM users,friends WHERE  users.id<>".$id." AND  (users.id=friends.request_id OR users.id=friends.response_id) AND (friends.response_id=".$id." OR friends.request_id=".$id.")  ");
		$st->bindParam("id", $id,PDO::PARAM_STR);
		$st->execute();
		$counter=$st->rowCount();

		if($counter>=0){
			$data=$st->fetchall(PDO::FETCH_ASSOC);
		}
		$db = null;
		return $data;

	}

	function new_friend_request_list($id){
		$data=array();
		$db = getDB();
		$st = $db->prepare("SELECT users.* FROM users,friends WHERE  users.id<>".$id." AND  (users.id=friends.request_id) AND friends.response_id=".$id." AND friends.seen='no' ");
		//$st->bindParam("receiver_id", $receiver_id,PDO::PARAM_STR);
		$st->execute();
		$counter=$st->rowCount();

		if($counter>=0){
			$data=$st->fetchall(PDO::FETCH_ASSOC);
		}
		$db = null;
		return $data;

	}
	function friend_status($user1,$user2){
		$count=0;
		$db = getDB();
		$st = $db->prepare("SELECT * FROM friends WHERE (request_id=:user1 AND response_id=:user2) OR (response_id=:user1 AND request_id=:user2)");
		$st->bindParam("user1", $user1,PDO::PARAM_STR) ;
		$st->bindParam("user2", $user2,PDO::PARAM_STR) ;
		$st->execute();
		$count=$st->rowCount();
		if($count>=0){
			$data=$st->fetch(PDO::FETCH_ASSOC);
		}
		$db = null;
		//return $data["stat"];
		return $count;
	}
	function not_seen_to_seen($response_id)
	{
		try {
			$db = getDB();
			$stmt = $db->prepare("UPDATE friends SET seen = :seen
				WHERE response_id = :response_id");
			$seen = "yes";
			$stmt->bindParam("seen", $seen, PDO::PARAM_STR);
			$stmt->bindParam("response_id", $response_id, PDO::PARAM_STR);
			$stmt->execute();
			$id = $db->lastInsertId(); // Last inserted row id
			$db = null;
			return true;

		} catch (PDOException $e) {
			echo '{"error":{"text":' . $e->getMessage() . '}}';
		}
	}

    function messageRecord($messages_id){
        $data=array();
        $db = getDB();
        $st = $db->prepare("SELECT * FROM messages WHERE messages_id=:messages_id");
        $st->bindParam("messages_id", $messages_id,PDO::FETCH_NUM);
        $st->execute();
        $count=$st->rowCount();
        if($count>=0){
            $data=$st->fetch(PDO::FETCH_OBJ);
        }
        $db = null;
        return $data;
    }
?>