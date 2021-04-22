## STUDY PROJECT
**Also see additional part INVOICE: https://github.com/elena100880/INVOICE-additional-part-of-study-project.**

**Launch with Docker in Linux**:

Execute commands:
+ `git clone https://github.com/elena100880/STUDY-PROJECT`

in project folder:
+ `compose install`
+ `docker-compose up`

Then open localhost/index.php/<route_path> in your browser.


***
**Dockerfile**

Docker-compose.yaml file in the project folder uses an official image php:7.4-apache.

Also, you can use my Dockerfile from rep: https://github.com/elena100880/dockerfile.

It includes php:8.0-apache official image (or you can change it to php:7.4-apache) and the installation of Composer, XDebug for VSC, Nano, some PHP extensions and enabling using mod rewrite (so you can skip index.php in URLs).

Execute the following commands:

  + `docker build . -t php:8.0-apache-xdebug` in the folder with Dockerfile.
  + `docker run -p -d 80:80 -v "$PWD":/var/www -w="/var/www" php:8.0-apache-xdebug composer install` in the project folder.
  + `docker run -d -p 80:80 -v "$PWD":/var/www --name oo php:8.0-apache-xdebug` in the project folder to launch the project.

***
**DataBase**

For easier using  **/var/data.db** file is added to the repository.

***
**Pages:**

+ **localhost/index.php/products** - list and filter for **products** in the shop with links to pages:
  * view a particular product - **localhost/index.php/product/{id}**;
  * add a product - **localhost/index.php/product/add**;
  * editing and deleting a particular product and **adding a product to cart** - **localhost/index.php/product/edit/{id}**;
  
+ **localhost/index.php/categories**  - list of **categories** of the products with links to:
  * editing a particular category- **localhost/index.php/category/edit/{id}** with links to:
    * adding a category - **https://localhost/index.php/category/add** ;
  
+ **localhost/index.php/category/tree/{id}** - tree of categories (parent-child tree), recursion used;
  
+ **logging in** is customised to stay in the previous page;




***
**Credentials**: 
+ login - 100880@gmail.com 
+ password - 1008
