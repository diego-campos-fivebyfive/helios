<template lang="pug">
  span.badge(v-if='this.total')
    | {{ total }}
</template>

<script>
  export default {
    props: {
      badge: {
        type: Object,
        required: true
      }
    },
    data: () => ({
      total: 0
    }),
    watch: {
      badge: {
        handler: 'resolveBadge',
        immediate: true
      }
    },
    methods: {
      resolveBadge() {
        if (this.badge.total) {
          this.total = this.badge.total
          return
        }

        if (this.badge.asyncTotal) {
          this.badge.asyncTotal().then(total => {
            this.total = total
          })
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
  .badge {
    background: $ui-blue-light;
    border-radius: $ui-corner/2;
    color: $ui-white-regular;
    font-size: 0.8em;
    float: right;
    margin-right: $ui-space-y;
    padding: $ui-space-y/6 $ui-space-y/2;
  }
</style>
