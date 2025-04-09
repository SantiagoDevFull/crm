<template>
    <div class="container mt-2 p-5 border rounded bg-white">
        <div>
            <button class="btn btn-success" @click.prevent="create()" title="Crear">Crear Usuario</button>
        </div>
        <hr>
        <table id="example" class="table table-striped text-center" style="width:100%">
            <thead>
                <tr>
                    <th class="col-2 text-center">NOMBRE</th>
                    <th class="col-2 text-center">EMAIL</th>
                    <th class="col-1 text-center">TELEFONO</th>
                    <th class="col-1 text-center">GENERO</th>
                    <th class="col-1 text-center">FECHA DE NACIMIENTO</th>
                    <th class="col-2 text-center">OBSERVACIONES</th>
                    <th class="col-3 text-center">OPCIONES</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in users" :key="user.id">
                    <td @dblclick.prevent="edit(user)">{{ user.name }}</td>
                    <td @dblclick.prevent="edit(user)">{{ user.user }}</td>
                    <td @dblclick.prevent="edit(user)">{{ user.telefono }}</td>
                    <td @dblclick.prevent="edit(user)">{{ user.genero }}</td>
                    <td @dblclick.prevent="edit(user)">{{ user.fecha_naci }}</td>
                    <td @dblclick.prevent="edit(user)">{{ user.obs }}</td>
                    <td>
                        <button class="btn btn-primary" @click.prevent="edit(user)" title="Editar">
                            <i class="fa-solid fa-pen-to-square fa-sm" style="color: #ffffff;"></i>
                        </button>
                        <button class="btn btn-danger" @click.prevent="deleteUser(user)" title="Eliminar">
                            <i class="fa-solid fa-trash-can fa-sm" style="color: #ffffff;"></i>
                        </button>
                        <button class="btn btn-info" @click.prevent="viewGroups(user)" title="Lista de Grupos">
                            <i class="fa-solid fa-people-group fa-sm" style="color: #ffffff;"></i>
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
        users: {
            type: Array,
            default: ''
        },
        url_delete: {
            type: String,
            default: '',
        },
        url_list_group: {
            type: String,
            default: '',
        }
    },
    mounted() {

    },
    methods: {
        create() {
            EventBus.$emit('create_modal');
        },
        edit(user) {
            EventBus.$emit('edit_modal', user);
        },
        deleteUser(user) {
            Swal.fire({
                title: '¡Cuidado!',
                text: '¿Seguro que desea eliminar al usuario ' + user.name + '?',
                icon: "warning",
                heightAuto: false,
                showCancelButton: true,
                confirmButtonText: 'Sí',
                cancelButtonText: 'No'
            }).then(result => {
                EventBus.$emit('loading', true);

                if (result.value) {
                    axios.post(this.url_delete, {
                        id: user.id,
                    }).then(response => {
                        EventBus.$emit('loading', false);
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
        viewGroups(user) {
            EventBus.$emit('loading', true);
            axios.post(this.url_list_group, {
                user_id: user.id,
            }).then(response => {
                EventBus.$emit('loading', false);
                EventBus.$emit('group_modal',response.data,user);
            }).catch(error => {
                console.log(error);
                console.log(error.response);
            });
        }
    }
}
</script>
