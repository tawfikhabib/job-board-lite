<?php
if (!defined('ABSPATH')) exit;

add_action('rest_api_init', function(){
    register_rest_route('jbl/v1', '/jobs', [
        'methods' => 'GET',
        'callback' => function(WP_REST_Request $req) {
            $per_page = min(50, max(1, (int)$req->get_param('per_page') ?: 10));
            $type = sanitize_text_field($req->get_param('type') ?: '');
            $args = [
                'post_type' => 'job',
                'posts_per_page' => $per_page,
                'tax_query' => [],
            ];
            if ($type) {
                $args['tax_query'][] = [
                    'taxonomy' => 'job_type',
                    'field' => 'name',
                    'terms' => $type,
                ];
            }
            $q = new WP_Query($args);
            $data = [];
            while ($q->have_posts()) { $q->the_post();
                $id = get_the_ID();
                $data[] = [
                    'id' => $id,
                    'title' => get_the_title(),
                    'link' => get_permalink(),
                    'company' => get_post_meta($id, 'jbl_company', true),
                    'location' => get_post_meta($id, 'jbl_location', true),
                    'type' => wp_get_post_terms($id, 'job_type', ['fields' => 'names']),
                ];
            }
            wp_reset_postdata();
            return rest_ensure_response($data);
        },
        'permission_callback' => '__return_true',
    ]);
});