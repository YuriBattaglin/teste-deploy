export const initialOf = (name?: string) => (name?.trim()?.charAt(0) || '?').toUpperCase()
export const capitalize = (str: string) => str.charAt(0).toUpperCase() + str.slice(1)