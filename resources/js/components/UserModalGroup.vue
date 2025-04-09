<template>
    <div class="modal fade" id="modalGroup" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Lista de Grupos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <form class="row g-3" @submit.prevent="formController(url, $event)">
                            <div class="col-md-6">
                                <select class="form-select" v-model="group_id" name="group_id" id="group_id"
                                    @focus="$parent.clearErrorMsg($event)">
                                    <option value="" selected disabled>[ Seleccionar Grupo]</option>
                                    <option v-for="item in groups_general" :value="item.id" :key="item.id">{{ item.name
                                        }}
                                    </option>
                                </select>
                                <div id="group_id-error" class="error invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success">Agregar</button>
                            </div>
                        </form>
                    </div>
                    <hr>
                    <table class="table table-striped text-center" style="width:100%">
                        <thead>
                            <tr>
                                <th class="col-6 text-center">GRUPO</th>
                                <th class="col-3 text-center">OPCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="groups.length == 0">
                                <td colspan="2">
                                    Ningún dato disponible en esta tabla
                                </td>
                            </tr>
                            <tr v-for="group in groups" :key="group.id">
                                <td>{{ group.group_name }}</td>
                                <td>
                                    <button class="btn btn-danger" @click.prevent="deleteGroupUser(group)"
                                        title="Eliminar">
                                        <i class="fa-solid fa-trash-can fa-sm" style="color: #ffffff;"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>

                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        url_delete_group: {
            type: String,
            default: ''
        },
        url: {
            type: String,
            default: ''
        },
        groups_general: {
            type: Array,
            default: ''
        }
    },
    data() {
        return {
            groups: [],
            user: '',
            group_id: ''
        }
    },
    mounted() {
        EventBus.$on('group_modal', function (data, user) {
            this.groups = [];
            this.groups = data;
            this.user = user;
            $('#modalGroup').modal('show');
        }.bind(this));
    },
    methods: {
        formController: function (url, event) {

            const existingGroup = this.groups.find(group => group.group_id === this.group_id);

            if (existingGroup) {
                Swal.fire({
                    title: 'Error!',
                    text: 'El grupo ya se encuentra asignado.',
                    icon: "error",
                    heightAuto: false,
                })
                return;
            }

            var target = $(event.target);
            EventBus.$emit('loading', true);

            axios.post(url, {
                group_id: this.group_id,
                user_id: this.user.id
            }).then(response => {
                EventBus.$emit('loading', false);

                this.group_id = '';
                this.groups = [];
                this.groups = response.data.list;
                this.$parent.alertMsg(response.data.info);

            }).catch(error => {
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
            });
        },
        deleteGroupUser(group) {
            Swal.fire({
                title: '¡Cuidado!',
                text: '¿Seguro que desea quitarlo del grupo ' + group.group_name + '?',
                icon: "warning",
                heightAuto: false,
                showCancelButton: true,
                confirmButtonText: 'Sí',
                cancelButtonText: 'No'
            }).then(result => {
                EventBus.$emit('loading', true);

                if (result.value) {
                    axios.post(this.url_delete_group, {
                        id: group.id,
                        user_id: this.user.id
                    }).then(response => {
                        EventBus.$emit('loading', false);
                        this.groups = [];
                        this.groups = response.data.list;
                        this.$parent.alertMsg(response.data.info);

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
