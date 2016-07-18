<?php
class userClass{
/* User Login */
public function userLogin($email,$password){
	try{
	$db = getDB();
	$hash_password= hash('sha256', $password); //Password encryption 
	$stmt = $db->prepare("SELECT id FROM users WHERE (email=:email ) AND password=:hash_password"); 
	$stmt->bindParam("email", $email,PDO::PARAM_STR) ;
	$stmt->bindParam("hash_password", $hash_password,PDO::PARAM_STR) ;
	$stmt->execute();
	$count=$stmt->rowCount();
	$data=$stmt->fetch(PDO::FETCH_OBJ);
	$db = null;
	if($count){
		$_SESSION['id']=$data->id; // Storing user session value
		return 1;
	}
	else{
		return 2;
	} 
	}
	catch(PDOException $e) {
	echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

/* User Registration */
public function userRegistration($firstname,$password,$email,$lastname,$profile_pic=""){
	try{
	$db = getDB();
	$st = $db->prepare("SELECT id FROM users WHERE email=:email"); 
	$st->bindParam("email", $email,PDO::PARAM_STR);
	$st->execute();
	$count=$st->rowCount();
	if($count<1){
		$stmt = $db->prepare("INSERT INTO users(firstname,password,email,lastname,profile_pic) VALUES (:firstname,:hash_password,:email,:lastname,:profile_pic)");
		$stmt->bindParam("firstname", $firstname,PDO::PARAM_STR) ;
		$hash_password= hash('sha256', $password); //Password encryption
		$stmt->bindParam("hash_password", $hash_password,PDO::PARAM_STR) ;
		$stmt->bindParam("email", $email,PDO::PARAM_STR) ;
		$stmt->bindParam("lastname", $lastname,PDO::PARAM_STR) ;
		$stmt->bindParam("profile_pic", $profile_pic,PDO::PARAM_STR) ;
		$stmt->execute();
		$id=$db->lastInsertId(); // Last inserted row id
		
		$target_dir="images/";
		if($profile_pic!=""){
			$target_file = $target_dir . $profile_pic;
			if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
			}
		}
		
		$db = null;
		$_SESSION['id']=$id;
		return 1;
	}
	else{
		$db = null;
		return 2;
	}
	
	} 
	catch(PDOException $e) {
	echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}
/* User Registration */
public function userEdit($id,$firstname,$lastname,$email,$password,$profile_pic=""){
	try{
		$db = getDB();
		
		$st = $db->prepare("SELECT id FROM users WHERE email=:email AND id<>:id"); 
		$st->bindParam("email", $email,PDO::PARAM_STR);
		$st->bindParam("id", $id,PDO::PARAM_STR);
		$st->execute();
		$count=$st->rowCount();
		if($count<1){
			$stmt = $db->prepare("UPDATE users SET firstname = :firstname,lastname = :lastname,email = :email,password = :hash_password,profile_pic = :profile_pic 
				WHERE id = :id");
			$stmt->bindParam("id", $id,PDO::PARAM_STR) ;
			$stmt->bindParam("firstname", $firstname,PDO::PARAM_STR) ;
			$stmt->bindParam("lastname", $lastname,PDO::PARAM_STR) ;
			$stmt->bindParam("email", $email,PDO::PARAM_STR) ;
			$hash_password= hash('sha256', $password); //Password encryption
			$stmt->bindParam("hash_password", $hash_password,PDO::PARAM_STR) ;
			$stmt->bindParam("profile_pic", $profile_pic,PDO::PARAM_STR) ;
			$stmt->execute();
			$id=$db->lastInsertId(); // Last inserted row id
			
			$target_dir="images/";
			if($profile_pic!=""){
				$target_file = $target_dir . $profile_pic;
				if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
				}
			}
			$db = null;
			return 1;
		}
		else{
			$db = null;
			return 2;
		}
		
		
	} 
	catch(PDOException $e) {
	echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

/* User Details */
public function userDetails($id)
{
try{
$db = getDB();
$stmt = $db->prepare("SELECT email,username,name,google_auth_code FROM users WHERE id=:id");
$stmt->bindParam("id", $id,PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_OBJ); //User data
return $data;
}
catch(PDOException $e) {
echo '{"error":{"text":'. $e->getMessage() .'}}';
}
}
}
?>