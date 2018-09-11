import Banner from './Banner'

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

export default {
  components: { Banner },
  models: {
    Info,
    Danger
  }
}
