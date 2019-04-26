<?php

class Bibox
{
    private $baseUrl;
	private $apiKey;
	private $apiSecret;
	
	public function __construct ($apiKey, $apiSecret){
		$this->apiKey    = $apiKey;
		$this->apiSecret = $apiSecret;
		$this->baseUrl   = 'https://api.bibox.com/v1/';
	}
	
	private function call ($command, $subCommand, $params = array()){
	    $paramsForget= $params;
	    $url  = $this->baseUrl.$command;
        
        $commands = json_encode(
            array(
                "cmd" => $command. "/".$subCommand,
                "body" => $params
                )
            );
        // print_r($commands); exit;
        $signature = hash_hmac('md5', $commands, $this->apiSecret); 
        
       
        $ch = curl_init($url);
        if (false) {// make false on prod or remove
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,['Content-Type: application/json',]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array("cmds" => $commands, 'apikey' => $this->apiKey, 'sign' => $signature)));
        $result = curl_exec($ch);
        print_r($result); exit;
        curl_close ($ch);
        return json_decode($result);
            
	}
	
	
	public function balances(){
	    $res = $this->call('transfer', 'transferIn');
	    return $res;
	}
	
}