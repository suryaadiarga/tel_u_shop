/** @type {import('tailwindcss').Config} */
export default {
  content: ["./index.html", "./src/**/*.{ts,tsx}"],
  theme: {
    extend: {
      fontFamily: {
        sans: ["\"Source Sans 3\"", "system-ui", "sans-serif"],
        display: ["\"Space Grotesk\"", "system-ui", "sans-serif"],
      },
      colors: {
        ink: "#0f172a",
        ember: "#f59e0b",
        glacier: "#0ea5a4",
        mist: "#e2e8f0",
      },
      boxShadow: {
        glow: "0 0 0 1px rgba(14, 165, 164, 0.15), 0 16px 40px rgba(15, 23, 42, 0.12)",
        panel: "0 18px 45px rgba(15, 23, 42, 0.12)",
      },
      borderRadius: {
        xl: "1.15rem",
      },
      keyframes: {
        "fade-in": {
          "0%": { opacity: 0, transform: "translateY(6px)" },
          "100%": { opacity: 1, transform: "translateY(0)" },
        },
        "slide-in-right": {
          "0%": { opacity: 0, transform: "translateX(12px)" },
          "100%": { opacity: 1, transform: "translateX(0)" },
        },
      },
      animation: {
        "fade-in": "fade-in 0.5s ease-out",
        "slide-in-right": "slide-in-right 0.45s ease-out",
      },
    },
  },
  plugins: [],
}
