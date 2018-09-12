<template lang="pug">
  .collection-timemark
    h3.collection-timemark-title
      | {{ title }}
    span.collection-timemark-createdat
      | {{ timestump.createdAt }}
    span.collection-timemark-timeago
      | ({{ timestump.timeAgo }})
    p.collection-timemark-description
      | {{ description }}
    nav.collection-timemark-links
      router-link.collection-timemark-links-link(
        v-for='link in links',
        :to='link.href')
        | {{ link.title }}
</template>

<script>
  import moment from 'moment'

  export default {
    props: {
      createdAt: {
        type: String,
        required: true
      },
      description: {
        type: String,
        required: true
      },
      links: {
        type: Array,
        required: false,
        default: () => ([])
      },
      showTimeAgo: {
        type: Boolean,
        required: false,
        default: true
      },
      title: {
        type: String,
        required: true
      }
    },
    data: () => ({
      timestump: {}
    }),
    watch: {
      showTimeAgo: {
        handler() {
          const getCreatedAt = () =>
            moment(this.createdAt, 'YYYY-MM-DD hh:mm:ss')
              .format('HH:mm')
            
          this.timestump.timeAgo = this.getTimeAgo()
          this.timestump.createdAt = getCreatedAt()
        },
        immediate: true
      }
    },
    methods: {
      getDayPeriod() {
        return moment(this.createdAt, 'YYYY-MM-DD hh:mm:ss').format('a')
      },
      getTimeAgo() {
        if (!this.showTimeAgo) {
          return false
        }

        const now = moment()
        const created = moment(this.createdAt, 'YYYY-MM-DD hh:mm:ss')
        const duration = moment.duration(-Math.abs(now.diff(created)))
        return duration.humanize(true)
      }
    }
  }
</script>

<style lang="scss">
  .collection-timemark-title {
    display: inline-block;
    padding-right: $ui-space-x/3;

    &:first-letter {
      text-transform: capitalize;
    }
  }

  .collection-timemark-createdat {
    padding-right: $ui-space-x/3;
  }

  .collection-timemark-description {
    padding-top: $ui-space-y/2;
  }

  .collection-timemark-links-link {
    display: list-item;
    list-style-type: disc;
    list-style-position: inside;
    padding-left: $ui-space-x;
    padding-top: $ui-space-y/2;
  }
</style>
