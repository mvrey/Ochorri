#!/bin/bash
#somos--soy-- imbecil. No necesitabamos cron. Solo necesitabamos un puto bucle infinito con una pausa de un segundo.
#ahora ya está
while (true) do
date
date >> /opt/lampp/htdocs/ochmvc/public/batch/cronlog
#/opt/lampp/bin/php /home/blacloud/htdocs/public/batch/cronned.php >> /home/blacloud/htdocs/public/cronlog
/opt/lampp/bin/php /opt/lampp/htdocs/ochmvc/public/controllers/batch/cronned.php >> /opt/lampp/htdocs/ochmvc/public/batch/cronlog
echo "\n" >> /opt/lampp/htdocs/ochmvc/public/batch/cronlog
sleep 1
done