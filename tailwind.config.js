import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        // Palet warna avatar inisial (didefinisikan dinamis di app/Models/Barang.php)
        { pattern: /^bg-(emerald|sky|violet|amber|rose|cyan|indigo|teal)-600$/ },
    ],

    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#ecfdf5', 100: '#d1fae5', 200: '#a7f3d0',
                    300: '#6ee7b7', 400: '#34d399', 500: '#10b981',
                    600: '#059669', 700: '#047857', 800: '#065f46',
                    900: '#064e3b',
                },
                sidebar: { DEFAULT: '#0f172a', hover: '#1e293b' },
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            keyframes: {
                marquee: {
                    '0%': { transform: 'translateX(100vw)' },
                    '100%': { transform: 'translateX(-100%)' },
                },
            },
            animation: {
                marquee: 'marquee 22s linear infinite',
            },
        },
    },

    plugins: [forms],
};
