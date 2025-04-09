<template>
    <div class="modal fade" id="blockModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
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

                        <div class="col-md-12 mb-2">
                            <label for="name" class="form-label">Nombre:</label>
                            <input v-model="model.name" type="text" class="form-control" id="name" name="name"
                                @focus="$parent.clearErrorMsg($event)">
                            <div id="name-error" class="error invalid-feedback"></div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label for="country_id" class="form-label">País:</label>
                            <select class="form-select" v-model="model.country_id" name="country_id" id="country_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="country in countries" :value="country.id" :key="country.id">{{ country.name }}</option>
                            </select>
                            <div id="country_id-error" class="error invalid-feedback"></div>
                        </div>

                        <div class="col-md-6 mb-2 flex flex-column">
                            <label class="form-check-label mb-2" for="geolocation">Habilitar geolocalización</label>
                            <switches v-model="model.geolocation"  name="geolocation" id="geolocation" class="form-check-input"></switches>
                        </div>

                        <div class="col-md-6 mb-2 flex flex-column">
                            <label class="form-check-label mb-2" for="view_products">Ver sección de productos</label>
                            <switches v-model="model.view_products"  name="view_products" id="view_products" class="form-check-input"></switches>
                        </div>

                        <div class="col-md-6 mb-2 flex flex-column">
                            <label class="form-check-label mb-2" for="back_state">Permitir retroceder estado</label>
                            <switches v-model="model.back_state"  name="back_state" id="back_state" class="form-check-input"></switches>
                        </div>

                        <div class="col-md-6 mb-2 flex flex-column">
                            <label class="form-check-label mb-2" for="sold_new_window">Abrir venta nueva en otra pestaña</label>
                            <switches v-model="model.sold_new_window"  name="sold_new_window" id="sold_new_window" class="form-check-input"></switches>
                        </div>

                        <div class="col-md-6 mb-2 flex flex-column">
                            <label class="form-check-label mb-2" for="sold_exists_window">Abrir venta existente en otra pestaña</label>
                            <switches v-model="model.sold_exists_window"  name="sold_exists_window" id="sold_exists_window" class="form-check-input"></switches>
                        </div>

                        <div class="col-md-6 mb-2 flex flex-column">
                            <label class="form-check-label mb-2" for="sold_notes">Permitir crear notas en las ventas</label>
                            <switches v-model="model.sold_notes"  name="sold_notes" id="sold_notes" class="form-check-input"></switches>
                        </div>

                        <div class="col-md-6 mb-2 flex flex-column">
                            <label class="form-check-label mb-2" for="list_sold_notes">Añadir notas en el listado de ventas</label>
                            <switches v-model="model.list_sold_notes"  name="list_sold_notes" id="list_sold_notes" class="form-check-input"></switches>
                        </div>

                        <div class="col-md-6 mb-2 flex flex-column">
                            <label class="form-check-label mb-2" for="change_state_list_sold">Permitir cambiar de estado en el listado de ventas</label>
                            <switches v-model="model.change_state_list_sold"  name="change_state_list_sold" id="change_state_list_sold" class="form-check-input"></switches>
                        </div>

                        <div class="col-md-6 mb-2 flex flex-column">
                            <label class="form-check-label mb-2" for="option_duplicate_sold">Habilitar opción "Duplicar venta"</label>
                            <switches v-model="model.option_duplicate_sold"  name="option_duplicate_sold" id="option_duplicate_sold" class="form-check-input"></switches>
                        </div>

                        <div class="col-md-12 mb-2 flex flex-column">
                            <label class="form-check-label mb-2" for="show_history_sold">Permitir que los supervisores visualicen el histórico de ventas de sus agentes (incluso con otros supervisores)</label>
                            <switches v-model="model.show_history_sold"  name="show_history_sold" id="show_history_sold" class="form-check-input"></switches>
                        </div>

                        <div class="col-md-12 mb-2">
                            <label for="range_date_id" class="form-label">Rango de fechas predeterminado en el listado de ventas:</label>
                            <select class="form-select" v-model="model.range_date_id" name="range_date_id" id="range_date_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="range_date in range_dates" :value="range_date.id" :key="range_date.id">{{ range_date.name }}</option>
                            </select>
                            <div id="range_date_id-error" class="error invalid-feedback"></div>
                        </div>

                        <div class="col-md-12">
                            <label for="group_export_sold_ids" class="form-label">Pueden exportar el listado de ventas:</label>
                            <multiselect v-model="model.group_export_sold_ids" :options="groups" name="group_export_sold_ids" label="name" track-by="id" :multiple="true"></multiselect>
                            <div id="group_export_sold_ids-error" class="error invalid-feedback"></div>
                        </div>

                        <div class="col-md-12">
                            <label for="group_view_edition_ids" class="form-label">Pueden ver quién(es) ha(n) abierto una venta en modo edición:</label>
                            <multiselect v-model="model.group_view_edition_ids" :options="groups" name="group_view_edition_ids" label="name" track-by="id" :multiple="true"></multiselect>
                            <div id="group_view_edition_ids-error" class="error invalid-feedback"></div>
                        </div>

                        <div class="col-md-12">
                            <label for="group_audit_data_sold_ids" class="form-label">Pueden auditar los datos de trazabilidad de las ventas:</label>
                            <multiselect v-model="model.group_audit_data_sold_ids" :options="groups" name="group_audit_data_sold_ids" label="name" track-by="id" :multiple="true"></multiselect>
                            <div id="group_audit_data_sold_ids-error" class="error invalid-feedback"></div>
                        </div>

                        <div class="col-md-12">
                            <label for="group_upload_massive_sold_ids" class="form-label">Pueden cargar masivamente ventas:</label>
                            <multiselect v-model="model.group_upload_massive_sold_ids" :options="groups" name="group_upload_massive_sold_ids" label="name" track-by="id" :multiple="true"></multiselect>
                            <div id="group_upload_massive_sold_ids-error" class="error invalid-feedback"></div>
                        </div>

                        <div class="col-md-12">
                            <label for="group_authorizate_duplicate_sold_ids" class="form-label">Pueden autorizar ventas con valores duplicados:</label>
                            <multiselect v-model="model.group_authorizate_duplicate_sold_ids" :options="groups" name="group_authorizate_duplicate_sold_ids" label="name" track-by="id" :multiple="true"></multiselect>
                            <div id="group_authorizate_duplicate_sold_ids-error" class="error invalid-feedback"></div>
                        </div>

                        <div class="col-md-12">
                            <label for="description" class="form-label">Descripción:</label>
                            <textarea v-model="model.description" class="form-control" id="description" name="description" @focus="$parent.clearErrorMsg($event)"></textarea>
                            <div id="description-error" class="error invalid-feedback"></div>
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
import Switches from 'vue-switches';

