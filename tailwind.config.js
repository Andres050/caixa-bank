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
                sans: ['Oswald', 'Inter', 'sans-serif'],
            },
            transitionDuration: {
                DEFAULT: '300ms',
                '400': '400ms'
            },
            keyframes: {
                appearTop: {
                    '0%': { transform: 'translateY(-100%)' },
                    '100%': { transform: 'translateY(0)' },
                },
                moveUpDown: {
                    '0%': { transform:'translatex(8px)' },
                    '50%': { transform: 'translatex(32px)' },
                    '100%': { transform:'translatex(8px)' }
                }
            },
            animation: {
                movingUp: 'moveUpDown 6s linear infinite',
            },
            colors: {
                primary: '#FFFFFF',
                secondary: '#9ca3af',
                third: '#374151',

                main1: '#0e0f11',
                main2: '#1a1b1e',
                main3: 'var(--color-main3)',

                navActive: 'var(--color-navActive)',
                navActiveBg: 'var(--color-navActiveBg)',

                success: '#33c034',
                error: '#d03022',
                warning: '#FF9800',
                info: '#055160'
            },
            dropShadow: {
                primary: '0px 0px 5px var(--color-main3)',
                secondary: '0px 0px 20px var(--color-main3)',
            },
            fill: theme => ({
                'main1': theme('colors.main1'),
                'main1Light': theme('colors.main1Light'),
                'main1Dark': theme('colors.main1Dark'),
                'main2': theme('colors.main2'),
                'main2Light': theme('colors.main2Light'),
                'main2Lighteen': theme('colors.main2Lighteen'),
                'main4': theme('colors.main4'),
                'main3': theme('colors.main3'),
                'main3100': theme('colors.main3100'),
                'main3Light': theme('colors.main2Light'),
                'main3Lighteen': theme('colors.main3Lighteen'),
                'neutral100': theme('colors.neutral100'),
                'neutral700': theme('colors.neutral700'),

                'primary': theme('colors.primary'),
                'heading': theme('colors.heading'),

                'text': theme('colors.text'),
                'text1': theme('colors.text1'),
                'border': theme('colors.border'),
                'transparent': theme('colors.transparent'),
                'current': theme('colors.current'),
            }),
            screens: {
                '-3xl': {'max': '1799px'},
                '3xl': {'min': '1800px'},
                '-2xl': {'max': '1535px'},
                '2xl': {'min': '1536px'},
                '-xl': {'max': '1279px'},
                '-lg': {'max': '1123px'},
                'lg': {'min': '1124px'},
                '-md': {'max': '767px'},
                '-sm': {'max': '639px'},
                'md-lg': {'min': '768px', 'max' : '1123px'},
                'md-xl': {'min': '768px', 'max' : '1279px'},
                'md-2xl': {'min': '768px', 'max' : '1535px'},
                'xl-2xl': {'min': '1280px', 'max' : '1535px'},
                'md-3xl': {'min': '768px', 'max' : '1799px'},
                'lowScreen': { 'raw': '(max-height: 850px)' },
                'lowScreenMobile': { 'raw': '(max-width: 767px) and (max-height: 850px)' },
                'tinyScreenMobile': { 'raw': '(max-width: 767px) and (max-height: 700px)' }
            },
        },
    },

    plugins: [forms],
};
