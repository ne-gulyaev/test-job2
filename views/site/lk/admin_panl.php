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
	$this->title = 'Admin panel';
	$users = Yii::$app->getUser()->identity->getAll();	
	if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])){
		for($q = 0; $q < count($users); $q++){
			if($users[$q]['id'] == $_POST['id']){
				if($_POST['active'] == 'upd'){
					$users[$q] = $UA->update($users[$q]);
				}elseif($_POST['active'] == 'rem'){
					$UA->remove($_POST['id']);
					unset($users[$q]);
				}elseif($_POST['active']=='chatGet'){
					echo $UA->getChat(Yii::$app->user->identity->id, $_POST['id']);
					exit();
				}elseif($_POST['active']=='chatSend'){
					echo $UA->addChat(Yii::$app->user->identity->id, $_POST['id'], $_POST['mes']);
					exit();
				}
				break;
			}
			if($q == count($users)-1){
				Modal::begin([
				'header' => '<h2>'.\Yii::t('yii', 'Page info').'</h2>',
				'options' => ['id' => 'error_modal'],
				'size' => Modal::SIZE_LARGE,
				]);
					echo \Yii::t('yii', 'Ошибка записи данных, попробуйте еще раз');

				Modal::end();
				View::addJs("$('#error_modal').modal('show');", \yii\web\View::POS_READY);
			}
		}
	}
	//$users = Yii::$app->getUser()->identity->getAll();
	
	
	
	function createUserLine ($e){
		$provider = new ArrayDataProvider([
    'allModels' => $e,
    'pagination' => [
        'pageSize' => 10,
    ],
    'sort' => [
        'attributes' => ['id', 'username', 'role'],
    ],
]);

Modal::begin([
    'header' => '<h2>'.\Yii::t('yii', 'Page info').'</h2>',
    'options' => ['id' => 'myModal'],
    'size' => Modal::SIZE_LARGE,
]);
echo \Yii::t('yii', 'Text...');

Modal::end();

Modal::begin([
    'header' => '<h2>'.\Yii::t('yii', 'Page info').'</h2>',
    'options' => ['id' => 'chatModal'],
    'size' => Modal::SIZE_LARGE,
]);

Modal::end();

Modal::begin([
    'header' => '<h2>'.\Yii::t('yii', 'user edit').'</h2>',
    'options' => ['id' => 'user_modal_edit'],
    'size' => Modal::SIZE_LARGE
]);

$form = ActiveForm::begin(

);
	
	
	echo Html::input("text", 'id', '', ['style'=>'display:none']);
	echo Html::input("text", 'active', 'upd', ['style'=>'display:none']);
	echo '<p><b>Логин</b>  ' . Html::input("text", 'login') . '</p>';
	echo '<p><b>Пароль</b>  ' . Html::input("text", 'password') . '</p>';
	echo '<p><b>Ключ</b>  ' . Html::input("text", 'key') . '</p>';
	echo '<p><b>Права</b>  ' . Html::input("text", 'role') . '</p>';
	echo '<p><b>ФИО</b>  ' . Html::input("text", 'name') . '</p>';
	 
    echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);
   
ActiveForm::end();
Modal::end();


