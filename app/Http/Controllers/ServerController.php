<?php
/**
 * Created by PhpStorm.
 * User: SKings
 * Date: 6/30/2016
 * Time: 2:04 PM
 */

namespace App\Http\Controllers;

use Auth;
use DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use PDO;

require_once 'config.php';
require_once 'function.php';

class ServerController extends Controller
{

    function getAjax($get){
        //echo $get;
        $query = $get;
        $getParams = array();
        foreach (explode('&', $query) as $chunk) {
            $param = explode("=", $chunk);
            if ($param) {
                //printf("Value for parameter \"%s\" is \"%s\"<br/>\n", urldecode($param[0]), urldecode($param[1]));
                $getParams[urldecode($param[0])] = urldecode($param[1]);
            }
        }


        if(isset($getParams['type'])){
            switch ($getParams['type']){
                case "refresh":
                    if(isset($getParams['nom'])) {
                        if(isset($getParams['state'])){
                            if($getParams['state']=='sent'){
                                if(isset($getParams['sortby'])){
                                    return $this->refresh($getParams['nom'], 'sent',$getParams['sortby']);
                                }else {
                                    return $this->refresh($getParams['nom'], 'sent');
                                }
                            }else{
                                if(isset($getParams['sortby'])){
                                    return $this->refresh($getParams['nom'],'inbox',$getParams['sortby']);
                                }else {
                                    return $this->refresh($getParams['nom'],'inbox');
                                }
                            }
                        }else{
                            if(isset($getParams['sortby'])){
                                return $this->refresh($getParams['nom'],'inbox',$getParams['sortby']);
                            }else {
                                return $this->refresh($getParams['nom'], 'inbox');
                            }
                        }
                    }
                    break;

                case "profile":
                    return $this->profile();
                    break;
                case "users":
                    return $this->usersPage();
                    break;
                case 'addContact':
                    if(isset($getParams['email'])){
                        return $this->addToContact($getParams);
                    }
                    break;
                case 'notif':
                    return $this->notif();
                    break;
                case 'notifications':
                    return $this->notificationPage();
                    break;
                case 'readMail':
                    //echo 'hi';
                    //echo $this->readEmail($getParams);
                    return $this->readEmail($getParams);

                    break;
                case 'deletemail':
                    //return 'salam';
                    return $this->deleteMail($getParams);
                    break;
            }
        }
    }
    function deleteMail($params){
        DB::table('messages')->where('messages_id','=',$params['id'])->delete();
        return '/inbox';
        //return Redirect::to('inbox');
    }
    function readEmail($params){
        $data=messageRecord($params['id']);

        $db = getDB();
        $stat="read";
        $messages_id=$params['id'];
        $stmt = $db->prepare("UPDATE messages SET stat = :stat WHERE messages_id = :messages_id");

        $stmt->bindParam("messages_id", $messages_id,PDO::PARAM_STR) ;
        $stmt->bindParam("stat", $stat,PDO::PARAM_STR) ;
        $stmt->execute();

        //$temp='<mails>';
        $temp='<mail >
				<from>'.flname($data->sender_id).'</from>
					<to>'.flname($data->receiver_id).'</to>
					<subject>'.$data->title.'</subject>
					<date>'.$data->date.'</date>
					<stat>'.$data->stat.'</stat>
					<id>'.$data->messages_id.'</id>
					<text>
					'.$data->body.'
					</text>
					<attachments>
						<attach>'.$data->attachment.'</attach>
					</attachments>
			</mail>';
        //print_r($data);
        //echo $temp;
        return $temp;
    }
    function notif(){
        $user = Auth::user();
        $ret_val = '<?xml version="1.0" encoding="utf-8"?>';
        $ret_val .= '<num>';
        $notseens = new_friend_request_list($user['id']);
        $ret_val .= count($notseens);
        $ret_val .= '</num>';
        return $ret_val;
    }

    function notificationPage(){
        $user = Auth::user();
        $user_list=new_friend_request_list($user['id']);

        $temp="<users>";
        foreach ($user_list as $idx=>$val){
            $user1=returnField("users","email",$val['email'],"id");
            $stat=friend_status($user1,$user['id']);
            $temp.="
			<user>
				<img></img>
				<first> ".$val['firstname']."</first>
				<last>".$val['lastname']."</last>
				<username>".$val['email']."</username>
				<img>".$val['profile_pic']."</img>
				<stat>".$stat."</stat>
			</user>";
        }

        $temp.="</users>";
        not_seen_to_seen($user['id']);
        echo $temp;
    }

