<?php 
	/*
		Template for author output
		if you want to override this file
		Copy this file to "your_theme_folder/plugins/hypercontact/templates" to change the default.
	*/
?>
	<div class="hc-author-box">
		<h3 class="hc-admin-name"><?php echo $author->display_name; ?></h3>
		<div class="hc-author-image">
			<?php echo $author->gravatar;  ?>
		</div>
		<div class="hc-author-info">
			<div class="hc-author-description">
				<?php echo $author->description; ?>
			</div>
			<ul class="hc-author-info-list">
				<?php foreach ($author as $key => $value): ?>
					<?php $fieldName = hc_get_field_name($key); ?>
					<?php if (($key != 'gravatar' && $key != 'display_name') && ($key != 'description' && $value != '')): ?>
						<li><?php echo $fieldName.': '. $value ?></li>
					<?php endif ?>
				<?php endforeach ?>
			</ul>
		</div>
	</div>