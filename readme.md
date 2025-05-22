# How to use it in development environment
In development environment please make sure, you added the following entries to your typoscript constant settings:

```
plugin.tx_rkwresourcespace_import {
  settings {
    localBufferDestination = /var/www/html/tmp/
    ipRestriction = ###allowedIps (comma separated)###
    resourceSpaceApi {
      baseUrl = https://###baseUrlOfResourceSpaceServer/api/
      user = ###apiUser###
      password = ###apiPassword###
      privateKey = ###apiPrivateKey###
    }
  }
}
```
Please replace the markers (###marker###) with your individual credentials. You may even comment the ipRestriction, if you do not need to test that. Then you will be allowd to access the resourcespace server with any IP.

If you want to test the corresponding ImportController you can use the following url directly within your TYPO3-Installation.

```https://###baseUrl###/index.php?id=###pidOfPlugin###&tx_rkwresourcespace_import[action]=new&tx_rkwresourcespace_import[controller]=Import&tx_rkwresourcespace_import[resourceSpaceImageId]=###resourceSpaceImageId###```

Please make sure, you have integrated the corresponding plugin ```RKW ResourceSpace: Import``` on the page with the given ```pidOfPlugin```
