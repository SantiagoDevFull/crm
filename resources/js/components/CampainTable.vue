<template>
    <div class="container mt-2 p-5 border rounded bg-white">
        <div>
            <button class="btn btn-success" @click.prevent="create()" title="Crear">Crear Campaña</button>
        </div>
        <hr>
        <table id="campains" class="table table-bordered border-primary  text-center" style="width:100%">
            <thead>
                <tr>
                    <th class="col-1 text-center">ID</th>
                    <th class="col-1 text-center">NOMBRE</th>
                    <th class="col-1 text-center">ESTADO</th>
                    <th class="col-4 text-center">PARÁMETROS</th>
                    <th class="col-2 text-center">OPCIONES</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="camp in campains" :key="camp.id">
                    <td @dblclick.prevent="edit(camp.id)">{{ camp.id }}</td>
                    <td @dblclick.prevent="edit(camp.id)">{{ camp.name }}</td>
                    <td @dblclick.prevent="edit(camp.id)">{{ camp.state == "1" ? "Activo" : "Inactivo" }}</td>
                    <td class="">
                        <a :href="'/pestaña-estado?id=' + camp.id" class="py-px px-1 text-2xs rounded-1 no-underline" style="background-color: #fb5597; color: #fff;">Pestañas de Estados</a>
                        <a :href="'/estado?id=' + camp.id" class="py-px px-1 text-2xs rounded-1 no-underline" style="background-color: #ff5b57; color: #fff;">Estados</a>
                        <a :href="'/bloque-de-campos?id=' + camp.id" class="py-px px-1 text-2xs rounded-1 no-underline" style="background-color: #90ca4b; color: #fff;">Bloques de Campos</a>
                        <a :href="'/campos?id=' + camp.id" class="py-px px-1 text-2xs rounded-1 no-underline" style="background-color: #32a932; color: #fff;">Campos</a>
                        <a href="#" class="py-px px-1 text-2xs rounded-1 no-underline" style="background-color: #727cb6; color: #fff;">Categorías</a>
                        <a href="#" class="py-px px-1 text-2xs rounded-1 no-underline" style="background-color: #8753de; color: #fff;">Productos</a>
                        <a href="#" class="py-px px-1 text-2xs rounded-1 no-underline" style="background-color: #49b6d6; color: #fff;">Promociones</a>
                        <a href="#" class="py-px px-1 text-2xs rounded-1 no-underline" style="background-color: #ffd900; color: #fff;">Supervisores</a>
                        <a href="#" class="py-px px-1 text-2xs rounded-1 no-underline" style="background-color: #f59c1a; color: #fff;">Agentes</a>
                        <a href="#" class="py-px px-1 text-2xs rounded-1 no-underline" style="background-color: #39424a; color: #fff;">Listas</a>
                        <a href="#" class="py-px px-1 text-2xs rounded-1 no-underline" style="background-color: #a7b6bf; color: #fff;">Tipificaciones</a>
                    </td>
                    <td>
                            <button v-if="camp.state == '1'" class="deshabilitar btn btn-secondary" @click.prevent="deshabilitar(camp.id)" title="Deshabilitar">
                                <i class="fa-solid fa-ban" style="color: #ffffff;"></i>
                            </button>
                            <button v-if="camp.state == '0'" class="habilitar btn btn-success" @click.prevent="habilitar(camp.id)" title="Habilitar">
                                <i class="fa-solid fa-check" style="color: #ffffff;"></i>
                            </button>
                            <button class="btn btn-primary" @click.prevent="edit(camp.id)" title="Editar">
                                <i class="fa-solid fa-pen-to-square fa-sm" style="color: #ffffff;"></i>
                            </button>
                            <button class="btn btn-danger" @click.prevent="deleteCamp(camp.id)" title="Eliminar">
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
        campains: {
            type: Array,
            default: []
        },
        url_get_campain: {
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
            axios.post(this.url_get_campain, {
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