export default {
    components: { Multiselect, Switches },
    props: {
        url: {
            type: String,
            default: ''
        },
        countries: {
            type: Array,
            default: []
        },
        range_dates: {
            type: Array,
            default: []
        },
        groups: {
            type: Array,
            default: []
        },
    },
    data() {
        return {
            model: {
                id: '',
                name: '',
                description: '',
                country_id: '',
                range_date_id: '',
                geolocation: false,
                view_products: false,
                back_state: false,
                sold_new_window: false,
                sold_exists_window: false,
                sold_notes: false,
                list_sold_notes: false,
                change_state_list_sold: false,
                option_duplicate_sold: false,
                show_history_sold: false,
                group_export_sold_ids: [],
                group_view_edition_ids: [],
                group_upload_massive_sold_ids: [],
                group_authorizate_duplicate_sold_ids: [],
                group_audit_data_sold_ids: [],
            },
            text: '',
            color: ''
        }
    },
    created() {

    },
    mounted() {
        EventBus.$on('clear_modal', function () {
            this.model = {
                id: '',
                name: '',
                country_id: '',
                range_date_id: '',
                description: '',
                geolocation: false,
                view_products: false,
                back_state: false,
                sold_new_window: false,
                sold_exists_window: false,
                sold_notes: false,
                list_sold_notes: false,
                change_state_list_sold: false,
                option_duplicate_sold: false,
                show_history_sold: false,
                group_export_sold_ids: [],
                group_view_edition_ids: [],
                group_upload_massive_sold_ids: [],
                group_authorizate_duplicate_sold_ids: [],
                group_audit_data_sold_ids: [],
            };

            this.text = "";

            $('#blockModal').modal('hide');
        }.bind(this));
        EventBus.$on('create_modal', function () {

            this.text = "Crear"
            this.color = "success";

            $('#blockModal').modal('show');
        }.bind(this));
        EventBus.$on('edit_modal', function (data) {

            const { campain, groups_campains_export_sold, groups_campains_view_edition, groups_campains_audit_data_sold, groups_campains_authorizate_duplicate_sold, groups_campains_upload_massive_sold } = data;

            const { id, name, description, country_id, geolocation, view_products, back_state, sold_new_window, sold_exists_window, sold_notes, list_sold_notes, change_state_list_sold, option_duplicate_sold, group_view_edition_ids, range_date_id } = campain;

            this.model.id = id;
            this.model.name = name;
            this.model.description = description;
            this.model.country_id = country_id;
            this.model.geolocation = geolocation;
            this.model.view_products = view_products;
            this.model.back_state = back_state;
            this.model.sold_new_window = sold_new_window;
            this.model.sold_exists_window = sold_exists_window;
            this.model.sold_notes = sold_notes;
            this.model.list_sold_notes = list_sold_notes;
            this.model.change_state_list_sold = change_state_list_sold;
            this.model.option_duplicate_sold = option_duplicate_sold;
            this.model.range_date_id = range_date_id;
            this.model.group_export_sold_ids = groups_campains_export_sold;
            this.model.group_view_edition_ids = groups_campains_view_edition;
            this.model.group_upload_massive_sold_ids = groups_campains_upload_massive_sold;
            this.model.group_authorizate_duplicate_sold_ids = groups_campains_authorizate_duplicate_sold;
            this.model.group_audit_data_sold_ids = groups_campains_audit_data_sold;

            this.text = "Actualizar"
            this.color = "primary";

            $('#blockModal').modal('show');
        }.bind(this));
    },
    methods: {
        formController: function (url, event) {
            var target = $(event.target);
            var url = url;
            var fd = new FormData();

            EventBus.$emit('loading', true);

            fd.append("id", this.model.id)
            fd.append("name", this.model.name)
            fd.append("description", this.model.description)
            fd.append("country_id", this.model.country_id)
            fd.append("range_date_id", this.model.range_date_id)
            fd.append("geolocation", this.model.geolocation)
            fd.append("view_products", this.model.view_products)
            fd.append("back_state", this.model.back_state)
            fd.append("sold_new_window", this.model.sold_new_window)
            fd.append("sold_exists_window", this.model.sold_exists_window)
            fd.append("sold_notes", this.model.sold_notes)
            fd.append("list_sold_notes", this.model.list_sold_notes)
            fd.append("change_state_list_sold", this.model.change_state_list_sold)
            fd.append("option_duplicate_sold", this.model.option_duplicate_sold)
            fd.append("show_history_sold", this.model.show_history_sold)

            const group_export_sold_ids = [];
            const group_view_edition_ids = [];
            const group_upload_massive_sold_ids = [];
            const group_authorizate_duplicate_sold_ids = [];
            const group_audit_data_sold_ids = [];

            for (let i = 0; i < this.model.group_export_sold_ids.length; i++) {
                const element = this.model.group_export_sold_ids[i];
                group_export_sold_ids.push(element.id);
            };
            for (let i = 0; i < this.model.group_view_edition_ids.length; i++) {
                const element = this.model.group_view_edition_ids[i];
                group_view_edition_ids.push(element.id);
            };
            for (let i = 0; i < this.model.group_upload_massive_sold_ids.length; i++) {
                const element = this.model.group_upload_massive_sold_ids[i];
                group_upload_massive_sold_ids.push(element.id);
            };
            for (let i = 0; i < this.model.group_authorizate_duplicate_sold_ids.length; i++) {
                const element = this.model.group_authorizate_duplicate_sold_ids[i];
                group_authorizate_duplicate_sold_ids.push(element.id);
            };
            for (let i = 0; i < this.model.group_audit_data_sold_ids.length; i++) {
                const element = this.model.group_audit_data_sold_ids[i];
                group_audit_data_sold_ids.push(element.id);
            };

            fd.append("group_export_sold_ids", JSON.stringify(group_export_sold_ids))
            fd.append("group_view_edition_ids", JSON.stringify(group_view_edition_ids))
            fd.append("group_upload_massive_sold_ids", JSON.stringify(group_upload_massive_sold_ids))
            fd.append("group_authorizate_duplicate_sold_ids", JSON.stringify(group_authorizate_duplicate_sold_ids))
            fd.append("group_audit_data_sold_ids", JSON.stringify(group_audit_data_sold_ids))

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