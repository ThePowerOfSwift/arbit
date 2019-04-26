<?php
class Withdraw_fee{
    public function get_fee($exchange, $coin){
        if($exchange == 'kraken'){
           if($coin == 'BTC'){
               return '0.0005';
           }else if($coin == 'ETH'){
               return '0.005';
           }else if($coin == 'LTC'){
               return '0.001';
           }else if($coin == 'BCH'){
               return '0.0001';
           }else if($coin == 'XRP'){
               return  '0.02';
           }else if($coin == 'EOS'){
               return  '0.05';
           }else if($coin == 'XMR'){
               return  '0.05';
           }else if($coin == 'XLM'){
               return  '0.00002';
           }else if($coin == 'DASH'){
               return  '0.005';
           }else{
              return 'no coin match';
           }
        }else if($exchange == 'poloniex'){
           if($coin == 'BTC'){
               return '0.0005';
           }else if($coin == 'ETH'){
               return '0.01';
           }else if($coin == 'LTC'){
               return '0.001';
           }else if($coin == 'BCH'){
               return '0.0001';
           }else if($coin == 'XRP'){
               return  '0.15';
           }else if($coin == 'EOS'){
               return  '0';
           }else if($coin == 'XMR'){
               return  '0.015';
           }else if($coin == 'XLM'){
               return  '0.00001';
           }else if($coin == 'DASH'){
               return  '0.01';
           }else{
               return 'no coin match';
           }
        }else if($exchange == 'binance'){
           if($coin == 'BTC'){
               return '0.0005';
           }else if($coin == 'ETH'){
               return '0.01';
           }else if($coin == 'LTC'){
               return '0.01';
           }else if($coin == 'BCH'){
               return '0.001';
           }else if($coin == 'XRP'){
               return  '0.25';
           }else if($coin == 'EOS'){
               return  '0.05';
           }else if($coin == 'XLM'){
               return  '0.01';
           }else if($coin == 'ADA'){
               return  '1';
           }else{
               return 'no coin match';
           }
        }else if($exchange == 'btcmarkets'){
           if($coin == 'BTC'){
               return '0.0001';
           }else if($coin == 'ETH'){
               return '0.001';
           }else if($coin == 'LTC'){
               return '0.001';
           }else if($coin == 'BCH'){
               return '0.0001';
           }else if($coin == 'XRP'){
               return  '0.15';
           }else{
              return 'no coin match';
           }
        }else if($exchange == 'bittrex'){
            if($coin == 'BTC'){
               return '0.0005';
           }else if($coin == 'ETH'){
               return '0.006';
           }else if($coin == 'LTC'){
               return '0.01';
           }else if($coin == 'BCH'){
               return '0.001';
           }else if($coin == 'XRP'){
               return  '1';
           }else if($coin == 'XMR'){
               return  '0.04';
           }else if($coin == 'DASH'){
               return  '0.002';
           }else if($coin == 'ADA'){
               return  '0.2';
           }else{
              return 'no coin match';
           }
        }else if($exchange == 'hitbtc'){
            if($coin == 'BTC'){
               return '0.001';
           }else if($coin == 'ETH'){
               return '0.00958';
           }else if($coin == 'LTC'){
               return '0.003';
           }else if($coin == 'BCH'){
               return '0.0018';
           }else if($coin == 'XRP'){
               return  '0.509';
           }else if($coin == 'EOS'){
               return  '0.01';
           }else if($coin == 'XMR'){
               return  '0.09';
           }else if($coin == 'DASH'){
               return  '0.03';
           }else if($coin == 'XLM'){
               return  '0.01';
           }else if($coin == 'ADA'){
               return  '0.01';
           }else{
              return 'no coin match';
           }
        }else if($exchange == 'independentreserve'){
            if($coin == 'BTC'){
               return '0.0002';
           }else if($coin == 'ETH'){
               return '0.001';
           }else if($coin == 'LTC'){
               return '0.001';
           }else if($coin == 'BCH'){
               return '0.0001';
           }else if($coin == 'XRP'){
               return  '0.15';
           }else{
              return 'no coin match';
           }
        }else if($exchange == 'huobi'){
            if($coin == 'BTC'){
               return '0.001';
           }else if($coin == 'ETH'){
               return '0.005';
           }else if($coin == 'LTC'){
               return '0.001';
           }else if($coin == 'BCH'){
               return '0.0001';
           }else if($coin == 'XRP'){
               return  '0.1';
           }else if($coin == 'XMR'){
               return  '0.01';
           }else if($coin == 'ADA'){
               return  '0.01';
           }else{
              return 'no coin match';
           }
        }else if($exchange == 'bithumb'){
            return '0';
        }else if($exchange == 'livecoin'){
            if($coin == 'BTC'){
                return '0.0005 ';
            }else if($coin == 'ETH'){
                return '0.01 ';
            }else if($coin == 'LTC'){
                return '0';
            }else if($coin == 'BCH'){
                return '0.001 ';
            }else if($coin == 'EOS'){
                return  '0';
            }else{
                return 'no coin match';
            }
        }else if($exchange == 'exmo'){
            if($coin == 'BTC'){
               return '0.0005';
           }else if($coin == 'ETH'){
               return '0.01';
           }else if($coin == 'LTC'){
               return '0.01';
           }else if($coin == 'BCH'){
               return '0.001';
           }else if($coin == 'XMR'){
               return  '0.05';
           }else if($coin == 'DASH'){
               return  '0.01';
           }else if($coin == 'XLM'){
               return  '0.01';
           }else if($coin == 'ADA'){
               return  '1';
           }else{
              return 'no coin match';
           }
        }else{
            return 'no exchange';
        }
    }
    
    public function tag_req($exchange, $coin){
        if($exchange == 'kraken'){
           return false;
        }else if($exchange == 'poloniex'){
           if($coin == 'XMR'){
               return true;
           }else{
               return false;
           }
        }else if($exchange == 'binance'){
           if($coin == 'XMR' || $coin == 'XRP'){
               return true;
           }else{
               return false;
           }
        }else if($exchange == 'btcmarkets'){
           if($coin == 'XRP'){
               return true;
           }else{
               return false;
           }
        }else if($exchange == 'bittrex'){
           if($coin == 'XRP'){
               return true;
           }else{
               return false;
           }
        }else if($exchange == 'hitbtc'){
            if($coin == 'XMR' || $coin == 'XRP'){
               return true;
           }else{
               return false;
           }
        }else if($exchange == 'independentreserve'){
            if($coin == 'XRP'){
               return true;
           }else{
               return false;
           }
        }else if($exchange == 'huobi'){
           if($coin == 'XRP' || $coin == 'XMR' || $coin == 'EOS'){
               return true;
           }else{
               return false;
           }
        }else if($exchange == 'bithumb'){
            if($coin == 'XRP' || $coin == 'XMR'){
               return true;
           }else{
               return false;
           }
        }else if($exchange == 'livecoin'){
               return false;
           
        }else if($exchange == 'exmo'){
           if($coin == 'XRP'){
               return true;
           }else{
               return false;
           }
        }else{
            return 'no exchange';
        }
    }
}