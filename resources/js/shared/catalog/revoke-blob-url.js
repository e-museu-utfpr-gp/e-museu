export function revokeObjectUrlIfBlob(el) {
    if (el && el.src && String(el.src).indexOf('blob:') === 0) {
        URL.revokeObjectURL(el.src);
    }
}
