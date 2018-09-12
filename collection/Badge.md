## Badge doc
---
### Descrição
Componente utilizado para exibir números ou mensagens.

---

### Particularidades
 - O componente somente será renderizado caso um valor *content* for atribuido a ele.

 - O componente da a possibilidade de receber uma *Promise* chamda *async*, que será resolvida logo após o componente ser instanciado, e logo em seguida renderizado.

Código:
```javascript
  this.badge.async().then(content => {
    this.content = content
  })
```

