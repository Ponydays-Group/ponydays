#!/bin/bash

if [[ -e ".git" ]]
then
    git pull &> ./tmp/git.deploy.out
    GIT_RET=$?
    if [[ ${GIT_RET} -ne 0 ]]
    then
        echo "cannot git pull the repository"
        echo "code: $GIT_RET"
        echo "output:"
        cat ./tmp/git.deploy.out
        exit 1
    fi

    cd ./frontend/
    PATH="$PATH:$PWD/node_modules/.bin"
    NODE_ENV=production webpack --config webpack.config.js --mode=production &> ../tmp/webpack.deploy.out
    WP_RET=$?
    if [[ ${WP_RET} -ne 0 ]]
    then
        echo "cannot update frontend bundles"
        echo "code: $WP_RET"
        echo "output:"
        cat ../tmp/webpack.deploy.out
        exit 1
    fi
else
    echo "should be run in a repository root"
fi
