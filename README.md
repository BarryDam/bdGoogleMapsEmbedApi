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