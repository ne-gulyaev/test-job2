<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\web\View;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use add\dll\userAction as UserAction;

$UA = new UserAction; 
$this->title = 'User panel';



if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])){
		
			
				if($_POST['active'] == 'upd'){
					$users[$q] = $UA->update($users[$q]);
				}elseif($_POST['active']=='chatGet'){
					echo $UA->getChat(Yii::$app->user->identity->id, 1);
					exit();
				}elseif($_POST['active']=='chatSend'){
					echo $UA->addChat(Yii::$app->user->identity->id, 1, $_POST['mes']);
					exit();
				}
			
			
			
	}


Modal::begin([
    'header' => '<h2>'.\Yii::t('yii', 'Page info').'</h2>',
    'options' => ['id' => 'chatModal'],
    'size' => Modal::SIZE_LARGE,
]);

Modal::end();
?>


<h1> <?=Yii::$app->user->identity->fio?> </h1>

<?php
$form = ActiveForm::begin(

);
	
	
	echo Html::input("text", 'id', Yii::$app->user->identity->id, ['style'=>'display:none']);
	echo Html::input("text", 'active', 'upd', ['style'=>'display:none']);
	echo '<p><b>Логин</b>  ' . Html::input("text", 'login', Yii::$app->user->identity->username) . '</p>';
	echo '<p><b>Пароль</b>  ' . Html::input("text", 'password', Yii::$app->user->identity->password) . '</p>';
	echo '<p><b>Токен</b>  ' .  Yii::$app->user->identity->accessToken . '</p>';
	echo '<p><b>ФИО</b>  ' . Html::input("text", 'name', Yii::$app->user->identity->fio) . '</p>';
	 
    echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);
   
ActiveForm::end();

$js = "
$('*[so-class = so-chat-info]').click(function(){
					fx(this);
					return false;
				});
				fx = function(t){
					so_alterid = 1;
					$.post('/web/index.php?r=site%2Flk', {'active':'chatGet', 'id': 1}, function(e){
						var modal = $('#chatModal');
						var modalBody = modal.find('.modal-body');
						var modalTitle = modal.find('.modal-header');
						e = JSON.parse(e);
						var s = '';
						for(var q = 0; q < e.length; q++){
							if(e[q]['user'] == so_id){
								s += '<p style = \'text-align:right\'>'+new Date(e[q]['date'])+'<br><b>'+e[q]['text']+'</b></p>';
							}else{
								s += '<p>'+new Date(e[q]['date'])+'<br><b>'+e[q]['text']+'</b></p>';
							}
						}
						s += '<input class = \'so-send-me\'></input>';
						modalBody.html(s);
						modal.modal('show');
						$('.so-send-me').keydown(function(e){
							if(e.keyCode === 13) {
								$('#chatModal').modal('hide');
								$.post('/web/index.php?r=site%2Flk', {'active':'chatSend', 'id': so_alterid, 'mes': $(this).val()}, function(){
									
								})
							}
						});
					});
				};
";

View::addJs($js, \yii\web\View::POS_READY);
?>

<button type="submit" class="btn btn-primary" so-class = "so-chat-info" >Чат</button>

<script>
	var so_id = <?=Yii::$app->user->identity->id;?>;
	var so_alterid;
	var fx;
</script>