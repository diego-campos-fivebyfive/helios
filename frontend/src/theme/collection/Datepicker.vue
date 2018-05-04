<template lang="pug">
  Field(:field='field')
    Datepicker.field(
      v-model='field.value',
      :format='customFormatter',
      :language='language',
      v-on:blur='$emit("validate", field)',
      v-on:input='$set(field, "value", $event)')
</template>

<script>
  import Datepicker from 'vuejs-datepicker'
  import { ptBR } from 'vuejs-datepicker/dist/locale'
  import Field from './Field'

  export default {
    components: {
      Datepicker,
      Field
    },
    data: () => ({
      language: ptBR
    }),
    props: [
      'field'
    ],
    methods: {
      customFormatter(date) {
        return this.$moment(date).format('DD/MM/YYYY')
      }
    }
  }
</script>

<style lang="scss" scoped>
  .vdp-datepicker {
    input {
      width: 100%;
      padding: 0.75rem;
    }
  }
</style>
