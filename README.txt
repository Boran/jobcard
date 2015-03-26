Experiment to view jobcards

Learning to use Fatfree (with bootstrap).

Prerequisites
- The fatfree framework, bootsrap, bootstrap-sortable are included.

- apache: enable the rewrite module
  a2enmod rewrite
  service apache2 restart

- Get this code
  cd /var/www   (or your webroot)
  git clone https://github.com/Boran/jobcard.git
  cd jobcard

  # tmp dir
  mkdir tmp
  chown www-data tmp
  # adjust settings
  vi config.ini

- need boranmpk mysql db

Docs to get going
----------------
http://fatfreeframework.com/home
https://github.com/bcosca/fatfree
http://stackoverflow.com/questions/tagged/fat-free-framework
https://groups.google.com/forum/?fromgroups#!forum/f3-framework

http://getbootstrap.com/
http://getbootstrap.com/components

