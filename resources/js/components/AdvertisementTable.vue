<template>
    <div class="container mt-2 p-5 border rounded bg-white">
        <div>
            <button class="btn btn-success" @click.prevent="create()" title="Crear">Crear Anuncio</button>
        </div>
        <hr>
        <table id="advertisements" class="table table-bordered border-primary  text-center" style="width:100%">
            <thead>
                <tr>
                    <th class="col-4 text-center">TÍTULO</th>
                    <th class="col-1 text-center">ESTADO</th>
                    <th class="col-2 text-center">OPCIONES</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="advertisement in advertisements" :key="advertisement.id">
                    <td @dblclick.prevent="edit(advertisement.id)">{{ advertisement.title }}</td>
                    <td @dblclick.prevent="edit(advertisement.id)">{{ advertisement.state == "1" ? "Activo" : "Inactivo" }}</td>
                    <td>
                            <button v-if="advertisement.state == '1'" class="deshabilitar btn btn-secondary" @click.prevent="deshabilitar(advertisement.id)" title="Deshabilitar">
                                <i class="fa-solid fa-ban" style="color: #ffffff;"></i>
                            </button>
                            <button v-if="advertisement.state == '0'" class="habilitar btn btn-success" @click.prevent="habilitar(advertisement.id)" title="Habilitar">
                                <i class="fa-solid fa-check" style="color: #ffffff;"></i>
                            </button>
                            <button class="btn btn-primary" @click.prevent="edit(advertisement.id)" title="Editar">
                                <i class="fa-solid fa-pen-to-square fa-sm" style="color: #ffffff;"></i>
                            </button>
                            <button class="btn btn-danger" @click.prevent="deleteCamp(advertisement.id)" title="Eliminar">
                                <i class="fa-solid fa-trash-can fa-sm" style="color: #ffffff;"></i>
                            </button>
                        </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>

export default {
    props: {
        advertisements: {
            type: Array,
            default: []
        },
        url_get_advertisement: {
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
    data() {},
    created() {},
    mounted() {
        EventBus.$on('refresh_table', function () {
            location.reload();
        }.bind(this));
    },
    methods: {
        create() {
            EventBus.$emit('clear_modal');
            EventBus.$emit('create_modal');
        },
        edit(id) {
            EventBus.$emit('clear_modal');
            EventBus.$emit('loading', true);
            axios.post(this.url_get_advertisement, {
                'id': id
            })
                .then(response => {
                    EventBus.$emit('loading', false);
                    EventBus.$emit('edit_modal', response.data);
                }).catch(error => {
                    console.log(error);
                });
        },
        deleteCamp(id) {
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