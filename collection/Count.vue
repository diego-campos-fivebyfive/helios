<template lang="pug">
  span.count(v-if='this.total')
    | {{ total }}
</template>

<script>
  export default {
    data: () => ({
      total: 0
    }),
    props: {
      count: {
        type: Object,
        required: true
      }
    },
    watch: {
      initialCount: {
        handler: 'resolveCount',
        immediate: true
      }
    },
    methods: {
      resolveCount() {
        if (this.count.total) {
          this.total = this.count.total
          return
        }

        if (this.count.asyncTotal) {
          this.count.asyncTotal().then(total => {
            this.total = total
          })
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
  .count {
    background: $ui-blue-light;
    border-radius: $ui-corner/2;
    color: $ui-white-regular;
    font-size: 0.8em;
    float: right;
    margin-right: $ui-space-y;
    padding: $ui-space-y/6 $ui-space-y/2;
  }
</style>
