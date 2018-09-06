import ptBR from './pt_BR'
import enUS from './en_US'

const themeLocales = {
  'en_US': enUS,
  'pt_BR': ptBR
}

export const getThemeLocale = userLanguage =>
  themeLocales[userLanguage]
