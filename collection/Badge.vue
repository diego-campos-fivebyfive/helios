<template lang="pug">
  label.badge(
    v-if='formattedContent',
    :class='labelType')
    | {{ formattedContent }}
</template>

<script>
  export default {
    props: {
      content: {
        type: [Number, String],
        required: false,
        default: 0
      },
      contentAsync: {
        type: Promise,
        required: false
      },
      labelType: {
        type: String,
        required: false,
        default: 'info'
      }
    },
    data: () => ({
      formattedContent: 0
    }),
    watch: {
      content: {
        handler: 'resolveData',
        immediate: true
      }
    },
    methods: {
      formatContent(content) {
        if (!Number.isInteger(content)) {
          return content
        }

        return (content < 100) ? content : '+99'
      },
      resolveData() {
        if (this.content) {
          this.formattedContent = this.formatContent(this.content)
          return
        }

        if (this.contentAsync) {
          this.contentAsync()
            .then(content => {
              this.formattedContent = this.formatContent(content)
            })
        }
      }
    }
  }
</script>

<style lang="scss">
  .badge {
    border-radius: $ui-corner / 2;
    color: $ui-white-regular;
    font-weight: 600;
    min-height: $ui-badge-label-miny;
    min-width: $ui-badge-label-minx;
    padding: 0.15em 0.4em;
    text-align: center;
    z-index: 105;

    &.info {
      background-color: $ui-blue-light;
    }

    &.warning {
      background-color: $ui-orange-light;
    }
  }
</style>
