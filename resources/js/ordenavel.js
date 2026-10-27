import Sortable from 'sortablejs';

/**
 * Lista que muda de ordem arrastando uma alça. Única porta de entrada para o SortableJS no projeto.
 */
export function tornarOrdenavel(lista, { itens, alca, aoSoltar }) {
    return Sortable.create(lista, {
        draggable: itens,
        handle: alca,
        animation: 150,
        ghostClass: 'ordenando',
        onEnd: (evento) => {
            if (evento.oldIndex !== evento.newIndex) {
                aoSoltar();
            }
        },
    });
}
