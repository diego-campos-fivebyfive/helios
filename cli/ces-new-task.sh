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

git checkout master
git fetch origin
git pull origin master
git checkout -b $issue_name
echo "Done, Agora na branch: $issue_name"
