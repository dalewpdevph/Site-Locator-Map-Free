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
					
					<?php the_title(); ?>
				</h1>

			</header><!-- .page-header -->
		
		<?php

		slm_map();

		if(have_posts()) { 
			
			while ( have_posts() ) { the_post();

				?>
				<div class="slm-entry">
					<h2><?php the_title(); ?></h2>
					<h4><?php the_slm_field("address"); ?></h4>
					<div class="slm-entry-content">
						<?php the_content(); ?>
					</div>
					<?php
						slm_display_fields();
					?>

				</div>
				<?php			
			
			}

		}
				?>

		</div><!-- #content -->
	</section><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>