<?php

/*
 * Register Widgets areas.
 */

function wpgrade_register_sidebars() {

	register_sidebar(array(
		'id' => 'sidebar1',
		'name' => __('Sidebar 1', wpGrade_txtd),
		'description' => __('The first (primary) sidebar.', wpGrade_txtd),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
	));
	register_sidebar(array(
		'id' => 'pagesidebar',
		'name' => __('Page Sidebar', wpGrade_txtd),
		'description' => __('The sidebar that will be used for pages.', wpGrade_txtd),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
	));
	register_sidebar(array(
		'id' => 'footerleft',
		'name' => __('Footer Left', wpGrade_txtd),
		'description' => __('Widget Area on the left side of the footer.', wpGrade_txtd),
		'before_widget' => '<div id="%1$s" class="widget widget_footer widget_footerleft %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widget-title widget-title_footer widget-title_footerleft">',
		'after_title' => '</h4>',
	));
    register_sidebar(array(
        'id' => 'footerright',
        'name' => __('Footer Right', wpGrade_txtd),
        'description' => __('Widget Area on the right side of the footer.', wpGrade_txtd),
        'before_widget' => '<div id="%1$s" class="widget widget widget_footer widget_footerright %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widget-title widget-title_footer widget-title_footerright">',
        'after_title' => '</h4>',
    ));

}
add_action('widgets_init', 'wpgrade_register_sidebars');

/*
 * Register custom widgets.
 */

/*
 * The social icons widget
 */
class wpgrade_social_links_widget extends WP_Widget {

	public function __construct()
	{
		parent::__construct( 'wpgrade_social_links', __( WPGRADE_THEMENAME .' Social Links', wpGrade_txtd ), array('description' => __( "Display the social links defined in the theme's options", wpGrade_txtd )) );
	}

	function widget($args, $instance) {
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		global $wpGrade_Options;

		$social_links = $wpGrade_Options->get('social_icons');
		$target = '';
		if ( $wpGrade_Options->get('social_icons_target_blank') ) {
			$target = 'target="_blank"';
		}
		// Reset Post Data
		wp_reset_postdata();

		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;
			if (count($social_links)): ?>
				<ul class="menu-footer_social">
					<?php foreach ($social_links as $domain => $value): if ($value): ?>
						<li class="footer-social-link">
							<a href="<?php echo $value ?>"<?php echo $target ?>>
								<?php switch($domain) {
									case 'youtube':
										?><i class="shc icon-e-play"></i>
										<?php break;
									default:
										?><i class="shc icon-e-<?php echo $domain; ?>"></i>
										<?php } ?>
							</a>
						</li>
					<?php endif; endforeach ?>
				</ul>
			<?php endif;
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	function form($instance) {
		!empty($instance['title'])  ? $title = esc_attr($instance['title']) : $title = __('We Are Social',wpGrade_txtd); ?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', wpGrade_txtd); ?>:</label>
			<input id="<?php echo $this->get_field_id('title'); ?>" class="widefat" type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
		</p>
	<?php }
}

class wpgrade_dribbble_widget extends WP_Widget {
	
	public function __construct()
	{
		parent::__construct( 'wpgrade_dribbble_widget', __( WPGRADE_THEMENAME .' Dribbble Widget',wpGrade_txtd), array('description' => __('Display Dribbble images in your sidebar or footer', wpGrade_txtd)) );
	}

	function widget($args, $instance) {
		extract( $args );
		$title 		= apply_filters('widget_title', $instance['title']);
		$username 	= $instance['username'];

	    require_once('vendor/dribbble/src/Dribbble/Dribbble.php');
	    require_once('vendor/dribbble/src/Dribbble/Exception.php');
	    $dribbble = new Dribbble();

		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;

		try {
			//limit the number of images
			$count = isset( $instance['count'] ) ? absint( $instance['count'] ) : 4;

		    $my_shots = $dribbble->getPlayerShots($username, 0, $count);
			// $my_shots = array_slice( $my_shots, 0, $count );
			
		    echo '<ul class="wpgrade-dribbble-items">';
		    
		    foreach ($my_shots->shots as $shot) {
		        echo '<li class="wpgrade-dribbble-item"><a class="wpgrade-dribbble-link" href="' . $shot->url . '"><img src="' . $shot->image_teaser_url . '" alt="' . $shot->title . '"></a></li>';
		    }
			
		    echo '</ul>';
		}
		catch (DribbbleException $e) {
		    echo 'Upss... Something is wrong. Check the widget settings.';
		}

		echo $after_widget;
	}
	
