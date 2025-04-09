<template>
    <div class="container mt-2 p-5 border rounded bg-white">
        <div>
            <button class="btn btn-success" :class="{ disabled: isDisabled }" @click.prevent="create()" title="Crear">Crear Bloque de Campos</button>
        </div>
        <hr>
        <table id="blocks" class="table table-bordered border-primary  text-center" style="width:100%">
            <thead>
                <tr>
                    <th class="col-1 text-center">CAMPAÑA</th>
                    <th class="col-2 text-center">NOMBRE</th>
                    <th class="col-1 text-center">ORDEN</th>
                    <th class="col-1 text-center">ESTADO</th>
                    <th class="col-2 text-center">OPCIONES</th>
                </tr>
            </thead>
        </table>
    </div>
</template>

<script>

export default {
    props: {
        url: {
            type: String,
            default: '',
        },
        url_get_block: {
            type: String,
            default: '',
        },
        url_delete: {
            type: String,
            default: '',
        },
        url_deshabilitar: {
            type: String,
            default: '',
        },
        url_habilitar: {
            type: String,
            default: '',
        }
    },
    data() {
        return {
            blocks: [],
            campain_id: '',
            isDisabled: true
        }
    },
    created() {},
    mounted() {
        EventBus.$on('refresh_table', function () {
            axios.post(this.url, {
                'campain_id': this.campain_id
            })
                .then(response => {
                    this.blocks = response.data;
                    $('#blocks').DataTable().clear().rows.add(this.blocks).draw();
                }).catch(error => {
                    console.log(error);
                });
        }.bind(this));
        EventBus.$on('show_table', function (data, campain_id) {
            this.blocks = data;
            this.campain_id = campain_id;

            this.$nextTick(() => {
                if ($.fn.DataTable.isDataTable('#blocks')) {
                    $('#blocks').DataTable().clear().destroy();
                }

                $('#blocks').DataTable({
                    data: this.blocks,
                    columns: [
                        {
                            title: 'CAMPAÑA',
                            data: 'campain_name',
                            render: (data, type, row) => {
                                return `<div class="w-100 h-100 cell" data-id="${row.id}">${data}</div>`
                            }
                        },
                        {
                            title: 'NOMBRE',
                            data: 'name',
                            render: (data, type, row) => {
                                return `<div class="w-100 h-100 cell" data-id="${row.id}">${data}</div>`
                            }
                        },
                        {
                            title: 'ORDEN',
                            data: 'order',
                            render: (data, type, row) => {
                                return `<div class="w-100 h-100 cell" data-id="${row.id}">${data}</div>`
                            }
                        },
                        {
                            title: 'ESTADO', data: 'state',
                            render: (data, type, row) => {
                                const state = data === 1 ? 'Activo' : 'Inactivo'
                                return `<div class="w-100 h-100 cell" data-id="${row.id}">${state}</div>`
                            }
                        },
                        {
                            title: 'OPCIONES',
                            data: null,
                            render: (data, type, row) => {
                                const stateButton = row.state == 1
                                    ? `<button class="deshabilitar btn btn-secondary" title="Deshabilitar" data-id="${row.id}">
                                            <i class="fa-solid fa-ban" style="color: #ffffff;"></i>
                                        </button>`
                                    : `<button class="habilitar btn btn-success" title="Habilitar" data-id="${row.id}">
                                            <i class="fa-solid fa-check" style="color: #ffffff;"></i>
                                        </button>`;

                                const actionButtons = `
                                    <button class="edit btn btn-primary" title="Editar" data-id="${row.id}">
                                        <i class="fa-solid fa-pen-to-square fa-sm" style="color: #ffffff;"></i>
                                    </button>
                                    <button class="delete btn btn-danger" title="Eliminar" data-id="${row.id}">
                                        <i class="fa-solid fa-trash-can fa-sm" style="color: #ffffff;"></i>
                                    </button>
    `;

                                return stateButton + actionButtons;
                            }
                        }
                    ],
                    createdRow: function (row, data, dataIndex) {
                        $(row).css('background-color', data.color);
                    }
                });
                $('#blocks').on('dblclick', '.cell', event => {
                    const id = $(event.currentTarget).data('id');
                    this.edit(id);
                });
                $('#blocks').on('click', '.edit', event => {
                    const id = $(event.currentTarget).data('id');
                    this.edit(id);
                });
                $('#blocks').on('click', '.delete', event => {
                    const id = $(event.currentTarget).data('id');
                    this.delete(id);
                });
                $('#blocks').on('click', '.deshabilitar', event => {
                    const id = $(event.currentTarget).data('id');
                    this.deshabilitar(id);
                });
                $('#blocks').on('click', '.habilitar', event => {
                    const id = $(event.currentTarget).data('id');
                    this.habilitar(id);
                });
            });
            this.isDisabled = false;
        }.bind(this));
    },
    methods: {
        create() {
            EventBus.$emit('clear_modal');
            EventBus.$emit('create_modal', this.campain_id);
        },
        edit(id) {
            EventBus.$emit('clear_modal');
            EventBus.$emit('loading', true);
            axios.post(this.url_get_block, {
                'id': id
            })
                .then(response => {
                    EventBus.$emit('loading', false);
                    EventBus.$emit('edit_modal', response.data);
                }).catch(error => {
                    console.log(error);
                });
        },
        delete(id) {
            Swal.fire({
                title: '¡Cuidado!',
                text: '¿Seguro que desea eliminar el registro seleccionado?',
                icon: "warning",
                heightAuto: false,
                showCancelButton: true,
                confirmButtonText: 'Sí',
                cancelButtonText: 'No'
            }).then(result => {
                EventBus.$emit('loading', true);

                if (result.value) {
                    axios.post(this.url_delete, {
                        id: id,
                    }).then(response => {
                        EventBus.$emit('loading', false);
                        EventBus.$emit('refresh_table');
                        this.$parent.alertMsg(response.data);
                    }).catch(error => {
                        console.log(error);
                        console.log(error.response);
                    });
                } else if (result.dismiss == Swal.DismissReason.cancel) {
                    EventBus.$emit('loading', false);
                }
            });
        },
        deshabilitar(id) {
            Swal.fire({
                title: '¡Cuidado!',
                text: '¿Seguro que desea deshabilitar el registro seleccionado?',
                icon: "warning",
                heightAuto: false,
                showCancelButton: true,
                confirmButtonText: 'Sí',
                cancelButtonText: 'No'
            }).then(result => {
                EventBus.$emit('loading', true);

                if (result.value) {
                    axios.post(this.url_deshabilitar, {
                        id: id,
                    }).then(response => {
                        EventBus.$emit('loading', false);
                        EventBus.$emit('refresh_table');
                        this.$parent.alertMsg(response.data);
                    }).catch(error => {
                        console.log(error);
                        console.log(error.response);
                    });
                } else if (result.dismiss == Swal.DismissReason.cancel) {
                    EventBus.$emit('loading', false);
                }
            });
        },
        habilitar(id) {
            Swal.fire({
                title: '¡Cuidado!',
                text: '¿Seguro que desea habilitar el registro seleccionado?',
                icon: "warning",
                heightAuto: false,
                showCancelButton: true,
                confirmButtonText: 'Sí',
                cancelButtonText: 'No'
            }).then(result => {
                EventBus.$emit('loading', true);

                if (result.value) {
                    axios.post(this.url_habilitar, {
                        id: id,
                    }).then(response => {
                        EventBus.$emit('loading', false);
                        EventBus.$emit('refresh_table');
                        this.$parent.alertMsg(response.data);
                    }).catch(error => {
                        console.log(error);
                        console.log(error.response);
                    });
                } else if (result.dismiss == Swal.DismissReason.cancel) {
                    EventBus.$emit('loading', false);
                }
            });
        },
    }
}

</script>