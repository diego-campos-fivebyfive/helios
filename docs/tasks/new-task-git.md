Nova Task - GIT
===============

## DEVELOPER

1. Puxar a Task para Open (Progress) no BucketBoard e analisa-la
----------------------------------------------------------------

2. Atualiza a Master
--------------------

```
$ git pull origin Master
```

3. Criar nova Branch
--------------------

```
$ git checkout -b issue-[NUMBER_ISSUE]
```

4. Executar task
----------------

5. Adiciona arquivos ao Commit
------------------------------

```
$ git add [FILE_OR_PATH]
```

6. Adiciona o Commit
--------------------

```
$ git commit -m "[WHAT_WERE_MAKE]"
```

7. Enviar para a Branch
-----------------------

```
$ git push origin [BRANCH_NAME]
```

8. Criar Pull Request (PR) no Bitbucket
---------------------------------------

9. Mover a Task para Resolved (Review) no BucketBoard
-----------------------------------------------------

10. Enviar link no Slack
------------------------

11. Voltar a Master
-------------------

```
$ git checkout master
```

12. Deletar Branch após Merge
-----------------------------

```
$ git branch -D [BRANCH_NAME]
```

13. forEach((task) => { começarNovamente(task) })
-------------------------------------------------


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


[Voltar](../tasks.md)