	/**
	 * Validate and update widget options.
	 */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['username'] = strip_tags($new_instance['username']);
		$instance['count'] = absint( $new_instance['count'] );
		return $instance;
	}

	function form($instance) {
		$title = isset( $instance['title'] ) ? $instance['title'] : __('Dribbble shots',wpGrade_txtd);
		$username = isset ($instance['username']) ? esc_attr($instance['username']) : '';
		//default to 8 images
		$count = isset( $instance['count'] ) ? absint( $instance['count'] ) : 4;
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', wpGrade_txtd); ?>:</label>
			<input id="<?php echo $this->get_field_id('title'); ?>" class="widefat" type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('username'); ?>"><?php _e('Dribbble username', wpGrade_txtd); ?>:</label>
			<input id="<?php echo $this->get_field_id('username'); ?>" class="widefat" type="text" name="<?php echo $this->get_field_name('username'); ?>" value="<?php echo $username; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Number of images',wpGrade_txtd); ?>:</label><br />
			<input type="number" min="1" max="20" value="<?php echo esc_attr( $count ); ?>" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" />
		</p>
		<?php
	}
}

class wpgrade_instagram_widget extends WP_Widget {
	
	public function __construct()
	{
		parent::__construct( 'wpgrade_instagram_widget', __( WPGRADE_THEMENAME .' Instagram Widget',wpGrade_txtd), array('description' => __('Display Instagram images in your sidebar or footer', wpGrade_txtd)) );
	}

	function widget($args, $instance) {
		extract( $args );
		$title 		= apply_filters('widget_title', $instance['title']);
		$username 	= $instance['username'];
		
		wp_register_script( 'instagram_photostream', WPGRADE_SCRIPT_URL."instagram_photostream_widget.js", array('jquery'), WPGRADE_VERSION, true );
        wp_enqueue_script( 'instagram_photostream' );

		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;

		//limit the number of images
		$count = isset( $args['count'] ) ? absint( $args['count'] ) : 8;
		
		$unique_id =  $username . $count ;
		$unique_id = preg_replace("/[^A-Za-z0-9]/", '', $unique_id);
		$html = '<ul id="' . $unique_id  .'" class="wpgrade-instagram-items"></ul>';
		$html .= '<script type="text/javascript"> jQuery(document).ready(function($){ ';
		$html .= '$("#' . $unique_id .'").instagram_photostream({user: "' . $username . '", limit:' . $count . '});';
		$html .= '});</script>';
		echo $html;

		echo $after_widget;
	}
	
	/**
	 * Validate and update widget options.
	 */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['username'] = strip_tags($new_instance['username']);
		$instance['count'] = absint( $new_instance['count'] );
		return $instance;
	}

	function form($instance) {
		$title = isset( $instance['title'] ) ? $instance['title'] : __('Instagram shots',wpGrade_txtd);
		$username = isset ($instance['username']) ? esc_attr($instance['username']) : '';
		//default to 8 images
		$count = isset( $instance['count'] ) ? absint( $instance['count'] ) : 8;
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', wpGrade_txtd); ?>:</label>
			<input id="<?php echo $this->get_field_id('title'); ?>" class="widefat" type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('username'); ?>"><?php _e('Instagram username', wpGrade_txtd); ?>:</label>
			<input id="<?php echo $this->get_field_id('username'); ?>" class="widefat" type="text" name="<?php echo $this->get_field_name('username'); ?>" value="<?php echo $username; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Number of images',wpGrade_txtd); ?>:</label><br />
			<input type="number" min="1" max="20" value="<?php echo esc_attr( $count ); ?>" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" />
		</p>
		<?php
	}
}

class wpgrade_contact_widget extends WP_Widget {

    public function __construct()
    {
        parent::__construct( 'wpgrade_contact_widget', __( WPGRADE_THEMENAME .' Contact Widget',wpGrade_txtd), array('description' => __('Display your contact information', wpGrade_txtd)) );
    }

