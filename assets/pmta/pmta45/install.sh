yum -y update;
ip=$(ip route get 8.8.8.8 | sed -n '/src/{s/.*src *\([^ ]*\).*/\1/p;q}')
yum -y install perl;
chmod 755 PowerMTA-4.5r8.rpm;
rpm -ivh PowerMTA-4.5r8.rpm;
mv license /etc/pmta/;
rm -rf /usr/sbin/pmtad;
mv pmtad /usr/sbin/;
chmod 750 /usr/sbin/pmtad;
chown pmta:pmta /etc/pmta/config;
chmod 640 /etc/pmta/config;
mkdir -p /var/spool/pmtaPickup/;
mkdir -p /etc/pmta/domainKeys/;
openssl genrsa -out /etc/pmta/domainKeys/$ip.private 1024
openssl rsa -in /etc/pmta/domainKeys/$ip.private -pubout -out /etc/pmta/domainKeys/$ip.public
mkdir -p /var/spool/pmtaPickup/;
mkdir -p /var/spool/pmtaPickup/Pickup;
mkdir -p /var/spool/pmtaPickup/BadMail;
mkdir -p /var/spool/pmtaIncoming;
chown pmta:pmta /var/spool/pmtaIncoming;
chmod 755 /var/spool/pmtaIncoming;
chown pmta:pmta /var/spool/pmtaPickup/*
mkdir -p /var/log/pmta;
mkdir -p /var/log/pmtaAccRep;
mkdir -p /var/log/pmtaErr;
mkdir -p /var/log/pmtaErrRep;
chown pmta:pmta  /var/log/pmta;
chown pmta:pmta  /var/log/pmtaAccRep;
chown pmta:pmta  /var/log/pmtaErr;
chown pmta:pmta /var/log/pmtaErrRep;
chmod 755 /var/log/pmta;
chmod 755 /var/log/pmtaAccRep;
chmod 755 /var/log/pmtaErr;
chmod 755 /var/log/pmtaErrRep;
echo "############# Enjoy the magic #############";
echo "############# Writen By Rekblog.com #######";