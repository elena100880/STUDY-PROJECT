## STUDY PROJECT
**Also see additional part INVOICE: https://github.com/elena100880/INVOICE-additional-part-of-study-project.**

**Launch with Docker in Linux**:

After uploading the project and executing command composer install inside the project folder You can launch the project with `docker-compose up` command in **Linux bash**. Then open http://localhost/index.php/"route_path.


***
**Dockerfile**

Docker-compose.yaml file in the project folder uses an official image php:7.4-apache.

Also, you can use Dockerfile from rep: https://github.com/elena100880/dockerfile.

It includes php:8.0-apache official image (or you can change it to php:7.4-apache) and the installation of Composer, XDebug (customised for VSC), Nano, some PHP and PECL extensions and enabling using mod rewrite (so you can skip index.php in URLs).

Execute the following commands:

  + `docker build . -t php:8.0-apache-xdebug` in the folder with Dockerfile.
  + `docker run -p -d 80:80 -v "$PWD":/var/www -w="/var/www" php:8.0-apache-xdebug composer install` in the project folder.
  + `docker run -d -p 80:80 -v "$PWD":/var/www --name oo php:8.0-apache-xdebug` in the project folder to launch the project.

***
**DataBase**

As there is a plain functional without pages for Adding/Editing such entities as Position/Supplier/Recipient - **/var/data.db** file with filled example-tables of positions/suppliers/recipients are added to the repository.

***
**Pages:**

***
**Credentials**: 
+ login - 100880@gmail.com 
+ password - 1008