    function widget($args, $instance) {
        extract( $args );
        $title 		= apply_filters('widget_title', $instance['title']);
        $social_label = $instance['social_label'];
        $social_name = $instance['social_name'];
        $social_link = $instance['social_link'];

        echo $before_widget;
        if ( $title ) echo $before_title . $title . $after_title;
        global $wpGrade_Options;
        $contactinfo = array();
        $contactinfo['address'] = $wpGrade_Options->get('contact_address');
        $contactinfo['phone'] = $wpGrade_Options->get('contact_phone');
        $contactinfo['email'] = $wpGrade_Options->get('contact_email');
        $contactinfo['social'] = array(
            'label' => $social_label,
            'name' => $social_name,
            'link' => $social_link,
        );

        if (count($contactinfo) > 0) {
            echo '<ul class="widget-contact-details">';
            foreach ($contactinfo as $info => $value) {
                echo '<li class="widget-contact-detail">';
                    switch ($info) {
                        case 'email':
                            echo '<span class="widget-contact-value"><a href="mailto:'. $value .'">'. $value .'</a></span>';
                            break;
                        case 'social':
                            echo '<span class="widget-contact-value"><a href="'.$value['link'].'" target="_blank">'. $value['name'] .'</a></span>';
                            break;
                        default:
                            echo '<span class="widget-contact-value">'. $value .'</span>';
                            break;
                    }
                echo '</li>';
            }
            echo '</ul>';
        } else {
            _e('No contact info to be displayed', wpGrade_txtd);
        }

        echo $after_widget;
    }

    /**
     * Validate and update widget options.
     */
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['social_label'] = strip_tags($new_instance['social_label']);
        $instance['social_name'] = strip_tags($new_instance['social_name']);
        $instance['social_link'] = strip_tags($new_instance['social_link']);
        return $instance;
    }

    function form($instance) {
        $title = isset( $instance['title'] ) ? $instance['title'] : __('Contact us',wpGrade_txtd);
        $social_label = isset( $instance['social_label'] ) ? $instance['social_label'] : '';
        $social_name = isset( $instance['social_name'] ) ? $instance['social_name'] : '';
        $social_link = isset( $instance['social_link'] ) ? $instance['social_link'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', wpGrade_txtd); ?>:</label>
            <input id="<?php echo $this->get_field_id('title'); ?>" class="widefat" type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
        </p>
        <h4>Display a social link (optional)</h4>
        <p>
            <label for="<?php echo $this->get_field_id('social_label'); ?>"><?php _e('Label', wpGrade_txtd); ?>:</label>
            <input id="<?php echo $this->get_field_id('social_label'); ?>" class="widefat" type="text" name="<?php echo $this->get_field_name('social_label'); ?>" value="<?php echo $social_label; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('social_name'); ?>"><?php _e('Name', wpGrade_txtd); ?>:</label>
            <input id="<?php echo $this->get_field_id('social_name'); ?>" class="widefat" type="text" name="<?php echo $this->get_field_name('social_name'); ?>" value="<?php echo $social_name; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('social_link'); ?>"><?php _e('Link', wpGrade_txtd); ?>:</label>
            <input id="<?php echo $this->get_field_id('social_link'); ?>" class="widefat" type="text" name="<?php echo $this->get_field_name('social_link'); ?>" value="<?php echo $social_link; ?>" />
        </p>
    <?php
    }
}

/*
 * Display the tag cloud
 */
function custom_tag_cloud_widget($args)
{
	$args['number'] = 0; //adding a 0 will display all tags
	$args['largest'] = 19; //largest tag
	$args['smallest'] = 19; //smallest tag
	$args['unit'] = 'px'; //tag font unit
	$args['format'] = 'list'; //ul with a class of wp-tag-cloud
	return $args;
}
add_filter( 'widget_tag_cloud_args', 'custom_tag_cloud_widget' );

/*
 * Display the Latest Posts Slider
 */
class wpgrade_posts_slider_widget extends WP_Widget {
	
