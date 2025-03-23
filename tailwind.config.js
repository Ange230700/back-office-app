// tailwind.config.js

module.exports = {
  content: [
    "./source/Templates/**/*.{twig,php}",
    "./source/**/*.js",
    "./public/**/*.php",
  ],
  theme: {
    extend: {
      fontFamily: {
        niramit: ["Niramit", "sans-serif"],
      },
    },
  },
  plugins: [require("flowbite/plugin")],
};
