# Docker TYPO3 CMS

[![](https://badge.imagelayers.io/hbokh/docker-typo3-cms.svg)](https://imagelayers.io/?images=hbokh/docker-typo3-cms:latest 'Get your own badge on imagelayers.io')


Container with the latest [TYPO3](http://typo3.org/typo3-cms/) CMS 6.2 LTS, served by nginx and PHP-FPM.  
Great for learning, testing and demo's. **Don't use in production!**

[![hbokh docker-typo3-cms](http://dockeri.co/image/hbokh/docker-typo3-cms)](https://hub.docker.com/u/hbokh/docker-typo3-cms/)

Inspired by and borrowed from [paimpozhil/magento-docker](https://registry.hub.docker.com/u/paimpozhil/magento-docker/).

## Quick start

[Install docker-compose](http://docs.docker.com/compose/install/#install-compose) and run `docker-compose up`.  

File: docker-compose.yml:

```
mariadb:
  image: paintedfox/mariadb:latest
  environment:
    - USER=mariadb
    - PASS=p4ssw0rd
typo3cms:
  image: hbokh/docker-typo3-cms:latest
  links:
    - mariadb:db
  ports:
    - "80:80"
```

Use `mariadb/p4ssw0rd` for the database-credentials.  

Restart after the stack has been stopped, use `docker-compose up --no-recreate`.

## Manually

The TYPO3-container needs a MySQL-container to link to.  
I used [paintedfox/mariadb](https://registry.hub.docker.com/u/paintedfox/mariadb/) (which equals MySQL 5.5).

First install and start the database:  
`docker run -td --name mariadb -e USER=mariadb -e PASS=p4ssw0rd paintedfox/mariadb`

Followed by the webserver on port 80 and linked to the database:  
`docker run -td --name typo3-cms -p 80:80 --link mariadb:db hbokh/docker-typo3-cms`

## Configure TYPO3 CMS

Open a webbrowser to `http://<container IP>/` and configure TYPO3.  
First time startup takes a while, because extensions etc. are downloaded and installed.  

For the database-host use the name "db", with USER and PASS as set for the database-container (`mariadb/p4ssw0rd`).

You can install the TYPO3 Introduction Package for a start:

![image](https://github.com/hbokh/docker-typo3-cms/raw/master/TYPO3_introduction.png)

## Build the container from source

`git clone https://github.com/hbokh/docker-typo3-cms.git .`

`docker build --rm=true -t hbokh/docker-typo3-cms .`

`docker run -td -p 80:80 --link mariadb:db hbokh/docker-typo3-cms`

## TODO

- Mount external data inside the container.

## Issues

###TYPO3 error

TYPO3 gives this error after installation:  

![image](https://github.com/hbokh/docker-typo3-cms/raw/master/TYPO3_error.png)

This is related to [TYPO3-CORE-SA-2014-001: Multiple Vulnerabilities in TYPO3 CMS](http://typo3.org/teams/security/security-bulletins/typo3-core/typo3-core-sa-2014-001/).

A fix is to login into the container and add a line to the file

 `/var/www/site/htdocs/typo3conf/LocalConfiguration.php`

using *docker exec* (introduced in Docker v1.3):  `$ docker exec -it <container ID> bash`  

```
$ docker exec -it typo3cms bash
root@01c255c6173d:/# vi /var/www/site/htdocs/typo3conf/LocalConfiguration.php
```

At the bottom of the file, within the SYS-array, add this line containing the *trustedHostsPattern*:

	'SYS' => array(
                [ ... ],
		'trustedHostsPattern' => '.*',
	),

This is somewhat of a showstopper to use the container straight away, but is only needed the first time.

### Environment

When running the DB-instance and webserver in seperate containers on separate hosts (e.g. when using Rancher), these environment setting have to be set too: `DB_ENV_USER=mariadb` and `DB_ENV_PASS=p4ssw0rd`. If not, an error like this will be shown in the container-log of the DB:

    Access denied for user 'root'@'<IP-address>' (using password: NO)

For some reason this is not needed when running on the same Docker-host.
