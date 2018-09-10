/* eslint-disable react/react-in-jsx-scope */
import Vue from 'vue'
import { storiesOf } from '@storybook/vue';
import { action } from '@storybook/addon-actions';
import { withNotes } from '@storybook/addon-notes';
import { withKnobs } from '@storybook/addon-knobs/vue';
import { registerStories } from 'vue-storybook'

import Banner from '../../collection/Banner'
Vue.component('Banner', Banner)

const req = require.context('../../collection', true, /\.story.vue$/)

{
  req.keys().forEach(filename => {
    registerStories(req, filename, storiesOf, {
      withKnobs,
      withNotes,
      action
    })
  })
}

/* eslint-enable react/react-in-jsx-scope */
