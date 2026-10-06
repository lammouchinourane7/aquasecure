import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Space Grotesk', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                ink: 'rgb(var(--color-ink-rgb) / <alpha-value>)',
                primary: 'rgb(var(--color-primary-rgb) / <alpha-value>)',
                'primary-light': 'rgb(var(--color-primary-light-rgb) / <alpha-value>)',
                'primary-strong': 'rgb(var(--color-primary-strong-rgb) / <alpha-value>)',
                bg: 'rgb(var(--color-bg-rgb) / <alpha-value>)',
                surface: 'rgb(var(--color-surface-rgb) / <alpha-value>)',
                border: 'rgb(var(--color-border-rgb) / <alpha-value>)',
                muted: 'rgb(var(--color-muted-rgb) / <alpha-value>)',
                alert: 'rgb(var(--color-alert-rgb) / <alpha-value>)',
                'alert-strong': 'rgb(var(--color-alert-strong-rgb) / <alpha-value>)',
                warning: 'rgb(var(--color-warning-rgb) / <alpha-value>)',
                'warning-strong': 'rgb(var(--color-warning-strong-rgb) / <alpha-value>)',
                success: 'rgb(var(--color-success-rgb) / <alpha-value>)',
                'success-strong': 'rgb(var(--color-success-strong-rgb) / <alpha-value>)',
            },
        },
    },

    plugins: [forms],
};