	public function __construct()
	{
		parent::__construct( 'wpgrade_posts_slider_widget', __('Fuse Latest Posts Slider',wpGrade_txtd), array('description' => __('Display the latest blog posts in your sidebar or footer', wpGrade_txtd)) );
	}

	function widget($args, $instance) {
		extract( $args );
		$title 		= apply_filters('widget_title', $instance['title']);
		// default to 4 posts
		$number 	= isset( $instance['number'] ) ? absint( $instance['number'] ) : 4;

		// The Query
		$options = array( 'posts_per_page' => $number );
		$latest_posts = new WP_Query( $options );

		// Reset Post Data
		wp_reset_postdata();

		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;
		if ( $latest_posts->have_posts() ): ?>
			<div class="latest-posts-slider wp-slider">
				<ul class="latest-posts slides">
					<?php while ( $latest_posts->have_posts() ) : $latest_posts->the_post(); ?>
						<li class="latest-posts-post slide">
							<a class="latest-posts-permalink" href="<?php the_permalink(); ?>">
								<h4 class="latest-posts-title"><?php the_title(); ?></h4>
								<div class="latest-posts-date"><?php the_time('F j, Y'); ?></div>
							</a>
						</li>
					<?php endwhile; ?>
				</ul>
			</div>
		<?php endif;
		echo $after_widget;
		
		wp_reset_query();
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = absint($new_instance['number']);
		return $instance;
	}

	function form($instance) {
		!empty($instance['title'])  ? $title = esc_attr($instance['title']) : $title = __('From the Blog',wpGrade_txtd);
		// default to 4 posts
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 4; ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">
				<?php __('Title:', wpGrade_txtd); ?>
			</label>
			<input id="<?php echo $this->get_field_id('title'); ?>" class="widefat" type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>">
				<?php __('Number of posts:', wpGrade_txtd); ?>
			</label>
			<input id="<?php echo $this->get_field_id('number'); ?>" class="widefat" type="number" name="<?php echo $this->get_field_name('number'); ?>" value="<?php echo $number; ?>" />
		</p>
		<?php
	}
}

/*
 * Display a Flickr photo stream
 */
class wpgrade_flickr_widget extends WP_Widget
{
	public function __construct()
	{
		parent::__construct( 'wpgrade_flickr_widget', __( WPGRADE_THEMENAME .' Flickr Widget',wpGrade_txtd), array('description' => __('Display Flickr images in your sidebar or footer (maximum 20 but we recommend less).',wpGrade_txtd),) );
	}

	/**
	 * The widget contents
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];
		if ( ! empty( $title ) )
		{
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$photos = $this->get_photos( array(
			'username' => $instance['username'],
			'count' => $instance['count'],
			'tags' => $instance['tags'],
		) );

		if ( is_wp_error( $photos ) )
		{
			echo $photos->get_error_message();
		}
		else
		{
			echo '<ul class="wpgrade-flickr-items">';
			foreach ( $photos as $photo )
			{
				$link = esc_url( $photo->link );
				$src = esc_url($this->resize($photo->media->m));
				$title = esc_attr( $photo->title );
				$item = sprintf( '<a class="wpgrade-flickr-link" href="%s"><img src="%s" alt="%s" /></a>', $link, $src, $title );
				$item = sprintf( '<li class="wpgrade-flickr-item">%s</li>', $item );
				echo $item;
			}
			echo '</ul>';
		}

		echo $args['after_widget'];
	}

	/**
	 * Returns an array of photos
	 */
	private function get_photos( $args = array() )
	{
		// do some caching to prevent Flickr from banning us
		$transient_key = md5( 'wpgrade-flickr-cache-' . print_r( $args, true ) );
		$cached = get_transient( $transient_key );
		
		//if there is a cached version use that one
		if ( $cached )
		{
			return $cached;
		}

		$username = urlencode(isset( $args['username'] ) ? $args['username'] : '');
		$tags = urlencode(isset( $args['tags'] ) ? $args['tags'] : '');
		$count = isset( $args['count'] ) ? absint( $args['count'] ) : 8;
		$query = array('tagmode' => 'any','tags' => $tags);

		// If username is actually an RSS feed
		if ( preg_match( '#^https?://api\.flickr\.com/services/feeds/photos_public\.gne#', $username ) )
		{
			$url = parse_url( $username );
			$url_query = array();
			wp_parse_str( $url['query'], $url_query );
			$query = array_merge( $query, $url_query );
		}
		else
		{
			$user = $this->request( 'flickr.people.findByUsername', array( 'username' => $username ) );
			if ( is_wp_error( $user ) )
			{
				return $user;
			}

			$user_id = $user->user->id;
			$query['id'] = $user_id;
		}

		$photos = $this->request_feed( 'photos_public', $query );

		if ( ! $photos )
		{
			return new WP_Error( 'error', 'Something went wrong.' );
		}

		$photos = array_slice( $photos, 0, $count );
		//cache the photos for an hour
		set_transient( $transient_key, $photos, apply_filters( 'wpgrade_flickr_widget_cache_timeout', 3600 ) );
		return $photos;
	}

