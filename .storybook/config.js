import { configure } from '@storybook/vue'

const stories = require.context('../stories', true, /.stories.js$/)

function loadStories() {
  stories.keys().forEach(filename => {
    stories(filename)
  })
}

configure(loadStories, module)
