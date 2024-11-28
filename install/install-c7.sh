#!/bin/bash

yum update -y

echo " Removing old Services  " 

yum remove -y httpd
yum remove -y libopendkim*
yum remove -y opendkim
yum remove -y postfix
yum remove -y php*
yum remove postgres\*
rm -rf /etc/httpd
rm -rf /etc/opendkim*
systemctl stop sendmail.service

echo " Stoping firwall and selinux "

setenforce 0
setenforce Disabled
systemctl stop iptables.service
chkconfig iptables off

echo " Installing Main services ... "

yum install -y openssh-clients
yum install -y glibc.i686
yum install -y pam.i686 pam
yum install -y nano
yum install -y rsync
yum install -y wget
yum install -y xinetd
yum install -y gcc
yum install -y make
yum install -y httpd      
yum install -y perl
yum install -y mod_ssl
yum install -y zip
yum install -y unzip
yum install -y yum-utils
yum update -y

echo " installing remi and epel "

#yum install https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
#yum install http://rpms.remirepo.net/enterprise/remi-release-7.rpm
wget https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
wget http://rpms.remirepo.net/enterprise/remi-release-7.rpm

rpm -Uvh epel-release-latest-7.noarch.rpm
rpm -Uvh remi-release-7.rpm

echo "Enabling php 7.0 "

yum-config-manager --enable remi-php70

echo " Instaling PHP moduls "

yum install -y php
yum install -y php-pgsql
yum install -y php-mysql
yum install -y php-common
yum install -y php-pdo
yum install -y php-opcache
yum install -y php-mcrypt
yum install -y php-imap
yum install -y php-mbstring
yum install -y php-soap
yum install -y php-xmlrpc
yum install -y cronie
yum install -y php-pecl-ssh2
yum update -y ca-certificates

echo " modifing servers info "

sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 5G/g' /etc/php.ini
sed -i 's/max_file_uploads = 20/max_file_uploads = 200/g' /etc/php.ini
sed -i 's/post_max_size = 8M/post_max_size = 5G/g' /etc/php.ini
sed -i 's/memory_limit = 128M/memory_limit = -1/g' /etc/php.ini
sed -i 's/max_input_time = 60/max_input_time = 3600/g' /etc/php.ini
sed -i 's/;max_input_nesting_level = 64/max_input_nesting_level = 10000/g' /etc/php.ini
sed -i 's/; max_input_vars = 1000/max_input_vars = 100000/g' /etc/php.ini
sed -i 's/default_socket_timeout = 60/default_socket_timeout = 360/g' /etc/php.ini
sed -i 's/max_execution_time = 30/max_execution_time = 3600/g' /etc/php.ini

echo " Restarting services "

systemctl restart httpd.service
systemctl enable httpd.service

echo " Installing Composer  "

curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer																								
echo " Installing Postgres DB  "

rpm -Uvh https://download.postgresql.org/pub/repos/yum/9.6/redhat/rhel-7-x86_64/pgdg-redhat-repo-latest.noarch.rpm
yum update -y

yum install postgresql96-server postgresql96-contrib -y
/usr/pgsql-9.6/bin/postgresql96-setup initdb
systemctl start postgresql-9.6.service
systemctl enable postgresql-9.6.service

rm -rf /var/lib/pgsql/9.6/data/pg_hba.conf
cp /usr/iresponse/install/pg_hba_trust.conf /var/lib/pgsql/9.6/data/pg_hba.conf

systemctl restart postgresql-9.6.service

su -c "psql" - postgres;
CREATE ROLE admin PASSWORD 'admin123' SUPERUSER CREATEDB CREATEROLE INHERIT LOGIN;
create database ir_system OWNER admin;
create database ir_clients OWNER admin;
\q

echo " Copying postgres files  "

rm -rf /var/lib/pgsql/9.6/data/pg_hba.conf
cp /usr/iresponse/install/pg_hba_md5.conf /var/lib/pgsql/9.6/data/pg_hba.conf

rm -rf /var/lib/pgsql/9.6/data/postgresql.conf
cp /usr/iresponse/install/postgresql.conf /var/lib/pgsql/9.6/data/postgresql.conf

echo " restarting services  "

systemctl restart postgresql-9.6.service
systemctl enable postgresql-9.6.service

echo " Installing phpPgAdmin "

firewall-cmd --permanent --add-service=http
sudo firewall-cmd --zone=public --permanent --add-service=http
sudo firewall-cmd --zone=public --permanent --add-port=5432/tcp
sudo firewall-cmd --reload

yum install -y epel-release
yum update -y
#yum install phpPgAdmin -y

############ Install phpPgAdmin
wget https://github.com/phppgadmin/phppgadmin/archive/REL_5-6-0.tar.gz
tar -zxvf REL_5-6-0.tar.gz
mv phppgadmin-REL_5-6-0/ /usr/share/phppgadmin
mv /usr/share/phppgadmin/conf/config.inc.php-dist /usr/share/phppgadmin/conf/config.inc.php

