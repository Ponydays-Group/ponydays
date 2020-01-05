#!/bin/bash

DEPLOY_LOG="`pwd`/tmp/deploy.log"

REPO_HASH_FILES=(
    "HEAD:frontend/package-lock.json"
    "HEAD:frontend"
    "HEAD:composer.lock"
)

REPO_HASH_CALLBACKS=(
    "update_npm"
    "rebuild_frontend"
    "update_composer"
)

log() {
    echo "$@" | tee -a ${DEPLOY_LOG}
}

log_file() {
    cat "$@" | tee -a ${DEPLOY_LOG}
}

take_repo_hashes() {
    TAKEN_REPO_HASHES=()
    for i in ${!REPO_HASH_FILES[@]}; do
        TAKEN_REPO_HASHES[$i]=$(git rev-parse ${REPO_HASH_FILES[$1]})
    done
}

check_production_changes() {
    if [[ `git status --porcelain` ]];
    then
        log "Warning: there are local changes in production!"
        git status --porcelain &> ./tmp/local_changes.deploy.out
        log_file ./tmp/local_changes.deploy.out
    fi
}

pull_git_repo() {
    git pull &> ./tmp/git.deploy.out
    rc=$?
    if [[ ${rc} != 0 ]]
    then
        log "Fatal error: cannot git pull the repository"
        log "Git exit code: $rc"
        log "Git output:"
        log_file ./tmp/git.deploy.out
        exit 1
    fi
}

start_deploy() {
    date | tee -a ${DEPLOY_LOG}

    check_production_changes

    log "Taking hashes of some repository blobs before synchronisation..."
    take_repo_hashes
    local HASHES_BEFORE=("${TAKEN_REPO_HASHES[@]}")

    log "Syncing the git repository..."
    pull_git_repo
    log_file ./tmp/git.deploy.out

    log "Checking repository hashes..."
    take_repo_hashes
    local HASHES_NOW=("${TAKEN_REPO_HASHES[@]}")

    for i in ${!REPO_HASH_FILES[@]}; do
        if [[ ${HASHES_BEFORE[$i]} != ${HASHES_NOW[$i]} ]];
        then
            log "'${REPO_HASH_FILES[$i]}' has been modified."
            log "Starting '${REPO_HASH_CALLBACKS[$i]}' sequence."
            ${REPO_HASH_CALLBACKS[$i]}
        fi
    done

    log "Done."
    log
}

update_npm() {
    log "Updating NPM.."
    pushd ./frontend/
    npm ci &> ../tmp/npm.deploy.out
    rc=$?
    if [[ ${rc} != 0 ]]
    then
        log "Error: cannot update npm"
        log "code: $rc"
        log "output:"
        log_file ./tmp/npm.deploy.out
    fi
    popd
}

rebuild_frontend() {
    log "Rebuilding frontend..."
    pushd ./frontend/
    PATH="$PATH:$PWD/node_modules/.bin"
    NODE_ENV=production webpack --config webpack.config.js --mode=production &> ../tmp/webpack.deploy.out
    rc=$?
    if [[ ${rc} != 0 ]]
    then
        log "Error: cannot update frontend bundles"
        log "code: $rc"
        log "output:"
        log_file ../tmp/webpack.deploy.out
    fi
    popd
}

update_composer() {
    log "Updating Composer..."
    php composer.phar install &> ./tmp/composer.deploy.out
    rc=$?
    if [[ ${rc} != 0 ]]
    then
        log "Error: cannot update composer"
        log "code: $rc"
        log "output:"
        log_file ../tmp/composer.deploy.out
    fi
}

if [[ -e ".git" ]]
then
    start_deploy
else
    echo "should be run in the project repository root"
fi
