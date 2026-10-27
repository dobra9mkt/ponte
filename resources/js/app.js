import Alpine from '@alpinejs/csp';
import { tornarOrdenavel } from './ordenavel';

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

const alternavel = () => ({
    aberto: false,
    abrir() {
        this.aberto = true;
    },
    fechar() {
        this.aberto = false;
    },
    alternar() {
        this.aberto = !this.aberto;
    },
    get classeSeta() {
        return this.aberto ? 'rotate-90' : '';
    },
});

Alpine.data('textoRecolhivel', () => ({
    aberto: false,
    cortado: false,
    init() {
        this.$nextTick(() => this.medir());
    },
    medir() {
        if (!this.aberto) {
            const texto = this.$refs.texto;
            this.cortado = texto.scrollHeight > texto.clientHeight + 1;
        }
    },
    alternar() {
        this.aberto = !this.aberto;
    },
    get podeAlternar() {
        return this.cortado || this.aberto;
    },
    get classeTexto() {
        return this.aberto ? 'line-clamp-none' : '';
    },
}));

Alpine.data('confirmar', alternavel);
Alpine.data('alternar', alternavel);

Alpine.data('servicoNovo', () => ({
    escolhido: '',
    init() {
        this.escolhido = this.$refs.servico.value;
    },
    escolher() {
        this.escolhido = this.$refs.servico.value;
    },
    get outro() {
        return this.escolhido === '';
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

Alpine.data('rascunho', () => ({
    estado: '',
    revisao: 0,
    espera: null,
    init() {
        this.revisao = Number(this.$root.dataset.revisao);
    },
    agendar() {
        clearTimeout(this.espera);
        this.espera = setTimeout(() => this.salvar(), 1500);
    },
    async salvar() {
        clearTimeout(this.espera);
        const textos = this.$root.dataset;
        const dados = new FormData();
        dados.append('_token', this.$root.querySelector('[name=_token]').value);
        dados.append('_method', 'PUT');
        dados.append('texto', this.$refs.texto.value);
        dados.append('revisao', this.revisao);
        this.estado = textos.salvando;

        try {
            const resposta = await fetch(textos.url, {
                method: 'POST',
                body: dados,
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            const corpo = await resposta.json();

            if (!resposta.ok) {
                this.estado = Object.values(corpo.errors ?? {})[0]?.[0] ?? textos.erro;
                return;
            }

            this.revisao = corpo.revisao;
            this.estado = textos.salvo;
        } catch {
            this.estado = textos.erro;
        }
    },
    get classeEstado() {
        return [this.$root.dataset.salvando, this.$root.dataset.salvo].includes(this.estado) ? 'text-mudo' : 'text-alerta font-medium';
    },
}));

Alpine.data('ordenarFases', () => ({
    init() {
        tornarOrdenavel(this.$root, { itens: '[data-fase-id]', alca: '[data-alca]', aoSoltar: () => this.salvar() });
    },
    async salvar() {
        const lista = this.$root;
        const dados = new FormData();
        dados.append('_token', lista.querySelector(':scope > [name=_token]').value);
        dados.append('_method', 'PUT');
        lista.querySelectorAll(':scope > [data-fase-id]').forEach((fase, indice) => {
            dados.append('fases[]', fase.dataset.faseId);
            const numero = fase.querySelector('[data-numero]');
            if (numero) {
                numero.textContent = indice + 1;
            }
        });

        try {
            const resposta = await fetch(lista.dataset.url, {
                method: 'POST',
                body: dados,
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (resposta.ok) {
                return;
            }
        } catch {
            // segue para o aviso abaixo
        }

        window.alert(lista.dataset.erro);
        window.location.reload();
    },
}));

window.Alpine = Alpine;
Alpine.start();
