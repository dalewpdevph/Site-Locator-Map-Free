<?php
/**
 * The template for displaying taxonomy pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package wpdevph
 */

get_header(); 
global $sitelocatormaptype, $wp_query;
?>

	<section id="primary" class="content-area">
		<div id="content" class="site-content entry-content" role="main">



			<header class="page-header">
				<h1 class="page-title">
					
					<?php
					$term =	$wp_query->queried_object;
					echo $term->name;
					?>
				</h1>

			</header><!-- .page-header -->
		
		<?php

		$sitelocatormaptype->the_map();
		
		if(have_posts()) { 
			
			while ( have_posts() ) { the_post();

				site_locator_map_get_template_part("location","content");
				
			}
			
			// Regular codes pagination
			slm_paginate();

		}
				?>

		</div><!-- #content -->
	</section><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>