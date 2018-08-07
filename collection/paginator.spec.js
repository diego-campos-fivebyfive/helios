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

  describe('getNavigationItems()', () => {
    it('should return an array with maximum of 5 items', () => {
      const expectedOne = pipe(
        mountPaginator
      )({
        pagination: {
          total: 5
        }
      }).getNavigationItems()

      const expectedTwo = pipe(
        mountPaginator
      )({
        pagination: {
          total: 9
        }
      }).getNavigationItems()

      expect(expectedOne).toHaveLength(5)
      expect(expectedTwo).toHaveLength(5)
    })

    it('should render the array returned by navigationItems()', () => {
      const expected = pipe(
        mountPaginator,
        getRenderedText
      )({
        pagination: {
          total: 5
        }
      })

      expect(expected).toBe('12345')
    })

    it('should contains at least one current item', () => {
      const expected = pipe(
        mountPaginator
      )({
        pagination: {
          total: 2,
          current: 1
        }
      }).getNavigationItems()

      expect(expected).toEqual([
        expect.objectContaining({ current: true }),
        expect.objectContaining({ current: false })
      ])
    })
  })
})
