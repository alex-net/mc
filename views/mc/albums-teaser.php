<?php 
use yii\helpers\Html;
$f=explode(',',$model['fn']);

?>
<div class="album-item">
	<?php if($f[0]):?>
	<div class='album-image'>
		<?=Html::a(Html::img(['image/file','preset'=>'albums-preset','fn'=>$f[0]]),'/albums/'.$model['alias'] );?>
	</div>
	<?php endif; ?>
	<h3><?=Html::a($model['title'],'/albums/'.$model['alias']);?></h3>
	
</div>