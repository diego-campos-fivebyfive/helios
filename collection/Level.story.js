import Level from './Level'

const Types = `
  <div style='display: flex;'>
    <Level label='black' :style='levelSize'/>

    <Level label='partner' :style='levelSize'/>

    <Level label='platinum' :style='levelSize'/>

    <Level label='premium' :style='levelSize'/>

    <Level label='titanium' :style='levelSize'/>
  </div>
`

export default {
  components: { Level },
  models: {
    Types
  },
  computed: {
    levelSize() {
      return `
        width:105px;
        margin: 10px;
      `
    }
  }
}
