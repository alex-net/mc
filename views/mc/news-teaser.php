<?php 
use yii\helpers\Html;
use yii\helpers\StringHelper;
$fn=explode(',',$model['fn']);
$body=$model['teaser'];
if (!$body)
	$body=StringHelper::truncateWords($model['body'],15);
//Yii::info($model,'news teaser');
?>
<div class="news-item">
	<?php if (!empty($fn)):?>
	<div class='news-image'>
		<?=Html::a(Html::img(['image/file','preset'=>'news-preset','fn'=>$fn[0]]),'/news/'.$model['alias'] );?>
	</div>
<?php endif;?>
	<h3><?=Html::a($model['title'],'/news/'.$model['alias']);?></h3>
	<?=$body;?>
	
</div>