<?php

/*
1. Create the Popup Box with popup_box_generate. Returns: name/selected id
2. Create Javascript code with popup_box_generate_show_script. Place it inside onclick=""

=

$p1 = new PopUp(title: "PopUp", submit_text: "Hello, how are ya?", allowclose: true, close_text:"Don't ask", close_redirecturl:"#", description: null, method: "post", type: "input_1", img: "https://i.postimg.cc/dtRL59Tc/empty-world-by-viylne-df0s5dw-fullview2.jpg");
$p1->addInput(new Input(id: "u", type: "text", title: "Username"));
if($p1->generateCode()){}
echo($p1->getCode());
echo('<script>'.$p1->script_js().'</script>');

*/

function popup_box_generate_show_script($name){
    return 'document.getElementById("'.$name.'").style.display = "flex";document.body.style.overflow = "hidden";';
}
class Input
{
    public $id;
    public $type;
    public $title;
    public $placeholder;
    public $json_content_encoded;
    public $selected;
    public $required;
    public $spellcheck;
    public $autocomplete;
    public $additional;
    public $value;
    
    public function __construct(
                                    $id = "",
                                    $type = "text",
                                    $title = null,
                                    $placeholder = null,
                                    $json_content_encoded = '{"error":"Not defined!"}',
                                    $selected = null,
                                    $required = "true",
                                    $spellcheck = "false",
                                    $autocomplete = "off",
                                    $additional = null,
                                    $value = null,
                                ){
        $this->id = $id;
        $this->type = $type;
        $this->title = $title;
        $this->placeholder = $placeholder;
        $this->json_content_encoded = $json_content_encoded;
        $this->selected = $selected;
        $this->required = $required;
        $this->spellcheck = $spellcheck;
        $this->autocomplete = $autocomplete;
        $this->additional = $additional;
        $this->value = $value;
    }
}
class PopUp {
    // Properties
    public $id = null;
    public $title = "Title";
    public $description = null;
    public $method = "post";
    public $type = "input_1";
    public $submit_text = "Save";
    public $submit_name = "submit";
    public $submit_url = null;
    public $close_text = "Cancel";
    public $close_redirecturl = null;
    public $allowclose = true;
    public $img = null;
    private $code = "";

    public $inputs = array();
    public function addInput($input = null){
        if($input != null && isset($input->id))
        array_push($this->inputs, $input);
    }

    public function __construct(
                                $id = "auto",
                                $title = "Title",
                                $description = null,
                                $method = "post",
                                $type = "input_1",
                                $submit_text = "Save",
                                $submit_name = "submit",
                                $submit_url = null,
                                $allowclose = true,
                                $close_text = "Close",
                                $close_redirecturl = null,
                                $img = null,
                                $inputs = array()
                            ){
                                echo ($this->submit_url);
        if($id == null || $id == "auto") $this->id = generateRandomString(12); else $this->id = $id;
        if($submit_url == null) $this->submit_url = $_SERVER['PHP_SELF']; else $this->submit_url = $submit_url;
        $this->title = $title;
        $this->description = $description;
        $this->method = $method;
        $this->type = $type;
        $this->submit_text = $submit_text;
        $this->img = $img;
        $this->allowclose = $allowclose;
        $this->submit_name = $submit_name;
        $this->close_text = $close_text;
        $this->close_redirecturl = $close_redirecturl;
        foreach($inputs as $put){
            $this->addInput($put);
        }
    }
    
