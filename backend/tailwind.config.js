/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    50: '#fff1f1',
                    100: '#ffd9d9',
                    200: '#ffb3b3',
                    300: '#ff8c8c',
                    400: '#ef4444',
                    500: '#b91c1c',
                    600: '#991b1b',
                },
            },
            boxShadow: {
                soft: '0 10px 30px rgba(0,0,0,0.06)',
            },
            borderRadius: {
                '2xl': '1.25rem',
            },
            spacing: {
                safe: 'env(safe-area-inset-bottom)',
            },
        },
    },
    plugins: [],
}
