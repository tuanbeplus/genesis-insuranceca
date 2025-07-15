<?php global $home_options; ?>
<div class="ss-home ss-testimonial">
    <div class="container">
        <div class="ss-testimonial--list">
            <?php foreach ($home_options['testimonials'] as $key => $testimonial): ?>
                <div class="item-testimonial ">
                  <span class="item-testimonial--symboy">“</span>
                  <div class="item-testimonial--content">
                    <?php echo get_field('content',$testimonial->ID); ?>
                  </div>
                  <div class="item-testimonial--author">
                    <img src="<?php echo get_field('avatar',$testimonial->ID); ?>" alt="" class="__avatar">
                    <div class="__name">
                      <h4><?php echo get_field('display_name',$testimonial->ID); ?></h4>
                      <div class="__id">
                        #<?php echo get_field('name_id',$testimonial->ID); ?>
                      </div>
                    </div>
                  </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
