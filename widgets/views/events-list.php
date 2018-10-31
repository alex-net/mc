<?php 
use yii\helpers\Html;
 ?>
<div class="events-slider">
<?php foreach($els as $el): ?>
	<div class="slider-el">
		<div class='bg-img' style="background-image: url(<?=$el['img'];?>);"></div>
			<div class="content">
				<div class="title"><?=Html::a($el['title'],$el['url']);?></div>
					<p><?=$el['teaser'];?></p>
					<div class="info">
						<span class='created'><?=date('d.m.Y',$el['created']);?></span>
					</div>
			</div>
	</div>

<?php endforeach;?>
</div>