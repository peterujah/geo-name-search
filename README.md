# geo-name-search
Php Geoname search wrapper, to help reduce too much api call

## Installation

Installation is super-easy via Composer:
```md
composer require peterujah/geo-name-search
```

# USAGES

First you have to create an account with geoname to acquire api username http://www.geonames.org/export/geonames-search.html

Initialize GeoNameSearch with `geoname api username`

```php
$geo = new \Peterujah\NanoBlock\GeoNameSearch("username");
```

List all states in any country `Nigeria`

```php 
$geo->states("Nigeria");
```

List all cities in any sates `Enugu`

```php 
$geo->cities("Enugu");
```

Search `geoname` with query and country

```php 
$geo->query("Query", "Country");
```
