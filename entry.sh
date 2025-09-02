#!/bin/bash

php /usr/local/src/migrations/apply.php

exec php -S 0.0.0.0:8000 -t .