<?php
$this->title = 'Administration Panel';


$user = Yii::$app->user->identity;
if($user != null){
	if($user->role == 1){	
		echo $this->render('admin_panl');	
	}else{	
		echo $this->render('user_panl');	
	}
}else{		
		header("Location: /web/?r=site%2Flogin");
		exit();
	}


?>
