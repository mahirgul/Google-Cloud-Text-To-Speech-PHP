<?php

class GoogleTTS
{
	function getSound($text, $apikey, $lang, $voiceName, $folder)
	{
		//get md5 hash filename
		$file_name = $folder.strtolower(md5($text));
		
		if(file_exists("$file_name.wav"))
		{
			//if file is exist return filename
			return $file_name;
		}
		else
		{
			//if file is not exist start process
			$text = trim($text);
			if($text == '') return false;
			//Create request text
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
					"languageCode"=> $lang,
					"name" =>$voiceName
				]
			];

			$data_string = json_encode($params);

			$url = "https://texttospeech.googleapis.com/v1/text:synthesize?fields=audioContent&key=$apikey";
			$handle = curl_init($url);
			//Create curl text			
			curl_setopt($handle, CURLOPT_CUSTOMREQUEST, "POST"); 
			curl_setopt($handle, CURLOPT_POSTFIELDS, $data_string);  
			curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($handle, CURLOPT_HTTPHEADER, [                                                                          
				'Content-Type: application/json',                                                                                
				'Content-Length: ' . strlen($data_string)
				]                                                                       
			);
			//run curl request
			$response = curl_exec($handle);              
			$responseDecoded = json_decode($response, true);  
			curl_close($handle);
			
			//if curl request getting back the data
			if($responseDecoded['audioContent'])
			{
				$speech_data = $responseDecoded['audioContent'];				
				
				if(file_put_contents("$file_name.mp3", base64_decode($speech_data)))
				{
					//Decode data and save file
					//mp3 file convert to wav
					shell_exec("ffmpeg -i $file_name.mp3 -ac 1 -ab 128k -ar 8000 -acodec pcm_s16le $file_name.wav");
					//set wav file chmod
					shell_exec("chmod 777 .$file_name.wav");
					//delete mp3 file
					shell_exec("rm $file_name.mp3");
					//return filename
					return $file_name;
				}			

			}
			return null;  
		}
	}
}

?>
