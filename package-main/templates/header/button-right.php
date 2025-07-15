<?php
$button_compare = get_field('button_compare','option');

if(!empty($button_compare)){
?>
	<div class="comparesolar-button-right">
			<a href="<?php echo $button_compare['button_link'] ?>" class="btn header-button-right"><?php echo $button_compare['button_text'] ?></a>
	</div>
<?php
}

 ?>
