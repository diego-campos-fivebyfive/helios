const memorial = [
  {
    version: 0001,
    status: true,
    start_at: '2017-07-11',
    end_at: '2017-08-11',
    products: [
      {
        code: 'ABC',
        family: 'inverter',
        price: 1000,
        markups: {
          platinum: [
            {
              start: 1000,
              end: 3000,
              markup: 0.1
            },
            {
              start: 30001,
              end: 5000,
              markup: 0.15
            }
          ],
          gold: [
            {
              start: 1000,
              end: 3000,
              markup: 0.15
            },
            {
              start: 30001,
              end: 5000,
              markup: 0.2
            }
          ]
        }
      },
      {
        code: 'DEF',
        family: 'structure',
        price: 1000,
        markups: {
          platinum: [
            {
              start: 1000,
              end: 3000,
              markup: 0.1
            },
            {
              start: 30001,
              end: 5000,
              markup: 0.15
            }
          ],
          gold: [
            {
              start: 1000,
              end: 3000,
              markup: 0.15
            },
            {
              start: 30001,
              end: 5000,
              markup: 0.2
            }
          ]
        }
      }
    ]
  }
]

module.exports = memorial
