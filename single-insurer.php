<?php
/**
 * The template for displaying single insurer posts
 *
 * @package Genesis Insurance
 */

get_header(); 
$websites = get_field('websites', get_the_ID());
$phones = get_field('phones', get_the_ID());
$email = get_field('email', get_the_ID());
$through_a_broker = get_field('through_a_broker', get_the_ID());
$broker_url = get_field('broker_website_url', 'option');
$broker_url = !empty($broker_url) ? $broker_url : 'https://www.needabroker.com.au/';
?>

<main class="content">
    <?php ica_insurer_breadcrumbs(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <?php while ( have_posts() ) : the_post(); ?>
            <h1><?php the_title() ?></h1>
            <?php if (has_post_thumbnail()): ?>
                <?php the_post_thumbnail('medium'); ?>
            <?php else: ?>
                <img class="placeholder-img" src="/wp-content/uploads/2024/08/1c-submissions-bg.png" alt="Placeholder Image">
            <?php endif; ?>
            <?php if (!empty($websites)): ?>
                <h3>Website</h3>
                <?php foreach ($websites as $item): ?>
                    <p>
                        <?php if (!empty($item['website_title'])): ?>
                            <strong><?php echo esc_html($item['website_title']) ?>: </strong>
                        <?php endif; ?>
                        <?php if (!empty($item['website_url'])): ?>
                            <a href="<?php echo esc_url($item['website_url']) ?>" target="_blank"><?php echo esc_url($item['website_url']) ?></a>
                        <?php endif; ?>
                    </p>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($phones)): ?>
                <h3>Phone</h3>
                <?php foreach ($phones as $item): ?>
                    <p>
                        <?php if (!empty($item['phone_title'])): ?>
                            <strong><?php echo esc_html($item['phone_title']) ?>: </strong>
                        <?php endif; ?>
                        <?php if (!empty($item['phone_number'])): ?>
                            <a href="tel:<?php echo esc_attr($item['phone_number']) ?>"><?php echo esc_html($item['phone_number']) ?></a>
                        <?php endif; ?>
                    </p>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($email)): ?>
                <h3>Email</h3>
                <p>
                    <a href="mailto:<?php echo esc_attr($email) ?>"><?php echo esc_html($email) ?></a>
                </p>
            <?php endif; ?>

            <?php
            $terms = wp_get_post_terms( get_the_ID(), 'insurer-category', array(
                'hide_empty' => false,
                'parent' => 0
            ) );

            if (!empty($terms) && !is_wp_error($terms) || $through_a_broker == true) {
                echo '<h3>Products offered</h3>';

                if ($through_a_broker == true) {
                    echo '<p>Products offered <a href="'. $broker_url .'" target="_blank">through a broker only</a></p>';
                }

                foreach ($terms as $term) {
                    $child_terms = wp_get_post_terms( get_the_ID(), 'insurer-category', array(
                        'hide_empty' => false,
                        'parent' => $term->term_id
                    ) );
                    echo '<p><strong>' . esc_html($term->name) . '</strong>';
                    if (!empty($child_terms) && !is_wp_error($child_terms)) {
                        $child_names = array();
                        foreach ($child_terms as $child) {
                            $child_names[] = $child->name;
                        }
                        echo  ': ' . implode(', ', $child_names);
                    }
                    echo '</p>';
                }
            }
            ?>
        <?php endwhile; // End of the loop. ?>
    </article><!-- #post -->
    <?php echo do_shortcode( '[insurers_global_search]' ) ?>
    <?php echo do_shortcode( '[insurers_notice]' ) ?>
</main>

<?php
get_footer();
