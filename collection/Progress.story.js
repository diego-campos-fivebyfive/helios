import Progress from './Progress'

const Simple = `
  <div>
    <Progress percent='0'/>

    <Progress percent='50'/>

    <Progress percent='100'/>
  </div>
`

export default {
  components: { Progress },
  models: {
    Simple
  }
}
