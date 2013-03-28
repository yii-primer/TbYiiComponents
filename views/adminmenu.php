
<ul class="nav nav-pills">
	<?php
	//echo ' !!!!!!' ;
	foreach ($params as $key => $value): ?>
	<?php  $params['action'] = Yii::app()->getController()->getAction()->getId();
		$active = Yii::app()->getController()->getAction()->getId();
		// внутри виджета определяем текущее дейсвтие  ?>
	<li<?php if ($active == end(explode("/", $value[0])))
		echo ' class="active"' ;
		?>>
		<?php echo CHtml::link($key,  $value); ?>
	</li>
	<?php endforeach; ?>
</ul>