<?php
/**
 * GeoNameSearch - Php Geoname search wrapper, to help reduce too much api call
 * @author      Peter Chigozie(NG) peterujah
 * @copyright   Copyright (c), 2021 Peter(NG) peterujah
 * @license     MIT public license
 */
namespace Peterujah\NanoBlock;
use \Peterujah\NanoBlock\Country;
use \Peterujah\NanoBlock\Cache;
use \GuzzleHttp\Client;
/**
 * Class GeoNameSearch.
 */
class GeoNameSearch {
	/**
	 * Hold the return type format 
	 */
    public const JSON = "json";
	public const XML = "xml";
	public const RDF = "rdf";
	
	/**
	 * Hold the verbosity of return styles
	 */
    public const SHORT = "SHORT";
	public const MEDIUM = "MEDIUM";
	public const LONG = "LONG";
 	public const FULL = "FULL";

	 /**
	 * Hold api endpoint url
	 */
    protected $endpoint = "http://api.geonames.org/search?";

	/**
	 * Hold api username
	 */
    protected $username;

	/**
	 * Hold the selected verbosity return style
	 */
    private $style;

	/**
	 * Hold the selected return type
	 */
    private $type;

	/**
	 * Hold the local path to save api data
	 */
    private $filepath;

	/**
	 * Hold bool status to prepend all states array
	 */
    private $includeAllStates;

	/**
	 * Hold the country object or array
	 */
    private $countryList;

	/**
	 * Hold the return language type
	 */
	private $language;

	/**
	 * Hold the maximum row to select
	 */
	private $maxLimit;

	/**
	 * Hold the query south bounding
	 */
	private $south;

	/**
	 * Hold the query north bounding
	 */
	private $north;

	/**
	 * Hold the query west bounding
	 */
	private $west;

	/**
	 * Hold the query east bounding
	 */
	private $east;

	/**
	 * Hold the status of caching method
	 */
	private $useCache;

    public function __construct($username){
        $this->setType(self::JSON);
        $this->setStyle(self::SHORT);
        $this->setCountries(null);
		$this->setFilepath(__DIR__ . "/temp/");
		$this->setLang("en");
		$this->allowAllStates(false);
		$this->useCache(true);
        $this->username = $username;
    }

	/**
     * Set to allow using Cache Class
     * @param bool $use true or false
     * @return GeoNameSearch|object $this
     */
	public function useCache(bool $use){
        $this->useCache = $use;
		return $this;
    }

	/**
     * Set return language type
     * @param string $lang language code
     * @return GeoNameSearch|object $this
     */
	public function setLang(string $lang){
        $this->language = $lang;
		return $this;
    }

	/**
     * Set return maximum limit
     * @param int $max max rows
     * @return GeoNameSearch|object $this
     */
	public function setLimit(int $max){
		$this->maxLimit = $max;
		return $this;
	}

	/**
     * Set south bounding
     * @param mixed $south point
     * @return GeoNameSearch|object $this
     */
	public function setSouth($south){
		$this->south = $south;
		return $this;
	}

	/**
     * Set north bounding
     * @param mixed $north point
     * @return GeoNameSearch|object $this
     */
	public function setNorth($north){
		$this->north = $north;
		return $this;
	}

	/**
     * Set west bounding
     * @param mixed $west point
     * @return GeoNameSearch|object $this
     */
	public function setWest($west){
		$this->west = $west;
		return $this;
	}

	/**
     * Set east bounding
     * @param mixed $east point
     * @return GeoNameSearch|object $this
     */
	public function setEast($east){
		$this->east = $east;
		return $this;
	}

	/**
     * Set return type
     * @param string $type
     * @return GeoNameSearch|object $this
     */
    public function setType(string $type){
        $this->type = $type;
		return $this;
    }

	/**
     * Set return style
     * @param string $style
     * @return GeoNameSearch|object $this
     */
    public function setStyle(string $style){
        $this->style = $style;
		return $this;
    }

	/**
     * Set the local path to store response data
     * @param string $path location ending with /
     * @return GeoNameSearch|object $this
     */
    public function setFilepath($path){
        $this->filepath = $path;
		return $this;
    }

	/**
     * Set countries array or use our default \Peterujah\NanoBlock\Country
     * @param object|array $objOrArr class instance or array
     * @return GeoNameSearch|object $this
     */
	public function setCountries($objOrArr){
        if(empty($objOrArr) && class_exists('\Peterujah\NanoBlock\Country')){
            $this->countryList = new Country(null, Country::SERVICE);
        }else{
            $this->countryList = $objOrArr;
        }
		return $this;
    }

	/**
     * Set to allow prepend of additional array in list with all states
     * @param bool $add true or false
     * @return GeoNameSearch|object $this
     */
    public function allowAllStates(bool $add){
        $this->includeAllStates = $add;
		return $this;
    }
    
	/**
     * Gets the full file path and file name
     * @return string directory filepath / filename
     */
    public function getFullPath(){
        return $this->filepath . (!empty($this->country) ? strtoupper($this->country) . "/" : "all/") . md5($this->query) . ".json";
    }

