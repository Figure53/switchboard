#!/bin/bash
rsync -avz --delete --rsh=ssh \
--exclude .DS_Store \
--exclude .git \
--exclude .gitignore \
--exclude template-CONFIG.php \
--exclude template-htaccess \
--exclude index-template.php \
site/ user@example.com:/path/to/switchboard
