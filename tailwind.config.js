 /** @type {import('tailwindcss').Config} */
    module.exports = {
      content: ["./*.{html,php}"],
    
      theme: {
        extend: {
          fontFamily: {
        'karla': ['Karla', 'sans-serif'],
        'rubik': ['Rubik', 'sans-serif'],
        'montserrat': ['Montserrat', 'sans-serif'],
        // Add more variations as needed
      }
        },
      },
      plugins: [],
    }