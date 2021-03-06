<?php

if( vkExUnit_content_filter_state() == 'content' )  add_filter( 'the_content', 'vkExUnit_add_relatedPosts_html' , 800 , 1 );
else add_action( 'loop_end', 'vkExUnit_add_related_loopend', 800, 1 );


function vkExUnit_add_related_loopend( $query ){
	if( ! $query->is_main_query() ) return;
	echo vkExUnit_add_relatedPosts_html('');
}

function vkExUnit_get_relatedPosts( $post_type = 'post', $taxonomy = 'post_tag', $max_show_posts = 10 ){
	$posts_array = '';
	$post_id = get_the_id();

	$terms = get_the_terms( $post_id, $taxonomy );
	
	if ( ! $terms  || ! is_array( $terms ) ) { return $posts_array; }
	$tags = array();
	foreach ( $terms as $t ) { $tags[] = $t->term_id; }

	$args_base = array(
		'posts_per_page'   => $max_show_posts,
		'offset'           => 0,
		'orderby'          => 'date',
		'order'            => 'DESC',
		'post__not_in'     => array( $post_id ),
		'post_type'        => $post_type,
		'post_status'      => 'publish',
		'suppress_filters' => true,
	);

	$args = $args_base;

	$args['tax_query'] = array(array(
        'taxonomy' => $taxonomy,
        'field' => 'id',
        'terms' => $tags,
        'include_children' => false,
        'operator' => 'AND'
    ) );

	$posts_array = get_posts( $args );

	if ( !is_array( $posts_array ) ) { $posts_array = array(); }

	$post_shortage = $max_show_posts - count( $posts_array );
	if ( $post_shortage > 0 ) {
		$args = $args_base;
		$args['posts_per_page'] = $post_shortage;
		foreach ( $posts_array as $post ) { 
			$args['post__not_in'][] = $post->ID;
		}
		$args['tax_query'] = array( array(
	        'taxonomy' => $taxonomy,
	        'field' => 'id',
	        'terms' => $tags,
	        'include_children' => false,
	        'operator' => 'IN'
	      ) );
		$singletags = get_posts( $args );
		if ( is_array( $singletags ) && count( $singletags ) ) { $posts_array = array_merge( $posts_array, $singletags ); }
	}

	$related_posts = $posts_array;
	return $related_posts;
}

function vkExUnit_add_relatedPosts_html( $content ) {
	if( ! is_single() ) return $content;

	if ( ! is_single() || get_post_type() != 'post' ) { return $content; }

	global $is_pagewidget;
	if ( $is_pagewidget ) { return $content; }

	/*-------------------------------------------*/
	/*  Related posts
	/*-------------------------------------------*/
	$related_posts = vkExUnit_get_relatedPosts();

	if ( !$related_posts ) { return $content; }

	// $posts_count = mb_convert_kana($relatedPostCount, "a", "UTF-8");

	if ( $related_posts ) {
		$relatedPostsHtml = '<!-- [ .relatedPosts ] -->';
		$relatedPostsHtml .= '<aside class="veu_relatedPosts veu_contentAddSection">';
		$relatedPostTitle = apply_filters( 'veu_related_post_title', __( 'Related posts','vkExUnit' ) );
		$relatedPostsHtml .= '<h1 class="mainSection-title">'.$relatedPostTitle.'</h1>';
		$i = 1;
		$relatedPostsHtml .= '<div class="row">';
		foreach ( $related_posts as $key => $post ) {
			$relatedPostsHtml .= '<div class="col-sm-6 relatedPosts_item">';
			$relatedPostsHtml .= '<div class="media">';
			if ( has_post_thumbnail( $post->ID ) ) :
				$relatedPostsHtml .= '<div class="media-left postList_thumbnail">';
				$relatedPostsHtml .= '<a href="'.get_the_permalink( $post->ID ).'">';
				$relatedPostsHtml .= get_the_post_thumbnail( $post->ID,'thumbnail' );
				$relatedPostsHtml .= '</a>';
				$relatedPostsHtml .= '</div>';
			endif;
			$relatedPostsHtml .= '<div class="media-body">';
			$relatedPostsHtml .= '<div class="media-heading"><a href="'.get_the_permalink( $post->ID ).'">'.$post->post_title.'</a></div>';
			$relatedPostsHtml .= '<div><i class="fa fa-calendar"></i>&nbsp;'.get_the_date( false , $post->ID ).'</div>';
			$relatedPostsHtml .= '</div>';
			$relatedPostsHtml .= '</div>';
			$relatedPostsHtml .= '</div>'."\n";
			$i++;
		} // foreach
		$relatedPostsHtml .= '</div>';
		$relatedPostsHtml .= '</aside><!-- [ /.relatedPosts ] -->';
		$content .= $relatedPostsHtml;
	}

	wp_reset_postdata();

	return $content;
}
