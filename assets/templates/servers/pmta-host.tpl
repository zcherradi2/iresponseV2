Listen $_PORT

<VirtualHost *:$_PORT>
    ServerName 172.81.63.236
    # Allow access to the root location
    <Location />
        # For Apache 2.2, uncomment the line below instead of the "Require" directive
        Order deny,allow
        Allow from all
    </Location>
    
    # Proxy setup to forward traffic to PowerMTA's web interface
    ProxyPass / http://127.0.0.1:8080/
    ProxyPassReverse / http://127.0.0.1:8080/

    # Allow embedding in iframe by removing the X-Frame-Options header
    Header always unset X-Frame-Options

    # OPTIONAL: Add Content-Security-Policy to allow iframe embedding
    Header set Content-Security-Policy "frame-ancestors 'self' http://172.81.63.236"

    # Log files for debugging
    ErrorLog ${APACHE_LOG_DIR}/pmta-error.log
    CustomLog ${APACHE_LOG_DIR}/pmta-access.log combined
</VirtualHost>

#Listen $_PORT
#<VirtualHost *:$_PORT>

#<Location />
#    Order deny,allow
#    Allow from all
#</Location>
 
#ProxyPass / http://127.0.0.1:8080/
#ProxyPassReverse / http://127.0.0.1:8080/
#Header unset X-Frame-Options
#</VirtualHost>