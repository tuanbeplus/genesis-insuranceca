<?php global $home_options; ?>
<div class="ss-home ss-service">
    <div class="container">
        <h2 class="ss-service--heading"><?php echo $home_options['heading_service'] ?></h2>
        <div class="ss-service--list">
            <?php foreach ($home_options['list_services'] as $key => $service): ?>
                <a href="<?php echo $service['link'] ?>" class="item-service <?php echo !$service['link'] ? 'no-link' : ''; ?>">
                    <?php if($service['icon']): ?>
                      <img src="<?php echo $service['icon'] ?>" alt="" class="item-service--icon">
                    <?php endif; ?>
                    <div class="item-service--content">
                      <?php echo $service['content'] ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
