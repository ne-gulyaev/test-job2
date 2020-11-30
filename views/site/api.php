<?php

use add\dll\userAction as UserAction;
$UA = new UserAction; 

$this->title = 'API';
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['c'])){
	if($_GET['c'] == 'auch'){
		$users = Yii::$app->getUser()->identity->getAll();	
		foreach ($users as $u){
			if($u['username'] == $_POST['login'] && $u['password'] == $_POST['password']){
				echo '{"status": true, "key": "'.$u['accessToken'].'"}';
				exit();
			}
		}
		echo '{"status": false, "error": "Данные авторизации указаны неверно"}';		
	}
	if($_GET['c'] == 'addUser'){
		$u = Yii::$app->getUser()->identity->findIdentityByAccessToken($_POST['key']);
		if(isset($u) && $u->role == 1){
			$UA->CreateUser($_POST['login'], $_POST['password'], $_POST['role'], $_POST['name']);
			echo '{"status": true}';
		}
	}
	if($_GET['c'] == 'getUser'){
		$u = Yii::$app->getUser()->identity->findIdentityByAccessToken($_POST['key']);
		if(isset($u) && $u->role == 1 || isset($u) && $u->id == $_POST['id']){
			echo '{"status": true, "user": '.json_encode(Yii::$app->getUser()->identity->findIdentity($_POST['id'])).'}';
		}else{
			echo '{"status": false, "error": "Данные авторизации указаны неверно"}';		
		}
	}
	exit();
	if($_GET['c'] == 'updUser'){
		$u = Yii::$app->getUser()->identity->findIdentityByAccessToken($_POST['key']);
		if(isset($u) && $u->role == 1 || isset($u) && $u->id == $_POST['id']){			
			$UA->update(Yii::$app->getUser()->identity->findIdentity($_POST['id']));
			echo '{"status": true}';
		}else{
			echo '{"status": false, "error": "Данные авторизации указаны неверно"}';		
		}
	}
	if($_GET['c'] == 'remUser'){
		$u = Yii::$app->getUser()->identity->findIdentityByAccessToken($_POST['key']);
		if(isset($u) && $u->role == 1 || isset($u) && $u->id == $_POST['id']){
			$UA->remove($_POST['id']);
			echo '{"status": true}';
		}else{
			echo '{"status": false, "error": "Данные авторизации указаны неверно"}';		
		}
	}
	if($_GET['c'] == 'getUsers'){
		$u = Yii::$app->getUser()->identity->findIdentityByAccessToken($_POST['key']);
		if(isset($u) && $u->role == 1){
			echo '{"status": true, "user": '.json_encode(Yii::$app->getUser()->identity->getAll()).'}';
		}else{
			echo '{"status": false, "error": "Данные авторизации указаны неверно"}';		
		}
	}
	if($_GET['c'] == 'getChat'){
		$u = Yii::$app->getUser()->identity->findIdentityByAccessToken($_POST['key']);
		if(isset($u)){
			echo '{"status": true, "chat": '.json_encode($UA->getChat($u, $_POST['toID'])).'}';
		}else{
			echo '{"status": false, "error": "Данные авторизации указаны неверно"}';		
		}
	}
	if($_GET['c'] == 'sendChat'){
		$u = Yii::$app->getUser()->identity->findIdentityByAccessToken($_POST['key']);
		if(isset($u)){
			if($u->role == 0){
				$UA->addChat($u, 1, $_POST['text']);				
			}else{
				$UA->addChat($u, $_POST['toID'], $_POST['text']);
			}
			echo '{"status": true}';
		}else{
			echo '{"status": false, "error": "Данные авторизации указаны неверно"}';		
		}
	}
	exit();
}

?>
<h1> API </h1>
<table>
	<tr><th>Адрес</th><th>Параметры</th><th>Действие</th><th>Ответ</th></tr>
	<tr><th>/web/index.php?r=site%2Fapi&c=auch</th><th>{login: ****,password: ****}</th><th>Авторизация</th><th>{"status":true/false, "key":""}</th></tr>
	<tr><th>/web/index.php?r=site%2Fapi&c=addUser</th><th>{key: ****,login: ****, password: ****, role: 0/1, name: ****}</th><th>Создать пользователя</th><th>{"status":true/false}</th></tr>
	<tr><th>/web/index.php?r=site%2Fapi&c=getUser</th><th>{key: ****,id: ****}</th><th>Получить пользователя</th><th>{"status":true/false, "user":{}}</th></tr>
	<tr><th>/web/index.php?r=site%2Fapi&c=updUser</th><th>{key: ****, id: ****, login: ****, password: ****, role: 0/1, name: ****}</th><th>Изменить пользователя</th><th>{"status":true/false}</th></tr>
	<tr><th>/web/index.php?r=site%2Fapi&c=remUser</th><th>{key: ****, id: ****}</th><th>Удалить пользователя</th><th>{"status":true/false}</th></tr>
	<tr><th>/web/index.php?r=site%2Fapi&c=getUsers</th><th>{key: ****}</th><th>Получить список пользователей</th><th>{"status":true/false, "users":[{},{}]}</th></tr>
	<tr><th>/web/index.php?r=site%2Fapi&c=geChat</th><th>{key: ****, toID: ****}</th><th>Сообщения с пользователем</th><th>{"status":true/false, "chat":[{},{}]}</th></tr>
	<tr><th>/web/index.php?r=site%2Fapi&c=sendChat</th><th>{key: ****, toID: ****, text: ****}</th><th>Отправить сообщение пользователю/админу</th><th>{"status":true/false}</th></tr>
	
</table>