import Banner from './Banner'

const Default = `
  <Banner
    icon='arrow-down'
    message='Lorem ipsum dolor sit amet consectetur adipiscing elit sed do
      eiusmod tempor incididunt ut labore et'
    title='Default banner!'
    type='default'>
  </Banner>
`

const Info = `
  <Banner
    icon='arrow-down'
    message='Lorem ipsum dolor sit amet consectetur adipiscing elit sed do
      eiusmod tempor incididunt ut labore et'
    title='Info banner!'
    type='info'>
  </Banner>
`

const Danger = `
  <Banner
    icon='arrow-down'
    message='Lorem ipsum dolor sit amet consectetur adipiscing elit sed do
      eiusmod tempor incididunt ut labore et'
    title='Danger banner!'
    type='danger'>
  </Banner>
`

const Warning = `
  <Banner
    icon='arrow-down'
    message='Lorem ipsum dolor sit amet consectetur adipiscing elit sed do
      eiusmod tempor incididunt ut labore et'
    title='Warning banner!'
    type='warning'>
  </Banner>
`

export default {
  components: { Banner },
  models: {
    Default,
    Danger,
    Info,
    Warning
  }
}
