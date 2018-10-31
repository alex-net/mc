<?php use yii\helpers\Html;?>
<div class="content-teaser-block"  >
	<?=Html::a( Html::img($fileurl),'/news/'.$content['alias'] );?>
	<div class='text'>
		<div class="bg">
			<div class="title"><?=Html::a($content['title'],'/news/'.$content['alias']);?></div>
		</div>
	</div>
</div>