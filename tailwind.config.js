/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.{vue,js,ts,blade.php}',
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    50:  '#eff6ff',
                    600: '#2563eb',
                    700: '#1d4ed8',
                },
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
}
