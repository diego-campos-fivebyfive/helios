import Button from './Button'

const DefaultBordered = `
  <div>
    <Button
      title='You can put a title here'
      :action='{}'
      label='Primary bordered'
      class='primary-bordered'>
    </Button>

    <Button
      title='You can put a title here'
      :action='{}'
      label='Primary common'
      class='primary-common'>
    </Button>

    <Button
      title='You can put a title here'
      :action='{}'
      label='Default bordered'
      class='default-bordered'>
    </Button>

    <Button
      title='You can put a title here'
      :action='{}'
      label='Default common'
      class='default-common'>
    </Button>

    <Button
      title='You can put a title here'
      :action='{}'
      label='Danger bordered'
      class='danger-bordered'>
    </Button>

    <Button
      title='You can put a title here'
      :action='{}'
      label='Danger common'
      class='danger-common'>
    </Button>
  </div>
`

const SizeSmall = `
  <div>
    <Button
      title='You can put a title here'
      :action='{}'
      label='Primary bordered small'
      class='primary-bordered size-small'>
    </Button>

    <Button
      title='You can put a title here'
      :action='{}'
      label='Primary common small'
      class='primary-common size-small'>
    </Button>

    <Button
      title='You can put a title here'
      :action='{}'
      label='Default bordered small'
      class='default-bordered size-small'>
    </Button>

    <Button
      title='You can put a title here'
      :action='{}'
      label='Default common small'
      class='default-common size-small'>
    </Button>

    <Button
      title='You can put a title here'
      :action='{}'
      label='Danger bordered small'
      class='danger-bordered size-small'>
    </Button>

    <Button
      title='You can put a title here'
      :action='{}'
      label='Danger common small'
      class='danger-common size-small'>
    </Button>
  </div>
`

const Actions = `
  <div>
    <Button
      link='/something-here/#'
      label='Router link button'
      class='primary-common'>
    </Button>

    <Button
      redirect='http://google.com'
      label='Clickable link button'
      target='_blank'
      class='primary-common'>
    </Button>

    <Button
      label='Normal button'
      class='primary-common'>
    </Button>
  </div>
`

export default {
  components: { Button },
  models: {
    Actions: Actions,
    Types: DefaultBordered,
    Sizes: SizeSmall
  }
}
