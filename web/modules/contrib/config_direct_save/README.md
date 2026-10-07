

* Introduction
* Requirements
* Installation
* Configuration
* Maintainers

## INTRODUCTION


Config Direct Save allow you to :
 Make backup of configurations (when you check the backup checkbox).
 Override the sync (all files with old configuration) by the new configurations
(replace all configurations files).


For the description visit project page:
<https://www.drupal.org/project/config_direct_save>

Bug reports, feature suggestions and latest developments:
<https://www.drupal.org/project/issues/2803927>


## REQUIREMENTS


**You should  before using the module** :
Give access(chmod -R 775 sites/default/files/config_HASH, HASH= Random string)
for overriding the directory sync and creating a new directories.


## INSTALLATION


 Install as you would normally install a contributed Drupal module. Visit
   <https://www.drupal.org/node/895232/> for further information.

For help regarding installation, visit:
<https://www.drupal.org/docs/extending-drupal/installing-modules>

Just install the module , go to
YOUR-SITE/admin/config/development/configuration/full/update

## CONFIGURATION


Place the entirety of this directory in the /modules folder of your Drupal
installation. Navigate to Administer > Extend. Check the 'Enabled' box next
to the 'Admin toolbar' and/or 'Admin toolbar Extra Tools' and then click
the 'Save Configuration' button at the bottom.

## MAINTAINERS


Current Maintainers:
*Mohamed Anis Taktak (matio89) - <https://www.drupal.org/u/matio89>
