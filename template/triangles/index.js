const defaultRefreshDuration = 12000
let defaultTrianglesSize = 15

let numPointsX
let numPointsY
let unitWidth
let unitHeight
let points
let svg

export const insertBackground = (
  element,
  width = window.innerWidth,
  height = window.innerHeight,
  trianglesSize = defaultTrianglesSize,
  refreshDuration = defaultRefreshDuration
  )  => {
    const startIn = refreshDuration / 10

    svg = this.create(width, height, trianglesSize, refreshDuration)
    setTimeout(() => this.animate(element, refreshDuration), startIn)
    document.querySelector(element).appendChild(svg)
}

export const create = (width, height, trianglesSize, refreshDuration) => {
  svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg')
  svg.setAttribute('width', width)
  svg.setAttribute('height', height)
  svg.setAttribute('class', 'triangles')

  let unitSize = (window.innerWidth+window.innerHeight) / trianglesSize
  numPointsX = Math.ceil(window.innerWidth / unitSize) + 1
  numPointsY = Math.ceil(window.innerHeight / unitSize) + 1
  unitWidth = Math.ceil(window.innerWidth / (numPointsX - 1))
  unitHeight = Math.ceil(window.innerHeight / (numPointsY - 1))

  points = [];

  for (let y = 0; y < numPointsY; y++) {
      for (let x = 0; x < numPointsX; x++) {
        const position = {
          x:unitWidth * x,
          y:unitHeight * y,
          originX:unitWidth * x,
          originY:unitHeight * y
        }
        points.push(position)
      }
  }

  this.randomize()

  for (let i = 0; i < points.length; i++) {
      if (
        points[i].originX != unitWidth*(numPointsX - 1)
        && points[i].originY != unitHeight*(numPointsY-1)
        ) {
          let topLeftX = points[i].x
          let topLeftY = points[i].y
          let topRightX = points[i + 1].x
          let topRightY = points[i + 1].y
          let bottomLeftX = points[i + numPointsX].x
          let bottomLeftY = points[i + numPointsX].y
          let bottomRightX = points[i + numPointsX+1].x
          let bottomRightY = points[i + numPointsX+1].y

          let rando = Math.floor(Math.random() * 2)

          for (let n = 0; n < 2; n++) {
              let polygon = document.createElementNS(
                svg.namespaceURI,
                'polygon')

              if (rando === 0) {
                  if (n === 0) {
                    polygon.point1 = i
                    polygon.point2 = i + numPointsX
                    polygon.point3 = i + numPointsX + 1
                    polygon.setAttribute(
                      'points',
                      topLeftX+','+topLeftY+' \
                      '+bottomLeftX+','+bottomLeftY+' \
                      '+bottomRightX+','+bottomRightY);
                  } else if (n === 1) {
                      polygon.point1 = i;
                      polygon.point2 = i+1;
                      polygon.point3 = i+numPointsX+1
                      polygon.setAttribute(
                        'points',topLeftX+','+topLeftY+' \
                        '+topRightX+','+topRightY+' \
                        '+bottomRightX+','+bottomRightY)
                  }
              } else if (rando === 1) {
                  if (n === 0) {
                      polygon.point1 = i
                      polygon.point2 = i+numPointsX
                      polygon.point3 = i + 1
                      polygon.setAttribute(
                        'points',topLeftX+','+topLeftY+' \
                        '+bottomLeftX+','+bottomLeftY+' \
                        '+topRightX+','+topRightY)
                  } else if (n === 1) {
                      polygon.point1 = i + numPointsX
                      polygon.point2 = i + 1
                      polygon.point3 = i  +numPointsX + 1
                      polygon.setAttribute(
                        'points',bottomLeftX+','+bottomLeftY+' \
                        '+topRightX+','+topRightY+' \
                        '+bottomRightX+','+bottomRightY)
                  }
              }
              polygon.setAttribute('fill',`rgba(0,0,0,${(Math.random() / 3)})`)
              let animate = document.createElementNS('http://www.w3.org/2000/svg','animate')
              animate.setAttribute('fill','freeze')
              animate.setAttribute('attributeName','points')
              animate.setAttribute('dur', `${refreshDuration}ms`)
              animate.setAttribute('calcMode','linear')
              polygon.appendChild(animate)
              svg.appendChild(polygon)
          }
      }
  }

  return svg
}

export const randomize = () => {
  for (let i = 0; i < points.length; i++) {
    if (points[i].originX !== 0 && points[i].originX !== unitWidth * (this.numPointsX - 1)) {
        points[i].x = points[i].originX + Math.random() * unitWidth-unitWidth / 10;
    }

    if (points[i].originY != 0 && points[i].originY != unitHeight*(this.numPointsY - 1)) {
        points[i].y = points[i].originY + Math.random() * unitHeight-unitHeight / 10;
    }
  }
}

export const animate = (element, refreshDuration) => {
  this.randomize();
  const triangles = document.querySelector(`${element} .triangles`)
  for (let i = 0; i < triangles.childNodes.length; i++) {
      let polygon = triangles.childNodes[i]
      let animate = polygon.childNodes[0]
      if(animate.getAttribute('to')) {
          animate.setAttribute('from',animate.getAttribute('to'))
      }
      animate.setAttribute(
        'to',points[polygon.point1].x+','+points[polygon.point1].y+' \
        '+points[polygon.point2].x+','+points[polygon.point2].y+' \
        '+points[polygon.point3].x+','+points[polygon.point3].y)

      animate.beginElement()
  }

  setTimeout(() => this.animate(element, refreshDuration), refreshDuration)
}
