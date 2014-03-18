<?php
	/**
	 * This classes uses google maps embed api for fast google maps
	 * more info https://developers.google.com/maps/documentation/embed/guide?hl=nl
	 * 
	 * make sure to set your apikey before using other functions by doing :
	 * bdGoogleMapsEmbedApi::$API_KEY = 'your apikey'
	 * 
	 * @author Barry Dam 2014
	 * @version 1.0.1 
	 */
	class bdGoogleMapsEmbedApi {
		/**
		 * set the apikey befor using this script by 
		 *  bdGoogleMapsEmbedApi::$API_KEY = KEY
		 */
		public static $API_KEY = false;
		/**
		 * private vars
		 */
			private $arrSettings = array(
				'mode' 		=> false,  //place, directions, view or search 
				'width'		=> '100%',
				'height'	=> 300 // default
			);
			/**
			 *  Default params needed for the Google Maps Embed API
			 *	$array will be extended in )__construct according to the map mode
			 */
			private $arrApiParamsRequired = array();
			private $arrApiParamsOptional 	= array(
				'center' 	=> false, // lat,lng
				'zoom'	 	=> false, // (int) zoomlevel
				'maptype' 	=> 'roadmap', // (string) roadmap (default) or satelite
				'language' 	=> false, // if not supported default lang by visitor browser will be used in de API
				'region'	=> false // defines the appropriate borders and labels to displa
			);

		/**
		 * Magic methods
		 */
			public function __construct($getMode = false, $getArrOptions = array())
			{
				$arrModes = array('place', 'directions', 'view', 'search' );
				if (! in_array($getMode, $arrModes)) {
					throw new Exception($getMode.' is not a valid mode! use '.implode(' or ', $arrModes));
				}
				$this->arrSettings['mode'] = $getMode;
				/**
				 * Add the required params to $arrApiParamsRequired
				 * && Add some optional params
				 */
				switch ($getMode) {
					case 'place' :
						$this->arrApiParamsRequired['q'] = false; // (string) place or address
						break;
					case 'directions' :
						$this->arrApiParamsRequired = array(
							'origin' 		=> false,						
							'destination' 	=> false
						);
						$arrApiParamsOptional['waypoints']	= false; // (string) when just one waypoint or (array) when multiple waypoints are used
						$arrApiParamsOptional['mode']		= false; // (string) driving, walking, bicycling, transit or flying
						$arrApiParamsOptional['avoid']		= false; // (string or array) tolls, ferries and / or higwights (e.g. tolls or array('tolls','ferries'))
						$arrApiParamsOptional['units']		= false; // (string) metric or imperial
						break;
					case 'view' :
						$this->arrApiParamsRequired['center'] = false; // (string) lat lng coords
						unset($this->arrApiParamsOptional['center']);
						break;
					case 'search' :
						$this->arrApiParamsRequired['q'] = false ; // (string)  specifies the search term. It can include a geographic restriction, such as in+Seattle or near+98033.
	 					break;
				}
				$this->setOptions($getArrOptions);
			}

		/**
		 * Setters
		 */	
			/**
			 * The height of the iframe 
			 */
			public function setHeight($getIntHeight = false) {
				$getIntHeight = str_ireplace('px', '', $getIntHeight);
				if (is_numeric($getIntHeight)) $this->arrSettings['height'] = $getIntHeight;
			}
			/**
			 * The width of the iframe 
			 */
			public function setWidth($getIntWidth = false) {
				$getIntWidth = str_ireplace('px', '', $getIntWidth);
				if (is_numeric($getIntWidth)) $this->arrSettings['width'] = $getIntWidth;
			}
			/**
			 * @param associative (array) $getArrOptions 
			 */
			public function setOptions($getArrOptions = array())
			{
				if (! $getArrOptions || ! is_array($getArrOptions)) return;
				foreach ($getArrOptions as $key => $val) $this->setOption($key, $val);

			}
			public function setOption($getKey = false, $getValue = false)
			{
				if (! $getKey || ! $getValue || ! $this->arrSettings['mode']) return; 
				/**
				 * First check for required params
				 */
				$boolOptionSet = false;
				switch ($getKey) {
					case 'q' :
						$boolOptionSet = true;
						if (in_array($this->arrSettings['mode'], array('place', 'search'))) {
							$this->arrApiParamsRequired['q'] = preg_replace('/\s+/', '+', $getValue);
						}
						break;
					case 'origin' :
					case 'destination' :
						$boolOptionSet = true;			
						if ($this->arrSettings['mode'] === 'directions') {
							$this->arrApiParamsRequired[$getKey] = preg_replace('/\s+/', '+', $getValue); 
						}
						break;
					case 'center' :
						$boolOptionSet = true;
						if ($this->arrSettings['mode'] === 'view') {
							/**
							 * When mode is view. center can only be a valid lat lng coord!
							 */
							$arrCoords = explode(',', $getValue);
							if (count($arrCoords) == 2) {
								$lat = trim($arrCoords[0]);
								$lng = trim($arrCoords[1]);
								if (
									is_numeric($lat) && 
									is_numeric($lng)
								) $this->arrApiParamsRequired['center'] = $lat.','.$lng;
							} 
						} else {
							$this->arrApiParamsRequired['center'] = preg_replace('/\s+/', '+', $getValue); 
						}
						break;
				}
				if ($boolOptionSet) return true;
				/**
				 * Optional directions params
				 */
				if ($this->arrSettings['mode'] === 'directions') {
					switch ($getKey) {
						case 'waypoints':
						case 'avoid':
							$boolOptionSet = true;
							if (is_array($getValue)) 
								$getValue = implode('|', $getValue);						
							$this->arrApiParamsOptional[$getKey] = preg_replace('/\s+/', ',', $getValue); 
							break;
						case 'mode' :
							$boolOptionSet = true;
							if (in_array($getValue, array('driving', 'walking', 'bicycling', 'transit', 'flying')))
								$this->arrApiParamsOptional['mode'] = $getValue;
							break;
						case 'units' :
							$boolOptionSet = true;	
							if (in_array($getValue, array('metric', 'imperial')))
								$this->arrApiParamsOptional['mode'] = $getValue;
							break;
					}
				}
				if ($boolOptionSet) return true;
				/**
				 * Other optional params
				 */
				switch ($getKey) {
					case 'zoom' :
						$boolOptionSet = true;
						if (is_numeric($getValue)) 
							$this->arrApiParamsOptional['zoom'] = $getValue;					
						break;
					case 'maptype' :
						if (in_array($getValue, array('roadmap', 'satellite')))  
							$this->arrApiParamsOptional['maptype'] = $getValue;
						$boolOptionSet = true;
						break;
					default :
						if (array_key_exists($getKey, $this->arrApiParamsOptional)) {
							$boolOptionSet = true;
							$this->arrApiParamsOptional[$getKey] = $getValue;
						} else {
							$boolOptionSet = false;
						}
						break;					
				}
				return $boolOptionSet;
			}

		/**
		 * getters 
		 */
			private function getApiUrl() {
				if (! self::$API_KEY) 
					throw new Exception('ApiKey is not set!');
				$arrUrlParams = array();
				$strUrlParams  = '';
				/**
				 * Check if the required fields are set
				 */
				foreach ($this->arrApiParamsRequired as $keyRequired => $valueRequired) {
					if ($valueRequired === false)
						throw new Exception('Required param '.$keyRequired.' is not set!');
					else
						$strUrlParams .= '&'.$keyRequired.'='.$valueRequired;
				}	
				/**
				 * Check the optional params 
				 */
				foreach ($this->arrApiParamsOptional as $keyOptional => $valueOptional) {
					if ($valueOptional !== false) $strUrlParams .= '&'.$keyOptional.'='.$valueOptional;
				}
				/**
				 * build and return the api url
				 */
				return 'https://www.google.com/maps/embed/v1/'.$this->arrSettings['mode'].'?key='.self::$API_KEY.$strUrlParams;
			}

			public function getContent()
			{
				$strUrl =  $this->getApiUrl();
				if (! $strUrl) return false;
				$strDivWith 	= (stripos($this->arrSettings['width'], '%') === false)? $this->arrSettings['width'].'px' : $this->arrSettings['width'] ;
				$strDivHeight 	= (stripos($this->arrSettings['height'], '%') === false)? $this->arrSettings['height'].'px' : $this->arrSettings['height'] ;
				return '
					<div class="bdGoogleMapsEmbedApi" style="height:'.$strDivHeight.';width:'.$strDivWith.';">
						<iframe
						  width="'.$this->arrSettings['width'].'"
						  height="'.$this->arrSettings['height'].'"
						  frameborder="0" style="border:0"
						  src="'.$strUrl.'">
						</iframe>
					</div>
				';
			}
			public function echoContent()
			{
				echo $this->getContent();
			}
		

		/** 
		* Convenience methods 
		*/
		

			private static function getAddressFromLatLng($getLat = false, $getLng = false)
			{
				if (! $getLat || ! $getLng) return false;
				$strUrl 	= 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.$getLat.','.$getLng.'&sensor=true';
				$strResult 	=  file_get_contents($strUrl);
				if (! $strResult) return $getLat.','.$getLng;
				$arrResult	= json_decode($strResult, true);
				if (! empty($arrResult['results'][0]['formatted_address']))	return $arrResult['results'][0]['formatted_address'];			
			}

			private static function getLatLngFromAddress($getAddress = false)
			{
				if (! $getAddress) return false ;
				$strUrl 	= 'http://maps.googleapis.com/maps/api/geocode/json?address='. preg_replace('/\s+/', ',', $getAddress) .'&sensor=true';
				$strResult 	=  file_get_contents($strUrl);
				if (! $strResult) return $getLat.','.$getLng;
				$arrResult	= json_decode($strResult, true);
				if (! empty($arrResult['results'][0]['geometry']['location']))	return $arrResult['results'][0]['geometry']['location']['lat'].','.$arrResult['results'][0]['geometry']['location']['lng'];			
				
			}
		/**
		 * Static methods
		 */

		/**
		 * Can only use this when @param (string) $getMode is 'place' or 'view'
		 */
		public static function createByLatLng($getLat = false, $getLng = false, $getMode = 'place') 
		{
			if (! $getLat || ! $getLng) 
				return false;
			if (! in_array($getMode, array('place', 'view')))
				throw new Exception('Param $getMode can only be place or view');
			$arrOptions = array();
			if ($getMode == 'place')
				$arrOptions['q'] = self::getAddressFromLatLng($getLat, $getLng);
			// both needed for place and view 
			$arrOptions['center'] = $getLat.','.$getLng;
			$obj = new bdGoogleMapsEmbedApi($getMode, $arrOptions);
			$obj->setOption('zoom', 15);
			return $obj;
		}

		public static function createByAddress($getAddress = false, $getMode = 'place') 
		{
			if (! $getAddress) return false;
			if (! in_array($getMode, array('place', 'view')))
				throw new Exception('Param $getMode can only be place or view');
			$arrOptions = array();
			if ($getMode == 'place')
				$arrOptions['q'] = $getAddress;
			// both needed for place and view 
			$arrOptions['center'] = self::getLatLngFromAddress($getAddress);
			$obj = new bdGoogleMapsEmbedApi($getMode, $arrOptions);
			$obj->setOption('zoom', 15);
			return $obj;
		}
	};
?>