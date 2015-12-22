<?php

/*
 * includes/site.class.php
 * 
 * web interface class
 */

class SmppiSite {

	function __construct() {
		global $db;
		
		$this->db = $db;

	}
	
	var $limit = 25;
	
	function getsms($direction=0,$page=1){
		
		/*
		 * get sms from db
		 * 
		 * array
		 */
		
		if($page<1) $page=1;
		$start = ($page-1)*$this->limit;
		
		$select = "select id, phonenumber, tstamp, msg, process, method, result from sms where direction = '{$direction}' order by id desc limit {$start}, {$this->limit};";
		
		$return = array();
		if($result = $this->db->query($select)){
			while($row = $result->fetch_assoc()){
				$return[] = $row;
			}
			return $return;
		}
		else{
			return false;
		}
		
	}
	
	function getsms_count($direction=0){
	
		/*
		 * get sms count
		 *
		 * integer
		 */
	
		$select = "select count(id) cnt from sms where direction = '{$direction}';";
	
		if($result = $this->db->query($select)){
			$row = $result->fetch_assoc();
			return $row['cnt'];
		}
		else{
			return false;
		}
	
	}
	
	function sendsms($phone,$msg,$translit=0,$method="gsm"){
		
		/*
		 * insert sms into queue
		 * 
		 * boolean
		 */
		
		if($phone != "" && $msg != ""){
		
			$phone = $this->check_phone($phone);
			if($translit == 1) $msg = $this->rus2lat($msg);
			$msg = $this->db->real_escape_string($msg);
			
			$insert = "insert into sms set phonenumber = '{$phone}', msg = '{$msg}', tstamp=now(), direction = 1, process = 0, method = '{$method}';";
			if($this->db->query($insert)){
				$sms_id = $this->db->insert_id;
				return $sms_id;
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
		
	}
	
	function pagination($iCurr, $iEnd, $iRange){
	
		/*
		 * pagination
		 *
		 * string
		 */
	
		$pagination = "<div class=\"pagination\">";
	
		if($iCurr <= $iRange){
			$iRange = $iRange + ($iRange - $iCurr) + 1;
		}
		elseif($iCurr >= ($iEnd - $iRange)){
			$iRange = $iRange + ($iRange - ($iEnd-$iCurr)) + 1;
		}
	
		if($iCurr == 1){
			$page_minus_link = "<a href=\"\">";
			$page_minus_class = "disabled";
		}
		else{
			$page_minus = $iCurr - ($iRange*2) - 1;
			$page_minus_link = "<a href=\"?page={$page_minus}\">";
			$page_minus_class = "";
		}
		if($iCurr == $iEnd){
			$page_plus_link = "<a href=\"\">";
			$page_plus_class = "disabled";
		}
		else{
			$page_plus = $iCurr + ($iRange*2) + 1;
			$page_plus_link = "<a href=\"?page={$page_plus}\">";
			$page_plus_class = "";
		}
	
		$startpage=$iCurr - $iRange;
		if($startpage < 1) $startpage = 1;
		$endpage=$iCurr + $iRange;
		if($endpage > $iEnd) $endpage = $iEnd;
	
		$pagination .= "<li><a href=\"?page=1\">&laquo;&laquo;</a></li>";
		$pagination .= "<li class=\"{$page_minus_class}\">{$page_minus_link}&laquo;</a></li>";
	
		for ($i=$startpage;$i<=$endpage;$i++) {
			if($iCurr == $i){
				$pagination .= "<li class=\"active\"><a href=\"?page={$i}\">{$i}</a></li>";
			}
			else{
				$pagination .= "<li><a href=\"?page={$i}\">{$i}</li>";
			}
		}
	
		$pagination .= "<li class=\"{$page_plus_class}\">{$page_plus_link}&raquo;</a></li>";
		$pagination .= "<li><a href=\"?page={$iEnd}\">&raquo;&raquo;</a></li>";
	
		$pagination .= "</div>";
	
		return $pagination;
	
	}
	
	function user_auth($login,$md5password){
		
		/*
		 * get web user id
		 * 
		 * integer or false
		 */
		
		$select = "select id from sms_users where `login` = '{$login}' and `password` = '{$md5password}' and interface = 'web' limit 1;";
		if($result = $this->db->query($select)){
			$row = $result->fetch_assoc();
			if(isset($row['id']) && $row['id'] != ""){
				return $row['id'];
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
		
	}
	
	function api_auth($login,$md5password,$ip){
	
		/*
		 * get api user id
		 *
		 * integer or false
		 */
	
		$select = "select id from sms_users where `login` = '{$login}' and `password` = '{$md5password}' and interface = 'api' and (ip = '*' or ip like '%{$ip}%') limit 1;";
		if($result = $this->db->query($select)){
			$row = $result->fetch_assoc();
			if(isset($row['id']) && $row['id'] != ""){
				return $row['id'];
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	
	}
	
	function user_check_key($md5key,$ip){
	
		/*
		 * check user auth key
		 * get web user id
		 *
		 * integer
		 */
	
		$select = "select id from sms_users where md5(concat(`id`,`login`,`password`,'{$ip}')) = '{$md5key}'  and interface = 'web' limit 1;";
		if($result = $this->db->query($select)){
			$row = $result->fetch_assoc();
			if(isset($row['id']) && $row['id'] != ""){
				return $row['id'];
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	
	}
	
	function user_get_key($id,$ip){
	
		/*
		 * make user auth key
		 *
		 * string
		 */
	
		$select = "select md5(concat(`id`,`login`,`password`,'{$ip}')) md5key from sms_users where id = '{$id}';";
		if($result = $this->db->query($select)){
			$row = $result->fetch_assoc();
			if(isset($row['md5key']) && $row['md5key'] != ""){
				return $row['md5key'];
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	
	}
	
	function user_rights($id){
	
		/*
		 * get user rights
		 *
		 * array
		 */
		
		$rights = array();
		$select = "select `right` from sms_users_rights where user_id = '{$id}';";
		if($result = $this->db->query($select)){
			while($row = $result->fetch_assoc()){
				$rights[] = $row['right']; 
			}
		}
		return $rights;	
	}
	
	function users_log($user_id,$descr,$ip){
	
		/*
		 * write log
		 *
		 * boolean
		 */
	
		$insert = "insert into sms_users_log set user_id = '{$user_id}', ip='{$ip}', descr = '{$descr}';";
		if($this->db->query($insert)){
			return true;
		}
		else{
			return false;
		}
	}
	
	function get_rights($id=0){
	
		/*
		 * get rights
		 *
		 * array
		 */
		
		if($id > 0){
			$select = "select r.`right`, r.`descr`, if(ur.id is null,'','checked') `checked` from sms_rights r 
					left join sms_users_rights ur on (r.`right` = ur.`right` and `user_id` = '{$id}')
					where 1 group by r.`right`;";
		}
		else{
			$select = "select r.`right`, r.`descr`, '' `checked` from sms_rights r
					where 1 order by r.`right`;";
		}
		
		$return = array();
		if($result = $this->db->query($select)){
			while($row = $result->fetch_assoc()){
				$return[] = $row;
			}
			return $return;
		}
		else{
			return false;
		}
	
	}
	
	function insert_right($id,$right){
	
		/*
		 * create user right
		 *
		 * boolean
		 */
	
		$insert = "insert into sms_users_rights set user_id = '{$id}', `right` = '{$right}';";
		
		if($result = $this->db->query($insert)){
			return true;
		}
		else{
			return false;
		}
	
	}
	
	function delete_rights($id){
	
		/*
		 * delete user right
		 *
		 * boolean
		 */
	
		$delete = "delete from sms_users_rights where user_id = '{$id}';";
	
		if($result = $this->db->query($delete)){
			return true;
		}
		else{
			return false;
		}
	
	}
	
	function get_users($id=0){
	
		/*
		 * get users
		 *
		 * array
		 */
		
		$user_option = ($id > 0) ? " and `id` = '{$id}' " : ""; 
		
		$select = "select `id`, `login`, `ip`, `interface` from sms_users where 1 {$user_option} order by login;";
		
		$return = array();
		if($result = $this->db->query($select)){
			while($row = $result->fetch_assoc()){
				$return[] = $row;
			}
			return $return;
		}
		else{
			return false;
		}
	
	}
	
	function create_user($params){
		
		/*
		 * create user
		 *
		 * integer
		 */
		
		$insert_array = array();
		if(is_array($params)){
			foreach ($params as $key=>$value){
				if($key == "password") {
					if($value != ""){
						$value = md5($value);
					}
					else{
						continue;
					}
				}
				$insert_array[] = " `{$key}` = '{$value}' ";
			}
			$insert_values = implode(",",$insert_array);
			
			$insert = "insert into sms_users set {$insert_values} ;";
			if($result = $this->db->query($insert)){
				$id = $this->db->insert_id;
				return $id;
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
		
	}
	
	function update_user($params){
	
		/*
		 * update user
		 *
		 * boolean
		 */
	
		$update_array = array();
		if(is_array($params)){
			foreach ($params as $key=>$value){
				if($key == "id") {
					$id = $value;
				}
				if($key == "password") {
					if($value != ""){
						$value = md5($value);
					}
					else{
						continue;
					}
				}
				$update_array[] = " `{$key}` = '{$value}' ";
			}
			$update_values = implode(",",$update_array);
			
			if(!empty($id)){	
				$update = "update sms_users set {$update_values} where `id` = '{$id}';";
				if($result = $this->db->query($update)){
					return true;
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	
	}
	
	function delete_user($id){
	
		/*
		 * delete user
		 *
		 * boolean
		 */
	
		if(!empty($id)){
			$delete = "delete from sms_users where `id` = '{$id}' limit 1;";
			if($result = $this->db->query($delete)){
				return true;
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
		
	}
	
	function rus2lat($text){
	
		/*
		 * translit
		 *
		 * string
		 */
	
		$iso9_table = array(
				'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Ѓ' => 'G',
				'Ґ' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Є' => 'Ye',
				'Ж' => 'Zh', 'З' => 'Z', 'Ѕ' => 'Z', 'И' => 'I', 'Й' => 'Y',
				'Ј' => 'J', 'І' => 'I', 'Ї' => 'Yi', 'К' => 'K', 'Ќ' => 'K',
				'Л' => 'L', 'Љ' => 'L', 'М' => 'M', 'Н' => 'N', 'Њ' => 'N',
				'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
				'У' => 'U', 'Ў' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'Ts',
				'Ч' => 'Ch', 'Џ' => 'Dh', 'Ш' => 'Sh', 'Щ' => 'Sch', 'Ъ' => '',
				'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
				'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'ѓ' => 'g',
				'ґ' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'є' => 'ye',
				'ж' => 'zh', 'з' => 'z', 'ѕ' => 'z', 'и' => 'i', 'й' => 'y',
				'ј' => 'j', 'і' => 'i', 'ї' => 'yi', 'к' => 'k', 'ќ' => 'k',
				'л' => 'l', 'љ' => 'l', 'м' => 'm', 'н' => 'n', 'њ' => 'n',
				'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
				'у' => 'u', 'ў' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts',
				'ч' => 'ch', 'џ' => 'dh', 'ш' => 'sh', 'щ' => 'sch', 'ь' => '',
				'ы' => 'y', 'ъ' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
		);
	
		$text = strtr($text, $iso9_table);
		$text = preg_replace("/[^A-Za-z0-9_\-\s\.]/", "", $text);
		
		return $text;
	
	}
	
	function check_phone($phonenumber){
		
		/*
		 * check RU phonenumbers
		 * 
		 * string
		 */
		
		$phonenumber = preg_replace("/\D/", "", $phonenumber);
		
		if(strlen($phonenumber) >= 10){
			$phonenumber = "7".substr($phonenumber, -10);
		}
		
		return $phonenumber;
		
	}
	
	function check_pid_file($match){
	
		/*
		 * check pid file
		 *
		 * boolean
		 */
		
		if(file_exists("/tmp/{$match}.pid")){
			return true;
		}
		else{
			return false;
		}	
	}
	
}
