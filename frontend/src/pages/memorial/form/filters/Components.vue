<template lang="pug">
  .col-components
    span.components-title Componentes
    label
      input(
        type='checkbox',
        v-on:change='$emit("updateFamiliesQuery", "module", $event)')
      Icon(name='th')
      | Módulos
    label
      input(
        type='checkbox',
        v-on:change='$emit("updateFamiliesQuery", "inverter", $event)')
      Icon(name='exchange')
      | Inversores
    label
      input(
        type='checkbox',
        v-on:change='$emit("updateFamiliesQuery", "string_box", $event)')
      Icon(name='plug')
      | String Box
    label
      input(
        type='checkbox',
        v-on:change='$emit("updateFamiliesQuery", "structure", $event)')
      Icon(name='sitemap')
      | Estrutura
    label
      input(
        type='checkbox',
        v-on:change='$emit("updateFamiliesQuery", "variety", $event)')
      Icon(name='wrench')
      | Variedades
</template>

<script>
  export default { }
</script>

<style lang="scss" scoped>
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
</style>
