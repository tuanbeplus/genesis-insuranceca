<?php
global $landing_fields;
 ?>
 <div id="hero-template" class="landing-section">
	 	<div class="wrap">
	 			<h1 class="ld-title"><?php echo $landing_fields['hero_heading']; ?></h1>
				<div class="ld-sub-title">
					<?php echo $landing_fields['hero_sub_heading']; ?>
				</div>
				<div class="ld-interests">
						<div class="hero-before-interests">
							<?php echo $landing_fields['hero_before_interests'] ?>
						</div>
						<div class="hero-interests">
							<?php foreach ($landing_fields['hero_interests'] as $value): ?>
									<div class="_item-interest">
											<div class="__icon">
													<img src="<?php echo $value['icon']; ?>" alt="">
											</div>
											<div class="__description">
													<?php echo $value['description']; ?>
											</div>
									</div>
							<?php endforeach; ?>
						</div>
						<?php if(!empty($landing_fields['button_find_deals'])): ?>
						<div class="button-find-deals">
								<a href="<?php echo $landing_fields['button_find_deals']['link_button']; ?>" class="btn btn-find-deals"><?php echo $landing_fields['button_find_deals']['text_button']; ?></a>
								<span><?php echo $landing_fields['button_find_deals']['text_after_button']; ?></span>
						</div>
						<?php endif; ?>
				</div>
	 	</div>
 </div>
