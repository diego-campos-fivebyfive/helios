import { axios } from '@/router'

export function woopra() {
  /* eslint-disable */
  if (process.env.AMBIENCE !== 'localhost') {
    ;!function(){var a,b,c,d=window,e=document,f=arguments,g="script",h=["config","track","trackForm","trackClick","identify","visit","push","call"],i=function(){var a,b=this,c=function(a){b[a]=function(){return b._e.push([a].concat(Array.prototype.slice.call(arguments,0))),b}};for(b._e=[],a=0;a<h.length;a++)c(h[a])};for(d.__woo=d.__woo||{},a=0;a<f.length;a++)d.__woo[f[a]]=d[f[a]]=d[f[a]]||new i;b=e.createElement(g),b.async=1,b.src="//static.woopra.com/js/w.js",c=e.getElementsByTagName(g)[0],c.parentNode.insertBefore(b,c)}("wo");

    const domain = process.env.API_URL.split('://')

    wo.config({
      domain: domain[1]
    })

    axios.get('api/v1/user/track-account')
      .then(response => {
        wo.identify(response.data)
        wo.track()
      })
  }
/* eslint-enable */
}
