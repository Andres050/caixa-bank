import fs from 'fs';
import config from './tailwind.config.js';

// Acceder a los colores extendidos del tema
const colors = config.theme?.extend?.colors ?? {};

fs.writeFileSync('./tailwind-colors.json', JSON.stringify(colors, null, 2));
console.log('✅ Colores exportados correctamente a tailwind-colors.json');
