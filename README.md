# Dandomain Image Bundle

[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/loevgaard/dandomain-image-bundle/master/LICENSE)

Simple bundle to help assist in image creation and manipulation on the Dandomain webshop

## Installation

Use [Composer](http://getcomposer.org/) and install with  

`$ composer require loevgaard/dandomain-image-bundle`

Then add the bundle in `AppKernel`:

```php
<?php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Loevgaard\DandomainImageBundle\LoevgaardDandomainImageBundle(),
    );
}
```

In your config.yml, add the required settings. Use the same values as in your Dandomain admin.

```yaml
loevgaard_dandomain_image:
    image_settings:
        product:
            width: 400
        related:
            width: 150
        thumbnail:
            width: 150
        popup:
            width: 800
```

## Usage
TODO

## Authors
Joachim Løvgaard - website: [loevgaard.dk](http://www.loevgaard.dk) - twitter: [@loevgaard](https://twitter.com/loevgaard)