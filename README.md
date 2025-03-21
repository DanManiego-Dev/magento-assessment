# Magento Custom Badges - Setup Guide

Ensure you have the following before proceeding:
- Docker installed on your machine
- Magento 2.4.7-p3 (Community Edition) compatibility
- Mark Shust's Docker Magento setup
- Git installed on your system

## Installation Steps
### Clone the Repository
* Navigate to your development directory and ``clone the repository``:
```sh
cd your-dev-directory
git clone https://github.com/DanManiego-Dev/magento-assessment.git
```

### Create and Navigate to the Project Directory
* Create a directory for testing the module and move into it:
```sh
mkdir -p ~/your-dev-directory/magento-assessment-testing
cd $_
```

### Setup the Magento Environment
* Run the following command to set up Magento using [``Mark Shust's Docker Magento``](https://github.com/markshust/docker-magento?tab=readme-ov-file#setup):
```sh
curl -s https://raw.githubusercontent.com/markshust/docker-magento/master/lib/onelinesetup | bash -s -- magento.test community 2.4.7-p3
```

### Test the URL
* Visit the [``https://magento.test/``](https://magento.test/) and [``https://magento.test/admin``](https://magento.test/admin) to check if the site is working

### Replace Default `app/code` with Module Source Code
* Replace the default `app/code` directory with the module's source code:
```sh
cp -R ~/your-dev-directory/magento-assessment/src/app/code src/app/
```

### Start the Magento Environment
* Run the following command to ``restart the Docker container``:
```sh
bin/restart
```

### Deploy Sample Data
* To ``add Magento's sample data``, execute:
```sh
bin/magento sampledata:deploy
```

### Setup Grunt (Optional)
* To setup ``grunt``, execute:
```sh
bin/setup-grunt
```

### Upgrade Magento Setup
* Run the following command to ``apply the necessary database changes``:
```sh
bin/magento setup:upgrade
```

### Execute Grunt (Optional)
* To execute ``grunt``, execute:
```sh
bin/grunt exec
```

### Enable and Configure the Module
* Enable the ``Custom Badges Module`` and disable the ``Two-factor Authentication modules``:
```sh
bin/magento module:enable DevTeam_CustomBadges
bin/magento module:disable Magento_AdminAdobeImsTwoFactorAuth Magento_TwoFactorAuth
```

### Upgrade Magento Setup
* Run the following command to ``apply the necessary database changes`` and ``flush the cache``:
```sh
bin/magento setup:upgrade
bin/magento cache:flush
```

## Assigning Custom Badges
To assign a custom badge:
* Login as [``Admin``](https://magento.test/admin)
* Go to ``Catalog > Products``
* ``Add or Edit`` a product
* Scroll down and open ``Images and Videos`` section
* Upload an ``Image``
* Click an ``Image``
    * Select ``Custom Badge`` as role
    * Although done automatically, you can manually enable the ``Hide from product page`` checkbox ***(Optional)***
* Click ``Save``