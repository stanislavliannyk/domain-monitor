/** Форматирование «X назад / X через» через нативный Intl.RelativeTimeFormat. */
export function diffForHumans(dateString) {
    if (!dateString) return '—'

    const diff = (new Date(dateString) - Date.now()) / 1000 // секунды, отрицательные = прошлое
    const abs  = Math.abs(diff)
    const rtf  = new Intl.RelativeTimeFormat('ru', { numeric: 'auto' })

    if (abs < 60)    return rtf.format(Math.round(diff), 'second')
    if (abs < 3600)  return rtf.format(Math.round(diff / 60), 'minute')
    if (abs < 86400) return rtf.format(Math.round(diff / 3600), 'hour')
    return rtf.format(Math.round(diff / 86400), 'day')
}

export function formatDatetime(dateString) {
    if (!dateString) return '—'
    return new Date(dateString).toLocaleString('ru-RU', {
        year:   'numeric',
        month:  '2-digit',
        day:    '2-digit',
        hour:   '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false,
    })
}
