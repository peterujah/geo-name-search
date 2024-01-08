<?php
/**
 * GeoNameSearch - Php Geoname search wrapper, to help reduce too much api call
 * @author      Peter Chigozie(NG) peterujah
 * @copyright   Copyright (c), 2021 Peter(NG) peterujah
 * @license     MIT public license
 */
namespace Peterujah\NanoBlock;
use \Exception;
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
    private string $style = 'SHORT';

	/**
	 * Hold the selected return type
	 */
    private string $type = 'json';

	/**
	 * Hold the local path to save api data
	 */
    private string $filepath = '';

	/**
	 * Hold bool status to prepend all states array
	 */
    private bool $includeAllStates = false;

	/**
	 * Hold the country object or array
	 */
    private $countryList;

	/**
	 * Hold the return language type
	 */
	private string $language = 'en';

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
	private bool $useCache = true;

	/**
	 * Hold the base country 
	 */
	private $baseCountry;

	/**
	 * Hold the directory cache base prefix 
	 */
	private $prefix;

	private bool $stripState = true;

    public function __construct(string $username, string $baseCountry = 'Nigeria'){
        //$this->setCountries(new Country(null, Country::BASIC));
		$this->setFilepath(__DIR__ . "/temp/");
        $this->username = $username;
		$this->baseCountry = $baseCountry;
    }

	/**
     * Set to allow using Cache Class
     * @param bool $use true or false
     * @return GeoNameSearch $this
     */
	public function useCache(bool $use): self 
	{
        $this->useCache = $use;
		return $this;
    }

	/**
     * Set return language type
     * @param string $lang language code
     * @return GeoNameSearch $this
     */
	public function setLang(string $lang): self 
	{
        $this->language = $lang;
		return $this;
    }

	/**
     * Set return maximum limit
     * @param int $max max rows
     * @return GeoNameSearch $this
     */
	public function setLimit(int $max): self 
	{
		$this->maxLimit = $max;
		return $this;
	}

	/**
     * Set south bounding
     * @param mixed $south point
     * @return GeoNameSearch $this
     */
	public function setSouth($south): self 
	{
		$this->south = $south;
		return $this;
	}

	/**
     * Set north bounding
     * @param mixed $north point
     * @return GeoNameSearch $this
     */
	public function setNorth($north): self 
	{
		$this->north = $north;
		return $this;
	}

	/**
     * Set west bounding
     * @param mixed $west point
     * @return GeoNameSearch $this
     */
	public function setWest($west): self 
	{
		$this->west = $west;
		return $this;
	}

	/**
     * Set east bounding
     * @param mixed $east point
     * @return GeoNameSearch $this
     */
	public function setEast($east): self 
	{
		$this->east = $east;
		return $this;
	}

	/**
     * Set return type
     * @param string $type
     * @return GeoNameSearch $this
     */
    public function setType(string $type): self 
	{
        $this->type = $type;
		return $this;
    }

	/**
     * Set return style
     * @param string $style
     * @return GeoNameSearch $this
     */
    public function setStyle(string $style): self 
	{
        $this->style = $style;
		return $this;
    }

	/**
     * Set the local path to store response data
     * @param string $path location ending with /
     * @return GeoNameSearch $this
     */
    public function setFilepath(string $path): self 
	{
        $this->filepath = $path;
		return $this;
    }

	/**
     * Set countries array or use our default \Peterujah\NanoBlock\Country
     * @param object|Country|array $countries class instance or array
	 * new Country(null, Country::BASIC);
     * @return GeoNameSearch $this
     */
	public function setCountries(mixed $countries): self 
	{
		$this->countryList = $countries;
		return $this;
    }

	/**
     * Set to allow prepend of additional array in list with all states
     * @param bool $add true or false
     * @return GeoNameSearch $this
     */
    public function allowAllStates(bool $add): self 
	{
        $this->includeAllStates = $add;
		return $this;
    }

	/**
     * Set remove state in state name before searching
     * @param bool $add true or false
     * @return GeoNameSearch $this
     */
    public function stripWordState(bool $strip): self 
	{
        $this->stripState = $strip;
		return $this;
    }

	/**
     * Gets the full file path and file name
	 * 
     * @return string directory filepath / filename
    */
    public function getFullPath(): string
	{
		$name = $this->get("name");
        return $this->filepath . (!empty($name) ? strtoupper($name) . "/{$this->prefix}/" : "ALL/{$this->prefix}/") . md5($this->query) . ".json";
    }

	/**
     * Gets the file path
	 * 
     * @return string directory filepath
    */
    public function getFilepath(): string 
	{
		$name = $this->get("name");
        return $this->filepath . (!empty($name) ? strtoupper($name) . "/{$this->prefix}/" : "ALL/{$this->prefix}/");
    }

	/**
     * Gets the file name
     * @return string directory filename
     */
    public function getFilename(): string 
	{
        return md5($this->query) . ".json";
    }

	/**
     * Gets all states array row
	 * 
     * @return array all states array
     */
	private function allArray(): array 
	{
        return [
            "lng" => null,
            "geonameId" => 683735635,
            "countryCode" => null,
            "name" => "All States",
            "toponymName" => "All States",
            "lat" => null,
            "fcl" => null,
            "fcode" => null
		];
    }

	/**
     * Gets country detail by key
	 * 
	 * @param string $key array key index
	 * 
     * @return string all states array
     */
	private function get(string $key): string
	{
		if(is_array($this->countryList)){
			$list = $this->countryList;
		}else{
			$list = $this->countryList->getPath($this->baseCountry, "list");
		}
		return $list[$key] ?? '';
	}

	/**
     * Query and return all states in a given country
	 * 
	 * @param string $country country name or country code
	 * 
     * @return array list states
     */
	public function states(string $country): array 
	{
		$this->baseCountry = $country;
		return $this->search($country, "", "states");
	}

	/**
     * Query and return all cities in a given state
	 * 
	 * @param string $state state name
	 * @param string $country states in country
	 * 
     * @return array list cities
     */
	public function cities(string $state, string $country): array 
	{
		$state = trim($this->stripState ? str_replace("state", "", strtolower($state)) : $state);
		$this->baseCountry = $country;
		return $this->search($state, "", "cities");
	}

	/**
     * Query and return cities, states in a given country or cities in a query state
	 * 
	 * @param string $country country
	 * 
     * @return array list states
     */
	public function query(string $country): array 
	{
		$this->baseCountry = $country;
		return $this->search($country, "", "query");
	}

	/**
     * Search query and return cities, states in a given country or cities in a query state
	 * @param string $query query city, states, place country
	 * @param string $country country
	 * @param string $prefix
	 * 
     * @return array list states
     */
    public function search(string $query, string $country, string $prefix): array 
	{
        $this->country = urlencode(htmlentities($country));
        $this->query = urlencode(htmlentities($query));
		$this->prefix = $prefix;
        $param  = [
            'type' => $this->type, 
            'style' => $this->style,
            'username' => $this->username,
			"lang" => $this->language
		];

		if(!empty($this->query)){
			$param["q"] = $this->query;
		}

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

		if(@file_exists($this->getFullPath())){
			$res = json_decode(@file_get_contents($this->getFullPath()), true);
		}else{
			$res = $this->fetch($this->endpoint . http_build_query($param), $param);
			if($res["status"] == 200 && $this->useCache){
				$this->store($res);
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
	 * 
	 * @param string $link request url and parameters
	 * @param array $param request array parameters
	 * 
     * @return array list api response
     */
    public function fetch(string $link, array $param): array 
	{
		try{
			$error = null;
			if(class_exists('\GuzzleHttp\Client')){
				$client = new \GuzzleHttp\Client();
				$response = $client->request('GET', $link, $param);
				$payload = $response->getBody()->getContents();
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

			if($error !== null){
				return [
					'status' => 202, 
					'statusText' => 'request_error',
					'message' => $error, 
				];
			}

			$data = json_decode($payload, true);
			if (empty($data["geonames"]) || (int) $data["totalResultsCount"] < 1) {
				return [
					'status' => 201, 
					'statusText' => 'data_empty',
					'url' => $link,
					'data' => $data,
					'message' => 'Empty response data.', 
					'error' => null,
				];
			}

			return [
				'status' => 200, 
				'statusText' => 'has_data',
				'data' => array_merge([
					'ISO' => $this->get("short_name"), 
					'prefix' => $this->get("prefix") ?? $this->get("code"), 
					'country' => $this->get("name") ?? null
				], $data)
			];
		} catch (Exception $e) {
			return [
				'status' => 202, 
				'statusText' => 'request_error',
				'message' => $e->getMessage(), 
				'error' => $e,
			];
		}
    }

	/**
     * Store api response data for later use
	 * 
	 * @param array $data json
	 * 
     * @return void 
     */
	private function store($data): void 
	{
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
	}
}