rm -rf /usr/share/phppgadmin/conf/config.inc.php
cp /usr/iresponse/install/config.inc.php /usr/share/phppgadmin/conf/config.inc.php

nano /etc/httpd/conf.d/phpPgAdmin.conf

echo "
Alias /db /usr/share/phppgadmin

<Directory /usr/share/phppgadmin>
     <IfModule mod_authz_core.c>
         # Apache 2.4
		<RequireAny>
         Require all granted
		</RequireAny>
     </IfModule>
     <IfModule !mod_authz_core.c>
         # Apache 2.2
         Order deny,allow
         Allow from 127.0.0.1
         Allow from ::1
     </IfModule>
</Directory> 

" > /etc/httpd/conf.d/phpPgAdmin.conf

systemctl restart httpd.service

echo "  "

psql -U admin -d ir_system -a -f /usr/iresponse/install/ir_system.sql
psql -U admin -d ir_clients -a -f /usr/iresponse/install/ir_clients.sql

echo " Instaling Java "

cd /opt/
wget --no-cookies --no-check-certificate --header "Cookie: gpw_e24=http%3A%2F%2Fwww.oracle.com%2F; oraclelicense=accept-securebackup-cookie" "https://github.com/frekele/oracle-java/releases/download/8u212-b10/jdk-8u212-linux-x64.tar.gz"
tar -xvf jdk-8u212-linux-x64.tar.gz
cd /opt/jdk1.8.0_212/
alternatives --install /usr/bin/java java /opt/jdk1.8.0_212/bin/java 2
alternatives --config java
alternatives --install /usr/bin/jar jar /opt/jdk1.8.0_212/bin/jar 2
alternatives --install /usr/bin/javac javac /opt/jdk1.8.0_212/bin/javac 2
alternatives --set jar /opt/jdk1.8.0_212/bin/jar
alternatives --set javac /opt/jdk1.8.0_212/bin/javac
export JAVA_HOME=/opt/jdk1.8.0_212
export JRE_HOME=/opt/jdk1.8.0_212/jre
export PATH=$PATH:/opt/jdk1.8.0_212/bin:/opt/jdk1.8.0_212/jre/bin
export PATH=$JAVA_HOME/bin:$PATH

echo 'apache ALL = NOPASSWD: /opt/jdk1.8.0_212/bin/java' | sudo EDITOR='tee -a' visudo
alternatives --config java 1
alternatives --config --auto java
alternatives --config --auto=1 java

echo " configuring HTTTP APP "



sed -i '#NameVirtualHost *:80/NameVirtualHost *:80/g' /etc/httpd/conf/httpd.conf

echo "
<VirtualHost *:80>
        ServerName #Ip_Dialk
        DocumentRoot '/usr/iresponse/public/'
        <Directory /usr/iresponse/public/>
                AllowOverride all
                Options Indexes FollowSymLinks
                Order Deny,Allow
                Require all granted
        </Directory>
</VirtualHost>
nano  /etc/httpd/conf.d/iresponse.conf

cd /usr/iresponse/
chown -R apache:apache /usr/iresponse
chown -R apache:apache /usr/iresponse/storage/logs
chown -R apache:apache /usr/iresponse/storage/*
chown -R apache:apache /usr/iresponse/public/*

systemctl restart httpd.service

echo "
Script Installed = = Success!
Wait for the restart and point the domain as per the instructions above. " 




0 * * * * java -Dfile.encoding=UTF8 -jar /usr/iresponse/app/api/iresponse_services.jar eyJ1c2VyLWlkIjoxLCJlbmRwb2ludCI6IlNlcnZlcnMiLCJhY3Rpb24iOiJjaGVja1NlcnZlcnNDb25uZWN0aXZpdHkiLCJwYXJhbWV0ZXJzIjpbXX0=
*/15 * * * * java -Dfile.encoding=UTF8 -jar /usr/iresponse/app/api/iresponse_services.jar eyJ1c2VyLWlkIjoxLCJlbmRwb2ludCI6IkFmZmlsaWF0ZSIsImFjdGlvbiI6ImdldENvbnZlcnNpb25zIiwicGFyYW1ldGVycyI6eyJhZmZpbGlhdGUtbmV0d29ya3MtaWRzIjpbXSwicGVyaW9kIjoidGhpcy1tb250aCJ9fQ==
0 */4 * * * java -Dfile.encoding=UTF8 -jar /usr/iresponse/app/api/iresponse_services.jar eyJ1c2VyLWlkIjoxLCJlbmRwb2ludCI6IlRvb2xzIiwiYWN0aW9uIjoiY29sbGVjdExlYWRlcnMiLCJwYXJhbWV0ZXJzIjp7InBlcmlvZCI6InRvZGF5In19


service crond restart
systemctl restart crond.service
systemctl status crond.service