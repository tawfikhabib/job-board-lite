<?php
if (!defined('ABSPATH')) exit;

function jbl_register_cpt_and_tax() {
    // Job CPT
    register_post_type('job', [
        'labels' => [
            'name' => __('Jobs', 'job-board-lite'),
            'singular_name' => __('Job', 'job-board-lite'),
            'add_new_item' => __('Add New Job', 'job-board-lite'),
            'edit_item' => __('Edit Job', 'job-board-lite'),
        ],
        'public' => true,
        'menu_icon' => 'dashicons-id',
        'has_archive' => true,
        'rewrite' => ['slug' => 'jobs'],
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
        'show_in_rest' => true,
    ]);

    // Job Type taxonomy (full-time, part-time, contract)
    register_taxonomy('job_type', 'job', [
        'labels' => [
            'name' => __('Job Types', 'job-board-lite'),
            'singular_name' => __('Job Type', 'job-board-lite'),
        ],
        'public' => true,
        'hierarchical' => false,
        'rewrite' => ['slug' => 'job-type'],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'jbl_register_cpt_and_tax');