<template>

    <div class="modal fade" id="userModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form class="row g-3" @submit.prevent="formController(url, $event)">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">{{ text }}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body row">
                        <input v-model="model.id" type="hidden" name="id" id="id">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nombre completo:</label>
                            <input v-model="model.name" type="text" class="form-control" id="name" name="name"
                                @focus="$parent.clearErrorMsg($event)">
                            <div id="name-error" class="error invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="user" class="form-label">Usuario:
                                <span class="badge bg-danger">{{ company.sufijo }}</span>
                            </label>

                            <input v-model="model.user" type="text" class="form-control" id="user" name="user"
                                @focus="$parent.clearErrorMsg($event)">
                            <div id="user-error" class="error invalid-feedback"></div>

                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Contraseña:</label>
                            <input v-model="model.password" type="text" class="form-control" id="password"
                                name="password" @focus="$parent.clearErrorMsg($event)">
                            <div id="password-error" class="error invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="telefono" class="form-label">Teléfono:</label>
                            <input v-model="model.telefono" type="text" class="form-control" id="telefono"
                                name="telefono" @focus="$parent.clearErrorMsg($event)">
                            <div id="telefono-error" class="error invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="genero" class="form-label">Género:</label>
                            <select class="form-select" v-model="model.genero" name="genero" id="genero"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                            </select>
                            <div id="genero-error" class="error invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="fecha_naci" class="form-label">Fecha de nacimiento:</label>
                            <input v-model="model.fecha_naci" type="date" name="fecha_naci" id="fecha_naci"
                                @focus="$parent.clearErrorMsg($event)" class="form-control">
                            <div id="fecha_naci-error" class="error invalid-feedback"></div>
                        </div>
                        <div class="col-md-12">
                            <label for="obs" class="form-label">Observaciones:</label>
                            <textarea class="form-control" v-model="model.obs" name="obs" id="obs"
                                @focus="$parent.clearErrorMsg($event)"></textarea>
                            <div id="obs-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white border-dark" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" :class="'btn btn-' + color">{{ text }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        url: {
            type: String,
            default: ''
        },
        company: {
            type: Object,
            default: ''
        },
    },
    data() {
        return {
            model: {
                id: '',
                name: '',
                user: '',
                password: '',
                telefono: '',
                genero: '',
                fecha_naci: '',
                obs: ''
            },
            text: '',
            color: ''
        }
    },
    created() {

    },
    mounted() {
        EventBus.$on('create_modal', function (data) {

            this.model.id = '';
            this.model.name = '';
            this.model.user = '';
            this.model.password = '';
            this.model.telefono = '';
            this.model.genero = '';
            this.model.fecha_naci = '';
            this.model.obs = '';

            this.text = "Crear"
            this.color = "success";

            $('#userModal').modal('show');
        }.bind(this));
        EventBus.$on('edit_modal', function (user) {

            this.model.id = user.id;
            this.model.name = user.name;
            this.model.user = user.user.slice(0, user.user.length - this.company.sufijo.length);
            this.model.password = '';
            this.model.telefono = user.telefono;
            this.model.genero = user.genero;
            this.model.fecha_naci = user.fecha_naci;
            this.model.obs = user.obs;

            this.text = "Actualizar"
            this.color = "primary";

            $('#userModal').modal('show');
        }.bind(this));
    },
    methods: {
        formController: function (url, event) {
            var vm = this;

            var target = $(event.target);
            var url = url;
            var fd = new FormData(event.target);

            EventBus.$emit('loading', true);

            // EventBus.$emit('loading', true);
            axios.post(url, fd, {
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded',
                }
            }).then(response => {
                EventBus.$emit('loading', false);
                this.$parent.alertMsg(response.data);

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

    }
}
</script>
