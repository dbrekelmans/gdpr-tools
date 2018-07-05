# GDPR-Tools

## TODO
* Support presets and custom for truncate configuration
* Check if unique generated value already exists in current database (otherwise get duplicate constraint on UPDATE because we update row by row)
* Polish README
* Document yaml configuration specifications on github wiki
* Support table name prefix for configuration presets

## Installation
```
composer require dbrekelmans/gdpr-tools
```

## Usage
```
php vendor/dbrekelmans/gdpr-tools/console.php <command>
```

## Commands

| Command | Description | Arguments |
| --- | --- | --- |
| `db:anonymise` | Anonymises database based on a yaml configuration. | __file__ - A yaml configuration file, must end with `.yml`. See _Configuration_ for details. |
| `db:truncate` | Truncates database tables based on a yaml configuration. | __file__ - A yaml configuration file, must end with `.yml`. See _Configuration_ for details. |

## Configuration

### Types
| Type | Options | Description |
| --- | --- | --- |
| `string` | `minlength`, `maxlength` | Random string of a-z characters with length between `minlength` and `maxlength`. |
| `int` | `min`, `max` | A random integer between `min` and `max` (inclusive). |
| `email` | | Valid email address. |
| `ip` | | Valid IPv4 address. |
| `regex` | `pattern` | Random string based on regex `pattern`. |
| `null` | | A NULL value. |
| `password` | `encryption` | Password with a certain `encryption`. |

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
        type: 
          name: email
        unique: true
      password:
        type:
          name: password
          options:
            encryption: sha512
      name:
        type: 
          name: string|null
      ip:
        type:
          name: ip
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
      anonymise_example:
        id:
          - 2
          - 5
        email:
          - example@example.com
```
