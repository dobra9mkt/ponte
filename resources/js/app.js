import Alpine from '@alpinejs/csp';

Alpine.data('menu', () => ({
    aberto: false,
    alternar() {
        this.aberto = !this.aberto;
    },
    fechar() {
        this.aberto = false;
    },
    get classeBarra() {
        return this.aberto ? 'barra-aberta' : '';
    },
}));

Alpine.data('confirmar', () => ({
    aberto: false,
    abrir() {
        this.aberto = true;
    },
    fechar() {
        this.aberto = false;
    },
}));

Alpine.data('senhaVisivel', () => ({
    visivel: false,
    alternar() {
        this.visivel = !this.visivel;
    },
    get tipo() {
        return this.visivel ? 'text' : 'password';
    },
    get rotulo() {
        return this.visivel ? 'Ocultar' : 'Mostrar';
    },
}));

window.Alpine = Alpine;
Alpine.start();
