## Button doc
---
### Descrição
Componente utilizado para criar botões de ações

---
### Particularidades
- É possível criar botões (tag button), links (tag a), e router-links:

  - Para criar *router-link* enviar url em um parâmetro chamado *link*.

  - Para criar um link *a*, enviar url em um parâmetro chamado *redirect*.

  - Para criar um botão *button*, enviar um parâmetro chamado *action*, enviando uma função.

- É possível criar um grupo de botões, simplesmente apontando a posição dos botões com o parâmtro *pos*, inserindo o valor correspondente a posição do botão: *first, middle, last*