    function refresh($limit,$kind,$sort='date'){
        $user = Auth::user();
        $ret_val = '<?xml version="1.0" encoding="utf-8"?>';
        $inbox = inbox_list($user['id'],$limit,$sort);
        if($kind == "sent"){
             $inbox = sent_list($user['id'],$limit,$sort);
        }
        $ret_val .= '<mails>';

        foreach ($inbox as $idx=>$val){
            //print_r($val);
            $stat = $val['stat'];
            if($stat == 'not seen'){
                $ret_val .= '<mail>';
            }else if($stat == 'read'){
                $ret_val .= '<mail read="true">';
            }else {
                $ret_val .= '<mail spam="true">';
            }

            if($kind == "sent"){
                $ret_val .= '<to>'.flname($val["receiver_id"]).'</to>';
            }else{
                $ret_val .= '<from>'.flname($val["sender_id"]).'</from>';
            }
            $ret_val .= '<id>'.$val['messages_id'].'</id>';
            $ret_val .= '<subject>'.$val["title"].'</subject>';
            $ret_val .= '<text>';
            if(strlen($val["body"]) > 10){
                $ret_val .= substr($val["body"],0,10).'...';
            }else{
                $ret_val .= $val["body"];
            }
            $ret_val .= '</text>';
            $ret_val .= '<date>'.$val["date"].'</date>';
            $ret_val .= '<attachments>';
            $ret_val .= '<attach></attach>';
            $ret_val .= '</attachments>';
            $ret_val .= '</mail>';
        }
        $ret_val .= '</mails>';
        return $ret_val;
    }

    function profile(){
        $user = Auth::user();
        //print_r( $user['firstname']);
        /*$ret_val = '<?xml version="1.0" encoding="utf-8"?>';*/
        $ret_val ='';
        $ret_val .= '<data>';
        $ret_val .= '<first>'.$user['firstname'].'</first>';
        $ret_val .= '<last>'.$user['lastname'].'</last>';
        $ret_val .= '<username>'.$user['email'].'</username>';
        $ret_val .= '<img>'.$user['profile_pic'].'</img>';
        $ret_val .= '<login>'.$user['updated_at'].'</login>';

        $friend=friend_list($user['id']);
        if(count($friend) > 0){
            $ret_val .= "<contacts>";
        }
        foreach($friend as $val){
            $ret_val.="<contact>
						<img>".$val['profile_pic']."</img>
						<first>".$val['firstname']."</first>
						<last>".$val['lastname']."</last>
						<username>".$val['email']."</username>
				</contact>";
        }
        if(count($friend) > 0){
            $ret_val .= "</contacts>";
        }
        $ret_val .= '</data>';
       // echo $ret_val;
        return $ret_val;
    }

    function usersPage(){
        $user = Auth::user();
        $user_list=user_list($user['id']);

        $temp="<users>";
        foreach ($user_list as $idx=>$val){
            $user1=returnField("users","email",$val['email'],"id");
            $stat=friend_status($user1,$user['id']);
            $temp.="
			<user>
				<first> ".$val['firstname']."</first>
				<last>".$val['lastname']."</last>
				<username>".$val['email']."</username>
				<img>".$val['profile_pic']."</img>
				<stat>".$stat."</stat>
			</user>";
        }

        $temp.="</users>";

        return $temp;
    }


    function addToContact($getParams){
        $user = Auth::user();

        $email=(isset($getParams['email']) && $getParams['email']!='')? $getParams['email']:"";
        $receiver_id=returnField("users","email",$email,"id");
        //$mailClass = new mailClass();
        $sender_id=$user['id'];
        return $this->addContact($sender_id,$receiver_id);
        //return $this->test();
    }


