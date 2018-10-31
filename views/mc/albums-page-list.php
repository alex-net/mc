<?php
use yii\helpers\Html;
use yii\widgets\ListView;
 ?>
<h1>Альбомы</h1>


<?=ListView::widget([
	'dataProvider'=>$list,
	'itemView'=>'albums-teaser',
	'summary'=>'',
]);
