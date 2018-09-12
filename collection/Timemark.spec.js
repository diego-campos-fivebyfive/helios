import Vue from 'vue'
import moment from 'moment'
import Timemark from './Timemark.vue'

const mountTimemark = propsData => {
  const Constructor = Vue.extend(Timemark)
  return new Constructor({ propsData }).$mount()
}

describe('Timemark.vue', () => {
  describe('getDayPeriod()', () => {
    it('should return a day period based on 12-hour format', () => {
      const schemaOne = mountTimemark({
        title: 'Timemark 01',
        createdAt: '2018-06-21 11:12:12',
        showTimeAgo: true,
        description: 'Lorem ipsum dolor sit amet'
      }).getDayPeriod()

      const schemaTwo = mountTimemark({
        title: 'Timemark 02',
        createdAt: '2017-08-28 12:27:17',
        showTimeAgo: true,
        description: 'Lorem ipsum dolor sit amet'
      }).getDayPeriod()

      const expectedOne = 'am'
      const expectedTwo = 'pm'

      expect(schemaOne).toEqual(expectedOne)
      expect(schemaTwo).toEqual(expectedTwo)
    })
  })

  describe('getTimeAgo()', () => {
    it('should return the diff time past between current time and notification creation time', () => {
      const schemaOne = mountTimemark({
        title: 'Timemark 01',
        createdAt: moment()
          .subtract(3, 'months')
          .format('YYYY-MM-DD hh:mm:ss'),
        showTimeAgo: true,
        description: 'Lorem ipsum dolor sit amet'
      }).getTimeAgo()

      const schemaTwo = mountTimemark({
        title: 'Timemark 02',
        createdAt: moment()
          .subtract(3, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        showTimeAgo: true,
        description: 'Lorem ipsum dolor sit amet'
      }).getTimeAgo()

      const expectedOne = '3 months ago'
      const expectedTwo = '3 hours ago'

      expect(schemaOne).toEqual(expectedOne)
    })

    it('should return false when showTimeAgo is false', () => {
      const schemaOne = mountTimemark({
        title: 'Timemark 01',
        createdAt: moment()
          .subtract(3, 'months')
          .format('YYYY-MM-DD hh:mm:ss'),
        showTimeAgo: true,
        description: 'Lorem ipsum dolor sit amet'
      }).getTimeAgo()

      const schemaTwo = mountTimemark({
        title: 'Timemark 02',
        createdAt: moment()
          .subtract(3, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        showTimeAgo: false,
        description: 'Lorem ipsum dolor sit amet'
      }).getTimeAgo()

      const expected = false

      expect(schemaOne).not.toEqual(expected)
      expect(schemaTwo).toEqual(expected)
    })
  })
})
