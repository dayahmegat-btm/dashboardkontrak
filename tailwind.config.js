import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                navy: {
                    DEFAULT: '#0B1A2B',
                    50: '#E6EAF0',
                    100: '#CCD5E1',
                    200: '#99ABC3',
                    300: '#6681A5',
                    400: '#335787',
                    500: '#0B1A2B',
                    600: '#091522',
                    700: '#07101A',
                    800: '#040B11',
                    900: '#020509',
                },
                gold: {
                    DEFAULT: '#B8893A',
                    50: '#F9F5EC',
                    100: '#F3EBD9',
                    200: '#E7D7B3',
                    300: '#DBC38D',
                    400: '#CFAF67',
                    500: '#B8893A',
                    600: '#936E2E',
                    700: '#6E5223',
                    800: '#4A3717',
                    900: '#251B0C',
                },
            },
        },
    },
    plugins: [forms, typography],
};
