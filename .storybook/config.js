import VueInfoAddon from 'storybook-addon-vue-info'
import { configure, storiesOf } from '@storybook/vue'
import { withNotes } from '@storybook/addon-notes'

const requireStories = require.context('../collection', true, /.story.js$/)

const addStory = (story, { components, models, computed, data, docs }) => {
  Object
    .entries(models)
    .forEach(([modelName, template]) => {
      story
        .add(modelName, withNotes(docs)(() => ({
          components,
          template,
          computed,
          data
        })))
    })
}

const loadStories = () => {
  requireStories.keys()
    .forEach(filename => {
      const storyName = filename.replace(/.\/|.story.js/g, '')
      const storyConfig = requireStories(filename)

      const story = storiesOf(storyName, module)
        .addDecorator(VueInfoAddon)

      try {
        addStory(story, Object.assign(storyConfig.default || storyConfig ,{
          docs: require(`../collection/${storyName}.md`)
        }))
      } catch(errorMessage) {
        throw new Error(errorMessage)
      }
    })
}

configure(loadStories, module)
