/**
 * Development-only console warnings (Vite `import.meta.env.DEV`).
 * Centralises the eslint exception for intentional dev diagnostics.
 */
export function devWarn(...args) {
    if (import.meta.env.DEV) {
        // eslint-disable-next-line no-console -- dev-only diagnostics; stripped from typical prod behaviour
        console.warn(...args);
    }
}
