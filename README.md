anycontent-cms-construction-kit-php
===================================


## Installation

Just create a composer.json file with following content

    {
        "require": {
            "php": ">=5.3",
            "nhagemann/anycontent-cms-construction-kit-php": "0.2.*@dev"
        },
        "scripts": {
            "post-update-cmd": "AnyContent\\CMCK\\Command\\Installer::postInstallUpdate",
            "post-install-cmd": "AnyContent\\CMCK\\Command\\Installer::postInstallUpdate"
        },
        "minimum-stability": "dev",
        "prefer-stable": true
    }
