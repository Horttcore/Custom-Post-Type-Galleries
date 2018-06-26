<?php
/**
 *
 *  Custom Post Type Produts
 *
 */
final class Custom_Post_Type_Galleries
{



    /**
     * Plugin constructor
     *
     * @access public
     * @return void
     * @since 0.1
     * @author Ralf Hortt <me@horttcore.de>
     **/
    public function __construct()
    {

        add_action( 'custom-post-type-galleries-widget-output', 'Custom_Post_Type_Galleries_Widget::widget_output', 10, 3 );
        add_action( 'custom-post-type-galleries-widget-loop-output', 'Custom_Post_Type_Galleries_Widget::widget_loop_output', 10, 3 );
        add_action( 'init', array( $this, 'register_post_type' ) );
        add_action( 'init', array( $this, 'register_taxonomy' ) );
        add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
        add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
        add_filter( 'widgets_init', array( $this, 'widgets_init' ) );
        add_action( 'attachments_register', [$this, 'register_attachments'] );


    } // END __construct



    /**
     * Load plugin translation
     *
     * @access public
     * @return void
     * @author Ralf Hortt <me@horttcore.de>
     * @since 0.1
     **/
    public function load_plugin_textdomain()
    {

        load_plugin_textdomain( 'custom-post-type-galleries', false, dirname( plugin_basename( __FILE__ ) ) . '/../languages/'  );

    } // END load_plugin_textdomain



    /**
     * Alter default query
     *
     * @param WP_Query $query WP Query object
     * @return void
     */
    public function pre_get_posts( $query )
    {

        if ( !is_post_type_archive( 'gallery' ) )
            return;

        if ( !$query->is_main_query() )
            return;

        if ( $query->get( 'order' ) )
            return;

        $query->set( 'orderby', ['menu_order' => 'ASC', 'title' => 'ASC'] );

    }

    
    /**
     *
     * Register attachments
     *
     * @access public
     * @return void
     * @since 0.2
     * @author Ralf Hortt <me@horttcore.de>
     */
    public function register_attachments( $attachments )
    {

        $attachments->register('gallery', [
            'label' => 'Gallery',
            'post_type' => ['gallery'],
            'position' => 'normal',
            'priority' => 'high',
            'filetype' => 'image',
            'button_text' => __('Select'),
            'modal_text' => __('Select'),
            'fields' => [],
        ]);


    } // END register_post_type


    /**
     *
     * Register post type
     *
     * @access public
     * @return void
     * @since 0.1
     * @author Ralf Hortt <me@horttcore.de>
     */
    public function register_post_type()
    {

        register_post_type( 'gallery', array(
            'labels' => array(
                'name' => _x( 'Galleries', 'post type general name', 'custom-post-type-galleries' ),
                'singular_name' => _x( 'Gallery', 'post type singular name', 'custom-post-type-galleries' ),
                'add_new' => _x( 'Add New', 'Gallery', 'custom-post-type-galleries' ),
                'add_new_item' => __( 'Add New Gallery', 'custom-post-type-galleries' ),
                'edit_item' => __( 'Edit Gallery', 'custom-post-type-galleries' ),
                'new_item' => __( 'New Gallery', 'custom-post-type-galleries' ),
                'view_item' => __( 'View Gallery', 'custom-post-type-galleries' ),
                'search_items' => __( 'Search Gallery', 'custom-post-type-galleries' ),
                'not_found' =>  __( 'No Galleries found', 'custom-post-type-galleries' ),
                'not_found_in_trash' => __( 'No Galleries found in Trash', 'custom-post-type-galleries' ),
                'parent_item_colon' => '',
                'menu_name' => __( 'Galleries', 'custom-post-type-galleries' )
            ),
            'public' => TRUE,
            'publicly_queryable' => TRUE,
            'show_ui' => TRUE,
            'show_in_menu' => TRUE,
            'query_var' => TRUE,
            'rewrite' => array(
                'slug' => _x( 'galleries', 'Post Type Slug', 'custom-post-type-galleries' ),
                'with_front' => FALSE,
            ),
            'capability_type' => 'post',
            'has_archive' => TRUE,
            'hierarchical' => FALSE,
            'menu_position' => NULL,
            'menu_icon' => 'dashicons-format-gallery',
            'supports' => array( 'title', 'editor', 'thumbnail', 'page-attributes' )
        ));

    } // END register_post_type



    /**
     *
     * Register taxonomy
     *
     * @access public
     * @return void
     * @since 0.1
     * @author Ralf Hortt <me@horttcore.de>
     */
    public function register_taxonomy()
    {

        register_taxonomy( 'gallery-category', array( 'gallery' ), array(
            'hierarchical' => TRUE,
            'labels' => array(
                'name' => _x( 'Gallery Categories', 'taxonomy general name', 'custom-post-type-galleries' ),
                'singular_name' => _x( 'Gallery Category', 'taxonomy singular name', 'custom-post-type-galleries' ),
                'search_items' =>  __( 'Search Gallery Categories', 'custom-post-type-galleries' ),
                'all_items' => __( 'All Gallery Categories', 'custom-post-type-galleries' ),
                'parent_item' => __( 'Parent Gallery Category', 'custom-post-type-galleries' ),
                'parent_item_colon' => __( 'Parent Gallery Category:', 'custom-post-type-galleries' ),
                'edit_item' => __( 'Edit Gallery Category', 'custom-post-type-galleries' ),
                'update_item' => __( 'Update Gallery Category', 'custom-post-type-galleries' ),
                'add_new_item' => __( 'Add New Gallery Category', 'custom-post-type-galleries' ),
                'new_item_name' => __( 'New Gallery Category Name', 'custom-post-type-galleries' ),
                'menu_name' => __( 'Gallery Categories', 'custom-post-type-galleries' ),
            ),
            'show_ui' => TRUE,
            'query_var' => TRUE,
            'rewrite' => array( 'slug' => _x( 'gallery-category', 'Gallery Category Slug', 'custom-post-type-galleries' ) ),
            'show_admin_column' => TRUE,
        ));

    } // END register_taxonomy



    /**
     * Register widgets
     *
     * @access public
     * @return void
     * @since 0.5.0
     * @author Ralf Hortt <me@horttcore.de>
     **/
    public function widgets_init()
    {

        register_widget( 'Custom_Post_Type_Galleries_Widget' );

    } // END widgets_init



} // END final class Custom_Post_Type_Galleries

new Custom_Post_Type_Galleries;
