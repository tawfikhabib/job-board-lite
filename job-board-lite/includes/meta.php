<?php
if (!defined('ABSPATH')) exit;

// Register meta (exposed to REST)
function jbl_register_meta() {
    $fields = [
        'company', 'location', 'salary_min', 'salary_max',
        'employment_type', 'apply_url', 'apply_email', 'closing_date'
    ];
    foreach ($fields as $key) {
        register_post_meta('job', "jbl_$key", [
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
            'auth_callback' => function() { return current_user_can('edit_posts'); }
        ]);
    }
}
add_action('init', 'jbl_register_meta');

// Metabox
function jbl_add_metabox() {
    add_meta_box('jbl_job_details', __('Job Details', 'job-board-lite'), 'jbl_metabox_html', 'job', 'normal', 'high');
}
add_action('add_meta_boxes', 'jbl_add_metabox');

function jbl_metabox_html($post) {
    wp_nonce_field('jbl_save_meta', 'jbl_nonce');
    $v = function($k) use ($post){ return esc_attr(get_post_meta($post->ID, "jbl_$k", true)); };
    ?>
    <p><label><?php _e('Company', 'job-board-lite'); ?></label>
    <input type="text" name="jbl_company" value="<?php echo $v('company'); ?>" /></p>

    <p><label><?php _e('Location', 'job-board-lite'); ?></label>
    <input type="text" name="jbl_location" value="<?php echo $v('location'); ?>" /></p>

    <p><label><?php _e('Salary Min', 'job-board-lite'); ?></label>
    <input type="number" name="jbl_salary_min" value="<?php echo $v('salary_min'); ?>" /></p>

    <p><label><?php _e('Salary Max', 'job-board-lite'); ?></label>
    <input type="number" name="jbl_salary_max" value="<?php echo $v('salary_max'); ?>" /></p>

    <p><label><?php _e('Employment Type', 'job-board-lite'); ?></label>
    <select name="jbl_employment_type">
        <?php
        $types = ['Full-time','Part-time','Contract','Internship','Temporary'];
        $current = $v('employment_type');
        foreach($types as $t){
            printf('<option %s>%s</option>', selected($current, $t, false), esc_html($t));
        }
        ?>
    </select></p>

    <p><label><?php _e('Apply URL', 'job-board-lite'); ?></label>
    <input type="url" name="jbl_apply_url" value="<?php echo $v('apply_url'); ?>" /></p>

    <p><label><?php _e('Apply Email', 'job-board-lite'); ?></label>
    <input type="email" name="jbl_apply_email" value="<?php echo $v('apply_email'); ?>" /></p>

    <p><label><?php _e('Closing Date', 'job-board-lite'); ?></label>
    <input type="date" name="jbl_closing_date" value="<?php echo $v('closing_date'); ?>" /></p>
    <?php
}

function jbl_save_metabox($post_id) {
    if (!isset($_POST['jbl_nonce']) || !wp_verify_nonce($_POST['jbl_nonce'], 'jbl_save_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

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
    foreach ($map as $key => $cb) {
        if (isset($_POST["jbl_$key"])) {
            update_post_meta($post_id, "jbl_$key", call_user_func($cb, $_POST["jbl_$key"]));
        }
    }
}
add_action('save_post_job', 'jbl_save_metabox');