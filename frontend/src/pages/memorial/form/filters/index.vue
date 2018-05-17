<template lang="pug">
  .filters
    Button.prev(
      type='default-bordered',
      icon='arrow-left',
      label='voltar',
      pos='single')
    Memorials.col-memorial(v-on:updateQuery='updateQuery')
    Levels(v-on:updateQuery='updateQuery')
    .col-components
      span.components-title Componentes
      label
        input(type='checkbox', v-on:change='updateQuery')
        Icon(name='th')
        | Módulos
      label
        input(type='checkbox', v-on:change='updateQuery')
        Icon(name='exchange')
        | Inversores
      label
        input(type='checkbox', v-on:change='updateQuery')
        Icon(name='plug')
        | String Box
      label
        input(type='checkbox', v-on:change='updateQuery')
        Icon(name='sitemap')
        | Estrutura
      label
        input(type='checkbox', v-on:change='updateQuery')
        Icon(name='wrench')
        | Variedades
    Button.copy(
      type='primary-common',
      icon='copy',
      label='Copiar',
      pos='single')
</template>

<script>
  import Levels from './Levels'
  import Memorials from './Memorials'

  export default {
    components: {
      Levels,
      Memorials
    },
    methods: {
      updateQuery() {
      }
    }
  }
</script>

<style lang="scss" scoped>
  .filters {
    align-items: center;
    display: flex;
    justify-content: space-between;
    text-align: left;
  }

  .col-memorial {
    max-width: 150px;
  }

  .col-components {
    text-align: left;

    label {
      display: inline-block;
      padding: $ui-space-y/1.25 $ui-space-x/3;
    }

    svg {
      margin: 0 $ui-space-x/6;
    }

    .components-title {
      display: block;
      font-weight: 600;
    }
  }

  .prev {
    margin-top: $ui-space-y*1.25;
  }

  .copy {
    margin-top: $ui-space-y*1.25;
  }
</style>
