<template>
    <div class="container mt-2 p-5 border rounded bg-white">
        <div>
            <button class="btn btn-success" @click.prevent="create()" title="Crear">Crear Grupo</button>
        </div>
        <hr>
        <table id="example" class="table table-striped text-center" style="width:100%">
            <thead>
                <tr>
                    <th class="col-5 text-center">GRUPO</th>
                    <th class="col-4 text-center">IP</th>
                    <th class="col-4 text-center">HORARIO</th>
                    <th class="col-3 text-center">OPCIONES</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="group in groups" :key="group.id">
                    <td @dblclick.prevent="edit(group)">{{ group.name }}</td>
                    <td @dblclick.prevent="edit(group)">{{ group.ip }}</td>
                    <td @dblclick.prevent="edit(group)">{{ group.horario_name }}</td>
                    <td>
                        <button class="btn btn-primary" @click.prevent="edit(group)" title="Editar">
                            <i class="fa-solid fa-pen-to-square fa-sm" style="color: #ffffff;"></i>
                        </button>
                        <button class="btn btn-danger" @click.prevent="deleteGroup(group)" title="Eliminar">
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
        groups: {
            type: Array,
            default: ''
        },
        url_delete: {
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
        edit(group) {
            EventBus.$emit('edit_modal', group);
        },
        deleteGroup(group) {
            Swal.fire({
                title: '¡Cuidado!',
                text: '¿Seguro que desea eliminar el grupo ' + group.name + '?',
                icon: "warning",
                heightAuto: false,
                showCancelButton: true,
                confirmButtonText: 'Sí',
                cancelButtonText: 'No'
            }).then(result => {
                EventBus.$emit('loading', true);

                if (result.value) {
                    axios.post(this.url_delete, {
                        id: group.id,
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
       
    }
}
</script>
