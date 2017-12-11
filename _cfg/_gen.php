<?php
/**
 * @package newspage-platform
 * @author Petru Szemereczki <petru.office92@gmail.com>
 * @since 1.0
 */

$hostname = 'robery.eu';
$projectdir = '/sites/' . $hostname;

$nginxconf =<<<CFG
server {
    listen 80;

    server_name {$hostname};
    root {$projectdir}/web;

    error_log {$projectdir}/var/nginx/error.log;
    access_log {$projectdir}/var/nginx/access.log;

    # strip app.php/ prefix if it is present
    rewrite ^/app\.php/?(.*)$ /$1 permanent;

    location / {
        index app.php;
        try_files \$uri @rewriteapp;
    }

    location @rewriteapp {
        rewrite ^(.*)$ /app.php/$1 last;
    }

    # pass the PHP scripts to FastCGI server from upstream phpfcgi
    location ~ ^/app\.php(/|$) {
        fastcgi_pass phpfcgi;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param  SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT \$document_root;
    }
}
CFG;

if (file_exists('nginx')) {
    unlink('nginx');
}

file_put_contents('nginx', 'upstream phpfcgi {
    server unix:/run/php/php7.0-fpm.sock;
}', FILE_APPEND);
file_put_contents('nginx', PHP_EOL, FILE_APPEND);
file_put_contents('nginx', $nginxconf, FILE_APPEND);