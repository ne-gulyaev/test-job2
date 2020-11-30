<?php
namespace add\dll;
use Yii;
	class UserAction{		
		public  function update($u){			
			if($u['username'] != $_POST['login'] || $u['password'] != $_POST['password'] || $u['authKey'] !=  $_POST['key']|| $u['role'] != $_POST['role']|| $u['fio'] != $_POST['name']){
				$p = $this->POSTsecurity($_POST);
				Yii::$app->db->createCommand("UPDATE `users` SET `username`='$p[login]',`password`='$p[password]',`authKey`='$p[key]',`role`='$p[role]',`fio`='$p[name]' WHERE `id` = $p[id]")->execute();
				$u['username'] = $_POST['login'];
				$u['password'] = $_POST['password'];
				$u['authKey'] = $_POST['key'];
				$u['role'] = $_POST['role'];
				$u['fio'] = $_POST['name'];
			}
			return $u;
		}
		
		public function remove($e){			
			Yii::$app->db->createCommand()->delete('users', "id = $e")->execute();
		}
		
		public function getChat($user, $to){			
			$mes = Yii::$app->db->createCommand("SELECT * FROM `chat` WHERE (`user` = $user AND `toUser` = $to) OR (`user` = $to AND `toUser` = $user) ORDER BY `date` DESC LIMIT 0,50")->queryAll();
			return json_encode($mes);
		}
		public function addChat($user, $to, $tx){			
			$mes = Yii::$app->db->createCommand("INSERT INTO `chat`(`user`, `toUser`, `text`) VALUES ('$user','$to','$tx')")->execute()();
			return json_encode($mes);
		}
		
		public function CreateUser($login, $pass, $role, $name){
			$tok = $this->randomString();
				Yii::$app->db->createCommand("INSERT INTO `users` (`username`, `password`, `accessToken`, `role`, `fio`) VALUES ('$login','$pass', $tok, '$role', '$name')")->execute()();		
				return true;
		}
		
		public function POSTsecurity($e){
		foreach($e as $p){
			$p = str_replace([" OR ", " or ", " Or ", " oR ", "nsert", "NSERT", "ELECT", "elect", "PDATE", "pdate"], "", $p);			
		}
		return $e;
	}
	public function randomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$rds = '';
		for ($i = 0; $i < $length; $i++) {
			$rds .= $characters[rand(0, $charactersLength - 1)];
		}
		return $rds;
	}
		
	}

?>