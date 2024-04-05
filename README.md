Hi,

Thank you for the opportunity to take this test. 

Here are the instructions on how to run the application. 

It is really simple and straight forwadr. 

1. clone the project
2. run composer install - this install all packages
3. setup env file. or i'll attach my sample of information needed at this file.
4. run migration: php artisan migrate - this setup the databse
5. run queue - php artisan queue:work
6. at the public directory /public/csv/ExportCSV.csv is a sample csv with 1000 data of: SKU,Name,Description,Brand 
7. the endpoint of the apis are in the  

How to test the api endpoint
1. you need to be registered in order to generate a token. 
    api/register - POST
2. login to generate token
    api/login - POST
3. to upload a csv file
    api/upload - POST
4. to get a product via the sku
    api/upload/266de418-cb3d-40e8-bbf7-7b8f70073a48 (in my example i auto generated a csv file with ) - GET
5. to logout - POST
    api/logout
6. i wrote test cases for the application on display of product. 
    run this command for test: php artisan test tests/Feature/ProductTest.php 



***************  env file ****************
JWT_SECRET=AD4PpbcgupH8Oxr3FblhOw56uUNuW6QqLEcQ15w9b4Uey95yRye226blWVmX4zLd

JWT_ALGO=HS256

JWT_SHOW_BLACKLIST_EXCEPTION=true