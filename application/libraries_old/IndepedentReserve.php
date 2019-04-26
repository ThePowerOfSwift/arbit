<?php
class IndependentReserve
{
    protected $apiKey;
    protected $apiSecret;
    protected $baseUrl;
    public function __construct($apiKey, $apiSecret)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->baseUrl = 'https://api.independentreserve.com/';
    }
    /**
     * Get the components required to make private API calls.
     * @return array
     */
    private function mapToKeyValue($url, $data) {
        $res = $url;
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $res .= ",$key=".array_values($value)[0]; // works only for case when array has one value. And no docs how to use for array with more then one value
            } else {
                $res .= ",$key=$value";
            }
        }
        return $res;
    }
     
    public function getSignature($url, $params)
    {
        $nonce = str_pad(str_replace('.', '', microtime(true)), 19, 0);
        if(is_array($params)){
            $params =  array_merge(['apiKey' => $this->apiKey, 'nonce' => $nonce], $params);
        }else{
            $params = array('apiKey' => $this->apiKey, 'nonce' => $nonce);
        }
        $strToSign = $this->mapToKeyValue($url,$params);
        $signature = strtoupper(hash_hmac('sha256', utf8_encode($strToSign), utf8_encode($this->apiSecret)));
        return [
            'apiKey' => $this->apiKey,
            'nonce' => $nonce,
            'signature' => $signature,
        ];
    }
    
    
    private function call($method, $params = null, $req, $type){
        
        $uri  = $this->baseUrl.$req.'/'.$method;
        $seg = $this->getSignature($uri, $params);
        if(is_array($params)){
            $data = array_merge($seg, $params);
            $json_date = json_encode($data);
        }else{
            $json_date = json_encode($seg);
        }
        
        //echo $json_date;
        //exit;
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_date);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        $headers = array();
        $headers[] = "Content-Type: application/json";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        return json_decode($result);
        curl_close ($ch);
        
    }
    
    public function get_deposit_address($currency){
        $method='GetDigitalCurrencyDepositAddress';
        $request = 'Private';
        $type='POST';
        $params = array('primaryCurrencyCode' => $currency);
        
        return $this->call($method, $params, $request, $type);
    }
    
    public function get_accounts(){
        $method='GetAccounts';
        $request = 'Private';
        $type='POST';
        $params = null;
        
        return $this->call($method, $params, $request, $type);
    }
    
    public function place_order($currency, $ordertype, $volume){
        $method='PlaceMarketOrder';
        $request = 'Private';
        $type='POST';
        $params = array('primaryCurrencyCode' => $currency, 'secondaryCurrencyCode' => 'Usd', 'orderType' => $ordertype, 'volume' => $volume.'');
        
        return $this->call($method, $params, $request, $type);
    }
    
    public function withdraw($amount, $address, $currency, $tag = false){
        $method='WithdrawDigitalCurrency';
        $request = 'Private';
        $type='POST';
        if($tag){ $params = array('amount' => $amount.'', 'withdrawalAddress' => $address, 'comment' => 'api withdraw', 'primaryCurrencyCode' => $currency, 'destinationTag' => $tag); }
        else{ $params = array('amount' => $amount.'', 'withdrawalAddress' => $address, 'comment' => 'api withdraw', 'primaryCurrencyCode' => $currency);}
        
        return $this->call($method, $params, $request, $type);
    }
    
}
