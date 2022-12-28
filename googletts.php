#!/usr/bin/php -q
<?php

//$speech_api_key = "your key";

saveSound();

function getSound($text)
        {    
			$speech_api_key = "your_google_tts_api_key";

            
            $text = trim($text);

            if($text == '') return false;
            
            $params = [
                "audioConfig"=>[
                    "audioEncoding"=>"LINEAR16",
                    "pitch"=> "1",
                    "speakingRate"=> "1",
                    "effectsProfileId"=> [
                        "medium-bluetooth-speaker-class-device"
                      ]
                ],
                "input"=>[
                    "text"=>$text
                ],
                "voice"=>[
                    "languageCode"=> "tr-TR",
                    "name" =>"tr-TR-Wavenet-D"
                ]
            ];

            $data_string = json_encode($params);

            $url = 'https://texttospeech.googleapis.com/v1/text:synthesize?fields=audioContent&key=' . $speech_api_key;
            $handle = curl_init($url);
            
            curl_setopt($handle, CURLOPT_CUSTOMREQUEST, "POST"); 
            curl_setopt($handle, CURLOPT_POSTFIELDS, $data_string);  
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handle, CURLOPT_HTTPHEADER, [                                                                          
                'Content-Type: application/json',                                                                                
                'Content-Length: ' . strlen($data_string)
                ]                                                                       
            );
            $response = curl_exec($handle);              
            $responseDecoded = json_decode($response, true);  
            curl_close($handle);
            if($responseDecoded['audioContent']){
                return $responseDecoded['audioContent'];                
            } 

            return false;  
        }

function saveSound()
   {
	   $text ="Hello, How can i help you.";
      $speech_data = getSound($text);//see method upper

      if($speech_data) {                
         $file_name = strtolower(md5(uniqid($text)) . '.mp3');
         //$path = FileUpload::getFolder();//just return directory path
         if(file_put_contents($file_name, base64_decode($speech_data))){
             return $file_name;
             }
         }

        return null;
   }


?>
