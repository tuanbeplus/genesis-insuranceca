<?php global $home_options; ?>
<div class="ss-home ss-plan ss-triangle-top" style="background-image:url(<?php echo $home_options['background_plan'] ?>)">
    <div class="__plan-triangle-top"></div>
    <div class="top-plan">
      <div class="container">
        <?php $work_plan = $home_options['work_plan']; ?>
        <div class="top-plan--left">
            <h2><?php echo $work_plan['heading'] ?></h2>
            <div class="top-plan--btn">
              <a href="<?php echo $work_plan['button']['link'] ?>" class="button btn-primary <?php echo $work_plan['button']['show_popup'] ? 'show-popup-video' : ''; ?>"><?php echo $work_plan['button']['label'] ?></a>
            </div>
        </div>
        <div class="top-plan--right">
            <div class="top-plan--sliders slick-sliders">
                <?php foreach ($home_options['slider_work'] as $key => $slider) {
                    ?>
                    <div class="item-plan-slide item-plan-slide-<?php echo $key; ?>">
                        <?php if($slider['image_1']): ?>
                          <img src="<?php echo $slider['image_1'] ?>" class="img-slider-1" alt="">
                        <?php endif; ?>

                        <?php if($slider['image_3']): ?>
                          <img src="<?php echo $slider['image_3'] ?>" class="img-slider-3" alt="">
                        <?php endif; ?>

                        <?php if($slider['image_2']): ?>
                          <img src="<?php echo $slider['image_2'] ?>" class="img-slider-2" alt="">
                        <?php endif; ?>
                        <span class="text-slider"><?php echo $slider['name'] ?></span>
                    </div>
                    <?php
                } ?>
            </div>
        </div>
      </div>
    </div>
    <div class="bottom-plan">
      <div class="container">
          <?php $info_plan = $home_options['info_plan']; ?>
          <div class="bottom-plan--info">
             <img src="<?php echo $info_plan['logo']; ?>" alt="">
             <h3><?php echo $info_plan['title'] ?></h3>
             <div class="bottom-plan--price">
                <span class="__symboy">$</span>
                <div class="__pricing">
                  <span><?php echo $info_plan['price']['number'] ?></span>
                  <div class="text-price">
                    <?php echo $info_plan['price']['text'] ?>
                  </div>
                </div>
             </div>
             <div class="bottom-plan--btn">
               <a href="<?php echo $info_plan['cta']['link'] ?>" class="button btn-primary"><?php echo $info_plan['cta']['label'] ?></a>
             </div>
          </div>
      </div>
    </div>
</div>