	/**
	 * Make a request to the Flickr API.
	 */
	private function request( $method, $args )
	{
		$args['method'] = $method;
		$args['format'] = 'json';
		// the api key we've aquired from Flickr
		$args['api_key'] = 'cf07a23ac8100c1b53c0fb164941bf62';
		$args['nojsoncallback'] = 1;
		$url = esc_url_raw( add_query_arg( $args, 'http://api.flickr.com/services/rest/' ) );
		
		$response = wp_remote_get( $url );
		if ( is_wp_error( $response ) )
		{
			return false;
		}

		$body = wp_remote_retrieve_body( $response );
 		$obj = json_decode( $body );

		if ( $obj && $obj->stat == 'fail' )
		{
			return new WP_Error( 'error', $obj->message );
		}

		return $obj ? $obj : false;
	}

	/**
	 * Fetch items from the Flickr Feed API.
	 */
	private function request_feed( $feed = 'photos_public', $args = array() )
	{
		$args['format'] = 'json';
		$args['nojsoncallback'] = 1;
		$url = sprintf( 'http://api.flickr.com/services/feeds/%s.gne', $feed );
		$url = esc_url_raw( add_query_arg( $args, $url ) );

		$response = wp_remote_get( $url );
		if ( is_wp_error( $response ) )
		{
			return false;
		}
		
		$body = wp_remote_retrieve_body( $response );
		$body = preg_replace( "#\\\\'#", "\\\\\\'", $body );
 		$obj = json_decode( $body );

		return $obj ? $obj->items : false;

	}
	
	/**
	 * Modify the url to get a new size of the image
	 */
	private function resize($url, $size = 'square')
	{
		$url_array = explode('/', $url);
		//strip the filename
		$photo = array_pop($url_array);

		switch($size)
		{
		  case 'square': $suffix = '_s.';  break;
		  case 'thumbnail': $suffix = '_t.';  break;
		  case 'small': $suffix = '_m.';  break;
		  case 'm640': $suffix = '_z.';  break;
		  case 'm800': $suffix = '_c.';  break;
		  case 's320': $suffix = '_n.';  break; 
		  case 's150': $suffix = '_q.';  break;           
		  case 'large': $suffix = '_b.';  break;
		  default:  $suffix = '.';  break; // Medium
		}
		
		// replace the old size marker with the needed one
		$url_array[] =  preg_replace('/(_(s|t|c|n|q|m|b|z))?\./i', $suffix, $photo);
		return implode('/', $url_array);
	}

	/**
	 * Validate and update widget options.
	 */
	function update( $new_instance, $old_instance )
	{
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['username'] = strip_tags( $new_instance['username'] );
		$instance['tags'] = strip_tags( $new_instance['tags'] );
		$instance['count'] = absint( $new_instance['count'] );
		return $instance;
	}

	/**
	 * Render widget controls.
	 */
	public function form( $instance )
	{
		$title = isset( $instance['title'] ) ? $instance['title'] : __('Flickr Shots',wpGrade_txtd);
		$username = isset( $instance['username'] ) ? $instance['username'] : '';
		$tags = isset( $instance['tags'] ) ? $instance['tags'] : '';
		$count = isset( $instance['count'] ) ? absint( $instance['count'] ) : 8;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title',wpGrade_txtd); ?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'username' ); ?>"><?php _e( 'Username or RSS url',wpGrade_txtd); ?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'username' ); ?>" name="<?php echo $this->get_field_name( 'username' ); ?>" type="text" value="<?php echo esc_attr( $username ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'tags' ); ?>"><?php _e( 'Tags' ,wpGrade_txtd); ?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'tags' ); ?>" name="<?php echo $this->get_field_name( 'tags' ); ?>" type="text" value="<?php echo esc_attr( $tags ); ?>" /><br />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Number of images',wpGrade_txtd); ?>:</label><br />
			<input type="number" min="1" max="20" value="<?php echo esc_attr( $count ); ?>" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" />
		</p>

