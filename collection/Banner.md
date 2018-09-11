PAGINATOR
=========

# Usage

*Utilizando com propriedade 'pagination'*:

```
<template>
  Paginator(:pagination='{ total: 2, current: 1 }')
</template>
```

Propriedades:

| pagination | Object | required |
| pagination.total | Number | required |
| pagination.current | Number | not required |


*Utilizando com propriedades 'total' e 'current'*:

```
<template>
  Paginator(total='2', current='1')
</template>
```

Propriedades:

| total | Number | required |
| current | Number | not required |
