import Button from './Button'

const Types = `
  <div>
    <Button
      title='You can put a title here'
      :action='() => {}'
      label='Primary bordered'
      class='primary-bordered'>
    </Button>

    <Button
      title='You can put a title here'
      :action='() => {}'
      label='Primary common'
      class='primary-common'>
    </Button>

    <Button
      title='You can put a title here'
      :action='() => {}'
      label='Primary strong'
      class='primary-strong'>
    </Button>

    <Button
      title='You can put a title here'
      :action='() => {}'
      label='Default bordered'
      class='default-bordered'>
    </Button>

    <Button
      title='You can put a title here'
      :action='() => {}'
      label='Default common'
      class='default-common'>
    </Button>

    <Button
      title='You can put a title here'
      :action='() => {}'
      label='Danger bordered'
      class='danger-bordered'>
    </Button>

    <Button
      title='You can put a title here'
      :action='() => {}'
      label='Danger common'
      class='danger-common'>
    </Button>
  </div>
`

const Sizes = `
  <div>
    <Button
      title='You can put a title here'
      :action='() => {}'
      label='Primary bordered small'
      class='primary-bordered size-small'>
    </Button>

    <Button
      title='You can put a title here'
      :action='() => {}'
      label='Primary common small'
      class='primary-common size-small'>
    </Button>

    <Button
      title='You can put a title here'
      :action='() => {}'
      label='Default bordered small'
      class='default-bordered size-small'>
    </Button>

    <Button
      title='You can put a title here'
      :action='() => {}'
      label='Default common small'
      class='default-common size-small'>
    </Button>

    <Button
      title='You can put a title here'
      :action='() => {}'
      label='Danger bordered small'
      class='danger-bordered size-small'>
    </Button>

    <Button
      title='You can put a title here'
      :action='() => {}'
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
      redirect='#'
      label='Clickable link button'
      target='_blank'
      class='primary-common'>
    </Button>

    <Button
      label='Normal button'
      action='() => {}'
      class='primary-common'>
    </Button>
  </div>
`

const Groups = `
  <div>
    <Button
      redirect='#'
      target='_blank'
      pos='first'
      label='Left border'
      class='primary-bordered'>
    </Button>
    <Button
      redirect='#'
      target='_blank'
      pos='middle'
      label='middle border'
      class='primary-bordered'>
    </Button>
    <Button
      redirect='#'
      target='_blank'
      pos='last'
      label='Right border'
      class='primary-bordered'>
    </Button>
  </div>
`

export default {
  components: { Button },
  models: {
    Actions,
    Types,
    Sizes,
    Groups
  }
}
