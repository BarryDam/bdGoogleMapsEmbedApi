bdGoogleMapsEmbedApi
===================

With bdGoogleMapsEmbedApi you can easily use the Google Maps Embed API (https://developers.google.com/maps/documentation/embed/)

Small examples:

By Address:
```php
	$bdGoogleMapsEmbedApi = bdGoogleMapsEmbedApi::createByAddress('Station Heerlen');
	$bdGoogleMapsEmbedApi->echoContent();
```

By Lat Lng
```php
	$bdGoogleMapsEmbedApi = bdGoogleMapsEmbedApi::createByLatLng(50.904747, 5.99162);
	$bdGoogleMapsEmbedApi->echoContent();
```

##BUY ME A COFFEE##
[![PayPayl donate button](https://img.shields.io/badge/paypal-donate-yellow.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XX68BNMVCD7YS "Donate once-off to this project using Paypal")