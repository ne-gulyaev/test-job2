<?php

	$user = Yii::$app->user->identity;
	
if($user != null){
	if($user->role == '1'){
		$id = $_GET['id'];
		var_dump($id);
		//Yii::$app->db->createCommand()->delete('users', "id = $id")->execute();
	}
}

		//header("Location: /web/?r=site%2Flk");
		exit();

?>