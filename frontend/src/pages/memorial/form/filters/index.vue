<template lang="pug">
  .filters
    CopyModal(
      ref='copyModal',
      :level='queryParams.level')
    Button.prev(
      type='default-bordered',
      icon='arrow-left',
      label='voltar',
      pos='single')
    Memorials.col-memorial(v-on:updateMemorialQuery='updateMemorialQuery')
    Levels(v-on:updateLevelQuery='updateLevelQuery')
    Components(
      :families='queryParams.families',
      v-on:updateFamiliesQuery='updateFamiliesQuery')
    Button.copy(
      type='primary-common',
      icon='copy',
      label='Copiar',
      pos='single',
      v-on:click.native='show')
</template>

<script>
  import Components from './Components'
  import CopyModal from '../CopyModal'
  import Levels from './Levels'
  import Memorials from './Memorials'

  export default {
    props: [
      'setMemorialId'
    ],
    data: () => ({
      queryParams: {
        families: [],
        level: 'titanium'
      }
    }),
    components: {
      Components,
      CopyModal,
      Levels,
      Memorials
    },
    methods: {
      show() {
        this.$refs.copyModal.show()
      },
      updateFamiliesQuery(value) {
        this.queryParams.families = value
        this.$emit('getMemorialGroups', this.queryParams)
      },
      updateLevelQuery(event) {
        this.queryParams.level = event.value
        this.$emit('getMemorialGroups', this.queryParams)
      },
      updateMemorialQuery(memorialId) {
        this.setMemorialId(memorialId)
          .then(() => {
            this.$emit('getMemorialGroups', this.queryParams)
          })
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

  .prev {
    margin-top: $ui-space-y*1.25;
  }

  .copy {
    margin-top: $ui-space-y*1.25;
  }
</style>
