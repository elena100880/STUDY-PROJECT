## STUDY PROJECT
**Also see additional part INVOICE: https://github.com/elena100880/INVOICE-additional-part-of-study-project.**

**Launch with Docker in Linux**:

Execute commands:
+ `git clone https://github.com/elena100880/STUDY-PROJECT`

in project folder (composer and docker-compose should be installed):
+ `composer install`
+ `docker-compose -f docker-compose_simple.yaml up` - it will build the image for php:8.0-apache and launch the project

Then open localhost/<route_path> in your browser.


***
**Dockerfile**

Docker-compose_simple.yaml uses a simple Dockerfile_simple, which uses official image php:8.0-apache, adds to this image pdo_mysql and enables mod rewrite (so you can skip index.php in URLs).

Also, you can use more complicated Dockerfile_mine.  It includes php:8.0-apache official image (or you can change it to php:7.4-apache) and the installation of Composer, XDebug for VSC, Nano, some PHP extensions.

Take notice that building image from Dockerfile_mine will take more time. 

In order to do so execute the following commands:
+ `git clone https://github.com/elena100880/STUDY-PROJECT` - if not executed yet

in project folder:
+ `composer install`  - if not executed yet
+ `docker rm $(docker ps -a -q)` - to remove already used containers
+ `sudo chmod 777 my_sql/ -R ` - if project was initialyy launched with another Dockerfie 

+ `docker-compose -f docker-compose_mine.yaml up`

Then open localhost/<route_path> in your browser.

***
**DataBase**

For easier using Database `<project-folder>/my_sql/sql_data/study_sql`  is added to the repository.

***
**Pages:**

+ **localhost/products** - list and filter for **products** in the shop with links to pages:
  * view a particular product - **localhost/product/{id}**;
  * add a product - **localhost/product/add**;
  * editing and deleting a particular product and **adding a product to cart** - **localhost/product/edit/{id}**;
  
+ **localhost/categories**  - list of **categories** of the products with links to:
  * editing a particular category- **localhost/category/edit/{id}** with links to:
    * adding a category - **/localhost/category/add** ;
  
+ **localhost/category/tree/{id}** - tree of categories (parent-child tree), recursion used;
  
+ **logging in** is customised to stay in the previous page;

+ **localhost/cart** - editing the cart with added products and link to: 
  * completing and sending the order - **/localhost/order**




***
**Credentials**: 

internet-shop:
+ login - 100880@gmail.com 
+ password - 1008

phpmyadmin:
+ login - root 
+ password - 1008
