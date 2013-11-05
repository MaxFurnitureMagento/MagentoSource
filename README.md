MagentoSource
=============

Source code for [MaxFurniture](http://www.maxfurniture.com) website

Install
-------

We assume that document root /var/www/html is configured with host `localhost`

* checkout source code into /var/www/html/maxf
* load database into created database maxf
* update admin password if needed, this tool is good at it [N98](https://github.com/netz98/n98-magerun)
* update configuration

```sql
    UPDATE core_config_data SET value='http://localhost/maxf/' WHERE path LIKE('%base_url');
    UPDATE core_config_data SET value='http://localhost/maxf/media/' WHERE path LIKE('%base_media_url');
    UPDATE core_config_data SET value='localhost' WHERE path='web/cookie/cookie_domain'

    # Remove all customers to avoid problems with real accounts
    DELETE from customer_entity;

    # Remove live payment configurations
    delete from core_config_data where path like('payment/authorizenet%');
    delete from core_config_data where path like('payment/amazonpayments%');
    delete from core_config_data where path like('payment/payflow%');
    delete from core_config_data where path like('payment/paypal%');

    # Remove live orders
    delete from sales_flat_order;
```

* Notice, for some reason in chrome, admin panel did not work for me. Use firefox instead

* Deactivate lightspeed and make requests flow through index.php

    cp .htaccess.develop .htaccess 
    
### Deploy code on production

1. Checkout source code from repository

    git clone https://github.com/MaxFurnitureMagento/MagentoSource.git 
    
2. Fix permissions (permissions are not ooptimal right now in terms of security)

    cd TO\_CODE\_DIR
    chmod -R 755 *
    chmod -R 777 var skin media axZm*

3. Add configurations

    cp .htaccess.producton .htaccess
    copy local.xml and lightspeed.xml from production

4. Sync folders:
    
    - `axZm/pic`
    - `axZm4/pic`
    - `media`
