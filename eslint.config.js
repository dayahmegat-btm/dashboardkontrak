import js from '@eslint/js';
import pluginVue from 'eslint-plugin-vue';

export default [
    js.configs.recommended,
    ...pluginVue.configs['flat/recommended'],
    {
        files: ['resources/**/*.js', 'resources/**/*.vue'],
        languageOptions: {
            ecmaVersion: 2022,
            sourceType: 'module',
            globals: {
                window: 'readonly',
                document: 'readonly',
                console: 'readonly',
                Alpine: 'readonly',
                Livewire: 'readonly',
            },
        },
        rules: {
            'no-console': 'warn',
            'no-unused-vars': 'warn',
            'vue/multi-word-component-names': 'off',
        },
    },
    {
        ignores: [
            'vendor/**',
            'node_modules/**',
            'public/**',
            'storage/**',
            'bootstrap/cache/**',
        ],
    },
];
