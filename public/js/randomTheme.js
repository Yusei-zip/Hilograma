function randomTheme() {
        const temas = ['dark', 'light', 'vintage', 'purple', 'ocean', 'forest', 'strawberry', 'cyberpunk', 'espresso'];
        const random = temas[Math.floor(Math.random() * temas.length)];
        document.documentElement.setAttribute('data-theme', random);
        localStorage.setItem('theme', random);
        console.log("Tema actual: " + random);

}
const loadTheme = () => {
        const saved = localStorage.getItem('theme') || 'dark';
        html.setAttribute('data-theme', saved);
        if (sunIcon) sunIcon.style.display = saved === 'dark' ? 'block' : 'none';
        if (moonIcon) moonIcon.style.display = saved === 'dark' ? 'none' : 'block';
    };
    loadTheme();