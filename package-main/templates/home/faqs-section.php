<?php global $home_options; ?>
<div class="ss-home ss-faqs" style="background-image:url('<?php echo $home_options['image_faqs'] ?>');">
      <div class="container">
        <div class="ss-faqs--left">
            <h2><?php echo $home_options['heading_faqs'] ?></h2>
            <div class="list-faqs">
                <?php foreach ($home_options['faqs'] as $key => $faq) {
                    ?>
                    <div class="item-faq item-faq-<?php echo $key; ?> <?php echo $key < 1 ? '__is-active __is-toggle' : ''; ?>">
                        <h4 class="__title"><?php echo $faq['question'] ?></h4>
                        <div class="__content">
                            <?php echo $faq['answer']; ?>
                        </div>
                    </div>
                    <?php
                } ?>
            </div>
        </div>
      </div>
</div>
