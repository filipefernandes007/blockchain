#Blockchain - _A blockchain prototype to understand the concept_ 

This project allows you to have a blockchain with minimum features. 
It is a Symfony 4.1 project. Feel free to test.

#Requirements

* VM Memory available >= 2048
* PHP 7.1
* [sqlite3](https://www.sqlite.org/download.html)
* [composer - Dependency Manager for PHP](https://getcomposer.org/download/) 
* [ext-pdo-sqlite](http://php.net/manual/en/ref.pdo-sqlite.php)
* [ext-sqlite3](http://php.net/manual/en/book.sqlite3.php)
* [ext-xml](http://php.net/manual/en/dom.setup.php)

#Install - Not using Vagrant

If you are not going to use Vagrant - that setups the application for you -, **ensure** that you have in your local machine
all the needed features (see Requirements).

```bash
$ git clone https://github.com/filipefernandes007/blockchain
$ cd blockchain
$ composer self-update
$ composer install
$ composer app-run
```

#Bonus

A Vagrant file if you prefer. Just run: 

```bash
$ vagrant up
```

#Troubleshooting with Vagrant

During my tests, i found the bellow message:

"_Vagrant was unable to mount VirtualBox shared folders. This is usually because the filesystem "vboxsf" is not available. This filesystem is made available via the VirtualBox Guest Additions and kernel module. Please verify that these guest additions are properly installed in the guest. This is not a bug in Vagrant and is usually caused by a faulty Vagrant box. For context, the command attempted was: mount -t vboxsf -o dmode=777,fmode=666,uid=1000,gid=1000 var_www /var/www The error output from the command was: No such device_"

If it is the case, don't worry, just run this command:

```bash
$ vagrant reload
```

If you see the same message again, go to this [link](https://stackoverflow.com/questions/43492322/vagrant-was-unable-to-mount-virtualbox-shared-folders). There are several and good approaches to follow.

#Run application

Now start the application if you haven't yet - it does not start automatically, but if you want, uncomment the `` php bin/console server:run 192.168.33.91:8000 `` command in your Vagrant file to do so next time you 'reload' Vagrant). 

In Vagrant:

```bash
$ vagrant ssh
$ cd var/www
$ composer run-vagrant-app # OR php bin/console server:run 192.168.33.91:8000 
```

In your local machine:

```bash
$ composer run-app 
```

#Unit tests

You can run tests with bash command ``` composer test ```

The unit tests interact with a test database : app_test.db.
All your API requests will interact with dev database : app.db 

###Troubleshooting

Apply ``` php bin/console cache:clear ``` if you get this message after ``` composer test ``` : 

"_Remaining deprecation notices (1)
   1x: The "Sensio\Bundle\FrameworkExtraBundle\Configuration\Route" annotation is deprecated since version 5.2. Use "Symfony\Component\Routing\Annotation\Route" instead._
"

Enjoy!