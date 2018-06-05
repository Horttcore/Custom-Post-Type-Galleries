<?php
/**
 * Widget
 *
 * @author Ralf Hortt
 */
if ( !class_exists( 'Custom_Post_Type_Galleries_Widget' ) ) :
class Custom_Post_Type_Galleries_Widget extends WP_Widget {



	/**
	 * Constructor
	 *
	 * @access public
	 * @since 0.5.0
	 * @author Ralf Hortt <me@horttcore.de>
	 */
	public function __construct()
	{

		$widget_ops = array(
			'classname' => 'widget-galleries',
			'description' => __( 'Displays a gallery loop', 'custom-post-type-galleries' ),
		);
		$control_ops = array( 'id_base' => 'widget-galleries' );
		parent::__construct( 'widget-galleries', __( 'Galleries', 'custom-post-type-galleries' ), $widget_ops, $control_ops );

	} // END __construct



	/**
	 * Widget settings
	 *
	 * @access public
	 * @param array $instance Widget instance
	 * @since 0.5.0
	 * @author Ralf Hortt <me@horttcore.de>
	 */
	public function form( $instance )
	{

		?>

		<p>
			<label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label><br>
			<input class="large-text" type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" id="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php if ( isset( $instance['title'] ) ) echo esc_attr( $instance['title'] ) ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_name( 'orderby' ); ?>"><?php _e( 'Order By:', 'custom-post-type-galleries' ); ?></label><br>
			<select class="large-text" name="<?php echo $this->get_field_name( 'orderby' ); ?>" id="<?php echo $this->get_field_name( 'orderby' ); ?>">
				<option <?php selected( $instance['orderby'], '' ) ?> value=""><?php _e( 'None', 'custom-post-type-galleries' ); ?></option>
				<option <?php selected( $instance['orderby'], 'ID' ) ?> value="ID"><?php _e( 'ID', 'custom-post-type-galleries' ); ?></option>
				<option <?php selected( $instance['orderby'], 'title' ) ?> value="title"><?php _e( 'Title', 'custom-post-type-galleries' ); ?></option>
				<option <?php selected( $instance['orderby'], 'date' ) ?> value="date"><?php _e( 'Publishing date', 'custom-post-type-galleries' ); ?></option>
				<option <?php selected( $instance['orderby'], 'menu_order' ) ?> value="menu_order"><?php _e( 'Menu order', 'custom-post-type-galleries' ); ?></option>
				<option <?php selected( $instance['orderby'], 'rand' ) ?> value="rand"><?php _e( 'Random', 'custom-post-type-galleries' ); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_name( 'order' ); ?>"><?php _e( 'Order:' ); ?></label><br>
			<select class="large-text" name="<?php echo $this->get_field_name( 'order' ); ?>" id="<?php echo $this->get_field_name( 'order' ); ?>">
				<option <?php selected( $instance['order'], 'ASC') ?> value="ASC"><?php _e( 'Ascending', 'custom-post-type-galleries' ); ?></option>
				<option <?php selected( $instance['order'], 'DESC') ?> value="DESC"><?php _e( 'Descending', 'custom-post-type-galleries' ); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_name( 'limit' ); ?>"><?php _e( 'Count:', 'custom-post-type-galleries' ); ?></label><br>
			<input class="large-text" type="number" name="<?php echo $this->get_field_name( 'limit' ); ?>" id="<?php echo $this->get_field_name( 'limit' ); ?>" value="<?php if ( isset( $instance['limit'] ) )  echo esc_attr( $instance['limit'] ) ?>">
		</p>

		<?php
		$category_dropdown = wp_dropdown_categories(array(
			'show_option_all' => __( 'All', 'custom-post-type-galleries' ),
			'taxonomy' => 'gallery-category',
			'name' => $this->get_field_name( 'gallery-category' ),
			'selected' => isset( $instance['gallery-category'] ) ? $instance['gallery-category'] : '',
			'hide_if_empty' => TRUE,
			'hide_empty' => FALSE,
			'hierarchical' => TRUE,
			'echo' => FALSE
		));

		if ( $category_dropdown ) :

			?>

			<p>
				<label for="<?php echo $this->get_field_name( 'gallery-category' ); ?>"><?php _e( 'Category' ); ?></label><br>
				<?php echo $category_dropdown ?>
			</p>

			<?php

		endif;

	} // END form



