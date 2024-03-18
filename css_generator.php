<?php

    $image = [];
    $array_width = [];
    $array_height = [];
    $array_height_decal = [];
    $image_final="sprite";
    $style = "style";
    $shortopt = "r" . "i:" . "s:";
    $longopt= array ("recursive","output-image:","output-style:");
    $terminal = getopt($shortopt,$longopt, $rest_index);
    if (isset($argv[$rest_index])){
        // $dir_path = $argv[$rest_index];
        $dir_path = array_slice($argv, $rest_index);
        my_merge_image($dir_path);
    }
    else {
        my_merge_image(["."]);
    }
    

    function my_merge_image($dir_path){

        $all_size = 0;
        $zero = 0;
        global $array_width;
        global $array_height;
        global $array_height_decal;
        global $image;
        global $image_final;
        global $recursive;
        global $style;
        global $terminal;
        $recursive = false;


        foreach ($terminal as $key => $mot) {
            switch ($key) {

                //pour les short
                case "r":
                    $recursive = true;
                    break;
                case "i":
                    $image_final = $mot;
                    break;
                case "s":
                    $style = $mot;
                    break;
                //pour les long  output-
                case "recursive":
                    $recursive = true;
                    break;
                case "output-image":
                    $image_final = $mot;
                    break;
                case "output-style":
                    $style = $mot;
                    break;
            }
        }
        
        my_scandir($dir_path);
        
        foreach ($image as $size) {
            $result = getimagesize($size);
            array_push($array_width, $result[0]);
            array_push($array_height, $result[1]);
            array_push($array_height_decal, $all_size);
            $all_size = $all_size + $result[1];
            // var_dump($result);
        }
        
        

        $biggest = max($array_width);
            
        $image_vide = imagecreatetruecolor($biggest, $all_size);
            

            foreach ($image as $value) {
                $img_cree = imagecreatefrompng($value);
                imagecopymerge ($image_vide, $img_cree,0,$array_height_decal[$zero],0,0,$array_width[$zero],$array_height[$zero],100);
                $zero = $zero + 1;
            }
            
            imagepng($image_vide, $image_final . ".png");

            // var_dump ($image);

            my_generate_css($style);
    }

    function my_scandir($dir_path){

        global $image_final;
        global $image;
        global $recursive;
        $array_dir = [];

        foreach ($dir_path as $dos){
            if (is_dir($dos)){

                if ($ouvert = opendir ($dos)){

                    while (false !== ($lu = readdir($ouvert))){ 

                        if ($lu != "." && $lu != ".." && $lu != ".git" && $lu != $image_final . ".png"){
                            switch (mime_content_type("$dos/$lu")) {

                                case "image/png":
                                    array_push ($image, "$dos/$lu");
                                    break;

                                case "directory":
                                    array_push ($array_dir, "$dos/$lu");
                                    break;
                            }
                        }
                    }  
                    closedir($ouvert);
                    
                }
            }
            else if (mime_content_type($dos) == "image/png"){
                array_push ($image, "$dos");
            }
        }
            if($recursive){
                if(count($array_dir) !== 0){
                    my_scandir($array_dir);
            }  
        }
    }
    // var_dump ($image);

    function my_generate_css($style){

        global $style;
        global $image;
        global $image_final;
        global $array_height;
        global $array_width;
        global $array_height_decal;
        $i = 0;

        $name = $style . ".css";

        if (file_exists($style . ".css")) {
            unlink($style . ".css");
        }

        foreach ($image as $cadre) {

            $data= ".img" . $i . "{ \n  background-image: url(\"" . $image_final . ".png\"); \n  background-position : 0px " . $array_height_decal[$i]*(-1) . "px;\n  width :" . $array_width[$i] . "px;\n  height :" . $array_height[$i] . "px;\n}\n";

            file_put_contents($name, $data, FILE_APPEND);
            $i = $i + 1;

        }
    }

    // var_dump($dir_path);
    // var_dump($argv[1]);
    // header("Content-Type: image/png");
    
    // var_dump($image)

?>