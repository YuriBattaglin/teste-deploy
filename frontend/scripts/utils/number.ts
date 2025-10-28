export function formatBRL(n?: number | null): string {
  const val = n ?? 0
  try {
    return new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL" }).format(val)
  } catch {
    return `R$ ${Number(val).toFixed(2)}`
  }
}
