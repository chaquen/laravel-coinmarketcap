<?php 
namespace adman9000\coinmarketcap;

class CoinmarketcapAPI
{
    protected $key;     // API key
    protected $secret;  // API secret
    protected $url;     // API base URL
    protected $version; // API version
    protected $curl;    // curl handle

    /**
     * Constructor for BinanceAPI
     *
     */
    function __construct()
    {
        $this->url = config('coinmarketcap.urls.api');
        $this->curl = curl_init();
        curl_setopt_array($this->curl, array(
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT => 'CMC PHP API Agent',
           // CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true)
        );
        
    }

    function __destruct()
    {
        curl_close($this->curl);
    }
	

     /**
     * Get ticker
     *
     * @return asset pair ticker info
	 * Optional parameters:
	 * (int) start - return results from rank [start] and above
	 * (int) limit - return a maximum of [limit] results (default is 100, use 0 to return all results)
	 * (string) convert - return price, 24h volume, and market cap in terms of another currency. Valid values are: 
	 * "AUD", "BRL", "CAD", "CHF", "CLP", "CNY", "CZK", "DKK", "EUR", "GBP", "HKD", "HUF", "IDR", "ILS", "INR", "JPY", "KRW", "MXN", "MYR", "NOK", "NZD", "PHP", "PKR", "PLN", "RUB", "SEK", "SGD", "THB", "TRY", "TWD", "ZAR"
     */
    public function getTicker($start=0, $limit=0, $convert=false)
    {
        return $this->request("v1/ticker", array("start"=>$start, "limit"=>$limit, "convert"=>$convert));
    }



    private function request($url, $params = [], $method = "GET") {


        //Add post vars
        if($method == "POST") {
            curl_setopt($ch,CURLOPT_POST, count($params));
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
        }
		else if(sizeof($params)>0) {
			$url .= "?".http_build_query($params);
		}
		
        // Set URL & Header
        curl_setopt($this->curl, CURLOPT_URL, $this->url . $url);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array());
		
        //Get result
        $result = curl_exec($this->curl);
        if($result===false)
            throw new \Exception('CURL error: ' . curl_error($this->curl));

         // decode results
        $result = json_decode($result, true);
        if(!is_array($result))
            throw new CoinmarketcapAPIException('JSON decode error');

        return $result;

    }

}