    function generateCode() {
        if($this->type != "alert" && count($this->inputs) == 0){return "Error: No jsonvalues detected!";}
        $this->code = "";

        $this->code .= '
        <style>
        '.popup_box_generate_styles().'
        </style>
        <form method="'.$this->method.'" action="'.$this->submit_url.'" id="'.$this->id.'_form">
        <div class="pp_popup" id="'.$this->id.'" style="display:none;">';
    
        if($this->img){ $this->code .= '<div style="padding: 10px 10px 10px;"><img class="pp_img" src="'.$this->img.'"/></div>'; }
    
        $this->code .= '
            <div class="pp_text_block">
                <div class="pp_title">
                '.$this->title.' 
                </div>
                '.(($this->description !== null) ? '<div class="pp_description">'.$this->description.'</div>' : "").'
            </div>';
            
        if($this->type == "input_1") {
            foreach ($this->inputs as $key => $value) {
    
                if($value->type == "text"){
                    $this->code .= '<label for="'.$value->id.'" class="pp_type_input_1_label">'.$value->title.(($value->required == "true") ? " *":"").'</label>';
                    $this->code .= '<input type="text" class="pp_type_input_text" id="'.$value->id.'" name="'.$value->id.'" value="'.$value->value.'" placeholder="'.$value->placeholder.'" required="'.$value->required.'" spellcheck="'.$value->spellcheck.'" autocomplete="'.$value->autocomplete.'" '.$value->additional.'/>';
                }else if($value->type == "email"){
                    $this->code .= '<label for="'.$value->id.'" class="pp_type_input_1_label">'.$value->title.(($value->required == "true") ? " *":"").'</label>';
                    $this->code .= '<input type="email" class="pp_type_input_email" id="'.$value->id.'" name="'.$value->id.'" value="'.$value->value.'" placeholder="'.$value->placeholder.'" required="'.$value->required.'" spellcheck="'.$value->spellcheck.'" autocomplete="'.$value->autocomplete.'" '.$value->additional.'/>';
                }else if($value->type == "password"){
                    $this->code .= '<label for="'.$value->id.'" class="pp_type_input_1_label">'.$value->title.(($value->required == "true") ? " *":"").'</label>';
                    $this->code .= '
                    <div style="display:block;width:390px;">
                    <input type="password" class="pp_type_input_password" id="'.$value->id.'" name="'.$value->id.'" value="'.$value->value.'" placeholder="'.$value->placeholder.'"  required="'.$value->required.'" spellcheck="'.$value->spellcheck.'" autocomplete="'.$value->autocomplete.'" '.$value->additional.'/>
                    <i class="fa fa-eye-slash pp_type_input_password_eye" 
                    onclick="
                        if(document.getElementById(\''.$value->id.'\').type == \'password\') { 
                            document.getElementById(\''.$value->id.'\').type = \'text\';
                            this.classList.remove(\'fa-eye-slash\');
                            this.classList.add(\'fa-eye\');
                        }else { 
                            document.getElementById(\''.$value->id.'\').type = \'password\';
                            this.classList.remove(\'fa-eye\');
                            this.classList.add(\'fa-eye-slash\');
                        }
                    "></i>
                    </div>';
                }else if($value->type == "dropdown"){
                    $this->code .= '<label for="'.$value->id.'" class="pp_type_input_1_label">'.$value->title.(($value->required == "true") ? " *":"").'</label>';
                    $this->code .= '<select name="'.$value->id.'" id="'.$value->id.'" form="'.$this->id.'_form" class="pp_type_input_dropdown" placeholder="'.$value->title.'"  required="'.$value->required.'" spellcheck="'.$value->spellcheck.'" autocomplete="'.$value->autocomplete.'" '.$value->additional.'>';
                    foreach (json_decode($value->json_content_encoded) as $key2 => $value2) {
                        $this->code .= '<option style="" value="'.$key2 .'" '.((isset($value->selected) && $value->selected == $key2) ? 'selected' : '').'>'.$value2 .'</option>';
                    }
                    $this->code .= '</select>';
                }else if($value->type == "select"){
                    $this->code .= '<label for="'.$value->id.'" class="pp_type_input_1_label">'.$value->title.(($value->required == "true") ? " *":"").'</label>';
                    $this->code .= '<input style="display:none;" type="text" value="'.$value->placeholder.'" name="'.$value->id.'" id="'.$value->id.'" form="'.$this->id.'_form" required="'.$value->required.'" spellcheck="'.$value->spellcheck.'" autocomplete="'.$value->autocomplete.'" '.$value->additional.'>';
                    $this->code .= '<script>const arr_'.$value->id.' = [];</script>';

                    foreach (json_decode($value->json_content_encoded) as $key2 => $value2) {
                        $this->code .= '<script>arr_'.$value->id.'.push(\'select_btn_'.$key2.'_'.$this->id.'_'.$value->id.'\');</script>
                        <div 
                        id="select_btn_'.$key2.'_'.$this->id.'_'.$value->id.'"
                        onclick="select_'.$value->id.'(\'select_btn_'.$key2.'_'.$this->id.'_'.$value->id.'\');" data-value="'.$key2 .'" 
                        class="'.((isset($value->selected) && $value->selected == $key2) ? 'pp_type_input_1_select_1' : 'pp_type_input_1_select_0').'"
                        >
                            
                            <label
                            style="margin: 0;position: absolute;top: 50%;left: 50%;-ms-transform: translate(-50%, -50%);transform: translate(-50%, -50%);cursor: pointer;">
                            '.$value2 .'
                            </label>

                        </div>';
                    }
                    $this->code .= '
                    <script>
                    for (let i = 0; i < arr_'.$value->id.'.length; i++) {
                        console.log("select_'.$value->id .' Buttons->"+ arr_'.$value->id.'[i]);
                    }
                        function select_'.$value->id.'(value){
                            document.getElementById("'.$value->id.'").value = document.getElementById(value).getAttribute("data-value");

                            for (let i = 0; i < arr_'.$value->id.'.length; i++) {
                                /*document.getElementById("'.$value->id.'").value = document.getElementById(arr_'.$value->id.'[i]).getAttribute("data-value");
                                alert(document.getElementById(arr_'.$value->id.'[i]).getAttribute("data-value"));*/

                                if(value == arr_'.$value->id.'[i]) {
                                    document.getElementById(arr_'.$value->id.'[i]).className = "pp_type_input_1_select_1";
                                }else {
                                    document.getElementById(arr_'.$value->id.'[i]).className = "pp_type_input_1_select_0";
                                }
                            }
                            //document.getElementById("'.$value->id.'").value = value;
                        }
                    </script>';
                }
                else if($value->type == "link"){
                    $this->code .= '<div style="width:390px;margin-top : 2px;margin-bottom : 3px;"><a class="pp_type_input_link" href="'.$value->placeholder.'">'.$value->title.'</a></div>';
                }else if($value->type == "hidden"){
                    $this->code .= '<input type="hidden" id="'.$value->id.'" name="'.$value->id.'" value="'.$value->value.'" '.$value->additional.'/>';
                }else{
                    $this->code .= '<label for="'.$value->id.'" class="pp_type_input_1_label">'.$value->title.'</label>';
                    $this->code .= '<input type="'.$value->type.'" class="pp_type_input_1" id="'.$value->id.'" name="'.$value->id.'" value="'.$value->value.'" placeholder="'.$value->placeholder.'" '.$value->additional.'/>';
                }
            }
        }
    
        $this->code .= '
            <div class="buttons">
                <div class="glow-on-hover button" onclick=\''.(($this->close_redirecturl == null) ? 'document.getElementById("'.$this->id.'").style.display = "none";document.body.style.overflow = "scroll";':'window.location.href="'.$this->close_redirecturl.'";').'\' style=\''.((!$this->allowclose)?'visibility: hidden;':'').'\'>'.$this->close_text.'</div>
                '.(($this->type != "alert" || true) ? 
                ('<div class="button button-primary" onClick="document.getElementById(\''.$this->id.'_submit\').click();">'.$this->submit_text.'</div>'):
                ('<div class="button button-primary" style="visibility:hidden;">'.$this->submit_text.'</div>')).'
            </div>
            <input type="submit" name="'.$this->submit_name.'" form="'.$this->id.'_form" value="Send" id="'.$this->id.'_submit" style="display:none;"/>
            <label style="font-size:12px;margin-top:-10px;margin-bottom:-8px;color: #c9cdd1;">©TechGrief</label>
            </div>
            </form>
        ';
        return true;
    }
    function getCode(){
        return $this->code;
    }
    function script_js(){
        return 'document.getElementById("'.$this->id.'").style.display = "flex";document.body.style.overflow = "hidden";';
    }
}

function popup_box_generate_styles(){
    if(isset($_GLOBALS["popup_box_generate_styles"])) return ""; else 
    $_GLOBALS["popup_box_generate_styles"] = true;
    return '
    @import url(\'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css\');

    @import url("https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;700&display=swap");
    .pp_popup {
        align-items: center;
        background-color: #fff;
        border-radius: 20px;
        box-shadow: 
            0 0.4px 3.6px rgba(0, 0, 0, 0.004),
            0 1px 8.5px rgba(0, 0, 0, 0.01), 0 1.9px 15.7px rgba(0, 0, 0, 0.019),
            0 3.4px 28.2px rgba(0, 0, 0, 0.03), 0 6.3px 54.4px rgba(0, 0, 0, 0.047),
            0 15px 137px rgba(0, 0, 0, 0.07);
            //box-shadow: rgba(0, 0, 0, 0.25) 0px 54px 55px, rgba(0, 0, 0, 0.12) 0px -12px 30px, rgba(0, 0, 0, 0.12) 0px 4px 6px, rgba(0, 0, 0, 0.17) 0px 12px 13px, rgba(0, 0, 0, 0.09) 0px -3px 5px;
            //box-shadow: rgba(0, 0, 0, 0.56) 0px 22px 70px 4px;
            box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;
            position:fixed;
        flex-direction: column;
        display:flex;
        width:430px;
        //min-width: ($_COOKIE["window_width"]*0.25)px;
        z-index:1;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        padding: 10px 10px 10px 10px;
        overflow: hidden;
        padding-top: 10px;
        font-family: "Source Sans Pro";
        user-select: none; /* supported by Chrome and Opera */
        -webkit-user-select: none; /* Safari */
        -khtml-user-select: none; /* Konqueror HTML */
        -moz-user-select: none; /* Firefox */
        -ms-user-select: none; /* Internet Explorer/Edge */
    }
    .pp_img {
        //background-color: transparent;
        //margin: 16px 0;
        //box-sizing: border-box;
        //margin: 0 20px 20px;
        //padding: 20px;
        border: 1px solid transparent;
        border-radius: 20px;
        box-sizing: border-box;
        width: 100%;
        background:none;
        box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
        opacity: 0.91;
        transition-duration: 400ms;
        transition-timing-function: ease;
    }
    .pp_img:hover {
        opacity: 1;
    }
    .pp_text_block {
        box-sizing: border-box;
        padding: 0 20px 10px;
        width: 100%;
    }
    .pp_title {
        margin-top: 10px;
        align-items: center;
        display: flex;
        font-size: 28px;
        font-weight: bold;
        position: relative;
        cursor:default;
    }
    .pp_description {
        color: #64686b;
        text-align: justify;
        font-size: 18px;
    }
    .pp_type_input_1_label {
        width: 390px;
        float: left;
        font-size: 16px;
        color: #64686b;
        font-weight: bold;
    }
    .pp_type_input_1 {
        color: #64686b;
        margin-top : 3px;
        margin-bottom : 5px;
        width: 390px;
        border: 2px solid #64686b;
        radius: 10px;
        font-size: 20px;
        padding: 4px 5px 4px 5px;
        outline: none;
        border-radius: 8px;
        letter-spacing: 1px;
    }
    .pp_type_input_text {
        color: #64686b;
        margin-top : 3px;
        margin-bottom : 5px;
        width: 390px;
        border: 2px solid #64686b;
        radius: 10px;
        font-size: 20px;
        padding: 4px 5px 4px 5px;
        outline: none;
        border-radius: 8px;
        letter-spacing: 1px;
        opacity: 0.7;
        transition-duration: 800ms;
        transition-timing-function: ease;
    }
    .pp_type_input_text:hover {
        opacity: 1;
    }
    .pp_type_input_email {
        color: #64686b;
        margin-top : 3px;
        margin-bottom : 5px;
        width: 390px;
        border: 2px solid #64686b;
        radius: 10px;
        font-size: 20px;
        padding: 4px 5px 4px 5px;
        outline: none;
        border-radius: 8px;
        letter-spacing: 1px;
        opacity: 0.7;
        transition-duration: 800ms;
        transition-timing-function: ease;
    }
    .pp_type_input_email:hover {
        opacity: 1;
    }
    .pp_type_input_password {
        color: #64686b;
        margin-top : 3px;
        margin-bottom : 5px;
        width: 345px;
        float:left;
        border: 2px solid #64686b;
        radius: 10px;
        font-size: 20px;
        padding: 4px 5px 4px 5px;
        outline: none;
        border-radius: 8px;
        letter-spacing: 1px;
        opacity: 0.7;
        transition-duration: 800ms;
        transition-timing-function: ease;
    }
    .pp_type_input_password:hover {
        opacity: 1;
    }
    .pp_type_input_password_eye {
        color: #64686b;
        float:right;
        margin-top : 3px;
        border: 2px solid transparent;
        radius: 10px;
        padding: 4.5px 6.5px 3.5px 5px;
        vertical-align: middle;
        font-size:22px;
        cursor:pointer;
        opacity: 0.7;
        transition-duration: 800ms;
        transition-timing-function: ease;
    }
    .pp_type_input_password_eye:hover {
        opacity: 1;
    }
    .pp_type_input_dropdown {
        color: #64686b;
        margin-top : 3px;
        margin-bottom : 5px;
        width: 390px;
        border: 2px solid #64686b;
        radius: 10px;
        font-size: 20px;
        padding: 5px 5px 5px 4px;
        outline: none;
        border-radius: 8px;
        letter-spacing: 1px;
        cursor: pointer;
        opacity: 0.7;
        transition-duration: 600ms;
        transition-timing-function: ease;
    }
    .pp_type_input_dropdown:hover {
        opacity: 1;
    }
    .pp_type_input_link {
        text-decoration: none;
        font-size: 15px;
        letter-spacing: 1px;
        opacity: 0.5;
        color: inherit;
        background: 
		linear-gradient(to right, transparent, transparent),
		linear-gradient(45deg, rgba(255, 0, 0, 1), rgba(255, 0, 180, 1), rgba(0, 100, 200, 1));
        background-size: 102% 1.5px, 0 1.5px;
        background-position: 100% 100%, 0 100%;
        background-repeat: no-repeat;
    }
    .pp_type_input_link:hover,
    .pp_type_input_link:focus {
        transition: all 0.5s ease-out;
        opacity: 0.9;
        background-size: 0 1.5px, 102% 1.5px;
    }
    .pp_type_input_1_select_0 {
        color: #64686b;
        margin-top : 3px;
        margin-bottom : 5px;
        width: 390px;
        height: 50px;
        border: 2px solid #64686b;
        font-size: 20px;
        outline: none;
        border-radius: 8px;
        letter-spacing: 1px;
        cursor: pointer;
        position: relative;
        opacity: 0.45;
        transition: opacity 0.1s ease-in;
        -moz-transition: opacity 0.1s ease-in;
        -webkit-transition: opacity 0.1s ease-in;
        -o-transition: opacity 0.1s ease-in;
        
        transition: font-weight 0.08s ease-in;
        -moz-transition: font-weight 0.08s ease-in;
        -webkit-transition: font-weight 0.08s ease-in;
        -o-transition: font-weight 0.08s ease-in;
    }
    .pp_type_input_1_select_0:hover {
        opacity: 0.6;
    }
    .pp_type_input_1_select_1 {
        color: #64686b;
        margin-top : 3px;
        margin-bottom : 5px;
        width: 390px;
        height: 50px;
        border: 2px solid #64686b;
        font-size: 20px;
        outline: none;
        border-radius: 8px;
        letter-spacing: 1px;
        cursor: pointer;
        position: relative;
        opacity: 1;
        transition: opacity 0.1s ease-in;
        -moz-transition: opacity 0.1s ease-in;
        -webkit-transition: opacity 0.1s ease-in;
        -o-transition: opacity 0.1s ease-in;
        font-weight: bold;
        
        transition: font-weight 0.08s ease-in;
        -moz-transition: font-weight 0.08s ease-in;
        -webkit-transition: font-weight 0.08s ease-in;
        -o-transition: font-weight 0.08s ease-in;
    }


    
    .buttons {
    display: flex;
    margin-top: 8px;
    width: 100%;
    }
    .button {
    align-items: center;
    background: #edf1f7;
    border-radius: 10px;
    cursor: pointer;
    display: flex;
    height: 50px;
    justify-content: center;
    margin: 0 5px 5px 20px;
    width: 100%;
    }

    .button:last-child {
        margin: 0 20px 15px 5px;
    }

    .button-primary {
        background-color: #0060f6;
        color: #fff;
        position: relative;
        z-index: 3;
    }
    .button-primary:before {
        content: "";
        background: linear-gradient(45deg, #ff0000, #ff7300, #fffb00, #48ff00, #00ffd5, #002bff, #7a00ff, #ff00c8, #ff0000);
        position: absolute;
        top: -2px;
        left:-2px;
        background-size: 400%;
        z-index: -1;
        filter: blur(5px);
        width: calc(100% + 4px);
        height: calc(100% + 4px);
        animation: glowing 20s linear infinite;
        opacity: 0;
        transition: opacity .3s ease-in-out;
        border-radius: 10px;
    }
    .button-primary:hover:before {
        opacity: 1;
    }

    .button-primary:after {
        z-index: -1;
        content: "";
        position: absolute;
        width: 100%;
        height: 100%;
        background: #0060f6;
        /*left: 0;
        top: 0;*/
        border-radius: 10px;
    }

    .glow-on-hover {
        position: relative;
        z-index: 3;
    }

    .glow-on-hover:before {
        content: "";
        background: linear-gradient(indianred, indianred);
        position: absolute;
        top: -3px;
        left:-3px;
        background-size: 400%;
        z-index: -1;
        filter: blur(5px);
        width: calc(100% + 4px);
        height: calc(100% + 4px);
        animation: glowing 20s linear infinite;
        opacity: 0;
        transition: opacity .3s ease-in-out;
        border-radius: 10px;
    }

    .glow-on-hover:hover:before {
        opacity: 1;
    }

    .glow-on-hover:after {
        z-index: -1;
        content: "";
        position: absolute;
        width: 100%;
        height: 100%;
        background: #edf1f7;
        /*left: 0;
        top: 0;*/
        border-radius: 10px;
    }

    @keyframes glowing {
        0% { background-position: 0 0; }
        50% { background-position: 400% 0; }
        100% { background-position: 0 0; }
    }';
}





function generateRandomString($len = 64) {
    //require($_SERVER["CONTEXT_DOCUMENT_ROOT"]."/sqlware.config");
    $characters = '0123456789abcdefghij0123456789klmnopq0123456789rstuvwxyzAB0123456789CDEFGHIJKLMNOPQR0123456789STUVWXYZ0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $len; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
?>