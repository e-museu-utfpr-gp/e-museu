import js from '@eslint/js';
import prettier from 'eslint-config-prettier';

export default [
    js.configs.recommended,
    prettier,
    {
        languageOptions: {
            ecmaVersion: 2024,
            sourceType: 'module',
            globals: {
                // Browser globals
                window: 'readonly',
                document: 'readonly',
                console: 'readonly',
                navigator: 'readonly',
                localStorage: 'readonly',
                sessionStorage: 'readonly',
                // Browser APIs
                setTimeout: 'readonly',
                clearTimeout: 'readonly',
                setInterval: 'readonly',
                clearInterval: 'readonly',
                confirm: 'readonly',
                alert: 'readonly',
                prompt: 'readonly',
                // Event objects
                event: 'readonly',
                Event: 'readonly',
                // jQuery (if used globally)
                $: 'readonly',
                jQuery: 'readonly',
                // Bootstrap
                bootstrap: 'readonly',
            },
        },
        rules: {
            // Customize rules as needed
            'no-unused-vars': ['warn', { 
                argsIgnorePattern: '^_',
                varsIgnorePattern: '^_',
                args: 'after-used',
                ignoreRestSiblings: true,
                caughtErrors: 'none',
            }],
            'no-console': 'off', // Allow console in development
            'no-undef': 'error',
            // Prettier handles formatting, so we don't need formatting rules here
        },
        ignores: [
            'node_modules/**',
            'vendor/**',
            'public/build/**',
            'public/hot',
            'storage/**',
            'bootstrap/cache/**',
            '*.min.js',
        ],
    },
];
