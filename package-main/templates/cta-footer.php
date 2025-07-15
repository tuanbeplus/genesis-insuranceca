<?php
$cta_footer = get_field('cta_footer','option');
$cta_button_find_deals_now = get_field('cta_button_find_deals_now','option');
 ?>
 <div id="cta-footer" class="landing-section">
 		<div class="wrap">
 				<?php if($cta_footer): ?>
					<h3 class="cta-footer-title"><?php echo $cta_footer; ?></h3>
				<?php endif; ?>
				<?php if(!empty($cta_button_find_deals_now)): ?>
					<a href="<?php echo $cta_button_find_deals_now['cta_link'] ?>" class="btn cta-footer-link"><?php echo $cta_button_find_deals_now['cta_text'] ?></a>
				<?php endif; ?>
 		</div>
 </div>