		<?php
	}
}

class wpgrade_twitter_widget extends WP_Widget {

    public function __construct()
    {

        parent::__construct( 'wpgrade_twitter_widget', __( WPGRADE_THEMENAME . ' Twitter Widget',wpGrade_txtd), array('description' => __('Display Latest Tweets', wpGrade_txtd)) );
    }

    function widget($args, $instance) {

        extract( $args );

        $username 	= $instance['username'];

        echo $before_widget;
        if ( isset($instance['title']) ) echo $before_title . apply_filters('widget_title', $instance['title']) . $after_title;

        $count = isset( $instance['count'] ) ? absint( $instance['count'] ) : 5;

        include_once('vendor/twitter-api/StormTwitter.class.php');

        global $wpGrade_Options;

        $config['key'] = $wpGrade_Options->get('twitter_consumer_key');
        $config['secret'] = $wpGrade_Options->get('twitter_consumer_secret');
        $config['token'] = $wpGrade_Options->get('twitter_oauth_access_token');
        $config['token_secret'] = $wpGrade_Options->get('twitter_oauth_access_token_secret');
        $config['screenname'] = $instance['username'];
        if ( isset($config['cache_expire']) && $config['cache_expire'] < 1) $config['cache_expire'] = 3600;
        $config['directory'] = plugin_dir_path(__FILE__) . 'vendor/twitter-api/cache';

        $twitter = new StormTwitter($config);
        $results = $twitter->getTweets($count, $username);

        $link = 'https://twitter.com/'. $username;

        if ( $results ){
            echo '<ul class="widget-tweets widget-tweets_footer slides">';
            foreach ($results as $key => $result) { ?>
                <li class="widget-tweets-tweet slide">
                    <?php
                    echo '<div class="twitter-tweet-meta">' .
                        '<span class="twitter-screenname">' . ucwords($config['screenname']) . '</span>' .
                        '<span class="twitter-username"><a href="'.$link.'">@' . $config['screenname'] . '</a></span>' .
                        '<span class="widget-tweets-tweet-date">' . wpgrade_convert_twitter_date($result["created_at"]) . '</span></div>'; ?>
                    <div class="widget-tweets-tweet-content"><?php echo $this->get_parsed_tweet($result); ?></div>
                </li>
            <?php
            }
            echo '</ul>';
        }

        echo $after_widget;
    }

