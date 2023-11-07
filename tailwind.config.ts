/** @type {import('tailwindcss').Config} */

import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import {createThemes} from "tw-colors";
import Color from 'color';

const alpha = (clr, val) => Color(clr).alpha(val).toString();
const lighten = (clr, val) => Color(clr).lighten(val).toString();
const darken = (clr, val) => Color(clr).darken(val).toString();

const colors = (clr, {inverse = false, step = .1} = {}) => ({
    DEFAULT: clr,
    light: inverse ? darken(clr, step) : lighten(clr, step),
    dark: inverse ? lighten(clr, step) : darken(clr, step),
});

const baseThemes = {
    light: {
        primary: colors('#83b71a'),
        secondary: colors('#6c757d'),
        success: colors('#00aac0'),
        info: colors('#00aac0'),
        warning: colors('#f37e16'),
        danger: colors('#c50005'),
        dark: '#303030',
        light: '#f1f1f1',
        back: {
            DEFAULT: '#ffffff',
            light: '#f1f1f1',
            dark: '#303030',
        },
        front: {
            DEFAULT: '#000000',
            light: '#424242',
            dark: '#ffffff',
        },
    },
    dark: {
        primary: colors('#83b71a'),
        secondary: colors('#6c757d'),
        success: colors('#00aac0'),
        info: colors('#00aac0'),
        warning: colors('#f37e16'),
        danger: colors('#c50005'),
        dark: '#303030',
        light: '#f1f1f1',
        back: {
            DEFAULT: '#303030',
            light: '#424242',
            dark: '#212121',
        },
        front: {
            DEFAULT: '#ffffff',
            light: '#c2c2c2',
            dark: '#ffffff',
        },
    }
}
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
            colors: {}
        },
    },
    plugins: [
        forms,
        createThemes(({light, dark}) => ({
            light: light(baseThemes.light),
            dark: dark(baseThemes.dark),
            broto: dark({
                ...baseThemes.dark,
                primary: colors('#b57000'),
                success: colors('#cdd911'),
            }),
            nightMode: dark({
                ...baseThemes.dark,
                back: {
                    DEFAULT: '#1e2f62',
                    light: '#05103A',
                    dark: '#0c1f66',
                },
            }),
        })),
    ],
}

