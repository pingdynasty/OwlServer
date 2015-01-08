<VirtualHost *:80>

        ServerName staging.hoxtonowl.com:80 
        DocumentRoot /var/www/hoxtonowl.com/subdomains/staging/httpdocs

        ErrorLog /var/www/hoxtonowl.com/subdomains/staging/logs/error.log
        LogLevel debug
	    CustomLog /var/www/hoxtonowl.com/subdomains/staging/logs/access.log combined
        php_flag display_errors on
        SetEnv APPLICATION_ENV staging

        <Directory /var/www/hoxtonowl.com/subdomains/staging/httpdocs/>
		    Options FollowSymLinks
	        AllowOverride Limit Options FileInfo
            Order allow,deny
            allow from all
            DirectoryIndex index.php

            <Files "xmlrpc.php">
                Order Allow,Deny
                deny from all
            </Files>

            <IfModule mod_rewrite.c>
                
                RewriteEngine On
                RewriteBase /

                # Mediawiki clean URLs:
                RewriteRule ^/?wiki(/.*)?$ %{DOCUMENT_ROOT}/mediawiki/index.php [L]

                # Wordpress:
                RewriteRule ^index\.php$ - [L]
                RewriteCond %{REQUEST_FILENAME} !-f
                RewriteCond %{REQUEST_FILENAME} !-d
                RewriteRule . /index.php [L]
            </IfModule>
        </Directory>

        <Directory /var/www/hoxtonowl.com/subdomains/staging/httpdocs/_meta/>
            Order Deny,Allow
            deny from all
        </Directory>

        <Directory /var/www/hoxtonowl.com/subdomains/staging/httpdocs/_deploy/>
            AuthName "Secure Area"
            AuthType Basic
            AuthUserFile /var/www/hoxtonowl.com/subdomains/staging/.htpasswd
            require valid-user 
        </Directory>
</VirtualHost>