import en_US from './en_US'
import pt_BR from './pt_BR'

const themeLocales = {
  en_US,
  pt_BR
}

export const getThemeLocale = userLanguage =>
  themeLocales[userLanguage]
