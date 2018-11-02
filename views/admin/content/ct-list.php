<?php
use yii\helpers\Html; 
use yii\grid\GridView;
$titles=[
	'page'=>'Текстовые страницы',
	'new'=>'Новости',
	'album'=>'Альбомы',
];
?>

<h1><?php echo $titles[$type];?></h1>
<div><?=Html::a('Добавить сущность',['admin/content/ct-add','type'=>$type]);?></div>

<?= GridView::widget([
	'dataProvider'=>$dp,
	'formatter'=>['class'=>\app\components\SiteFormatter::className()],
	'columns'=>[
		[
			'label'=>'Заголовок',
			'attribute'=>'title',
			'content'=>function($m){
				return sprintf('%s (%s)',
					Html::a($m['title'],['admin/content/ct-edit','type'=>$m['type'],'id'=>$m['cid']]),
					Html::a('перейти','/'.$m['calias'])
				);
			}
		],
		'calias:text:путь',
		'created:datetimeru:Создан',
		'status:cstatus:Статус',
		'owner:text:Автор',
	]
]);?>