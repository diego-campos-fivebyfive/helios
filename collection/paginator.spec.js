import Vue from 'vue'
import Paginator from './Paginator.vue'

const pipe = (...fns) =>
  x => fns.reduce((y, f) => f(y), x)

const mountPaginator = propsData => {
  const Constructor = Vue.extend(Paginator)
  return new Constructor({ propsData }).$mount()
}

const getRenderedText = Component => {
  return Component.$el.textContent
}

const getProps = Component => {
  return Component.$options.props
}

describe('Paginator.vue', () => {
  it('should mount Paginator with a pagination prop', () => {
    const expected = pipe(
      mountPaginator,
      getProps
    )({ pagination: {} }).pagination

    expect(expected).toBeDefined()
  })

  describe('showPagination()', () => {
    it('should return false when total is 0 or undefined', () => {
      const expectedOne = pipe(
        mountPaginator
      )({ pagination: {} }).showPagination()

      const expectedTwo = pipe(
        mountPaginator
      )({
        pagination: {
          total: 0
        }
      }).showPagination()

      expect(expectedOne).not.toEqual(true)
      expect(expectedTwo).not.toEqual(true)
    })
  })
})
