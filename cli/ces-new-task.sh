#!/usr/bin/env bash

CREDENTIALS_FILE=~/.cpf-credentials

function keepCredentials {
    printf "\n" > /dev/tty
    read -p "Do you wanna this script to keep your credentials? in $CREDENTIALS_FILE (y/n):" keepCredentialsFlag > /dev/tty
    if [[ $keepCredentialsFlag == "y" ]]; then
        user=$1
        passwd=$2
        rm -rf  $CREDENTIALS_FILE
        touch $CREDENTIALS_FILE
        echo $user >> $CREDENTIALS_FILE
        echo $passwd >> $CREDENTIALS_FILE
    fi
}


function hasCredentials {
    test -e $CREDENTIALS_FILE

    [[ $? = 0 ]] && {
        echo 1
    }
}

function restoreLoginCredential {
    head -n1 ~/.cpf-pr-credentials | tr -d "[:space:]"
}

function restorePasswdCredential {
    tail -n1 ~/.cpf-pr-credentials | tr -d "[:space:]"
}

cd $CLIPP_PATH

if [[ -z $1 ]] || [[ $1 == '--help' ]] || [[ $1 == '-h' ]]; then
    printf "Creates a new task in you computer, move it on github
    and notify everybody you are working on it.

    Usage:

    cpf-new-task isseeId

    Ex:

    cpf-new-task 3084\n"
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


echo "Entering master to create branch from it..."
git checkout master
git fetch origin
git pull origin master
git checkout -b $issue_name


if [[ $(hasCredentials)  -eq 1 ]]; then
    repositoryUser=$(restoreLoginCredential)
    repositoryUserPassword=$(restorePasswdCredential)
    echo "Your credentials are stored on $CREDENTIALS_FILE"
else
    read -p "Type your Github user :" repositoryUser
    printf "\n"
    read -p "Type your Github password:" -s repositoryUserPassword

    $(keepCredentials $repositoryUser $repositoryUserPassword)
fi

curl -u $repositoryUser:$repositoryUserPassword https://api.github.com/repos/compufour/compufacil/issues/$issue_name/labels -d '["Stage: In Progress"]'

cpf-notify-slack "$USER created local branch $issue_name"
cpf-notify-user "A new branch $issue_name was created in your workspace"

cpf-metric "compufacil.new-task" 1


