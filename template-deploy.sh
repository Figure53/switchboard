#!/bin/bash
rsync -avz --delete --rsh=ssh \
--exclude .DS_Store \
--exclude .git \
--exclude .gitignore \
--exclude CONFIG-template.php \
site/ user@example.com:/path/to/switchboard
