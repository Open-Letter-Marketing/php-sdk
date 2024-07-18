# OLC SDK for PHP

The OLC SDK for PHP provides developers with a straightforward interface to
integrate OLC's services into PHP applications. It abstracts away the complexities
of direct API interaction, offering simplified methods to perform common tasks
such as data retrieval, updates, and authentication.

This SDK aims to streamline the development process by providing consistent and
reliable access to OLC's functionalities, ensuring seamless integration and optimal
performance for PHP-based projects.

## Installation
```shell
composer require olc/olc-php
```

## Version Guidance

| Version    | Status | PHP Version |
|------------|--------|-------------|
| dev-master | Latest | &gt;=7.4    |


## API Keys
To get api keys, you must sign up to the OLC website at https://openletterconnect.com/ and
request a new API key.

## Quick Start

```php
<?php declare(strict_types=1);

use Olc\Olc;
use Olc\core\OlcRequestError;

// To create an instance of Olc, pass your API key as a parameter
$instance = Olc::create('your_api_key');

try {
  $response = $instance->templates()->all();
  print_r($response['data']);
} catch (OlcRequestError $e) {
  echo 'Failed to retrieve templates: ' . $e->getMessage();
}
```

## `.env` Configuration &amp; Usage
You can also configure your API key in a `.env` file. This file should be located in
the root of your project.

To do so, simply add the following to your `.env` file:
```ini
OLC_API_KEY="your_api_key"
OLC_API_VERSION="v1"
OLC_API_ENDPOINT="https://api.openletterconnect.com"
```
Please note that the `OLC_API_VERSION` and `OLC_API_ENDPOINT` keys are optional, and
the default values are used if not specified.

Once done, You can use a different method to create an instance of Olc, as shown in the
example below:

```php
<?php declare(strict_types=1);

use Olc\libs\Instance;
use Olc\core\OlcRequestError;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
  $response = Instance::getInstance()->templates()->all();
  print_r($response['data']);
} catch (OlcRequestError $e) {
  echo 'Failed to retrieve templates: ' . $e->getMessage();
}
```

## API Modules
The OLC SDK for PHP is built to be modular, allowing you to easily use with ease,

for instance, the method `Instance::getInstance()->*` returns an instances of the following modules. 
Each module has its own set of methods that can be used to interact with the OLC API.

| module         | Description                                                                    |
|----------------|--------------------------------------------------------------------------------|
| `user`         | User module that retrieve the user information *against the api key*.          |
| `customFields` | Custom fields module which performs actions against user-defined input fields. |
| `templates`    | Fetches or creates new templates.                                              |
| `products`     | Retrieve account level products.                                                    |
| `orders`       | Create or fetch orders, calculate cost and other operations.                   |
| `orderDetails` | Retrieve detailed information against the order.                               |

For detailed information regarding each module, please refer to the [API Reference](https://open-letter-marketing.github.io/php-sdk/).

## Templates

### Creating a new template:
To create a new template, you can use the `templates` module. In the following example, we will create a new template.
```php
<?php declare(strict_types=1);

use Olc\libs\Instance;
use Olc\core\OlcRequestError;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
  $response = Instance::getInstance()->templates()->create([
    'title' => 'My Template',
    'productId' => 9,
    'jsonFile' => __DIR__ . '/assets/template.json',
    'thumbnailFile' => __DIR__ . '/assets/image.jpg',
    'backThumbnailFile' => __DIR__ . '/assets/image.jpg',
    'fields' => [
      [
        'key' => '{{CF.FIRST_NAME}}',
        'value' => 'First Name',
      ],
    ],
  ]);
  print_r($response['data']);
} catch (OlcRequestError $e) {
  echo 'Failed to retrieve templates: ' . $e->getMessage();
}
```

## What's next?
To work with other modules, please refer to the [Core API Reference](https://open-letter-marketing.github.io/php-sdk/classes/Olc-core-OlcInstance.html#method_templates).
