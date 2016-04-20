<?php
namespace Cuisine\Front;

/**
 * A few shortcodes to make our lives easier
 */
class Shortcodes {

	/**
	 * Shortcodes instance.
	 *
	 * @var \Cuisine\Front\Shortcodes
	 */
	private static $instance = null;


	/**
	 * Init events & vars
	 */
	function __construct(){

		//setup the events
		$this->setShortcodes();

	}

	/**
	 * Init the Shortcodes class
	 *
	 * @return \Cuisine\View\Shortcodes
	 */
	public static function getInstance(){

	    if ( is_null( static::$instance ) ){
	        static::$instance = new static();
	    }
	    return static::$instance;
	}


	/**
	 * Set the events for this request
	 *
	 * @return void
	 */
	private function setShortcodes(){

		add_shortcode( 'intro', array( &$this, 'makeIntro' ) );
		add_shortcode( 'break', array( &$this, 'makeBreak' ) );
		add_shortcode( 'line', array( &$this, 'makeLine' ) );

		add_shortcode( 'button', array( &$this, 'makeButton' ) );
		add_shortcode( 'link', array( &$this, 'makeLink' ) );

		add_shortcode( 'telephone', array( &$this, 'phoneNumber' ) );
		add_shortcode( 'email', array( &$this, 'mailTo' ) );
	}


	/**
	 * Intro shortcode 
	 * 
	 * @param  array $atts     Attributes
	 * @param  string $content
	 * @return string
	 */
	public function makeIntro( $atts, $content = null ){
		
		$html = '<div class="intro">';

			$html .= do_shortcode( wpautop( $content ) );

		$html .= '</div>';

		return $html;
	}


	/**
	 * Break shortcode 
	 * 
	 * @param  array $atts     Attributes
	 * @param  string $content
	 * @return string
	 */
	public function makeBreak( $atts, $content = null ){

		return '<hr class="break no-line">';

	}


	/**
	 * Line shortcode 
	 * 
	 * @param  array $atts     Attributes
	 * @param  string $content
	 * @return string
	 */
	public function makeLine( $atts, $content = null ){

		return '<hr class="break line">';

	}


	/**
	 * Link shortcode 
	 * 
	 * @param  array $atts    [description]
	 * @param  string $content [description]
	 * @return [type]          [description]
	 */
	public function makeLink( $atts, $content = null ){


		$html = '<a href="'.$atts['link'].'"';

			if( isset( $atts['target'] ) )
				$html .= ' target="'.$atts['target'].'"';

			if( isset( $atts['event'] ) )
				$html .= ' onclick="_gaq.push([\'_trackEvent\', \'Clicks\', \''.$atts['event'].'\']);"';

			$html .= '">';

				$html .= do_shortcode( $content );

		$html .= '</a>';

		return $html;
	}


	/**
	 * Button shortcode 
	 * 
	 * @param  array $atts    [description]
	 * @param  string $content [description]
	 * @return [type]          [description]
	 */
	public function makeButton( $atts, $content = null ){


		$html = '<a href="'.$atts['link'].'"';

			if( isset( $atts['target'] ) )
				$html .= ' target="'.$atts['target'].'"';

			if( isset( $atts['event'] ) )
				$html .= ' onclick="_gaq.push([\'_trackEvent\', \'Clicks\', \''.$atts['event'].'\']);"';


			$html .= ' class="button';

			$html .= '">';

			if( isset( $atts['icon'] ) )
				$html .= '<span class="icon fa fa-'.$atts['icon'].'"></span>';

				$html .= do_shortcode( $content );

		$html .= '</a>';

		return $html;
	}

	/**
	 * Phonenumber shortcode 
	 * 
	 * @param  array $atts    [description]
	 * @param  string $content [description]
	 * @return [type]          [description]
	 */
	public function phoneNumber( $atts, $content = null ){

		$mobilePrefix = ( wp_is_mobile() ) ? 'tel' : 'callto';

		$html = '<span itemscope itemtype="http://schema.org/LocalBusiness"><a itemprop="telephone" class="phone-link" href="'.$mobilePrefix.':'.do_shortcode( $content ).'">';

				$html .= do_shortcode( $content ).'&shy';

		$html .= '</a></span>';

		return $html;
	}

	/**
	 * Email shortcode 
	 * 
	 * @param  array $atts    [description]
	 * @param  string $content [description]
	 * @return [type]          [description]
	 */
	public function mailTo( $atts, $content = null ){
		
		$html = '<span itemscope itemtype="http://schema.org/LocalBusiness"><a itemprop="email" class="email-link" href="mailto:'.do_shortcode( $content ).'">';

				$html .= do_shortcode( $content ).'&shy';

		$html .= '</a></span>';

		return $html;
	}





}

if( !is_admin() || ( is_admin() && defined( 'DOING_AJAX' ) ) )
	\Cuisine\Front\Shortcodes::getInstance();