	/**
     * Gets the file path
     * @return string directory filepath
     */
    public function getFilepath(){
        return $this->filepath . (!empty($this->country) ? strtoupper($this->country) . "/" : "all/");
    }

	/**
     * Gets the file name
     * @return string directory filename
     */
    public function getFilename(){
        return md5($this->query) . ".json";
    }

	/**
     * Gets all states array row
     * @return array all states array
     */
	private function allArray(){
        return array(
            "lng" => null,
            "geonameId" => 683735635,
            "countryCode" => null,
            "name" => "All States",
            "toponymName" => "All States",
            "lat" => null,
            "fcl" => null,
            "fcode" => null
        );
    }

	/**
     * Gets country detail by key
	 * @param string $key array key index
     * @return array all states array
     */
	private function get($key){
		if(is_array($this->countryList)){
			$list = $this->countryList;
		}else{
			$list = $this->countryList->getPath($this->country, "list");
		}
		return $list[$key] ?? null;
	}

	/**
     * Query and return all states in a given country
	 * @param string $country country name or country code
     * @return object|array list states
     */
	public function states($country){
		return $this->query($country, "");
	}

	/**
     * Query and return all cities in a given state
	 * @param string $state state name
     * @return object|array list cities
     */
	public function cities($state){
		return $this->query($state, "");
	}

	/**
     * Query and return cities, states in a given country or cities in a query state
	 * @param string $query query city, states, place country
	 * @param string $country country
     * @return object|array list states
     */
    public function query($query, $country = ""){
        $this->country = urlencode(htmlentities($country));
        $this->query = urlencode(htmlentities($query));
        $param  = array(
            'q' => $this->query,
            'type' => $this->type, 
            'style' => $this->style,
            'username' => $this->username,
			"lang" => $this->language
        );

		if(!empty($this->country)){
			$param["country"] = $this->country;
		}

		if(!empty($this->maxLimit)){
			$param["maxRows"] = $this->maxLimit;
		}

		if(!empty($this->south)){
			$param["south"] = $this->south;
		}

		if(!empty($this->north)){
			$param["north"] = $this->north;
		}

		if(!empty($this->west)){
			$param["west"] = $this->west;
		}

		if(!empty($this->east)){
			$param["east"] = $this->east;
		}

		if($this->useCache && class_exists('\Peterujah\NanoBlock\Cache')){
			$res = (new Cache($this->query, $this->getFilepath()))->widthExpired("ALL", function () use($param) {
				return $this->fetch($this->endpoint . http_build_query($param), $param);
			}, 60*1000, true);
		}else{
			if(@file_exists($this->getFullPath())){
				$res = json_decode(@file_get_contents($this->getFullPath()));
			}else{
				$res = $this->fetch($this->endpoint . http_build_query($param), $param);
				if($res["status"] == 200){
					$res = $this->store($res);
				}
			}
		}

		if($res["status"] == 200 && $this->includeAllStates){
			$res["data"]["geonames"][] = $this->allArray();
			sort($res["data"]["geonames"]);
		}
		return $res;
    }

	/**
     * Fetch free data from geoname server
	 * @param string $link request url and parameters
	 * @param array $param request array parameters
     * @return object|array list api response
     */
    public function fetch($link, $param){
		if(class_exists('\GuzzleHttp\Client')){
			$client = new Client();
			$req = $client->request('GET', $link, $param);
			$payload = $req->getBody();
			$error = null;
		}else{
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $link);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
			curl_setopt($curl, CURLOPT_POST,1);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
			curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
			curl_setopt($curl, CURLOPT_TIMEOUT, 120);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
			$payload = curl_exec($curl);
			$error = curl_error($curl);
			curl_close ($curl);
		}

        if(!empty($error)){
			$res = array(
				'status' => 202, 
				'statusText' => 'request_error',
				'message' => $error, 
			);
		}else{
			$data = json_decode($payload, true);
			if(empty($data["geonames"]) or (int) $data["totalResultsCount"] < 1){
				$res = array(
					'status' => 201, 
					'statusText' => 'data_empty',
					'url' => $link,
					'data' => $data,
					'message' => 'empty res data.', 
					'error' => (!empty($error) ? $error : null), 
				);
			}else{
				$res = array(
					'status' => 200, 
					'statusText' => 'has_data',
					'data' => array_merge(array(
						'ISO' => $this->get("short_name"), 
						'prefix' => $this->get("prefix") ?? $this->get("code"), 
						'country' => $this->get("name") ?? null
					), $data)
				);
			}
        }
        return $res;
    }

	/**
     * Store api response data for later use
	 * @param array $data json
     * @return json api response
     */
	private function store($data){
		if(!@is_dir($this->getFilepath())){
			@mkdir($this->getFilepath(), 0777, true);
			@chmod($this->getFilepath(), 0755); 
		}
		
		if(@file_exists($this->getFullPath())){
			@unlink($this->getFullPath());
		} 

		$fp = @fopen($this->getFullPath(), 'w');
		@fwrite($fp, json_encode($data));
		@fclose($fp);
		return $data;
	}
}
