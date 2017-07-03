Tarefas
=======

## Etapas no Dashboard

To Do -> Progress -> Blocked ou Review -> Test -> Done


## Passo a Passo

#### Developer

1. Puxar para Progress e analisar Tasks
---------------------------------------

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

9. Mover para review no Board
-----------------------------

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


#### Reviewer

1. Revisar código
-----------------

2. Aprovar
----------

3. Fazer Merge
--------------

4. Avisar do Merge no Slack
---------------------------


#### Tester

1. Testar Homolog
-----------------

2. Mover para Done
------------------
