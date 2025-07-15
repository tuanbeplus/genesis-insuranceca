<?php global $home_options; ?>
<div class="ss-home ss-cta">
    <div class="ss-cta--top">
      <div class="container">
        <div class="list-step-actions">
          <?php foreach ($home_options['step_cta'] as $key => $step) {
            ?>
            <div class="item-action">
                <img src="<?php echo $step['icon'] ?>" alt="">
                <div class="item-action--title">
                  <?php echo $step['title'] ?>
                </div>
                <div class="item-action--content">
                  <?php echo $step['content'] ?>
                </div>
            </div>
            <?php
          } ?>
        </div>
      </div>
    </div>
    <div class="ss-cta--bottom">
      <div class="container">
          <div class="cta-content">
              <?php echo $home_options['description_cta']; ?>
          </div>
          <div class="cta-btn">
            <a href="<?php echo $home_options['button_hero']['link'] ?>" class="button btn-primary bg-white"><?php echo $home_options['button_hero']['label'] ?></a>
          </div>
      </div>
    </div>
</div>
