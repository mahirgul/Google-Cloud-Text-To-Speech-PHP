<?php

class GoogleTTS
{
	function getSound($text, $apikey, $lang, $voiceName, $folder)
	{
		//if folder is not exist create folder
		if (!file_exists($folder)) 
		{
			mkdir($folder, 0777, true);
		}
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
					"audioEncoding"=>"ALAW",
					"sampleRateHertz"=>8000
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
				$fp = fopen("${filename}.wav", 'w');
        			fwrite($fp,base64_decode($responseDecoded['audioContent']));
        			fclose($fp);
			}
			return null;  
		}
	}
}

?>
