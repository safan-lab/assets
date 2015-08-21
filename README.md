Assets Module for Safan Framework
===============

REQUIREMENTS
------------
PHP > 5.4.0

SETUP
------------

If you're using [Composer](http://getcomposer.org/) for your project's dependencies, add the following to your "composer.json":

```
"require": {
    "safan-lab/assets": "1.0.*"
}
```
Update Modules Config List - safan-framework-standard/application/Settings/modules.config.php
```
<?php
return [
    // Safan Framework default modules route
    'Safan'         => 'vendor/safan-lab/safan/Safan',
    'SafanResponse' => 'vendor/safan-lab/safan/SafanResponse',
    // Write created or installed modules route here...
    'Assets' => 'vendor/safan-lab/assets/Assets'
];
```
Add Configuration - safan-framework-standard/application/Settings/main.config.php
```
<?php
'init' => [
    'assets' => [
        'class'  => 'Assets\Assets',
        'method' => 'init',
        'params' => [
            'path' => 'assets'
        ]
    ]
]
```