    /** mailclass */
    function addContact($request_id,$response_id){
        try{
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO friends(request_id,response_id) VALUES (:request_id,:response_id)");
            $stmt->bindParam("request_id", $request_id,PDO::PARAM_STR) ;
            $stmt->bindParam("response_id", $response_id,PDO::PARAM_STR) ;
            $stmt->execute();
            $id=$db->lastInsertId(); // Last inserted row id
            $db = null;
            return 1;

        }
        catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    function sendMail($sender_id,$receiver_id,$title,$body,$attachment=""){
        try{
            $db = getDB();

            /***********************************************/
            $stmt = $db->prepare("SELECT id FROM users WHERE email=:receiver_id");
            $stmt->bindParam("receiver_id", $receiver_id,PDO::PARAM_STR) ;
            $stmt->execute();
            $count=$stmt->rowCount();
            /************************************************************************/
            if($count>0){
                $receiver_id=returnField("users","email",$receiver_id,"id");

                $stmt = $db->prepare("SELECT stat FROM friends WHERE (request_id=:sender_id AND response_id=:receiver_id) OR (response_id=:sender_id AND request_id=:receiver_id)");
                $stmt->bindParam("sender_id", $sender_id,PDO::PARAM_STR) ;
                $stmt->bindParam("receiver_id", $receiver_id,PDO::PARAM_STR) ;
                $stmt->execute();
                $countFriend=$stmt->rowCount();

                if($countFriend>0){
                    $stmt = $db->prepare("INSERT INTO messages(sender_id,receiver_id,title,body,attachment) VALUES (:sender_id,:receiver_id,:title,:body,:attachment)");
                    $stmt->bindParam("sender_id", $sender_id,PDO::PARAM_STR) ;
                    $stmt->bindParam("receiver_id", $receiver_id,PDO::PARAM_STR) ;
                    $stmt->bindParam("title", $title,PDO::PARAM_STR) ;
                    $stmt->bindParam("body", $body,PDO::PARAM_STR) ;
                    $stmt->bindParam("attachment", $attachment,PDO::PARAM_STR) ;
                    $stmt->execute();
                    $id=$db->lastInsertId(); // Last inserted row id
                    $response =1;
                    $target_dir="attachment/";
                    if($attachment!=""){
                        $target_file = $target_dir .$attachment;
                        if (!file_exists("attachment")) {
                            mkdir('attachment', 0777, true);
                        }
                        if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) {
                        }

                    }
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

    /** */

    function postAjax(){
        $curUser = Auth::user();
        $ajaxParams = Request::all();
        if(isset($ajaxParams['type'])){
            if($ajaxParams['type']=="profile") {
                return $this->editProfile($curUser['id'], $ajaxParams);
            }elseif($ajaxParams['type']=="compose"){
                //return $ajaxParams;
                $state = $this->sendThatMail($curUser,$ajaxParams);
                if($state == 0 ){
                    return Redirect::to('compose')->withErrors(['Not Contact', 'Email Exists!']);
                }elseif($state == 1){
                    return Redirect::to('inbox');
                }else{
                    return Redirect::to('compose')->withErrors(['Not Exists', 'Email Exists!']);
                }
            }
        }

        return Request::all();
    }

    function sendThatMail($user,$ajaxParams){
 //       require_once 'classes/mailClass.php';
 //       $mailClass = new mailClass();
        $sender_id=$ajaxParams['sender_id'];
        $receiver_id=$ajaxParams['receiver_id'];
        $title=$ajaxParams['title'];
        $body=$ajaxParams['body'];
        //$attachment =  (isset(2))?basename($_FILES["attachment"]["name"]):"";
        if(basename($_FILES["attachment"]["name"])!=""){
            $attachment = basename($_FILES["attachment"]["name"]);
        }
        else{
            $attachment = Null;
        }
        $x=$this->sendMail($sender_id,$receiver_id,$title,$body,$attachment);
        return $x;
    }

    function editProfile($id,$ajaxParams){


        $emails = DB::table('users')->select('email')->where('id','<>',$id)->get();

        foreach($emails as $email){
            if( $email->email == $ajaxParams['email']){
                return Redirect::to('profile')->withErrors(['Email Exists!', 'Email Exists!']);
            }
        }
        if(isset($ajaxParams['avatar'])) {
            $target_file = "images/".$_FILES['avatar']["name"];
            $save_dir =  "images/".time().$_FILES['avatar']["name"];
            if (!file_exists("images")) {
                mkdir('images', 0777, true);
            }
            if (move_uploaded_file($_FILES['avatar']["tmp_name"], $save_dir)) {
            }

            DB::table('users')
                ->where('id', $id)
                ->update(['email' => $ajaxParams['email'],
                    'firstname' => $ajaxParams['firstname'], 'lastname'=>$ajaxParams['lastname'],
                    'password' => bcrypt($ajaxParams['password']), 'profile_pic'=>$save_dir]);
        }else{
            DB::table('users')
                ->where('id', $id)
                ->update(['email' => $ajaxParams['email'],
                    'firstname' => $ajaxParams['firstname'], 'lastname'=>$ajaxParams['lastname'],
                    'password' => bcrypt($ajaxParams['password'])]);
        }
        return Redirect::to('profile');
    }

}