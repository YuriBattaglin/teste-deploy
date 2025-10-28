import { dayjs } from "element-plus"

export function formatDateBR(iso?: string): string {
  if (!iso) return ""
  const normalized = iso.trim()
  if (!normalized) return ""
  const [datePart] = normalized.split("T")
  const parts = datePart.split("-")
  if (parts.length >= 3) {
    const [year, month, day] = parts
    if (year && month && day) {
      return `${day.padStart(2, "0")}/${month.padStart(2, "0")}`
    }
  }
  const d = new Date(normalized)
  if (Number.isNaN(d.getTime())) return ""
  const dd = String(d.getUTCDate()).padStart(2, "0")
  const mm = String(d.getUTCMonth() + 1).padStart(2, "0")
  return `${dd}/${mm}`
}

export const isValidDate = (d: unknown): d is Date => d instanceof Date && !Number.isNaN(d.getTime())

export const toYMD = (d: Date): string => {
  const y = d.getFullYear()
  const m = String(d.getMonth() + 1).padStart(2, "0")
  const day = String(d.getDate()).padStart(2, "0")
  return `${y}-${m}-${day}`
}

export const startOfIsoWeek = (d: Date): Date => {
  const date = new Date(d.getFullYear(), d.getMonth(), d.getDate(), 12, 0, 0, 0)
  const dow = date.getDay()
  const diffToMonday = dow === 0 ? -6 : 1 - dow
  date.setDate(date.getDate() + diffToMonday)
  return date
}

export const extractDatesFromWeek = (
  val: unknown
): { start_date: string | null; end_date: string | null } => {
  let base: Date | null = null
  if (isValidDate(val)) {
    base = val as Date
  } else if (typeof val === "string" && val.trim()) {
    const tmp = new Date(val)
    if (!Number.isNaN(tmp.getTime())) base = tmp
  }
  if (!base) return { start_date: null, end_date: null }
  const start = startOfIsoWeek(base)
  const end = new Date(start)
  end.setDate(start.getDate() + 6)
  return {
    start_date: toYMD(start),
    end_date: toYMD(end),
  }
}

export function getWeekNumber(startDate?: string): string {
  if (!startDate) return ""
  const start = dayjs(startDate)
  if (!start.isValid()) return ""
  return `Semana ${start.isoWeek()}`
}

export function getMonths(locale = "pt-BR") {
  return Array.from({ length: 12 }, (_, i) => ({
    value: i + 1,
    label: new Date(2000, i, 1).toLocaleDateString(locale, { month: "long" }),
  }))
}

export function getYears(start = 2020, end = new Date().getFullYear() + 1) {
  return Array.from({ length: end - start + 1 }, (_, i) => start + i)
}

export function formatWeekRangeLabel(weekStart: string): string {
  const { start_date, end_date } = extractDatesFromWeek(weekStart)
  if (!start_date || !end_date) return "—"
  const w = getWeekNumber(start_date)
  const [ys, ms, ds] = start_date.split("-").map(Number)
  const [ye, me, de] = end_date.split("-").map(Number)
  const start = new Date(ys, ms - 1, ds)
  const end = new Date(ye, me - 1, de)
  const br = (d: Date) =>
    d.toLocaleDateString("pt-BR", { day: "2-digit", month: "2-digit" })
  return `${w} • ${br(start)} – ${br(end)}`
}
