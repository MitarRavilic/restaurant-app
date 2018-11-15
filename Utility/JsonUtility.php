<?php
    namespace App\Utility;

    class JsonUtility {
        
        public function loadRawFromJson(string $post_field_name){
            $dataFromPost = \filter_input(INPUT_POST, $post_field_name);
            return $dataFromPost;
        }
        #neugnjezdeni objekti
        public function unpackSingleFromJson($dataFromPost){
            $decodedData = json_decode(html_entity_decode($dataFromPost), true);
            $splitted = explode('}', $decodedData);
            array_pop($splitted);

           
                $splitted[0] = str_replace('[', '', $splitted[0]);
                $splitted[0] = str_replace(']', '', $splitted[0]);
                $splitted[0] = $splitted[0] . '}';
                $splitted[0] = json_decode($splitted[0]);
            
           

            return $splitted[0];
        }

        #ugnjezdeni objekti
        public function unpackNestedFromJson($dataFromPost){
            
            $decodedData = json_decode(html_entity_decode($dataFromPost), true);
            $splitted = explode('}', $decodedData);
            array_pop($splitted);


            for($i=0; $i < count($splitted);$i++){
                $splitted[$i] = str_replace('[', '', $splitted[$i]);
                $splitted[$i] = str_replace(']', '', $splitted[$i]);
                $splitted[$i] = $splitted[$i] . '}';
                $splitted[$i] = json_decode($splitted[$i]);
            }

            return $splitted;
        }


    }