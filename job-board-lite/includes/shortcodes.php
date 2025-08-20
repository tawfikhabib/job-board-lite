<?php
if (!defined('ABSPATH')) exit;

// [job_board type="full-time" s="keyword" per_page="10"]
function jbl_job_board_shortcode($atts) {
    $atts = shortcode_atts([
        'type' => '',
        's' => '',
        'per_page' => 10,
        'paged' => max(1, get_query_var('paged') ?: get_query_var('page') ?: 1),
    ], $atts, 'job_board');

    wp_enqueue_style('jbl-style');

    $tax_query = [];
    if (!empty($atts['type'])) {
        $tax_query[] = [
            'taxonomy' => 'job_type',
            'field' => 'name',
            'terms' => sanitize_text_field($atts['type']),
        ];
    }

    $q = new WP_Query([
        'post_type' => 'job',
        's' => sanitize_text_field($atts['s']),
        'posts_per_page' => (int)$atts['per_page'],
        'paged' => (int)$atts['paged'],
        'tax_query' => $tax_query ?: null,
    ]);

    ob_start(); ?>
    <div class="jbl-jobs">
        <form method="get" class="jbl-filters">
            <input type="hidden" name="page_id" value="<?php echo get_queried_object_id(); ?>" />
            <input type="text" name="s" placeholder="<?php esc_attr_e('Search jobs...', 'job-board-lite'); ?>" value="<?php echo esc_attr(get_query_var('s')); ?>" />
            <?php
            wp_dropdown_categories([
                'show_option_all' => __('All Types', 'job-board-lite'),
                'taxonomy' => 'job_type',
                'name' => 'job_type',
                'selected' => isset($_GET['job_type']) ? (int) $_GET['job_type'] : 0,
                'hide_empty' => false,
            ]);
            ?>
            <button type="submit"><?php _e('Filter', 'job-board-lite'); ?></button>
        </form>

        <?php if ($q->have_posts()): while ($q->have_posts()): $q->the_post(); ?>
            <article class="jbl-job">
                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <div class="jbl-meta">
                    <?php
                    $company = get_post_meta(get_the_ID(), 'jbl_company', true);
                    $location = get_post_meta(get_the_ID(), 'jbl_location', true);
                    $type = wp_get_post_terms(get_the_ID(), 'job_type', ['fields' => 'names']);
                    ?>
                    <span class="jbl-badge"><?php echo esc_html($company ?: __('Company N/A', 'job-board-lite')); ?></span>
                    <span> • <?php echo esc_html($location ?: __('Location N/A', 'job-board-lite')); ?></span>
                    <?php if (!empty($type)) : ?><span> • <?php echo esc_html($type[0]); ?></span><?php endif; ?>
                </div>
                <div><?php the_excerpt(); ?></div>
            </article>
        <?php endwhile; else: ?>
            <p><?php _e('No jobs found.', 'job-board-lite'); ?></p>
        <?php endif; wp_reset_postdata(); ?>

        <div class="jbl-pagination">
            <?php
            echo paginate_links([
                'total' => $q->max_num_pages,
            ]);
            ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('job_board', 'jbl_job_board_shortcode');
// [job_submit]
function jbl_job_submit_shortcode() {
    if (!is_user_logged_in()) {
        return '<p>'.__('Please log in to submit a job.', 'job-board-lite').'</p>';
    }
    wp_enqueue_style('jbl-style');

    $msg = '';
    if ('POST' === $_SERVER['REQUEST_METHOD'] && isset($_POST['jbl_submit_nonce']) && wp_verify_nonce($_POST['jbl_submit_nonce'], 'jbl_submit')) {
        $post_id = wp_insert_post([
            'post_type' => 'job',
            'post_title' => sanitize_text_field($_POST['title'] ?? ''),
            'post_content' => wp_kses_post($_POST['description'] ?? ''),
            'post_status' => 'pending',
        ]);
        if ($post_id) {
            $map = [
                'company' => 'sanitize_text_field',
                'location' => 'sanitize_text_field',
                'salary_min' => 'floatval',
                'salary_max' => 'floatval',
                'employment_type' => 'sanitize_text_field',
                'apply_url' => 'esc_url_raw',
                'apply_email' => 'sanitize_email',
                'closing_date' => 'sanitize_text_field',
            ];
            foreach ($map as $k => $cb) {
                if (isset($_POST[$k])) update_post_meta($post_id, "jbl_$k", call_user_func($cb, $_POST[$k]));
            }
            if (!empty($_POST['job_type'])) {
                wp_set_post_terms($post_id, [sanitize_text_field($_POST['job_type'])], 'job_type', false);
            }
            $msg = '<div class="notice notice-success">'.__('Submitted! Pending review.', 'job-board-lite').'</div>';
        } else {
            $msg = '<div class="notice notice-error">'.__('Submission failed.', 'job-board-lite').'</div>';
        }
    }

    ob_start(); ?>
    <div class="jbl-form">
        <?php echo $msg; ?>
        <form method="post">
            <?php wp_nonce_field('jbl_submit', 'jbl_submit_nonce'); ?>
            <label><?php _e('Job Title', 'job-board-lite'); ?></label>
            <input type="text" name="title" required />

            <label><?php _e('Description', 'job-board-lite'); ?></label>
            <textarea name="description" rows="6"></textarea>

            <label><?php _e('Company', 'job-board-lite'); ?></label>
            <input type="text" name="company" />

            <label><?php _e('Location', 'job-board-lite'); ?></label>
            <input type="text" name="location" />

            <label><?php _e('Employment Type', 'job-board-lite'); ?></label>
            <select name="employment_type">
                <option>Full-time</option><option>Part-time</option><option>Contract</option><option>Internship</option><option>Temporary</option>
            </select>

            <label><?php _e('Job Type (taxonomy name)', 'job-board-lite'); ?></label>
            <input type="text" name="job_type" placeholder="e.g., Engineering" />

            <label><?php _e('Salary Min', 'job-board-lite'); ?></label>
            <input type="number" name="salary_min" />

            <label><?php _e('Salary Max', 'job-board-lite'); ?></label>
            <input type="number" name="salary_max" />

            <label><?php _e('Apply URL', 'job-board-lite'); ?></label>
            <input type="url" name="apply_url" />

            <label><?php _e('Apply Email', 'job-board-lite'); ?></label>
            <input type="email" name="apply_email" />

            <label><?php _e('Closing Date', 'job-board-lite'); ?></label>
            <input type="date" name="closing_date" />

            <button type="submit"><?php _e('Submit Job', 'job-board-lite'); ?></button>
        </form>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('job_submit', 'jbl_job_submit_shortcode');

add_filter('single_template', function($template){
    if (is_singular('job')) {
        $theme_template = locate_template(['single-job.php']);
        if (!$theme_template) {
            return JBL_PATH . 'templates/single-job.php';
        }
    }
    return $template;
});
