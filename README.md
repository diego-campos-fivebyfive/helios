# PADRÕES ADOTADOS PARA O FRONTEND
## 1. Estrutura de pastas
### 1.1. Clients
Os clientes são definidos no interior da pasta raiz do projeto e são prefixados com `web-` seguido do nome do cliente. Exemplo: `web-sices`, `web-integrador`. Cada cliente é composto por suas páginas, tema e demais configurações necessárias.

Exemplo da árvore de `/src` de Client:
<span id="clientsTree"/>
> **Árvore de Client**
```
web-client
├── /src
|     ├── /account
|     |    ├── /config
|     .    |    └── routes.js
|     .    ├── /data
|     .    |    ├── service.js
|          |    ├── mutations.js
|          |    ├── index.js
|          |    └── ...
|          ├── /pages
|          |    ├── /Accout.vue
|          |    ├── /AccoutForms.vue
|          |    └── ...
|          └─── /components
|               ├── /table
|               |    ├── /filters
|               |    |    └── FilterLevel.vue
|               |    |    └── ...
|               |    ├── TableBlocked.vue
.               |    └── ...
.               ├── /list
.               |    └── ...
└─              └── ...
```
### 1.2. Pages
Na Imagem-1 é possível observar o que entende-se por páginas. Um exemplo de `page` e `pageForm` é `'account'` e `'accountForm'` respectivamente.

![Imagem-1](./pages.png "pageForm")


Cada página é definida dentro do diretório `pages` no interior de cada módulo, sendo assim temos: `web-client/src/module/pages/page-name`. Ver
<a href="#clientsTree"> `Árvore de Client` </a>.

As páginas fazem a composição entre componentes (pai-filho) com `'slots'`.

## 1.3. Components
A pasta componets é uma composição entre collections e regras de negócio específicas de funcionalidades de interfaces (`pages`), como por exemplo: `Table`, `Banner` e `Button`, estes que contêm arquivos `.vue` que descrevem ações específicas do componente. Ver
<a href="#clientsTree"> `Árvore de Client` </a>.

> NOTA: Se não houver um wrapper de regra, o componente é mantido em `page`, como é feito com `notification`. <br>
> Entende-se por wrapper uma combinação, ex.: `[Table + List]`.

## 2. Padrão de Nomenclatura

### 2.1. Composição de nome de Funções Padrões
O nome de uma função deve começar sempre por um verbo de ação, seguido do nome do objeto ou coleção que será aplicada a ação e se necessário, a propriedade de objeto/coleção.

- Coleções (array) devem estar sempre no plural.
- Objetos devem estar sempre no singular.
- Propriedades são as próprias propriedades de objeto/coleção, bem como itens de array ou primitivos.

Exemplo:

```
|   verbo   |  Objeto  | Propriedade |
--------------------------------------
|    get    | Coupon   |      -      | //getCoupon
|    set    | Coupons  |     Id      | //getCouponId
|    get    | Coupons  |    Name     | //getCouponsName
|   remove  | Accounts |      -      | //removeAccounts
|   update  | Memorial |    Title    | //updateMemorialTitle
|   rename  | Account  |   Contacts  | //renameAccountContacts
|    ...    |   ...    |     ...     |
```
> NOTA: Propriedade de objetos podem ter seus valores sendo outras coleções ou objetos. Exemplo:
>```
> coupon: {
>   account: [
>   ]
>}
>```

### 2.2. Composição de heap
O nome de uma heap deve começar sem a prefixação de um verbo de ação. Sendo assim, contém apenas o nome do objeto ou coleção e se necessário, a propriedade de objeto/coleção.

Exemplo:

```
|   objeto   | Propriedade |
----------------------------
| pagination |      -      | //pagination
|   terms    |     Id      | //termsId
|    ...     |     ...     |
```

### 2.3. Composição de Data
> ### Service
- Objeto único que tem como nome o atributo nome de services pré-definidos. Exemplo: `list`, `create`, `update` e `remove`, que devem ser usado ao invés de `get`, `post`, `put` e `delete`.
> ### State / Data
- Seguem a mesma regra de `'heap'`, ou seja, sem prefixo de verbo no início.

> ### Actions
- Antes do method deve ser prefixada a palavra `when`. Exemplo: `whenMarkAsRead()`
> ### Getters
- O method é sempre prefixado com `get`. Exemplo: `getCoupon()`
> ### Mutations / Methods
- Sempre prefixadas com methods (funções padrões). O prefixo `get` também pode ser usado (não é exclusívo de Getters). Exemplo: `MarkAsReadMessage()`

Exemplo:
```
| prefixo |   verbo    |  Objeto  |   Variação  |
-------------------------------------------------
|    -    |     -      |   list   |      -      | // service
|    -    |     -      |   terms  |      -      | // state/data
|  when   |   accept   |   Term   |      -      | // actions
|    -    |    get     |   Terms  |      -      | // getters
|    -    |    get     |   Terms  |      -      | // mutation/methods
|   ...   |    ...     |    ...   |     ...     |
```

## 3. Estrutura de um arquivo .vue
- Um arquivo .vue é composto por três partes, são elas `template`, `script` e `style`. Exemplo:
```html
<template lang="pug">
  ...
</template>


<script>
  ...
</script>


<style lang="scss">
  ...
</style>
```
## 3.1. Template
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

## 3.2. Script
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
  this.setMenuType()
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
    - `name`
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
    components: { // grupo 1
      // ...
    },
    props:{ // grupo 2
      // ...
    },
    data: () => ({ // grupo 2
      // ...
    }),
    watch: { // grupo 3
      // ...
    },
    mounted() { // grupo 4
      setMenuType() {}
    },
    methods: { // grupo 5
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
## 3.3. Style
### Ordem dos elementos e classes e seus atributos
- Se necessário variáveis locais, as mesmas são declaradas no início de `<style>`. Exemplo:
```css
<style lang="scss" scoped>
  $notfound-logo-x: 150px;
  $notfound-description-x: 300px;
</style>
```
- Os elementos e classes de utilização geral são estruturados logo após a declaração de variáveis e, na ausência das mesmas, ocupam o início de `<style>` . Demais elementos e classes de utilização individual são estruturados logo abaixo dos de utilização geral. Ambos seguem ordem alfabética em suas respectivas disposições e são separados por uma linha vazia.
Exemplo:
```CSS
/* bad: */

<style lang="scss" scoped>
  .header {...} /* utilização  geral */
  .sidebar-collapse { /* utilização  individual */
    .logo {...}
  }
  $child_background_: #dff0d8;
  .sidebar-common { /* utilização  individual */
    .info {...}
    .header {...}
  }

  .title {...} /* utilização  geral */
</style>


/* good: */

<style lang="scss" scoped>
  $child_background_: #dff0d8;

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
