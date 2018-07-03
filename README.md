# GDPR-Tools

## TODO
* Support presets and custom for truncate configuration
* Check if unique generated value already exists in current database (otherwise get duplicate constraint on UPDATE because we update row by row)
* Polish README
* Document yaml configuration specifications on github wiki
* Add type options (optional, depending on which options the type offers)
* RegexType
* IbanType
* CreditcardType
* Change PasswordSha512Type to Sha512Type
* Remove UsernameType (should be replaced by StringType or RegexType)
* Support table name prefix for configuration presets

## Installation
```
composer require dbrekelmans/gdpr-tools
```

## Usage
```
php vendor/gdpr-tools/console.php <command>
```


## Commands

| Command | Description | Arguments |
| --- | --- | --- |
| `db:anonymise` | Anonymises database based on a yaml configuration. | __file__ - A yaml configuration file, must end with `.yml`. See _Configuration_ for details. |
| `db:truncate` | Truncates database tables based on a yaml configuration. | __file__ - A yaml configuration file, must end with `.yml`. See _Configuration_ for details. |

## Configuration

#### Example
```yaml
database:
  scheme: pdo_mysql
  host: 127.0.0.1
  port: 3306
  name: dbname
  user: dbuser
  password: dbpass

truncate:
  - webform_submissions
  - webform_submissions_data

anonymise:
  presets:
    - drupal8
  
  custom:
    anonymise_example:
      email:
        type: email
        unique: true
      password:
        type: password-sha512
      name:
        type: string|null
      ip:
        type: ip
      number:
        type:
          name: int|null
          options:
            min: 0
            max: 100
        
  exclude:
    drupal8:
      users_field_data:
        uid:
          - 0
          - 1
          - 46
    custom:
      anonymise_test:
        id:
          - 2
          - 5
        email:
          - example@example.com
```
