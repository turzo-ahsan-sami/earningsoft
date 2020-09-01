<?php

    namespace App\Traits;

    trait CreateForm {

        /*
        |--------------------------------------------------------------------------
        | Text Block
        |--------------------------------------------------------------------------
        */
       
        /**
         * [text description]
         * @return [string] [makes the text filed string]
         */
        public static function text(){
            
            $numOfArguments = func_num_args();      

            if ($numOfArguments==3) {
                return self::simpleText(func_get_args());
            }
            elseif ($numOfArguments==4) {
                if (is_array(func_get_args()[3])) {
                    return self::textWithAttr(func_get_args());
                }
                else{
                    return self::textWithDivRatio(func_get_args());
                }
                
            }
            elseif ($numOfArguments==5) {
                return self::textWithAttrNDivRatio(func_get_args());  
            }
        }


        
        /**
         * [simpleText description]
         * @param  [array] $arguments [name,value,label]
         * @return [string] 
         */
        public static function simpleText($arguments){
           $labelSize = 4;       
           $fieldSize = 8;

            $markUp = "<div class='form-group'>".
                                "<label for='".$arguments[0]."' class='col-sm-".$labelSize." control-label'>".$arguments[2]."</label>".
                            "<div class='col-sm-".$fieldSize."'>".
                                "<input type='text' name='".$arguments[0]."' value='".$arguments[1]."' class='form-control'>".
                             "</div>".                             
                        "</div>";

            return $markUp;
        }

        /**
         * [textWithAttr description]
         * @param  [type] $arguments [name,value,label,attr[]]
         * @return [string]  
         */
        public static function textWithAttr($arguments){
            $labelSize = 4;       
            $fieldSize = 8;
            
            $attrString = "";
            $classString = "";
            if (count($arguments[3])>0) {
                foreach ($arguments[3] as $key => $value) {
                    if ($key=='class') {
                        $classString = $classString." ". $key. "= '".$value."' ";
                    }
                    else{
                     $attrString = $attrString." ". $key. "= '".$value."' ";
                    }
                }
            }

            $markUp = "<div class='form-group'>".
                                "<label for='".$arguments[0]."' class='col-sm-".$labelSize." control-label'>".$arguments[2]."</label>".

                            "<div class='col-sm-".$fieldSize."'>".
                                "<input type='text'  name='".$arguments[0]."' value='".$arguments[1]."' class='form-control ".$classString."' ".$attrString." >".
                             "</div>".
                             
                        "</div>";

            return $markUp;
        }

        /**
         * [textWithDivRatio description]
         * @param  [type] $arguments [name,value,label,divRatio]
         * @return [string]
         */
        public static function textWithDivRatio($arguments){
            
            $divSizeArray = explode(':',$arguments[3]);
            $labelSize = $divSizeArray[0];       
            $fieldSize = $divSizeArray[1];

            $markUp = "<div class='form-group'>".
                                "<label for='".$arguments[0]."' class='col-sm-".$labelSize." control-label'>".$arguments[2]."</label>".
                            "<div class='col-sm-".$fieldSize."'>".
                                "<input type='text' name='".$arguments[0]."' value='".$arguments[1]."' class='form-control'>".
                             "</div>".                             
                        "</div>";

            return $markUp;
        }

        /**
         * [textWithAttrNDivRatio description]
         * @param  [type] $arguments [name,value,label,attr[],divRatio]
         * @return [string]  
         */
        public static function textWithAttrNDivRatio($arguments){
            $divSizeArray = explode(':',$arguments[4]);
            $labelSize = $divSizeArray[0];       
            $fieldSize = $divSizeArray[1];
            
            $attrString = "";
            $classString = "";
            if (count($arguments[3])>0) {
                foreach ($arguments[3] as $key => $value) {
                    if ($key=='class') {
                        $classString = $classString." ". $key. "= '".$value."' ";
                    }
                    else{
                     $attrString = $attrString." ". $key. "= '".$value."' ";
                    }
                }
            }

            $markUp = "<div class='form-group'>".
                                "<label for='".$arguments[0]."' class='col-sm-".$labelSize." control-label'>".$arguments[2]."</label>".

                            "<div class='col-sm-".$fieldSize."'>".
                                "<input type='text'  name='".$arguments[0]."' value='".$arguments[1]."' class='form-control ".$classString."' ".$attrString." >".
                             "</div>".
                             
                        "</div>";

            return $markUp;
        }
        /*
        |--------------------------------------------------------------------------
        | End Text Block
        |--------------------------------------------------------------------------
        */


        /*
        |--------------------------------------------------------------------------
        | Select Block
        |--------------------------------------------------------------------------
        */
       
        /**
         * [select description]
         * @return [string] [makes the text filed string]
         */
        public static function select(){

            $numOfArguments = func_num_args();

            if ($numOfArguments==4) {
                return self::simpleSelect(func_get_args());
            }
            elseif ($numOfArguments==5) {
                if (is_array(func_get_args()[4])) {
                    return self::selectWithAttr(func_get_args());
                }
                else{
                    return self::selectWithDivRatio(func_get_args());  
                }
                
            }
            
            elseif ($numOfArguments==6) {
                return self::selectWithAttrNDivRatio(func_get_args());  
            }
        }


        /**
         * [simpleSelect description]
         * @param  [array] $arguments [name,options[],defaultOption,label]
         * @return [string]
         */
        public static function simpleSelect($arguments){
            $labelSize = 4;
            $fieldSize = 8;

            $optionString = "";
            $optionSelected = $arguments[2];
            
            foreach ($arguments[1] as $key => $value) {
                $selectString = "";                
                if ($key==$optionSelected) { 
                    $selectString = "selected='selected'";
                }

                $optionString = $optionString. "<option value='".$key."' ".$selectString.">".$value."</option>";
            }

            $markUp = "<div class='form-group'>".
                    "<label for='".$arguments[0]."' class='col-sm-".$labelSize." control-label'>".$arguments[3]."</label>".
                    "<div class='col-sm-".$fieldSize."'>".
                    "<select  name='".$arguments[0]."' class='form-control'>".
                    $optionString.
                    "</select>".
                     "</div>".
                    "</div>";

            return $markUp;
        }

        /**
         * [selectWithAttr description]
         * @param  [array] $arguments [name,options[],defaultOption,label,attr[]]
         * @return [string]
         */
        public static function selectWithAttr($arguments){
            $labelSize = 4;
            $fieldSize = 8;

            $optionString = "";
            $optionSelected = $arguments[2];
            
            foreach ($arguments[1] as $key => $value) {
                $selectString = "";                
                if ($key==$optionSelected) { 
                    $selectString = "selected='selected'";
                }

                $optionString = $optionString. "<option value='".$key."' ".$selectString.">".$value."</option>";
            }


            $attrString = "";
            $classString = "";
            if (count($arguments[4])>0) {
                foreach ($arguments[4] as $key => $value) {
                    if ($key=='class') {
                        $classString = $classString." ". $key. "= '".$value."' ";
                    }
                    else{
                     $attrString = $attrString." ". $key. "= '".$value."' ";
                    }
                }
            }

            $markUp = "<div class='form-group'>".
                    "<label for='".$arguments[0]."' class='col-sm-".$labelSize." control-label'>".$arguments[3]."</label>".
                    "<div class='col-sm-".$fieldSize."'>".
                    "<select  name='".$arguments[0]."' class='form-control".$classString."' ".$attrString.">".
                    $optionString.
                    "</select>".
                     "</div>".
                    "</div>";

            return $markUp;
        }

        /**
         * [selectWithDivRatio description]
         * @param  [array] $arguments [name,options[],defaultOption,label,divRatio]
         * @return [string]
         */
        public static function selectWithDivRatio($arguments){
            $divSizeArray = explode(':',$arguments[4]);
            $labelSize = $divSizeArray[0];       
            $fieldSize = $divSizeArray[1];

            $optionString = "";
            $optionSelected = $arguments[2];
            
            foreach ($arguments[1] as $key => $value) {
                $selectString = "";                
                if ($key==$optionSelected) { 
                    $selectString = "selected='selected'";
                }

                $optionString = $optionString. "<option value='".$key."' ".$selectString.">".$value."</option>";
            }

            $markUp = "<div class='form-group'>".
                    "<label for='".$arguments[0]."' class='col-sm-".$labelSize." control-label'>".$arguments[3]."</label>".
                    "<div class='col-sm-".$fieldSize."'>".
                    "<select  name='".$arguments[0]."' class='form-control'>".
                    $optionString.
                    "</select>".
                     "</div>".
                    "</div>";

            return $markUp;
        }

        /**
         * [selectWithAttrNDivRatio description]
         * @param  [array] $arguments [name,options[],defaultOption,label,attr[],divRatio]
         * @return [string]
         */
        public static function selectWithAttrNDivRatio($arguments){
            $divSizeArray = explode(':',$arguments[5]);
            $labelSize = $divSizeArray[0];       
            $fieldSize = $divSizeArray[1];

            $optionString = "";
            $optionSelected = $arguments[2];
            
            foreach ($arguments[1] as $key => $value) {
                $selectString = "";                
                if ($key==$optionSelected) { 
                    $selectString = "selected='selected'";
                }

                $optionString = $optionString. "<option value='".$key."' ".$selectString.">".$value."</option>";
            }


            $attrString = "";
            $classString = "";
            if (count($arguments[4])>0) {
                foreach ($arguments[4] as $key => $value) {
                    if ($key=='class') {
                        $classString = $classString." ". $key. "= '".$value."' ";
                    }
                    else{
                     $attrString = $attrString." ". $key. "= '".$value."' ";
                    }
                }
            }

            $markUp = "<div class='form-group'>".
                    "<label for='".$arguments[0]."' class='col-sm-".$labelSize." control-label'>".$arguments[3]."</label>".
                    "<div class='col-sm-".$fieldSize."'>".
                    "<select  name='".$arguments[0]."' class='form-control".$classString."' ".$attrString.">".
                    $optionString.
                    "</select>".
                     "</div>".
                    "</div>";

            return $markUp;
        }

        /*
        |--------------------------------------------------------------------------
        | End Select Block
        |--------------------------------------------------------------------------
        */
       
        /*
        |--------------------------------------------------------------------------
        | Radio Block
        |--------------------------------------------------------------------------
        */
        
        /**
         * [radio description]
         * @return [string] [makes the radio string]
         */
        public static function radio(){            

            $numOfArguments = func_num_args();

            if ($numOfArguments==3) {
                return self::simpleRadio(func_get_args());
            }

            elseif ($numOfArguments==4) {
                
                if (is_bool(func_get_args()[2])) {
                    return self::simpleRadioWithSelected(func_get_args());
                }
                else{
                    return self::radioWithDivRatio(func_get_args());  
                }
                
            }
            elseif ($numOfArguments==5) {
                return self::radioWithDivRatio(func_get_args());  
            }
            
        }


        /**
         * [simpleRadio description]
         * @param  [type] $arguments [name,value[],label]
         * @return [string]  
         */
        public static function simpleRadio($arguments){

            $labelSize = 4;       
            $fieldSize = 8;


            $inputString = "";

            foreach ($arguments[1] as $value => $label) {
                 $inputString = $inputString . "<div><input type='radio' name='".$arguments[0]."' value='".$value."' ><label>".$label."</label></div><br>";
            }

            $markUp = "<div class='form-group'>".
                            "<label for='".$arguments[0]."' class='col-sm-".$labelSize." control-label'>".$arguments[2]."</label>".
                            "<div class='col-sm-".$fieldSize."'>".
                                $inputString.
                            "</div>". 
                       "</div>";

            return $markUp;
            
            
        }
        
        /*
        |--------------------------------------------------------------------------
        | End Radio Block
        |--------------------------------------------------------------------------
        */



        

    }