    /**
     * Validate and update widget options.
     */
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['username'] = strip_tags($new_instance['username']);
        $instance['count'] = absint( $new_instance['count'] );
        return $instance;
    }

    function form($instance) {
        $title = isset( $instance['title'] ) ? $instance['title'] : __('Tweets',wpGrade_txtd);
        $username = isset ($instance['username']) ? esc_attr($instance['username']) : '';
        $count = isset( $instance['count'] ) ? absint( $instance['count'] ) : 5; ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', wpGrade_txtd); ?>:</label>
            <input id="<?php echo $this->get_field_id('title'); ?>" class="widefat" type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('username'); ?>"><?php _e('Twitter username', wpGrade_txtd); ?>:</label>
            <input id="<?php echo $this->get_field_id('username'); ?>" class="widefat" type="text" name="<?php echo $this->get_field_name('username'); ?>" value="<?php echo $username; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Number of tweets',wpGrade_txtd); ?>:</label><br />
            <input type="number" min="1" max="20" value="<?php echo esc_attr( $count ); ?>" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" />
        </p>
    <?php
    }
	
	function get_parsed_tweet ($tweet) {
		// check if any entites exist and if so, replace then with hyperlinked versions
		$tweet_text = $tweet['text'];
		if (!empty($tweet['entities']['urls']) || !empty($tweet['entities']['hashtags']) || !empty($tweet['entities']['user_mentions'])) {
			foreach ($tweet['entities']['urls'] as $url) {
					$find = $url['url'];
					$replace = '<a href="'.$find.'" target="_blank" rel="nofollow">'.$find.'</a>';
					$tweet_text = str_replace($find,$replace,$tweet_text);
			}

			foreach ($tweet['entities']['hashtags'] as $hashtag) {
					$find = '#'.$hashtag['text'];
					$replace = '<a href="http://twitter.com/#!/search/%23'.$hashtag['text'].'" target="_blank" rel="nofollow">'.$find.'</a>';
					$tweet_text = str_replace($find,$replace,$tweet_text);
			}

			foreach ($tweet['entities']['user_mentions'] as $user_mention) {
					$find = "@".$user_mention['screen_name'];
					$replace = '<a href="http://twitter.com/'.$user_mention['screen_name'].'" target="_blank" rel="nofollow">'.$find.'</a>';
					$tweet_text = str_ireplace($find,$replace,$tweet_text);
			}
		} else {
			//lets try some other way to properly autolink and shit the tweet text
			include_once('vendor/twitter-api/Autolink.php');
			$autolinker = new Twitter_Autolink($tweet_text);

			$tweet_text = $autolinker->addLinks(); 
		}
		
		return $tweet_text;
	}
}

function wpgrade_convert_twitter_date( $time ) {
    $date = strtotime( $time );
	//return util::human_time_diff($date);
	return gbs_relative_time($date);
}

/**
 * Format timestamp into relative date, with proper i18n
 */
function gbs_relative_time( $timestamp ){
                     
	$difference = current_time( 'timestamp' ) - $timestamp;
	
	if ( $difference >= 60*60*24*365 ){        // if more than a year ago
		$int = intval( $difference / ( 60*60*24*365 ) );
		$r = sprintf( _n( '%d year ago', '%d years ago', $int, wpGrade_txtd ), $int );
	} elseif ( $difference >= 60*60*24*7*5 ){  // if more than five weeks ago
		$int = intval( $difference / ( 60*60*24*30 ) );
		$r = sprintf( _n( '%d month ago', '%d months ago', $int, wpGrade_txtd ), $int );
	} elseif ( $difference >= 60*60*24*7 ){        // if more than a week ago
		$int = intval( $difference / ( 60*60*24*7 ) );
		$r = sprintf( _n( '%d week ago', '%d weeks ago', $int, wpGrade_txtd ), $int );
	} elseif ( $difference >= 60*60*24){      // if more than a day ago
		$int = intval( $difference / ( 60*60*24 ) );
		$r = sprintf( _n( '%d day ago', '%d days ago', $int, wpGrade_txtd ), $int );
	} elseif ( $difference >= 60*60 ){         // if more than an hour ago
		$int = intval( $difference / ( 60*60 ) );
		$r = sprintf( _n( '%d hour ago', '%d hours ago', $int, wpGrade_txtd ), $int );
	} elseif ( $difference >= 60 ){            // if more than a minute ago
		$int = intval( $difference / ( 60 ) );
		$r = sprintf( _n( '%d minute ago', '%d minutes ago', $int, wpGrade_txtd ), $int );
	} else {                                // if less than a minute ago
		$r = __( 'moments ago', wpGrade_txtd );
	}

	return $r;
}

// Register the widgets

add_action('widgets_init', create_function('', 'return register_widget("wpgrade_social_links_widget");'));
add_action('widgets_init', create_function('', 'return register_widget("wpgrade_contact_widget");'));
add_action('widgets_init', create_function('', 'return register_widget("wpgrade_dribbble_widget");'));
//add_action('widgets_init', create_function('', 'return register_widget("wpgrade_instagram_widget");'));
add_action('widgets_init', create_function('', 'return register_widget("wpgrade_contact_widget");'));
add_action('widgets_init', create_function('', 'return register_widget("wpgrade_posts_slider_widget");'));
add_action('widgets_init', create_function('', 'return register_widget("wpgrade_flickr_widget");'));
add_action('widgets_init', create_function('', 'return register_widget("wpgrade_twitter_widget");'));