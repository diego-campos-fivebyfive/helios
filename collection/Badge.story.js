import Badge from './Badge'

const Async = `
  <Badge :badge="{ async }"/>
`
const Static = `
  <Badge :badge="{ content: 12 }"/>
`

export default {
  components: { Badge },
  models: {
    Async,
    Static
  },
  computed: {
    async() {
      return () => Promise.resolve(20)
    }
  }
}
