import Alpine from 'alpinejs';
import { Html5Qrcode } from 'html5-qrcode';

window.Alpine = Alpine;
window.Html5Qrcode = Html5Qrcode;

Alpine.start();

document.addEventListener('focusin', (event) => {
    const el = event.target;
    if (el.tagName !== 'INPUT' || el.type !== 'number') return;
    if (el.readOnly || el.disabled || el.value === '') return;
    if (Number(el.value) === 0) el.value = '';
});