<template>
    <div>
        <div>
            <a class="btn btn-success mt-4 mx-2" :href=" url_edit + '?id=' + tab_state_id " title="Crear">Crear Nuevo</a>
        </div>
        <hr>
        <div class="overflow-x-scroll w-full">
            <table id="forms" class="table table-bordered border-primary text-center" style="width:100%">
                <thead>
                    <tr>
                        <th class="col-1 text-center">ID</th>
                        <th class="col-1 text-center">FECHA DE CREACIÓN</th>
                        <th class="col-1 text-center">FECHA DE EDICIÓN</th>
                        <th class="col-1 text-center">AGENTE</th>
                        <th class="col-1 text-center">ESTADO</th>
                        <th class="col-2 text-center">OPCIONES</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        url: {
            type: String,
            default: '',
        },
        url_edit: {
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
        },
        url_download: {
            type: String,
            default: '',
        }
    },
    data() {
        return {
            forms: [],
            fields: [],
            tab_state_id: "",
            campain_id: "",
        }
    },
    mounted() {
        EventBus.$on('refresh_table', function (tab_state_id, campain_id) {
            const fd = new FormData();

            this.tab_state_id = tab_state_id;
            this.campain_id = campain_id;

            fd.append('campain_id', this.campain_id);
            fd.append('tab_state_id', this.tab_state_id);

            axios.post(this.url, fd, {
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded',
                }
            })
                .then(response => {
                    const { forms, fields } = response.data;

                    this.forms = forms;
                    this.fields = fields;

                    const columns = [];

                    for (let i = 0; i < this.fields.length; i++) {
                        const field = this.fields[i];

                        columns.push({
                                        title: field.name.toUpperCase(), data: 'data',
                                        render: (data, type, row) => {
                                            const dataParse = JSON.parse(data);
                                            let val = "";

                                            if (dataParse) {
                                            const block = dataParse[field.block_id];

                                            if (block) {
                                                const f = block[field.id];

                                                switch (field.type_field_id) {
                                                case 5:
                                                    val = new Date(f).toLocaleString()
                                                    break;

                                                case 6:
                                                    val = new Date(f).toLocaleString()
                                                    break;

                                                case 8:
                                                    val = f ? "Activo" : "Inactivo"
                                                    break;

                                                case 9:
                                                    val = `<i class="download fa-solid fa-cloud-arrow-down fa-2xl cursor-pointer" style="color: #22c55e;" data-files="${f}"></i>`
                                                    break;

                                                case 10:
                                                    val = `<i class="download fa-solid fa-cloud-arrow-down fa-2xl cursor-pointer" style="color: #22c55e;" data-files="${f}"></i>`
                                                    break;

                                                default:
                                                    val = f;
                                                    break;
                                                }
                                            };
                                            };

                                            return val;
                                        }
                                    });
                    };

                    $('#forms').DataTable().clear().rows.add(this.forms).draw();
                    $('#forms').DataTable({
                        data: this.forms,
                        columns: [
                            { title: 'ID', data: 'id' },
                            { title: 'FECHA DE CREACIÓN', data: 'created_at',
                                render: (data, type, row) => {
                                    return new Date(data).toLocaleString()
                                }
                            },
                            { title: 'FECHA DE EDICIÓN', data: 'updated_at',
                                render: (data, type, row) => {
                                    return new Date(data).toLocaleString()
                                }
                            },
                            { title: 'AGENTE', data: 'created_at_user' },
                            ...columns,
                            {
                                title: 'ESTADO', data: 'state',
                                render: (data, type, row) => {
                                    return data === 1 ? 'Activo' : 'Inactivo';
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
                                        </button>`;

                                    return stateButton + actionButtons;
                                }
                            },
                        ],
                        createdRow: function (row, data, dataIndex) {
                            $(row).css('background-color', data.color);
                        }
                    });
                }).catch(error => {
                    console.log(error);
                });
        }.bind(this));
        EventBus.$on('show_table', function (forms, fields, tab_state_id, campain_id) {
            this.forms = forms;
            this.fields = fields;
            this.tab_state_id = tab_state_id;
            this.campain_id = campain_id;

            const columns = [];

            for (let i = 0; i < this.fields.length; i++) {
                const field = this.fields[i];

                columns.push({
                                title: field.name.toUpperCase(), data: 'data',
                                render: (data, type, row) => {
                                    const dataParse = JSON.parse(data);
                                    let val = "";

                                    if (dataParse) {
                                        const block = dataParse[field.block_id];

                                        if (block) {
                                            const f = block[field.id];

                                            switch (field.type_field_id) {
                                            case 5:
                                                val = new Date(f).toLocaleString()
                                                break;

                                            case 6:
                                                val = new Date(f).toLocaleString()
                                                break;

                                            case 8:
                                                val = f ? "Activo" : "Inactivo"
                                                break;

                                            case 9:
                                                val = `<i class="download fa-solid fa-cloud-arrow-down fa-2xl cursor-pointer" style="color: #22c55e;" data-files="${f}"></i>`
                                                break;

                                            case 10:
                                                val = `<i class="download fa-solid fa-cloud-arrow-down fa-2xl cursor-pointer" style="color: #22c55e;" data-files="${f}"></i>`
                                                break;

                                            default:
                                                val = f;
                                                break;
                                            }
                                        };
                                    };

                                    return `<div class="w-100 h-100 cell" data-id="${row.id}">${val}</div>`
                                }
                            });
            };

            this.$nextTick(() => {
                if ($.fn.DataTable.isDataTable('#forms')) {
                    $('#forms').DataTable().clear().destroy();
                }

                $('#forms').DataTable({
                    data: this.forms,
                    columns: [
                        {
                            title: 'ID',
                            data: 'id',
                            render: (data, type, row) => {
                                return `<div class="w-100 h-100 cell" data-id="${row.id}">${data}</div>`
                            }
                        },
                        {
                            title: 'FECHA DE CREACIÓN',
                            data: 'created_at',
                            render: (data, type, row) => {
                                const date = new Date(data).toLocaleString()
                                return `<div class="w-100 h-100 cell" data-id="${row.id}">${date}</div>`
                            }
                        },
                        {
                            title: 'FECHA DE EDICIÓN',
                            data: 'updated_at',
                            render: (data, type, row) => {
                                const date = new Date(data).toLocaleString()
                                return `<div class="w-100 h-100 cell" data-id="${row.id}">${date}</div>`
                            }
                        },
                        { title: 'AGENTE', data: 'created_at_user' },
                        ...columns,
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
                        },
                    ],
                    createdRow: function (row, data, dataIndex) {
                        $(row).css('background-color', data.color);
                    }
                });
                $('#forms').on('dblclick', '.cell', event => {
                    const id = $(event.currentTarget).data('id');
                    this.edit(id);
                });
                $('#forms').on('click', '.edit', event => {
                    const id = $(event.currentTarget).data('id');
                    this.edit(id);
                });
                $('#forms').on('click', '.delete', event => {
                    const id = $(event.currentTarget).data('id');
                    this.delete(id);
                });
                $('#forms').on('click', '.deshabilitar', event => {
                    const id = $(event.currentTarget).data('id');
                    this.deshabilitar(id);
                });
                $('#forms').on('click', '.habilitar', event => {
                    const id = $(event.currentTarget).data('id');
                    this.habilitar(id);
                });
                $('#forms').on('click', '.download', event => {
                    const files = $(event.currentTarget).data('files');
                    this.downloadFile(files);
                });

            });
            this.isDisabled = false;
        }.bind(this));
    },
    methods: {
            edit(id) {
                EventBus.$emit('loading', true);
                window.location.href = this.url_edit + "?id=" + this.tab_state_id + "&form_id=" + id;
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
                    if (result.value) {
                        axios.post(this.url_deshabilitar, {
                            id: id,
                        }).then(response => {
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
            downloadFile: async function(files) {
                const ids = [];

                if (typeof files == "string") {
                    const array = files.split(",");

                    for (let i = 0; i < array.length; i++) {
                    const id = array[i];
                    ids.push(+id);
                    };
                } else {
                    ids.push(files)
                };

                for (let i = 0; i < ids.length; i++) {
                    const id = ids[i];

                    EventBus.$emit('loading', true);

                    try {
                        const response = await axios({
                            url: this.url_download.replace('__ID__', id),
                            method: 'GET',
                            responseType: 'blob'
                        });

                        let fileName = 'archivo.pdf';

                        const contentDisposition = response.headers['content-disposition'];

                        if (contentDisposition) {
                            const fileArray = contentDisposition.split(";")

                            if (fileArray.length > 1) {
                                const name = fileArray[1].replace("filename=", "");

                                if (name) fileName = name;
                            };
                        };

                        const url = window.URL.createObjectURL(new Blob([response.data]));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', fileName);
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        EventBus.$emit('loading', false);
                    } catch (error) {
                        EventBus.$emit('loading', false);
                        console.log(error.response);
                        var obj = error.response.data.errors;
                        $('.modal').animate({
                            scrollTop: 0
                        }, 500, 'swing');
                        $.each(obj, function (i, item) {
                            let c_target = target.find("#" + i + "-error");
                            if (!c_target.attr('data-required')) {
                                let p = c_target.prev();
                                p.addClass('is-invalid');
                            } else {
                                c_target.css('display', 'block');
                            }
                            c_target.html(item);
                        });
                    }
                };
            }
    }
}
</script>