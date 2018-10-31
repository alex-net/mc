<?php
use yii\helpers\Html;
use yii\widgets\ListView;
 ?>
<h1>Новости</h1>


<?=ListView::widget([
	'dataProvider'=>$list,
	'itemView'=>'news-teaser',
	'summary'=>'',
]);
