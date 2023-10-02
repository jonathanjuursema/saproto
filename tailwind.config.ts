/** @type {import('tailwindcss').Config} */

import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import {createThemes} from "tw-colors";
import Color from 'color';
const alpha = (clr, val) => Color(clr).alpha(val).toString();
const lighten = (clr, val) => Color(clr).lighten(val).toString();
const darken = (clr, val) => Color(clr).darken(val).toString();

const colors = (clr) => ({
    DEFAULT: clr,
    light: lighten(clr, .1),
    dark: darken(clr, .1),
});
export default {
    content: [
        "./resources/views/inertia/app.blade.php",
        "./resources/js/**/*.{js,ts,vue}",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Lato', ...defaultTheme.fontFamily.sans],
            },
            colors: {
            }
        },
    },
    plugins: [
        forms,
        createThemes(({light, dark}) => ({
            light: light({
                primary: colors('#83b71a'),
                secondary: colors('#6c757d'),
                success: colors('#00aac0'),
                info: colors('#00aac0'),
                warning: colors('#f37e16'),
                danger: colors('#c50005'),
                dark: '#303030',
                light: '#f1f1f1',
            }),
            broto: dark({
                primary: colors('#b57000'),
                success: colors('#cdd911'),
                dark: '#303030',
                light: '#303030',
            }),
        })),
    ],
}

