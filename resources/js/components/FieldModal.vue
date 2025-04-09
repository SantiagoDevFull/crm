<template>
    <div class="modal fade" id="fieldModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form class="row g-3 mb-3" @submit.prevent="formController(url, $event)">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">{{ text }}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body row">
                        <input v-model="model.id" class="d-none" type="text" name="id" id="id">
                        <input v-model="model.campain_id" class="d-none" type="text" name="campain_id" id="campain_id">

                        <div class="col-md-8">
                            <label for="name" class="form-label">Nombre:</label>
                            <input v-model="model.name" type="text" class="form-control" id="name" name="name"
                                @focus="$parent.clearErrorMsg($event)">
                            <div id="name-error" class="error invalid-feedback"></div>
                        </div>

                        <div class="col-md-4">
                            <label for="order" class="form-label">Orden:</label>
                            <input v-model="model.order" type="text" class="form-control" id="order" name="order"
                                @focus="$parent.clearErrorMsg($event)">
                            <div id="order-error" class="error invalid-feedback"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="block_id" class="form-label">Bloque de Campos:</label>
                            <select class="form-select" v-model="model.block_id" name="block_id" id="block_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="block in blocks" :value="block.id" :key="block.id">{{ block.name }}</option>
                            </select>
                            <div id="block_id-error" class="error invalid-feedback"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="type_field_id" class="form-label">Tipo de Campo:</label>
                            <select class="form-select" v-model="model.type_field_id" name="type_field_id" id="type_field_id" @focus="$parent.clearErrorMsg($event)" @change="handleSelectTypeFieldChange">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="type_field in type_fields" :value="type_field.id" :key="type_field.id">{{ type_field.name }}</option>
                            </select>
                            <div id="type_field_id-error" class="error invalid-feedback"></div>
                        </div>

                        <div class="col-md-6" v-if="viewOptions">
                            <label for="options" class="form-label">Valores:</label>
                            <textarea v-model="model.options" class="form-control" id="options" name="options" @focus="$parent.clearErrorMsg($event)"></textarea>
                            <div id="options-error" class="error invalid-feedback"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="width_id" class="form-label">Ancho del campo:</label>
                            <select class="form-select" v-model="model.width_id" name="width_id" id="width_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                    <option v-for="width in widths" :value="width.id" :key="width.id">{{ width.col }}/12 | {{ ((width.col / 12) * 100).toFixed(2) }}%</option>
                            </select>
                            <div id="width_id-error" class="error invalid-feedback"></div>
                        </div>

                        <div class="col-md-12">
                            <label for="state_ids" class="form-label">Pestaña de Estados en los que va a aparecer este campo:</label>
                            <multiselect v-model="model.tab_state_ids" :options="tab_states" name="tab_state_ids" label="name" track-by="id" :multiple="true"></multiselect>
                            <!-- <select class="select2-multiple form-select" v-model="model.state_ids" multiple size="3" name="state_ids[]" id="state_ids" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="state in states" :value="state.id" :key="state.id">{{ state.name }}</option>
                            </select> -->
                            <div id="state_ids-error" class="error invalid-feedback"></div>
                        </div>

                        <div class="col-md-12">
                            <label for="group_edit_ids" class="form-label">Pueden acceder a este campo [edición]:</label>
                            <multiselect v-model="model.group_edit_ids" :options="groups" name="group_edit_ids" label="name" track-by="id" :multiple="true"></multiselect>
                            <!-- <select class="select2-multiple form-select" v-model="model.group_edit_ids" multiple size="3" name="group_edit_ids[]" id="group_edit_ids" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="group in groups" :value="group.id" :key="group.id">{{ group.name }}</option>
                            </select> -->
                            <div id="group_edit_ids-error" class="error invalid-feedback"></div>
                        </div>

                        <div class="col-md-12">
                            <label for="group_view_ids" class="form-label">Pueden acceder a este campo [solo visualización]:</label>
                            <multiselect v-model="model.group_view_ids" :options="groups" name="group_view_ids" label="name" track-by="id" :multiple="true"></multiselect>
                            <!-- <select class="select2-multiple form-select" v-model="model.group_view_ids" multiple size="3" name="group_view_ids[]" id="group_view_ids" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="group in groups" :value="group.id" :key="group.id">{{ group.name }}</option>
                            </select> -->
                            <div id="group_view_ids-error" class="error invalid-feedback"></div>
                        </div>

                        <div class="col-md-12">
                            <label for="group_have_comment_ids" class="form-label">Pueden agregar comentarios sobre este campo:</label>
                            <multiselect v-model="model.group_have_comment_ids" :options="groups" name="group_have_comment_ids" label="name" track-by="id" :multiple="true"></multiselect>
                            <!-- <select class="select2-multiple form-select" v-model="model.group_have_comment_ids" multiple size="3" name="group_have_comment_ids[]" id="group_have_comment_ids" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="group in groups" :value="group.id" :key="group.id">{{ group.name }}</option>
                            </select> -->
                            <div id="group_have_comment_ids-error" class="error invalid-feedback"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-check-label" for="required">Campo obligatorio</label>
                            <switches v-model="model.required"  name="required" id="required" class="form-check-input"></switches>
                            <!-- <input v-model="model.required" name="required" id="required" class="form-check-input" type="checkbox" role="switch"> -->
                        </div>

                        <div class="col-md-6">
                            <label class="form-check-label" for="unique">Campo unico</label>
                            <switches v-model="model.unique"  name="unique" id="unique" class="form-check-input"></switches>
                            <!-- <input v-model="model.unique" name="unique" id="unique" class="form-check-input" type="checkbox" role="switch"> -->
                        </div>

                        <div class="col-md-6">
                            <label class="form-check-label" for="bloq_mayus">Campo es en mayúsculas</label>
                            <switches v-model="model.bloq_mayus"  name="bloq_mayus" id="bloq_mayus" class="form-check-input"></switches>
                            <!-- <input v-model="model.bloq_mayus" name="bloq_mayus" id="bloq_mayus" class="form-check-input" type="checkbox" role="switch"> -->
                        </div>

                        <div class="col-md-6">
                            <label class="form-check-label" for="in_solds_list">Agregar campo al listado de ventas</label>
                            <switches v-model="model.in_solds_list"  name="in_solds_list" id="in_solds_list" class="form-check-input"></switches>
                            <!-- <input v-model="model.in_solds_list" name="in_solds_list" id="in_solds_list" class="form-check-input" type="checkbox" role="switch"> -->
                        </div>

                        <div class="col-md-6">
                            <label class="form-check-label" for="in_notifications">Agregar campo a las notificaciones</label>
                            <switches v-model="model.in_notifications"  name="in_notifications" id="in_notifications" class="form-check-input"></switches>
                            <!-- <input v-model="model.in_notifications" name="in_notifications" id="in_notifications" class="form-check-input" type="checkbox" role="switch"> -->
                        </div>

                        <div class="col-md-6">
                            <label class="form-check-label" for="in_general_search">Agregar campo en el buscador general</label>
                            <switches v-model="model.in_general_search"  name="in_general_search" id="in_general_search" class="form-check-input"></switches>
                            <!-- <input v-model="model.in_general_search" name="in_general_search" id="in_general_search" class="form-check-input" type="checkbox" role="switch"> -->
                        </div>

                        <div class="col-md-6">
                            <label class="form-check-label" for="has_edit">Puede editarse en el listado de ventas</label>
                            <switches v-model="model.has_edit"  name="has_edit" id="has_edit" class="form-check-input"></switches>
                            <!-- <input v-model="model.has_edit" name="has_edit" id="has_edit" class="form-check-input" type="checkbox" role="switch"> -->
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white border-dark" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" :class="'btn btn-'">{{ text }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import 'vue-multiselect/dist/vue-multiselect.min.css';
import Multiselect from 'vue-multiselect';
import Switches from 'vue-switches';

export default {
    components: { Multiselect, Switches },
    props: {
        url: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            model: {
                id: '',
                campain_id: '',
                block_id: '',
                type_field_id: '',
                width_id: '',
                tab_state_ids: [],
                group_edit_ids: [],
                group_view_ids: [],
                group_have_comment_ids: [],
                required: false,
                unique: false,
                bloq_mayus: false,
                in_solds_list: false,
                in_notifications: false,
                in_general_search: false,
                has_edit: false,
                name: '',
                order: '',
                options: '',
            },
            blocks: [],
            type_fields: [],
            widths: [],
            states: [],
            tab_states: [],
            groups: [],
            viewOptions: false,
            text: '',
        }
    },
    created() {

    },
    mounted() {
        EventBus.$on('clear_modal', function () {
            this.model = {
                id: '',
                campain_id: '',
                block_id: '',
                type_field_id: '',
                width_id: '',
                state_ids: [],
                group_edit_ids: [],
                group_view_ids: [],
                group_have_comment_ids: [],
                required: false,
                unique: false,
                bloq_mayus: false,
                in_solds_list: false,
                in_notifications: false,
                in_general_search: false,
                has_edit: false,
                name: '',
                order: '',
                options: '',
            };

            this.text = "";

            $('#fieldModal').modal('hide');
        }.bind(this));
        EventBus.$on('create_modal', function (data) {
            const { campain_id, blocks, states, tab_states, type_fields, groups, widths } = data;

            this.model.campain_id = campain_id;
            this.blocks = blocks;
            this.states = states;
            this.tab_states = tab_states;
            this.type_fields = type_fields;
            this.groups = groups;
            this.widths = widths;

            this.text = "Crear"
            this.color = "success";

            $('#fieldModal').modal('show');
        }.bind(this));
        EventBus.$on('edit_modal', function (data) {
            const { field, tab_states_fields, groups_fields_edit, groups_fields_view, groups_fields_have_comment, blocks, states, tab_states, type_fields, groups, widths } = data;

            this.model.id = field.id;
            this.model.campain_id = field.campain_id;
            this.model.block_id = field.block_id;
            this.model.width_id = field.width_id;
            this.model.type_field_id = field.type_field_id;
            this.model.name = field.name;
            this.model.order = field.order;
            this.model.tab_state_ids = tab_states_fields;
            this.model.group_edit_ids = groups_fields_edit;
            this.model.group_view_ids = groups_fields_view;
            this.model.group_have_comment_ids = groups_fields_have_comment;
            this.model.required = field.required ? true : false;
            this.model.unique = field.unique ? true : false;
            this.model.bloq_mayus = field.bloq_mayus ? true : false;
            this.model.in_solds_list = field.in_solds_list ? true : false;
            this.model.in_notifications = field.in_notifications ? true : false;
            this.model.has_edit = field.has_edit ? true : false;
            this.blocks = blocks;
            this.states = states;
            this.tab_states = tab_states;
            this.type_fields = type_fields;
            this.groups = groups;
            this.widths = widths;

            this.text = "Actualizar"
            this.color = "primary";

            $('#fieldModal').modal('show');
        }.bind(this));
    },
    methods: {
        formController: function (url, event) {
            var vm = this;

            var target = $(event.target);
            var url = url;
            var fd = new FormData();

            EventBus.$emit('loading', true);

            fd.append("id", this.model.id)
            fd.append("campain_id", this.model.campain_id)
            fd.append("block_id", this.model.block_id)
            fd.append("type_field_id", this.model.type_field_id)
            fd.append("width_id", this.model.width_id)
            fd.append("name", this.model.name)
            fd.append("order", this.model.order)
            fd.append("options", this.model.options)
            fd.append("unique", this.model.unique)
            fd.append("required", this.model.required)
            fd.append("bloq_mayus", this.model.bloq_mayus)
            fd.append("in_solds_list", this.model.in_solds_list)
            fd.append("in_notifications", this.model.in_notifications)
            fd.append("has_edit", this.model.has_edit)
            fd.append("in_general_search", this.model.in_general_search)

            const group_edit_ids = [];
            const group_view_ids = [];
            const group_have_comment_ids = [];
            const tab_state_ids = [];

            for (let i = 0; i < this.model.group_edit_ids.length; i++) {
                const element = this.model.group_edit_ids[i];
                group_edit_ids.push(element.id);
            };
            for (let i = 0; i < this.model.group_view_ids.length; i++) {
                const element = this.model.group_view_ids[i];
                group_view_ids.push(element.id);
            };
            for (let i = 0; i < this.model.group_have_comment_ids.length; i++) {
                const element = this.model.group_have_comment_ids[i];
                group_have_comment_ids.push(element.id);
            };
            for (let i = 0; i < this.model.tab_state_ids.length; i++) {
                const element = this.model.tab_state_ids[i];
                tab_state_ids.push(element.id);
            };

            fd.append("group_edit_ids", JSON.stringify(group_edit_ids))
            fd.append("group_view_ids", JSON.stringify(group_view_ids))
            fd.append("group_have_comment_ids", JSON.stringify(group_have_comment_ids))
            fd.append("tab_state_ids", JSON.stringify(tab_state_ids))

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

        handleSelectTypeFieldChange: function () {
            if (this.model.type_field_id == 3 || this.model.type_field_id == 4) {
                this.viewOptions = true;
            } else {
                this.viewOptions = false;
            }
        },
    }
}

</script>