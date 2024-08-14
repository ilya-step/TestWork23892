<?php
/**
 * The template for displaying all single posts.
 *
 * @package storefront
 */

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

		<?php
		while ( have_posts() ) :
			the_post();

			do_action( 'storefront_single_post_before' );

			get_template_part( 'content', 'single' );



		// Получаем ID текущей записи
		$post_id = get_the_ID();

		// Получаем текущую температуру
		$current_temperature = get_city_weather_data($post_id);

		if ($current_temperature !== false): ?>
		<p>Текущая температура: <?php echo $current_temperature['temperature']; ?> °C</p>
		<?php else: ?>
		<p>Не удалось получить данные о температуре.</p>
		<?php endif;





		do_action( 'storefront_single_post_after' );

		endwhile; // End of the loop.
		?>
	</main><!-- #main -->
</div><!-- #primary -->

<?php
do_action( 'storefront_sidebar' );
get_footer();