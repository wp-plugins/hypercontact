<?php 
	/*
		Template for widget output
		if you want to override this file
		Copy this file to "your_theme_folder/plugins/hypercontact/templates" to change the default.
	*/
?>
	<ul class="hc_user_list">
		<?php foreach ($user as $index => $info): ?>
			<?php if ($info != ''): ?>
				<li><?php echo $info ?></li>
			<?php endif ?>	
		<?php endforeach ?>
	</ul>