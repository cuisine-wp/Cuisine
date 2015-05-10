<?php
namespace Cuisine\Fields;


class SelectField extends ChoiceField{


    /**
     * Method to override to define the input type
     * that handles the value.
     *
     * @return void
     */
    protected function fieldType(){
        $this->type = 'select';
    }

   

    /**
     * Build the html
     *
     * @return String;
     */
    public function build(){

        $choices = $this->getChoices();
        $choices = $this->parseChoices( $choices );

        $html = '<select ';

            $html .= 'id="'.$this->id.'" ';

            $html .= 'class="'.$this->getClass().'" ';

            $html .= 'name="'.$this->name.'" ';

            $html .= $this->getValidation();

        $html .= '>';

        foreach( $choices as $choice ){

            $html .= $this->buildOption( $choice );

        }

        $html .= '</select>';

        return $html;
    }




}