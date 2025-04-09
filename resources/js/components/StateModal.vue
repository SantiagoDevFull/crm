<template>

    <div class="modal fade" id="stateModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form class="row g-3 mb-3" @submit.prevent="formController(url, $event)">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">{{ text }}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body row">
                        <input v-model="model.id" type="text" name="id" id="id">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nombre:</label>
                            <input v-model="model.name" type="text" class="form-control" id="name" name="name"
                                @focus="$parent.clearErrorMsg($event)">
                            <div id="name-error" class="error invalid-feedback"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="tab_state_id" class="form-label">Pestañas de Estado:</label>
                            <select class="form-select" v-model="model.tab_state_id" name="tab_state_id"
                                id="tab_state_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="tab_state in tab_states" :value="tab_state.id" :key="tab_state.id">{{
                                    tab_state.name }}</option>
                            </select>
                            <div id="tab_state_id-error" class="error invalid-feedback"></div>
                        </div>

                        <div class="col-md-4">
                            <label for="color" class="form-label">Color:</label>
                            <input v-model="model.color" type="color" class="form-control" id="color" name="color"
                                @focus="$parent.clearErrorMsg($event)">
                            <div id="color-error" class="error invalid-feedback"></div>
                        </div>

                        <div class="col-md-4">
                            <label for="order" class="form-label">Orden:</label>
                            <input v-model="model.order" type="text" class="form-control" id="order" name="order"
                                @focus="$parent.clearErrorMsg($event)">
                            <div id="order-error" class="error invalid-feedback"></div>
                        </div>

                        <div class="col-md-4">
                            <label for="state_user" class="form-label">Estados usado para:</label>
                            <select class="form-select" v-model="model.state_user" name="state_user" id="state_user"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option value="0">Crear Ventas</option>
                                <option value="1">Editar Ventas</option>
                                <option value="2">Crear y/o Editar Ventas</option>

                            </select>
                            <div id="state_user-error" class="error invalid-feedback"></div>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>

                        <div v-if="model.state_user == '1' || model.state_user == '2'" class="col-md-12">
                            <label for="states_ids" class="form-label">Estados sobre los que se va a cambiar la venta a este estado:</label>
                            <multiselect v-model="model.states_ids" :options="states" name="states_ids" label="name" track-by="id" :multiple="true"></multiselect>
                            <div id="states_ids-error" class="error invalid-feedback"></div>
                        </div>

                        <div class="col-md-4 form-check form-switch">
                            <label class="form-check-label" for="flexSwitchCheckChecked">Resaltar en
                                notificaciones</label>
                            <input v-model="model.not" name="not" id="not" class="form-check-input" type="checkbox"
                                role="switch">
                        </div>

                        <div class="col-md-4 form-check form-switch">
                            <label class="form-check-label" for="flexSwitchCheckChecked">Estado es agendado</label>
                            <input v-model="model.age" name="age" id="age" class="form-check-input" type="checkbox"
                                role="switch">
                        </div>

                        <div class="col-md-4 form-check form-switch">
                            <label class="form-check-label" for="flexSwitchCheckChecked">Estado es comisionable</label>
                            <input v-model="model.com" name="com" id="com" class="form-check-input" type="checkbox"
                                role="switch">
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
import 'vue-multiselect/dist/vue-multiselect.min.css';
import Multiselect from 'vue-multiselect';

export default {
    components: { Multiselect },
    props: {
        url: {
            type: String,
            default: ''
        },
        url_get_tab_states: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            model: {
                id: '',
                tab_state_id: '',
                state_user: '',
                name: '',
                color: '',
                order: '',
                not: '',
                age: '',
                com: '',
                states_ids: []
            },
            text: '',
            color: '',
            tab_states: [],
            states: []
        }
    },
    created() {

    },
    mounted() {
        EventBus.$on('list_tab_states', function (campain_id) {

            EventBus.$emit('loading', true);
            axios.post(this.url_get_tab_states, {
                'campain_id': campain_id
            })
                .then(response => {
                    EventBus.$emit('loading', false);
                    this.tab_states = response.data;
                }).catch(error => {
                    console.log(error);
                });

        }.bind(this));

        EventBus.$on('clear_modal', function () {
            this.model.id = '';
            this.model.tab_state_id = '';
            this.tab_states = [];
            this.model.name = '';
            this.model.order = '';
            this.model.color = '';
            this.model.state_user = '';
            this.model.not = '';
            this.model.age = '';
            this.model.com = '';

            this.text = ""
            this.color = "";

            $('#stateModal').modal('hide');
        }.bind(this));
        EventBus.$on('create_modal', function (states) {
            this.states = states;

            this.text = "Crear";
            this.color = "success";

            $('#stateModal').modal('show');
        }.bind(this));
        EventBus.$on('edit_modal', function (data, states) {
            const { state, states: statesIn } = data;

            this.states = states

            this.model.id = state.id;
            this.model.tab_state_id = state.tab_state_id;
            this.model.name = state.name;
            this.model.order = state.order;
            this.model.color = state.color;
            this.model.state_user = state.state_user;
            this.model.not = state.not;
            this.model.age = state.age;
            this.model.com = state.com;
            this.model.states_ids = statesIn

            this.text = "Actualizar"
            this.color = "primary";

            $('#stateModal').modal('show');
        }.bind(this));
    },
    methods: {
        formController: function (url, event) {
            var vm = this;

            var target = $(event.target);
            var url = url;
            var fd = new FormData();

            fd.append("id", this.model.id)
            fd.append("tab_state_id", this.model.tab_state_id)
            fd.append("state_user", this.model.state_user)
            fd.append("name", this.model.name)
            fd.append("order", this.model.order)
            fd.append("color", this.model.color)
            fd.append("state_user", this.model.state_user)
            fd.append("not", this.model.not)
            fd.append("age", this.model.age)
            fd.append("com", this.model.com)

            const state_ids = [];

            for (let i = 0; i < this.model.states_ids.length; i++) {
                const element = this.model.states_ids[i];
                state_ids.push(element.id);
            };

            fd.append("state_ids", JSON.stringify(state_ids))

            EventBus.$emit('loading', true);

            axios.post(url, fd, {
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded',
                }
            }).then(response => {
                EventBus.$emit('loading', false);
                EventBus.$emit('refresh_table');
                EventBus.$emit('clear_modal');
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
