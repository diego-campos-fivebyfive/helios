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

  it('should render the array returned by navigationItems()', () => {
    const schemaOne = pipe(
      mountPaginator,
      getRenderedText
    )({
      pagination: {
        total: 5
      }
    })

    const schemaTwo = pipe(
      mountPaginator,
      getRenderedText
    )({
      pagination: {
        total: 10,
        current: 5
      }
    })

    expect(schemaOne).toBe('12345Próximo')
    expect(schemaTwo).toBe('Anterior1...34567...10Próximo')
  })

  describe('getCurrent()', () => {

  })

  describe('getInitialRangeIndex()', () => {

  })

  describe('getNavigationItems()', () => {
  })

  describe('getNextControls()', () => {
    it('should exhibit neither the spread item nor the last index when last range item and index are the same', () => {
      const schema = pipe(
        mountPaginator,
        Paginator => pipe(
          Paginator.getRangeItems,
          Paginator.getNextControls
        )()
      )({
        pagination: {
          total: 9,
          current: 9
        }
      })

      const expected = expect.arrayContaining([
        expect.objectContaining({
          label: '...'
        }),
        expect.objectContaining({
          label: 9,
          value: 9
        })
      ])

      expect(schema).not.toEqual(expected)
    })

    it('should exhibit the last index with spread item when there are more than 5 items', () => {
      const schema = pipe(
        mountPaginator,
        Paginator => pipe(
          Paginator.getRangeItems,
          Paginator.getNextControls
        )()
      )({
        pagination: {
          total: 9,
          current: 1
        }
      })

      const expected = expect.arrayContaining([
        expect.objectContaining({
          label: '...'
        }),
        expect.objectContaining({
          label: 9,
          value: 9
        })
      ])

      expect(schema).toEqual(expected)
    })

    it('should exhibit the next button when there is a next item', () => {
      const schemaOne = pipe(
        mountPaginator,
        Paginator => pipe(
          Paginator.getRangeItems,
          Paginator.getNextControls
        )()
      )({
        pagination: {
          total: 9,
          current: 1
        }
      })

      const schemaTwo = pipe(
        mountPaginator,
        Paginator => pipe(
          Paginator.getRangeItems,
          Paginator.getNextControls
        )()
      )({
        pagination: {
          total: 9,
          current: 9
        }
      })

      const expected = expect.arrayContaining([
        expect.objectContaining({
          label: 'Próximo'
        })
      ])

      expect(schemaOne).toEqual(expected)
    })

    it('should sum current page plus one and add it to next button as value', () => {
      const schema = pipe(
        mountPaginator,
        Paginator => pipe(
          Paginator.getRangeItems,
          Paginator.getNextControls
        )()
      )({
        pagination: {
          total: 3,
          current: 2
        }
      })

      const expected = expect.arrayContaining([
        expect.objectContaining({
          label: 'Próximo',
          value: 3
        })
      ])

      expect(schema).toEqual(expected)
    })
  })

  describe('getPrevControls()', () => {
    it('should exhibit the first index with spread item when the first range item is not 1', () => {
      const schema = pipe(
        mountPaginator,
        Paginator => pipe(
          Paginator.getRangeItems,
          Paginator.getPrevControls
        )()
      )({
        pagination: {
          total: 9,
          current: 5
        }
      })

      const expected = expect.arrayContaining([
        expect.objectContaining({
          label: 1,
          value: 1
        }),
        expect.objectContaining({
          label: '...'
        })
      ])

      expect(schema).toEqual(expected)
    })

    it('should exhibit the prev button when there is a prev item', () => {
      const schemaOne = pipe(
        mountPaginator,
        Paginator => pipe(
          Paginator.getRangeItems,
          Paginator.getPrevControls
        )()
      )({
        pagination: {
          total: 9,
          current: 4
        }
      })

      const expected = expect.arrayContaining([
        expect.objectContaining({
          label: 'Anterior'
        })
      ])

      expect(schemaOne).toEqual(expected)
    })

    it('should decrease one and add it as prev button value', () => {
      const schema = pipe(
        mountPaginator,
        Paginator => pipe(
          Paginator.getRangeItems,
          Paginator.getPrevControls
        )()
      )({
        pagination: {
          total: 9,
          current: 4
        }
      })

      const expected = expect.arrayContaining([
        expect.objectContaining({
          label: 'Anterior',
          value: 3
        })
      ])

      expect(schema).toEqual(expected)
    })
  })

  describe('getRangeItems()', () => {
    it('should return a maximum of 5 range items when there are 5 items or more', () => {
      const schemaOne = mountPaginator({
        pagination: {
          total: 5
        }
      }).getRangeItems()

      const schemaTwo = mountPaginator({
        pagination: {
          total: 9
        }
      }).getRangeItems()

      const schemaThree = mountPaginator({
        pagination: {
          total: 3
        }
      }).getRangeItems()

      expect(schemaOne).toHaveLength(5)
      expect(schemaTwo).toHaveLength(5)
      expect(schemaThree).toHaveLength(3)
    })

    it('should keep current item in the middle when there are 2 prev items and 2 next items when there are 5 items or more', () => {
      const schema = mountPaginator({
        pagination: {
          total: 15,
          current: 10
        }
      }).getRangeItems()

      const expected = expect.arrayContaining([
        expect.objectContaining({
          label: 8,
          value: 8
        }),
        expect.objectContaining({
          label: 9,
          value: 9
        }),
        expect.objectContaining({
          label: 10,
          value: 10,
          current: true
        }),
        expect.objectContaining({
          label: 11,
          value: 11
        }),
        expect.objectContaining({
          label: 12,
          value: 12
        })
      ])

      expect(schema).toEqual(expected)
    })

    it('should keep current item after the middle when there are any or 1 next item', () => {
      const schemaOne = mountPaginator({
        pagination: {
          total: 15,
          current: 14
        }
      }).getRangeItems()

      const schemaTwo = mountPaginator({
        pagination: {
          total: 15,
          current: 15
        }
      }).getRangeItems()

      const expectedOne = expect.arrayContaining([
        expect.objectContaining({
          label: 11,
          value: 11
        }),
        expect.objectContaining({
          label: 12,
          value: 12
        }),
        expect.objectContaining({
          label: 13,
          value: 13
        }),
        expect.objectContaining({
          label: 14,
          value: 14,
          current: true
        }),
        expect.objectContaining({
          label: 15,
          value: 15
        })
      ])

      const expectedTwo = expect.arrayContaining([
        expect.objectContaining({
          label: 11,
          value: 11
        }),
        expect.objectContaining({
          label: 12,
          value: 12
        }),
        expect.objectContaining({
          label: 13,
          value: 13
        }),
        expect.objectContaining({
          label: 14,
          value: 14
        }),
        expect.objectContaining({
          label: 15,
          value: 15,
          current: true
        })
      ])

      expect(schemaOne).toEqual(expectedOne)
      expect(schemaTwo).toEqual(expectedTwo)
    })

    it('should contains at least one current item, setting first item as current (1) when there is no current arg', () => {
      const schemaOne = mountPaginator({
        pagination: {
          total: 2
        }
      }).getRangeItems()

      const schemaTwo = mountPaginator({
        pagination: {
          total: 2,
          current: 1
        }
      }).getRangeItems()

      const expected = expect.arrayContaining([
        expect.objectContaining({
          current: true
        })
      ])

      expect(schemaOne).toEqual(expected)
      expect(schemaTwo).toEqual(expected)
    })
  })

  describe('showPagination()', () => {
    it('should return false when total is 0 or there is no total arg', () => {
      const schemaOne = mountPaginator({
          pagination: {}
      }).showPagination()

      const schemaTwo = mountPaginator({
        pagination: {
          total: 0
        }
      }).showPagination()

      expect(schemaOne).not.toEqual(true)
      expect(schemaTwo).not.toEqual(true)
    })
  })
})
