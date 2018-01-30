# Turiknox Link Guest Orders
## Overview

A Magento 2 module that checks if newly registered customers have made orders in the past as a guest. If so, the orders are assigned to their account.

The functionality runs via the cron once a day at 1am by default.


## Requirements

Magento 2.1.x, 2.2.x

## Installation

Copy the contents of the module into your Magento root directory.

Enable the module via the command line:

/path/to/php bin/magento module:enable Turiknox_LinkGuestOrders

Run the database upgrade via the command line:

/path/to/php bin/magento setup:upgrade

Run the compile command and refresh the Magento cache:

/path/to/php bin/magento setup:di:compile
/path/to/php bin/magento cache:clean

## Usage

Stores -> Configuration -> Customer -> Customer Configuration -> Link Guest Orders