	/**
	 * Save widget settings
	 *
	 * @access public
	 * @param array $new_instance New widget instance
	 * @param array $old_instance Old widget instance
	 * @since 0.5.0
	 * @author Ralf Hortt <me@horttcore.de>
	 */
	public function update( $new_instance, $old_instance )
	{

		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['orderby'] = $new_instance['orderby'];
		$instance['order'] = $new_instance['order'];
		$instance['limit'] = $new_instance['limit'];

		$instance['gallery-category'] = ( isset( $new_instance['gallery-category'] ) ) ? $new_instance['gallery-category'] : FALSE;

		return $instance;

	} // END update



	/**
	 * Output
	 *
	 * @access public
	 * @param array $args Arguments
	 * @param array $instance Widget instance
	 * @since 0.5.0
	 * @author Ralf Hortt <me@horttcore.de>
	 */
	public function widget( $args, $instance )
	{

		$query = array(
			'post_type' => 'gallery',
			'showposts' => $instance['limit'],
			'orderby' => $instance['orderby'],
			'order' => $instance['order'],
		);

		if ( 0 != $instance['gallery-category'] ) :
			$query['tax_query'] = array(
				array(
					'taxonomy' => 'gallery-category',
					'field' => 'term_id',
					'terms' => $instance['gallery-category'],
				)
			);
		endif;

		$query = apply_filters( 'custom-post-type-galleries-widget-query', $query, $args, $instance );

		$query = new WP_Query( $query );

		if ( $query->have_posts() ) :

			/**
			 * Widget output
			 *
			 * @param array $args Arguments
			 * @param array $instance Widget instance
			 * @param obj $query WP_Query object
			 * @hooked Custom_Post_Type_Widget::widget_output - 10
			 */
			do_action( 'custom-post-type-galleries-widget-output', $args, $instance, $query );

		endif;

		wp_reset_query();

	} // END widget



	/**
	 * Widget loop output
	 *
	 * @static
	 * @access public
	 * @param array $args Arguments
	 * @param array $instance Widget instance
	 * @param obj $query WP_Query object
	 * @author Ralf Hortt <me@horttcore.de>
	 * @since 0.5.0
	 **/
	static public function widget_loop_output( $args, $instance, $query )
	{

		get_template_part('views/content-archive', get_post_type() );

	} // END widget_loop_output



	/**
	 * Widget output
	 *
	 * @static
	 * @access public
	 * @param array $args Arguments
	 * @param array $instance Widget instance
	 * @param obj $query WP_Query object
	 * @author Ralf Hortt <me@horttcore.de>
	 * @since 0.5.0
	 **/
	static public function widget_output( $args, $instance, $query )
	{

		echo $args['before_widget'];

		echo $args['before_title'] . $instance['title'] . $args['after_title'];

		?>

		<section class="gallery-list">

			<?php

			while ( $query->have_posts() ) : $query->the_post();

				/**
				 * Loop output
				 *
				 * @param array $args Arguments
				 * @param array $instance Widget instance
				 * @param obj $query WP_Query object
				 * @hooked Custom_Post_Type::widget_loop_output - 10
				 */
				do_action( 'custom-post-type-galleries-widget-loop-output', $args, $instance, $query );

			endwhile;

			?>

		</section><!-- .gallery-list -->

		<?php

		echo $args['after_widget'];

	} // END widget_output



} // END final class Custom_Post_Type_Galleries_Widget

endif;
