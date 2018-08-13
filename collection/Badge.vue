<template lang="pug">
  span.badge(v-if='this.content')
    | {{ content }}
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
      content: 0
    }),
    watch: {
      badge: {
        handler: 'resolveBadge',
        immediate: true
      }
    },
    methods: {
      resolveBadge() {
        if (this.badge.content) {
          this.content = this.badge.content
          return
        }

        if (this.badge.async) {
          this.badge.async().then(content => {
            this.content = content
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
