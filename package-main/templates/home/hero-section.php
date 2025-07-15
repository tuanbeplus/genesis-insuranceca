<?php global $home_options; ?>
<div class="ss-home ss-hero">
    <div class="top-hero" style="background-image:url(<?php echo $home_options['background_hero'] ?>)">
      <div class="container">
        <h1 class="top-hero--heading"><?php echo $home_options['heading_hero'] ?></h1>
        <div class="top-hero--sub-heading">
          <?php echo $home_options['sub_heading_hero'] ?>
        </div>
        <div class="top-hero--btn">
          <a href="<?php echo $home_options['button_hero']['link'] ?>" class="button btn-primary bg-white"><?php echo $home_options['button_hero']['label'] ?></a>
        </div>
      </div>
    </div>
    <?php $more_info_hero = $home_options['more_info_hero']; ?>
    <div class="bottom-hero">
      <div class="container">
        <div class="bottom-hero--left">
            <img src="<?php echo $more_info_hero['image']; ?>" alt="">
        </div>
        <div class="bottom-hero--right">
            <h2><?php echo $more_info_hero['title']; ?></h2>
            <div class="bottom-hero--des">
              <?php echo $more_info_hero['sub_title']; ?>
            </div>
        </div>
      </div>
    </div>
</div>
