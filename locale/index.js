import en_US from './en_US'
import es_MX from './es_MX'
import pt_BR from './pt_BR'

const themeLocales = {
  en_US,
  es_MX,
  pt_BR
}

export const getThemeLocale = userLanguage =>
  themeLocales[userLanguage]
