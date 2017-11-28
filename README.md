# Ilga Synchronization

## Use Case


## Concepts and Principles

### Subscription
* Subscription on region.
* Subscription on global code.

### Message

| field | description |
| --- | --- |
| ilga_id | Unique identifier that is the same in both databases. In the world database its the CiviCRM id. In the Europe database, it is a custom field on contacts|

## Application Programmatic Interface (api)

## User interface

### Settings
This extension has an own preference screen. Go to *Administer* | *System Settings* | *Ilga Synchronization* or directly
to the url: _http://<host>/civicrm/admin/sync_

### Manual Push

### Manual Pull

## Tests

This extension contains a number of tests. Run them by calling `phpunit4` in the extension directory.
`phpunit4` is part of buildkit but can be downloaded seperately at [https://phar.phpunit.de](https://phar.phpunit.de/)


