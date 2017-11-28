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
| organization_name |
| nick name | 
| email | The primary email address |
| website | The primary website |
| address | The primary address, consists of a number of fields as defined below |
| membertype | 

The address part of the message has the following fields

| field | description |
| --- | --- |
| street_address | street and housnumber|
|supplemental_address_1 ||
|supplemental_address_2 ||
|city ||
|postal_code ||
|country| iso code of the country , remark the country determins the region of the address |

## Application Programmatic Interface (api)


## Setup

### Configuration in the settings screen
This extension has an own preference screen. Go to *Administer* | *System Settings* | *Ilga Synchronization* or directly
to the url: _http://<host>/civicrm/admin/sync_

### Enabling the Synchronization Job

### User interface for daily tasks.

### Compare a contact record between Europe and World
In the synchronized situation both records are the same.

### Sync activities
Log what the sync does. TO Do add a short description how these can be used.

### Manual Push

### Manual Pull

## Tests
This extension contains a number of tests. Run them by calling `phpunit4` in the extension directory.
`phpunit4` is part of buildkit but can be downloaded seperately at [https://phar.phpunit.de](https://phar.phpunit.de/)


