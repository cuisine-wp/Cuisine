<?php
    
namespace Cuisine\Field\Fields;


class DefaultField{

    var $id;

    var $name;

    var $properties;


    /**
     * Define a core Field.
     *
     * @param array $properties The text field properties.
     */
    public function __construct( $name, $props = array() ){

        $this->id = md5( $name );
        $this->name = $name;
        $this->properties = $props;
        $this->fieldType();
        $this->setDefaults();
    }


    /**
     * Set the default values:
     *
     * @return void
     */
    private function setDefaults(){


        if( !isset( $this->properties['label'] ) )
            $this->properties['label'] = false;

        if( !isset( $this->properties['defaultValue'] ) )
            $this->properties['defaultValue'] = false;

        if( !isset( $this->properties['placeholder'] ) )
            $this->properties['placeholder'] = false;

        if( !isset( $this->properties['validation'] ) )
            $this->properties['validation'] = false;

        if( !isset( $this->properties['choices'] ) )
            $this->properties['choices'] = false;

        if( !isset( $this->properties['class'] ) )
            $this->properties['class'] = array(
                                                'field',
                                                'field-'.$this->name,
                                                'type-'.$this->type
            );

    }


    /**
     * Get Label
     * 
     * @return String
     */
    public function getLabel(){

        if( $this->properties['label'] )
            return '<label for="'.$this->id.'">'.$this->properties['label'].'</label>';

    }


    /**
     * Get the default value html
     * 
     * @return String
     */
    public function getDefault(){

        if( $this->properties['defaultValue' ] )
            return ' value="'.$this->properties['defaultValue'].'"';
 
    }


    /**
     * Get placeholder
     * 
     * @return String
     */
    public function getPlaceholder(){

        if( $this->properties['placeholder'] )
            return ' placeholder="'.$this->properties['placeholder'].'"';

    }


    public function getValidation(){

        if( $this->properties['validation'] )
            return ' data-validate="'.implode( ',', $this->properties['validation'] );

    }



    /**
     * Create the class for the html output
     * 
     * @return String
     */
    public function getClass(){

        $classes = $this->properties['class'];
        $output = implode( ' ', $classes );

        return $output;

    }


    /**
     * Echo the html class
     * 
     * @return [type] [description]
     */
    public function echoClass(){

        echo $this->getClass();

    }


    /**
     * Get choices
     *
     * @return [type] [description]
     */
    public function getChoices(){

        if( $this->properties['choices'] )
            return $this->properties['choices'];

    }



}


   
