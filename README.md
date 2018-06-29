# GDPR-Tools

## TODO
* Polish README
* Document yaml configuration specifications on github wiki

## Installation
```
composer require dbrekelmans/gdpr-tools
```

## Usage
```
php vendor/gdpr-tools/console.php <command>
```


## Commands

| Command | Description | Arguments | Options |
| --- | --- | --- | --- |
| `db:anonymise` | Anonymises database based on a yaml configuration. | __file__ - A yaml configuration file, must end with `.yml`. See _Anonymise configuration_ for details. | |

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
      
except:
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
