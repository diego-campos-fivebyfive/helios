<template lang="pug">
  nav.quick-access
    router-link.quick-access-item(
      v-for='(item, itemName) in quickAccess',
      :key='itemName',
      :to='item.link')
      Icon.quick-access-item-icon(
        v-if='item.icon',
        :name='item.icon')
      Badge.quick-access-item-badge(
        :content='states[itemName]',
        :contentAsync='item.getInitial',
        labelType='warning')
</template>

<script>
  import { mapMutations } from 'vuex'
  import quickAccess from '@/../theme/quick-access'
  import { ring } from 'apis'

  export default {
    computed: {
      quickAccess() {
        return quickAccess
      },
      states() {
        return this.$store.state['theme/quick-access']
      }
    },
    mounted() {
      Object.entries(quickAccess)
        .forEach(([itemName, item]) => {
          item.getInitial()
            .then(initial => {
              this.setContent({ toQuickAccessKey: itemName, content: initial })
            })
        })
    },
    methods: {
      ...mapMutations('theme/quick-access', [
        'setContent'
      ])
    },
    sockets: Object
      .entries(quickAccess)
      .reduce((acc, [itemName, { socketEvent, channel }]) => {
        acc[socketEvent] = {
          channel,
          handler() {
            ring.play()

            this.setContent({
              toQuickAccessKey: itemName,
              content: this.states[itemName] + 1
            })
          }
        }

        return acc
      }, {})
  }
</script>

<style lang="scss" scoped>
  .quick-access-item {
    color: $ui-gray-regular;
    margin: $ui-space-y / 1.5 $ui-space-x / 2.5;
    position: relative;
  }

  .quick-access-item-icon {
    display: inline-block;
    z-index: 105;
  }

  .quick-access-item-badge {
    font-size: 0.75rem;
    position: absolute;
    right: $ui-badge-label-minx / 2 - $ui-badge-label-space-x;
    bottom: $ui-badge-label-miny - $ui-badge-label-space-y;
  }
</style>
