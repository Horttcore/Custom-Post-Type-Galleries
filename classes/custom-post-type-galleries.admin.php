<?php
/**
 *
 *  Custom Post Type Produts
 *
 */
class Custom_Post_Type_Galleries_Admin
{


	/**
	 * Plugin constructor
	 *
	 * @access public
	 * @return void
	 * @since v0.3
	 * @author Ralf Hortt <me@horttcore.de>
	 **/
	public function __construct()
	{

		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );

	} // END __construct


	/**
	 * Update messages
	 *
	 * @access public
	 * @param array $messages Messages
	 * @return array Messages
	 * @since v0.3
	 * @author Ralf Hortt <me@horttcore.de>
	 **/
	public function post_updated_messages( $messages )
	{

		$post             = get_post();
		$post_type        = 'gallery';
		$post_type_object = get_post_type_object( $post_type );

		$messages[$post_type] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Gallery updated.', 'custom-post-type-galleries' ),
			2  => __( 'Custom field updated.' ),
			3  => __( 'Custom field deleted.' ),
			4  => __( 'Gallery updated.', 'custom-post-type-galleries' ),
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Gallery restored to revision from %s', 'custom-post-type-galleries' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Gallery published.', 'custom-post-type-galleries' ),
			7  => __( 'Gallery saved.', 'custom-post-type-galleries' ),
			8  => __( 'Gallery submitted.', 'custom-post-type-galleries' ),
			9  => sprintf( __( 'Gallery scheduled for: <strong>%1$s</strong>.', 'custom-post-type-galleries' ), date_i18n( __( 'M j, Y @ G:i', 'custom-post-type-galleries' ), strtotime( $post->post_date ) ) ),
			10 => __( 'Gallery draft updated.', 'custom-post-type-galleries' )
		);

		if ( $post_type_object->publicly_queryable ) :

			$permalink = get_permalink( $post->ID );

			$view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View gallery', 'custom-post-type-galleries' ) );
			$messages[ $post_type ][1] .= $view_link;
			$messages[ $post_type ][6] .= $view_link;
			$messages[ $post_type ][9] .= $view_link;

			$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
			$preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview gallery', 'custom-post-type-galleries' ) );
			$messages[ $post_type ][8]  .= $preview_link;
			$messages[ $post_type ][10] .= $preview_link;

		endif;

		return $messages;

	} // END post_updated_messages


} // END class Custom_Post_Type_Galleries_Admin

new Custom_Post_Type_Galleries_Admin;
