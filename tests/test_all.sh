#!/bin/bash

PROJECT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." >/dev/null 2>&1 && pwd )"

php $PROJECT_DIR/vendor/bin/phpunit --bootstrap $PROJECT_DIR/vendor/autoload.php --include-path $PROJECT_DIR/ $PROJECT_DIR/tests
