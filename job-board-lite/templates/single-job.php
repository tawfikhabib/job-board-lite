<?php
if (!defined('ABSPATH')) exit;
get_header();
the_post(); ?>
<main class="jbl-single">
  <h1><?php the_title(); ?></h1>
  <div class="jbl-meta">
    <?php
    $company = get_post_meta(get_the_ID(), 'jbl_company', true);
    $location = get_post_meta(get_the_ID(), 'jbl_location', true);
    $salary_min = get_post_meta(get_the_ID(), 'jbl_salary_min', true);
    $salary_max = get_post_meta(get_the_ID(), 'jbl_salary_max', true);
    $apply_url = get_post_meta(get_the_ID(), 'jbl_apply_url', true);
    $apply_email = get_post_meta(get_the_ID(), 'jbl_apply_email', true);
    ?>
    <p><strong><?php echo esc_html($company); ?></strong> — <?php echo esc_html($location); ?></p>
    <?php if ($salary_min || $salary_max): ?>
      <p><?php _e('Salary:', 'job-board-lite'); ?> <?php echo esc_html($salary_min.' - '.$salary_max); ?></p>
    <?php endif; ?>
  </div>
  <article><?php the_content(); ?></article>
  <p>
    <?php if ($apply_url): ?>
      <a class="button" href="<?php echo esc_url($apply_url); ?>" target="_blank" rel="noopener"><?php _e('Apply Online', 'job-board-lite'); ?></a>
    <?php elseif ($apply_email): ?>
      <a class="button" href="mailto:<?php echo antispambot($apply_email); ?>"><?php _e('Apply via Email', 'job-board-lite'); ?></a>
    <?php endif; ?>
  </p>
</main>
<?php get_footer();