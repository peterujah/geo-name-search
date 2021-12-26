# geo-name-search
Php Geoname search wrapper, to help reduce too much api call

## Installation

Installation is super-easy via Composer:
```md
composer require peterujah/geo-name-search
```

# USAGES

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



| Options         | Description                                                                         |
|-----------------|-------------------------------------------------------------------------------------|
| prepare(string)            | Call "prepare" with sql query string to prepare query execution                                                   |
| query(string)            | Call "query" width sql query without "bind" and "param"                                                  |
| bind(param, value, type)          | Call "bind" to bind value to the pdo prepare method                                  |
| param(param, value, type)           | Call "param" to bind parameter to the pdo statment                                    |
| execute()           | Execute prepare statment                                       |
| rowCount()           | Get result row count                                      |
| getOne()           | Get one resault row, this is useful when you set LIMIT 1                                       |
| getAll()           | Retrieve all result                                      |
| getInt()           | Gets integer useful when you select COUNT()                                      |
| getAllObject()          | Gets result object                                       |
| getLastInsertedId()           | Gets list inserted id from table                                      |
| free()           | Free database connection                                       |
| dumpDebug()           | Dump debug sql query parameter                                      |
| errorInfo()           | Print PDO prepare statment error when debug is enabled                                     |
| error()           | Print connection or execution error when debug is enabled                                     |
| setDebug(bool)           | Sets debug status                                       |
| setConfig(array)           | Sets connection config array                                       |
| conn()           | Retrieve DBController Instance useful when you call "setConfig(config)"                                    |


Connection Config array example 

```php 
[
     PORT => 3306,
     HOST => "localhost",
     VERSION => "mysql",
     NAME => "dbname",
     USERNAME => "root",
     PASSWORD => ""
]
```
