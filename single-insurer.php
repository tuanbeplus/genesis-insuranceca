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
$dist_method_meta = get_post_meta( get_the_ID(), 'insurer_distribution_method', true );
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
            // Get top-level categories
            $terms = wp_get_post_terms( get_the_ID(), 'insurer-category', array(
                'hide_empty' => false,
                'parent' => 0
            ) );

            // Prepare arrays to hold products by distribution method
            $products_by_method = [
                'direct' => [],
                'broker' => [],
            ];

            if (!empty($terms) && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    // Get child terms (product types) for this main category
                    $child_terms = wp_get_post_terms( get_the_ID(), 'insurer-category', array(
                        'hide_empty' => false,
                        'parent' => $term->term_id
                    ) );

                    // If no child terms, treat the parent as its own child
                    if (empty($child_terms) || is_wp_error($child_terms)) {
                        $method = isset($dist_method_meta[$term->term_id]) ? $dist_method_meta[$term->term_id] : 'direct';
                        if (!isset($products_by_method[$method][$term->term_id])) {
                            $products_by_method[$method][$term->term_id] = [
                                'term_name' => $term->name,
                                'children' => [],
                            ];
                        }
                        $products_by_method[$method][$term->term_id]['children'][] = $term->name;
                    } else {
                        foreach ($child_terms as $child) {
                            $method = isset($dist_method_meta[$child->term_id]) ? $dist_method_meta[$child->term_id] : 'direct';
                            if (!isset($products_by_method[$method][$term->term_id])) {
                                $products_by_method[$method][$term->term_id] = [
                                    'term_name' => $term->name,
                                    'children' => [],
                                ];
                            }
                            $products_by_method[$method][$term->term_id]['children'][] = $child->name;
                        }
                    }
                }

                // Output for Direct
                if (!empty($products_by_method['direct'])) {
                    echo '<h3>Products offered direct</h3>';
                    foreach ($products_by_method['direct'] as $main_cat) {
                        echo '<p><strong>' . esc_html($main_cat['term_name']) . '</strong>';
                        if (!empty($main_cat['children'])) {
                            echo ': ' . esc_html(implode(', ', $main_cat['children']));
                        }
                        echo '</p>';
                    }
                }

                // Output for Broker
                if (!empty($products_by_method['broker'])) {
                    echo '<h3>Products offered through a broker</h3>';
                    foreach ($products_by_method['broker'] as $main_cat) {
                        echo '<p><strong>' . esc_html($main_cat['term_name']) . '</strong>';
                        if (!empty($main_cat['children'])) {
                            echo ': ' . esc_html(implode(', ', $main_cat['children']));
                        }
                        echo '</p>';
                    }
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
