const html = document.documentElement;
const sunIcon = document.getElementById('sunIcon');
const moonIcon = document.getElementById('moonIcon');

const loadTheme = () => {
    const saved = localStorage.getItem('theme') || 'dark';
    html.setAttribute('data-theme', saved);
    if (sunIcon) sunIcon.style.display = saved === 'dark' ? 'block' : 'none';
    if (moonIcon) moonIcon.style.display = saved === 'dark' ? 'none' : 'block';
};
loadTheme();

function toggleTheme() {
    const temas = ['dark', 'light', 'vintage', 'purple', 'ocean', 'forest', 'strawberry', 'cyberpunk', 'espresso'];
    const current = document.documentElement.getAttribute('data-theme') || 'dark';

    let index = temas.indexOf(current);
    let next = temas[(index + 1) % temas.length];

    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    console.log("Tema actual: " + index);
}
document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || 'dark');