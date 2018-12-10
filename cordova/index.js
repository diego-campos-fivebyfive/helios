const cordovaScript = document.createElement('script')
cordovaScript.setAttribute('src', '../cordova.js')
document.body.appendChild(cordovaScript)

document.body.onLoad = () => {
  document.addEventListener('deviceready', () => {
    if (process.env.NODE_ENV !== 'production') {
      console.info('Cordova started')
    }
  
    window.open = cordova.InAppBrowser.open
  }, false)
}
