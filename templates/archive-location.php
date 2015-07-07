<?php
/**
 * The template for displaying Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package wpdevph
 */

get_header(); ?>

	<section id="primary" class="content-area">
		<div id="content" class="site-content entry-content" role="main">



			<header class="page-header">
				<h1 class="page-title">
					
					Locations
				</h1>

			</header><!-- .page-header -->
		<div class='site-locator-map-wrap'>
		<?php

		
		slm_result_message();
		slm_map();
		
		slm_search_form();
		
		if(have_posts()) { 
			?>
			<div class="slm-lists"> <!-- Required div for ajax -->
			<?php
			while ( have_posts() ) { the_post();

				site_locator_map_get_template_part("location","content");
				
			}
			?>
			</div>
			<?php
			// Regular codes pagination
			slm_paginate();

		}
		?>
		</div>
		</div><!-- #content -->
	</section><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>