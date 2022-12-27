#GDPR Module
The GDPR extension for Magento 2 developed by Risecommerce allows your store compliant with General Data Protection Regulation using privacy policy consents, cookie restriction notice, delete or anonymous customer data like personal information, orders, newsletters, etc...

##Support: 
version - 2.3.x, 2.4.x

##How to install Extension

1. Download the archive file.
2. Unzip the file
3. Create a folder [Magento_Root]/app/code/Risecommerce/Gdpr
4. Drop/move the unzipped files to directory '[Magento_Root]/app/code/Risecommerce/Gdpr'

#Enable Extension:
- php bin/magento module:enable Risecommerce_Gdpr
- php bin/magento setup:upgrade
- php bin/magento setup:di:compile
- php bin/magento setup:static-content:deploy
- php bin/magento cache:flush

#Disable Extension:
- php bin/magento module:disable Risecommerce_Gdpr
- php bin/magento setup:upgrade
- php bin/magento setup:di:compile
- php bin/magento setup:static-content:deploy
- php bin/magento cache:flush