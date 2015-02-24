#!/bin/sh

git stash
git pull origin master
git stash pop
composer update
monit -g cme restart
