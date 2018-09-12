## Pagination
---
### Descrição
Componente utilizado para a criação de paginação de registros.

---
Particularidades:
- É possível criar a pagianção com as seguintes estruturas:
```javascript
  {
    total: 10,
    current: 5,
    links: {
      prev: true,
      self: "#?page=5",
      next: "#?page=6"
    }
  }
```
ou simplesmente enviando dois parâmetros:
```
:total='5' :current='1'
```
