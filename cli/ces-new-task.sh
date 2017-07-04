#!/usr/bin/env bash

cd $SICES_PATH

if [[ -z $1 ]] || [[ $1 == '--help' ]] || [[ $1 == '-h' ]]; then
    printf "Iniciando uma nova tarefa.

    Usage:

    cpf-new-task issue-[NUMBER]

    Ex:

    cpf-new-task issue-3084\n"
    exit 0;
fi

issue_name=$1
result=$(git branch | grep $issue_name)
pull=0

if [[ -n $result ]]; then
    printf "\e[33mThis branch already exists.\e[0m " > /dev/tty
    read -p "Would you like to update it? (Y/n) " -n 1 -r

    if [[ $REPLY =~ ^[Yy]$ ]]
    then
        pull=1
    fi

    git fetch && git checkout $issue_name

    if [[ $pull = 1 ]]; then
        git pull origin master
    fi

    exit 0;
fi

git checkout master
git fetch origin
git pull origin master
git checkout -b $issue_name
echo "Done"
