Nova Task - CES
===============

## DEVELOPER

1. Puxar a Task para Open (Progress) no BucketBoard e analisa-la
----------------------------------------------------------------

2. Iniciar nova Branch

```
$ ./cli/ces-new-task.sh issue-[NUMBER_ISSUE]
```

3. Executar task
----------------

4. Adiciona arquivos ao Commit
------------------------------

```
$ git add [FILE_OR_PATH]
```

5. Adiciona o Commit
--------------------

```
$ git commit -m "[WHAT_WERE_MAKE]"
```

6. Enviar para a Branch
-----------------------

```
$ git push origin [BRANCH_NAME]
```

7. Criar Pull Request (PR) no Bitbucket
---------------------------------------
8. Mover a Task para Resolved (Review) no BucketBoard
-----------------------------------------------------

9. Enviar link no Slack
------------------------

10. Voltar a Master
-------------------

```
$ git checkout master
```

11. Deletar Branch local após Merge
-----------------------------------

```
$ git branch -D [BRANCH_NAME]
```


## REVIEWER

1. Revisar código
-----------------

2. Aprovar
----------

3. Fazer Merge
--------------

4. Avisar do Merge no Slack
---------------------------

5. Mover Task para Hold On (Test) no BucketBoard
------------------------------------------------


## TESTER

1. Testar Homolog
-----------------

2. Mover a Task para Closed (Done) no BucketBoard
-------------------------------------------------


Proxima leitura: [Workflow da Equipe](workflow.md)