$js = "
				$('*[so-class = so-user-info]').on('click',function(event){  
                    event.preventDefault();
                    var myModal = $('#myModal');
                    var modalBody = myModal.find('.modal-body');
                    var modalTitle = myModal.find('.modal-header');
                    var data = so_users[$(this).data('c')]; 
                    modalTitle.find('h2').html(data[\"fio\"]);
					var s = '';
					var k = Object.keys(data);
					var tx = ['ID', 'Логин','Пароль','Ключ','Токен','Права','ФИО'];
					for(var g = 0; g < k.length; g++){
						s+='<p>' + '<b>'+ tx[g] +':  </b>' + data[k[g]] + '</p>' ;
					}
                    modalBody.html(s);
                    myModal.modal('show');
					//return false;
					}
				);
				
				$('*[so-class = so-user-edit]').on('click',function(event){  
                    event.preventDefault();
                    var myModal = $('#user_modal_edit');
                    var modalBody = myModal.find('.modal-body');
                    var modalTitle = myModal.find('.modal-header');
                    var data = so_users[$(this).data('c')]; 
                    modalTitle.find('h2').html(data[\"fio\"]);
					var s = '';
					var k = Object.keys(data);
					//var tx = ['ID', 'Логин','Пароль','Ключ','Токен','Права','ФИО'];
					myModal.find('input[name=id]').val(data[k[0]]);
					myModal.find('input[name=login]').val(data[k[1]]);
					myModal.find('input[name=password]').val(data[k[2]]);
					myModal.find('input[name=key]').val(data[k[3]]);
					myModal.find('input[name=role]').val(data[k[5]]);
					myModal.find('input[name=name]').val(data[k[6]]);
                    myModal.modal('show');
					//return false;
					}
				);
				$('*[so-class = so-user-trash]').click(function(){
					if(confirm('Действительно удалить пользователя?')){
						$.post('/web/index.php?r=site%2Flk', {'active':'rem', 'id': so_users[$(this).data('c')]['id']}, function(e){location.reload();});
					}
					return false;
				});
				
				$('*[so-class = so-chat-info]').click(function(){
					fx(this);
					return false;
				});
				fx = function(t){
					so_alterid = so_users[$(t).data('c')]['id'];
					$.post('/web/index.php?r=site%2Flk', {'active':'chatGet', 'id': so_users[$(t).data('c')]['id']}, function(e){
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

	return	GridView::widget([
		'dataProvider' => $provider,
		'columns' => [
			'id',
			'fio',
			'username',
			'password',
			'authKey',
			'role',
			['class' => 'yii\grid\ActionColumn', 'template' => '{view2} {update2} {del} {mess}', 'buttons'=>['mess' => function($url, $model, $key){
				
				$iconName = "comment";
				$title = \Yii::t('yii', 'chat');
				$id = 'chat-'.$key;
				$options = [
					'title' => $title,
					'aria-label' => $title,
					'data-pjax' => '0',
					'data-c' => $key,
					'so-class' => 'so-chat-info',
					'id' => $id
				];
				
				$url = Url::current(['', 'id' => $key]);
				$icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-$iconName"]);
            return Html::a($icon, $url, $options);
				
				
			},
			'view2' => function($url, $model, $key){				
				$iconName = "user";
				$title = \Yii::t('yii', 'Просмотр пользоателя');
				$id = 'user-'.$key;
				$options = [
					'title' => $title,
					'aria-label' => $title,
					'data-pjax' => '0',
					'data-c' => $key,
					'so-class' => 'so-user-info',
					'id' => $id
				];				
				$url = Url::current(['', 'id' => $key]);
				$icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-$iconName"]);
				return Html::a($icon, $url, $options);
			},
			'del' => function($url, $model, $key){				
				$iconName = "trash";
				$title = \Yii::t('yii', 'Удалить');
				$id = 'trash-'.$key;
				$options = [
					'title' => $title,
					'aria-label' => $title,
					'data-pjax' => '0',
					'data-c' => $key,
					'so-class' => 'so-user-trash',
					'id' => $id
				];				
				$url = Url::current(['', 'id' => $key]);
				$icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-$iconName"]);
				return Html::a($icon, $url, $options);
			},
			'update2' => function($url, $model, $key){
				
				$iconName = "pencil";
				$title = \Yii::t('yii', 'Редактировать');
				$id = 'edit-'.$key;
				$options = [
					'title' => $title,
					'aria-label' => $title,
					'data-pjax' => '0',
					'data-c' => $key,
					'so-class' => 'so-user-edit',
					'id' => $id
				];
				
				$url = Url::current(['', 'id' => $key]);
				$icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-$iconName"]);
            
				

            return Html::a($icon, $url, $options);
				
				
			}
			
			
			]			
			],
		],
	]); 
	}
	
	

?>

<h1> Пользоватили </h1>

<?= createUserLine($users);?>

<script>
	var so_users = JSON.parse('<?=json_encode($users);?>');
	var so_id = <?=Yii::$app->user->identity->id;?>;
	var so_alterid;
	var fx;
</script>