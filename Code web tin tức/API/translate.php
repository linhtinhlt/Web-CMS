<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subscriptionKey = "7VNCw05vQMqiPyV7Lwech3T9AwQ57hzu6MLnOrGhZGCOTHZS0ir8JQQJ99AKACqBBLyXJ3w3AAAbACOGW1Mv";
    $endpoint = "https://api.cognitive.microsofttranslator.com/translate?api-version=3.0";
    $region = "southeastasia";

    $texts = json_decode($_POST['text'], true); 
    $toLanguage = $_POST['to'];
    $fromLanguage = $_POST['from'];

    function loadDictionary($filePath) {
        $json = file_get_contents($filePath); 
        return json_decode($json, true); 
    }

   
    $dictionary = loadDictionary('./glossary.json'); 

  
    function translateText($text, $dictionary, $direction) {
        if ($direction == 'vi_to_en') {
            foreach ($dictionary['vi_to_en'] as $vi => $en) {
                $text = str_ireplace($vi, $en, $text);
            }
        } else {
            
            foreach ($dictionary['en_to_vi'] as $en => $vi) {
                $text = str_ireplace($en, $vi, $text);
            }
        }
        return $text;
    }

    $texts = array_map(function($text) use ($dictionary, $toLanguage, $fromLanguage) {
        $direction = ($fromLanguage == 'vi' && $toLanguage == 'en') ? 'vi_to_en' : 'en_to_vi';
        return translateText($text, $dictionary, $direction); // Dịch văn bản với từ điển
    }, $texts);

  
    $url = $endpoint . "&to=" . $toLanguage;
    $headers = [
        "Ocp-Apim-Subscription-Key: $subscriptionKey",
        "Ocp-Apim-Subscription-Region: $region",
        "Content-Type: application/json"
    ];

    $body = json_encode(array_map(fn($text) => ['Text' => $text], $texts));

    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

    $response = curl_exec($ch);
    curl_close($ch);

    header('Content-Type: application/json');
    echo $response;
}
?>
