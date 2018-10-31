
<?php foreach($bb as $b):?>
	<?php if ($b->cid):?>
	<div class="content-teaser-block" >
		<?=$out[$b->cid];?>
	</div>
	<?php else:?>
	<div class='sidebar-block-el'>
		<?=$b->content;?>
	</div>
	<?php endif;?>	
<?php endforeach;?>