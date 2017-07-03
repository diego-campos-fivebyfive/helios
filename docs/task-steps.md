Passo a Passo de Tarefas
========================

### Developer

1. Puxar para Progress e analisar Tasks

2. Atualiza a Master
```
$ git pull origin master
```

3. Criar nova branch
```
$ git checkout -b issue-[NUMBER_ISSUE]
```

4. Executar tarefas

5. Adiciona arquivos ao Commit
```
$ git add [FILE_OR_PATH]
```

6. Adiciona o Commit
```
$ git commit -m "[WHAT_WERE_MAKE]"
```

7. Enviar para a Branch
```
$ git push origin [BRANCH_NAME]
```

8. Criar Pull Request (PR),
   Mover para review,
   Enviar link no Slack

9. Deletar Branch após Merge
```
$ git branch -D [BRANCH_NAME]
```

10. ForEach((task) => { começarNovamente(task) })


### Reviewer

1. Revisar código,
   Aprovar,
   Fazer Merge,
   Avisar do Merge no Slack


### Tester

1. Testar Homolog
