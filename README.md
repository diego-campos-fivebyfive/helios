# PADRÕES ADOTADOS PARA O FRONTEND
## 1. Template
### Declaração de tipagem de componentes
Quando um componente tiver uma tipagem, ex:
`sidebarType: 'common' || 'collapse'` é utilizado uma `div` contendo a `:class`, a qual recebe o `type` para que dessa forma seja possível englobar todo o componente. Exemplo:
```html
// bad:

<template lang="pug">
   router-link.header(to='/',
   :class='sidebarType')
    ...
</template>


// good:

<template lang="pug">
  div(:class='`sidebar-${sidebarType}`')
    router-link.header(to='/')
    ...
</template>
```

## 2. Script
### Definição de conteúdo de grupos de state
O conteúdo de grupos de state `watch` e do `vue life-cycle` é composto sempre por métodos, nunca pela própria regra. Exemplo:
```js
// bad:

mounted() {
  this.menu = this.user.admin
    ? menuAdmin
    : menuUser
}


// good:

mounted() {
  setMenuType()
},
methods: {
  setMenuType() {
    // ...
  }
}
```

### Ordem dos atributos de components
Os atributos de componentes são definidos na seguinte ordem:
1. Grupo de config de component:
    - `id`
    - `render`
    - `components`

2. Grupo de data:
    - `props`
    - `data`
    - `computed`

3. Grupo de state de data:
    - `watch`

4. Grupo de state de component:
  - vue life-cycle
    - `beforeCreate`, `created`, `beforeMount`, `mounted`, `beforeUpdate`, `updated`, `activated`, `deactivated`, `beforeDestroy`, `destroyed`, `errorCaptured`.

5. Grupo de methods
    - `methods`
    - `socket`

Exemplo:
```js
<script>
export default {
    components: { // 1
      // ...
    },
    props:{ // 2
      // ...
    },
    data: () => ({ //2
      // ...
    }),
    watch: { // 3
      // ...
    },
    mounted() { // 4
      setMenuType() {}
    },
    methods: { // 5
      setMenuType() {
        // ...
      }
    }
  }
</script>
```
### Ordem do conteúdo de atributos de components
O conteúdo de um componente de atributo segue ordem alfabética. Exemplo:
```js
props: {
  dropdown: {
    // ...
  },
  hasRoles: {
    // ...
  },
  sidebarType: {
    // ...
  }
}

/*...*/

methods: {
  closeDropdown() {
    // ...
  },
  hideDropdown() {
    // ...
  },
  openCommonDropdown() {
    // ...
  },
  showDropdown() {
    // ...
  },
  toogleList() {
    // ...
  }
}
```
## 3. Style
### Ordem dos elementos e classes e seus atributos
- Os elementos e classes de utilização geral são estruturados sempre no início de `<style>`. Demais elementos e classes de utilização individual são estruturados logo abaixo dos de utilização geral. Ambos seguem ordem alfabética em suas respectivas disposições e são separados por uma linha vazia.
Exemplo:
```CSS
/* bad: */

<style lang="scss" scoped>
.header {...} /* utilização  geral */
.sidebar-collapse { /* utilização  individual */
  .logo {...}
}
.sidebar-common { /* utilização  individual */
  .info {...}
  .header {...}
}

.title {...} /* utilização  geral */
</style>


/* good: */

<style lang="scss" scoped>
.header {...} /* utilização  geral */

.title {...} /* utilização  geral */

.sidebar-collapse { /* utilização  individual */
  .logo {...}
}

.sidebar-common { /* utilização  individual */
  .header {...}
  .info {...}
}
</style>
```
- Os atributos dos elementos e classes são definidos sempre em ordem alfabética. Exemplo:
```CSS
/* bad: */

.name {
    padding: $ui-space-y/4;
    font-weight: 600;
    text-align: center;
    display: block;
  }


/* good: */

.name {
    display: block;
    font-weight: 600;
    padding: $ui-space-y/4;
    text-align: center;
  }
```
