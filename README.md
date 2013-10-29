MagentoSource
=============

Source code for [MaxFurniture](http://www.maxfurniture.com) website

Install
-------

We assume that document root /var/www/html is configured with host `localhost`

* checkout source code into /var/www/html/maxf
* load database into created database maxf
* update admin password if needed, this tool is goodat it [N98](https://github.com/netz98/n98-magerun)
* update configuration

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

* Notice, for some reason in chrome, admin panel did not work for me. Use firefox instead

* Deactivate lightspeed and make requests flow through index.php


    7. #DirectoryIndex lightspeed.php
    8. DirectoryIndex index.php

    58. #RewriteRule .* lightspeed.php [L]
    59. RewriteRule .* index.php [L]



