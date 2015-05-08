<?php
namespace Cuisine\Metabox;

use Cuisine\Session\Session;
use Cuisine\User\User;
use Cuisine\Validator\Validator;
use Cuisine\Views\Metabox;

class MetaboxBuilder {

	
	/**
	 * Metabox instance data.
	 *
	 * @var Array
	 */
	private $data;


	/**
	 * The current user instance.
	 *
	 * @var \Cuisine\User\User
	 */
	private $user;


	/**
	 * The metabox view, in raw html
	 *
	 * @var \Cuisine\View\Metabox
	 */
	private $view;


	/**
	 * Whether or not check for user capability.
	 *
	 * @var bool
	 */
	private $check = false;

	/**
	 * The capability to check.
	 *
	 * @var string
	 */
	private $capability;



	/**
	 * Build a metabox instance.
	 *
	 * @param \Cuisine\Validation\Validation $validator
	 * @param \Cuisine\User\User $user
	 */
	function __construct(){

		//$this->user = $user;
		//$this->validator = $validator;
		$this->data = array();
		$this->view = new Metabox();

		add_action( 'save_post', array( &$this, 'save' ) );
	}


	/**
	 * Set a new metabox.
	 *
	 * @param string $title The metabox title.
	 * @param string $postType The metabox parent slug name.
	 * @param array $options Metabox extra options.
	 * @param \Cuisine\View\MetaboxView
	 * @return object
	 */
	public function make( $title, $postType, array $options = array(), $view = null ){

	  	$this->data['title'] = $title;
	    $this->data['postType'] = $postType;
	    $this->data['options'] = $this->parseOptions($options);

	    if ( !is_null( $view ) ){

	        $this->view = $view;
	    
	    }

	    return $this;
	}

	/**
	 * Build the set metabox.
	 *
	 * @param array $fields A list of fields to display.
	 * @return \Cuisine\Metabox\MetaboxBuilder
	 */
	public function set(array $fields = array()){

	    // Check if sections are defined.
	    $this->sections = $this->getSections( $fields );

	    $this->data['fields'] = $fields;

	   	add_action( 'add_meta_boxes', array( &$this, 'display' ) );

	    return $this;
	}


	/**
	 * Restrict access to a specific user capability.
	 *
	 * @param string $capability
	 * @return void
	 */
	public function can($capability){
	    $this->capability = $capability;
	    $this->check = true;
	
	}


	/**
	 * The wrapper display method.
	 *
	 * @return void
	 */
	public function display(){

	    if( $this->check && !$this->user->can( $this->capability ) ) return;

	    $id = md5( $this->data['title']);

	    // Fields are passed to the metabox $args parameter.
	    add_meta_box( $id, $this->data['title'], array($this, 'build'), $this->data['postType'], $this->data['options']['context'], $this->data['options']['priority'], $this->data['fields'] );
	}


	/**
	 * Call by "add_meta_box", build the HTML code.
	 *
	 * @param \WP_Post $post The WP_Post object.
	 * @param array $datas The metabox $args and associated fields.
	 * @throws MetaboxException
	 * @return void
	 */
	public function build($post, array $datas) {
	   
	    // Add nonce fields
	    wp_nonce_field(Session::nonceAction, Session::nonceName);
	    echo $this->view->render();
	}


	/**
	 * The wrapper install method. Save container values.
	 *
	 * @param int $postId The post ID value.
	 * @return void
	 */
	public function save( $postId ){

	    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

	    $nonceName = (isset($_POST[Session::nonceName])) ? $_POST[Session::nonceName] : Session::nonceName;
	    if (!wp_verify_nonce($nonceName, Session::nonceAction)) return;

	    // Check user capability.
	    if ( $this->check && $this->data['postType'] === $_POST['post_type'] ){

	        if ( !$this->user->can( $this->capability ) ) return;

	    }

	    $fields = array();

	    // Loop through the registered fields.
	    // With sections.
	    if ( !empty($this->sections ) ) {
	        
	        foreach ($this->data['fields'] as $fs){

	            $fields = $fs;
	        
	        }

	    } else {
	        
	        $fields = $this->data['fields'];
	    
	    }

	    $this->register( $postId, $fields );

	}

	/**
	 * Register validation rules for the custom fields.
	 *
	 * @param array $rules A list of field names and their associated validation rule.
	 * @return \Cuisine\Metabox\MetaboxBuilder
	 */
	public function validate(array $rules = array()) {

	    $this->data['rules'] = $rules;

	    return $this;
	}

	/**
	 * Register the metabox and its fields into the DB.
	 *
	 * @param int $postId
	 * @param array $fields
	 * @return void
	 */
	private function register( $postId, array $fields ) {
	    
	    foreach($fields as $field){

	       // $value = isset( $_POST[ $field['name'] ] ) ? $_POST[ $field['name'] ] : $this->parseValue( $field );
	       
	       $value = isset( $_POST[ $field['name'] ] ) ? $_POST[ $field['name'] ] : '';

	        // Apply validation if defined.
	        // Check if the rule exists for the field in order to validate.
	    /*    if ( isset( $this->data['rules'][ $field['name'] ] ) ) {

	            $rules = $this->data['rules'][ $field['name'] ];

	            // Check if $rules array is an associative array
	            if ( $this->validator->isAssociative($rules) &&
	            	 'infinite' == $field->getFieldType() ) {

	                // Check Infinite fields validation.
	                foreach ($value as $row => $rowValues) {

	                    foreach ($rowValues as $name => $val) {
	                    
	                        if (isset($rules[$name])) {

	                            $value[$row][$name] = 	$this->validator->single(
	                            								$val,
	                            								$rules[$name]
	                            					 	);
	                        }
	                    }
	                }

	            } else {
	                $value =	$this->validator->single(
	                				$value,
	                				$this->data['rules'][$field['name']]
	                			);
	            }
	        }
		*/
	        update_post_meta( $postId, $field['name'], $value );
	    }
	}

	/**
	 * Check metabox options: context, priority.
	 *
	 * @param array $options The metabox options.
	 * @return array
	 */
	private function parseOptions(array $options) {

	    return wp_parse_args($options, array(
	        'context'   => 'normal',
	        'priority'  => 'default'
	    ));
	
	}


	/**
	 * Set the metabox view sections.
	 *
	 * @param array $fields
	 * @return array
	 */
	private function getSections(array $fields) {

	    $sections = array();

	    foreach ($fields as $section => $subFields) {
	        
	        if ( !is_numeric( $section ) ) {
	        
	            array_push($sections, $section);
	        
	        }
	    }

	    return $sections;
	}

	/**
	 * Set the default 'value' property for all fields.
	 *
	 * @param \WP_Post $post
	 * @param array $fields
	 * @return void
	 */
	private function setDefaultValue( \WP_Post $post, array $fields ) {
	    
	    foreach ( $fields as $field ){

	        // Check if saved value
	        $value = get_post_meta($post->ID, $field['name'], true);

	        // If none of the above condition is matched
	        // simply assign the post meta default or saved value.
	        //$field['value'] = $this->parseValue($field, $value);
	    	$field['value'] = '';
	    }
	}









}

