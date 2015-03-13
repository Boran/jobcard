Experiment to view jobcards

Learning to use Fatfree (with bootstrap).

prerequisites: install in /var/www:
- need boranmpk mysql db
- fatfree-master
 wget https://github.com/bcosca/fatfree/archive/master.zip
 unzip master.zip
 mv fatfree-master /var/www

- bootstrap
 wget http://getbootstrap.com/2.3.2/assets/bootstrap.zip
 unzip bootstrap.zip

- apache: enable the rewrite module
  a2enmod rewrite
  service apache2 restart

- Get this code
  cd /var/www   (or webroot)
  git clone https://github.com/Boran/jobcard.git
  cd jobcard

  # tmp dir
  mkdir tmp
  chown www-data tmp
  # adjust settings
  vi config.ini


Doc to get going
----------------
http://fatfreeframework.com/home
https://github.com/bcosca/fatfree
http://stackoverflow.com/questions/tagged/fat-free-framework
https://groups.google.com/forum/?fromgroups#!forum/f3-framework

http://getbootstrap.com/

