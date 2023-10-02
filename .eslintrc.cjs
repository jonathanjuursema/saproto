module.exports = {
    env: {
        browser: true,
        es2022: true,
    },
    parser: 'vue-eslint-parser',
    parserOptions: {
        ecmaVersion: 2022,
        parser: '@typescript-eslint/parser',
    },
    extends: [
        'eslint:recommended',
        'plugin:@typescript-eslint/recommended',
        'plugin:vue/vue3-recommended',
        'plugin:prettier/recommended',
    ],
    plugins: [
        'vue',
        '@typescript-eslint',
        'prettier',
    ],
    ignorePatterns: [
        'resources/js/models/',
    ],
    rules: {
        'no-undef': 'off',
    },
};