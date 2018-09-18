<template lang="pug">
  .collection-timemark
    h3.collection-timemark-title
      | {{ title }}
    span.collection-timemark-createdat
      | {{ timestump.createdAt }}
    span.collection-timemark-timeago
      | ({{ timestump.timeAgo }})
    p.collection-timemark-description(
      v-if='descriptionHtml',
      v-html='description')
    p.collection-timemark-description(v-else)
      | {{ description }}
    nav.collection-timemark-links
      ul
        li
          a.collection-timemark-links-link(
            v-for='link in links',
            :href='link.url')
            | {{ link.label }}
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
      descriptionHtml: {
        type: Boolean,
        required: false,
        default: true
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
    font-size: 1.05em;
    font-weight: 600;
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
    text-transform: lowercase;

    &:first-letter {
      text-transform: capitalize;
    }
  }

  .collection-timemark-links {
    ul {
      list-style: none;
    }
  }

  .collection-timemark-links-link {
    color: $ui-text-main;
    display: list-item;
    list-style-type: disc;
    list-style-position: inside;
    padding-left: $ui-space-x;
    padding-top: $ui-space-y/2;

    &:first-letter {
      text-transform: capitalize;
    }
  }
</style>
