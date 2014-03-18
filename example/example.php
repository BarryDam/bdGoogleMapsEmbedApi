<?php
	require getenv('requires');
	bdGoogleMapsEmbedApi::$API_KEY = YOUR_API_KEY;

	echo '
		<div style="border:1px solid #000;margin:10px 0;padding:10px;background-color:#e8e8e8">
			<h2 style="margin:0 0 5px 0"> Example 1: createByAddress</h2>
			$objGoogleMapsByAddress = bdGoogleMapsEmbedApi::createByAddress(\'Station Heerlen\', \'place\');<br />
			$objGoogleMapsByAddress->echoContent();
		</div>
	';

	$objGoogleMapsByAddress = bdGoogleMapsEmbedApi::createByAddress('Station Heerlen');
	$objGoogleMapsByAddress->echoContent();


	echo '
		<br />
		<div style="border:1px solid #000;margin:10px 0;padding:10px;background-color:#e8e8e8">
			<h2 style="margin:0 0 5px 0"> Example 2: createByLatLng</h2>
			$objGoogleMapsByAddress = bdGoogleMapsEmbedApi::createByLatLng(50.904747, 5.99162);<br />
			$objGoogleMapsByAddress->echoContent();
		</div>
	';

	$objGoogleMapsByAddress = bdGoogleMapsEmbedApi::createByLatLng(50.904747, 5.99162);
	$objGoogleMapsByAddress->echoContent();



?>