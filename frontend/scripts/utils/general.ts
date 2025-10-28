export const setPageTitle = (pageName: string) => {
  const config = useRuntimeConfig()
  if (typeof document !== 'undefined') {
    document.title = `${pageName} - ${config.public.appName}`
  }
}
