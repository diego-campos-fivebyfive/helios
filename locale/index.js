const themeLocales = {
  en_US: require('./en_US'),
  pt_BR: require('./pt_BR')
}

export const getThemeLocale = userLanguage =>
  themeLocales[userLanguage]
