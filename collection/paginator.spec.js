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
    const schema = pipe(
      mountPaginator,
      getProps
    )({ pagination: {} }).pagination

    expect(schema).toBeDefined()
  })

  describe('showPagination()', () => {
    it('should return false when total is 0 or undefined', () => {
      const schemaOne = pipe(
        mountPaginator
      )({ pagination: {} }).showPagination()

      const schemaTwo = pipe(
        mountPaginator
      )({
        pagination: {
          total: 0
        }
      }).showPagination()

      expect(schemaOne).not.toEqual(true)
      expect(schemaTwo).not.toEqual(true)
    })
  })

  describe('getNavigationItems()', () => {
    it('should return a simple pagination array when there are 5 items or less', () => {
      const schemaOne = pipe(
        mountPaginator
      )({
        pagination: {
          total: 5
        }
      }).getNavigationItems()

      const schemaTwo = pipe(
        mountPaginator
      )({
        pagination: {
          total: 9
        }
      }).getNavigationItems()

      expect(schemaOne).toHaveLength(5)
      expect(schemaTwo).not.toHaveLength(5)
    })

    it('should render the array returned by navigationItems()', () => {
      const schema = pipe(
        mountPaginator,
        getRenderedText
      )({
        pagination: {
          total: 5
        }
      })

      expect(schema).toBe('12345')
    })

    it('should contains at least one current item', () => {
      const schema = pipe(
        mountPaginator
      )({
        pagination: {
          total: 2,
          current: 1
        }
      }).getNavigationItems()

      const expected = [
        expect.objectContaining({ current: true }),
        expect.objectContaining({ current: false })
      ]

      expect(schema).toEqual(expected)
    })

    it('should set first item as current when there is no current arg', () => {
      const schema = pipe(
        mountPaginator
      )({
        pagination: {
          total: 2
        }
      }).getNavigationItems()

      const expected = [
        expect.objectContaining({ current: true }),
        expect.objectContaining({ current: false })
      ]

      expect(schema).toEqual(expected)
    })

    it('should exhibit the last index with spread item when there are more than 5 items', () => {
      const schema = pipe(
        mountPaginator
      )({
        pagination: {
          total: 9,
          current: 1
        }
      }).getNavigationItems()

      const expected = expect.arrayContaining([
        expect.objectContaining({ value: '...' }),
        expect.objectContaining({ value: 9 })
      ])

      expect(schema).toEqual(expected)
    })
  })
})
