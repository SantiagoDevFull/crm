<template>

    <div class="modal fade" id="userGroupModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
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
                            <label for="company_id" class="form-label">Compañía:</label>
                            <select class="form-select" v-model="model.company_id" name="company_id" id="company_id"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="company in companies" :value="company.id" :key="company.id">{{
                                    company.name }}</option>
                            </select>
                            <div id="company_id-error" class="error invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nombre:</label>
                            <input v-model="model.name" type="text" class="form-control" id="name" name="name"
                                @focus="$parent.clearErrorMsg($event)">
                            <div id="name-error" class="error invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="ip" class="form-label">IP:</label>
                            <input v-model="model.ip" type="text" class="form-control" id="ip" name="ip"
                                @focus="$parent.clearErrorMsg($event)">
                            <div id="ip-error" class="error invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="horario_id" class="form-label">Horario:</label>
                            <select class="form-select" v-model="model.horario_id" name="horario_id" id="horario_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="hour in hours" :value="hour.id" :key="hour.id">{{ hour.name }}</option>
                            </select>
                            <div id="horario_id-error" class="error invalid-feedback"></div>
                        </div>
                        <div class="col-md-12">
                            <label for="ip" class="form-label">Permisos:</label>
                            <liquor-tree ref="permissions" :data="treeData" :options="{ checkbox: true }" :checkbox="true" @input="onInputChange"></liquor-tree>
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
import LiquorTree from 'liquor-tree';


export default {
    components: { LiquorTree },
    props: {
        url: {
            type: String,
            default: ''
        },
        companies: {
            type: Array,
            default: ''
        },
        hours: {
            type: Array,
            default: ''
        }
    },
    data() {
        return {
            model: {
                id: '',
                company_id: '',
                name: '',
                ip: '',
                horario_id: '',
                permissions: {
                    enterprise_configuration: [],
                    presence_configuration: [],
                    administration_configuration: [],
                    collaborative: [],
                    campaign_configuration: []
                }
            },
            text: '',
            color: '',
            treeData: [
                {
                    id: 'enterprise_configuration',
                    text: 'Configuración de Empresa',
                    children: [
                        {
                            id: 'enterprise_configuration.my_enterprise',
                            text: 'Mi Empresa',
                        },
                    ]
                },
                {
                    id: 'presence_configuration',
                    text: 'Configuración de Asistencia',
                    children: [
                        {
                            id: 'presence_configuration.hours',
                            text: 'Horarios',
                        },
                        {
                            id: 'presence_configuration.disconnection',
                            text: 'Tipos de Desconexión',
                        },
                        {
                            id: 'presence_configuration.sedes',
                            text: 'Sedes',
                        },
                    ]
                },
                {
                    id: 'administration_configuration',
                    text: 'Administracion de Usuarios',
                    children: [
                        {
                            id: 'administration_configuration.user_groups',
                            text: 'Grupos de Usuarios',
                        },
                        {
                            id: 'administration_configuration.users',
                            text: 'Usuarios',
                        },
                    ]
                },
                {
                    id: 'collaborative',
                    text: 'Colaborativo',
                    children: [
                        {
                            id: 'collaborative.advertisements',
                            text: 'Anuncios',
                        },
                        {
                            id: 'collaborative.popups_welcome',
                            text: 'Popups de Bienvenida',
                        },
                    ]
                },
                {
                    id: 'campaign_configuration',
                    text: 'Configuración de Campañas',
                    children: [
                        {
                            id: 'campaign_configuration.campaign',
                            text: 'Campañas',
                        },
                        {
                            id: 'campaign_configuration.tab_states',
                            text: 'Pestaña de Estado',
                        },
                        {
                            id: 'campaign_configuration.states',
                            text: 'Estados',
                        },
                        {
                            id: 'campaign_configuration.blocks',
                            text: 'Bloque de Campos',
                        },
                        {
                            id: 'campaign_configuration.fields',
                            text: ' Campos',
                        },
                    ]
                },
            ],
        }
    },
    created() {

    },
    mounted() {
        EventBus.$on('create_modal', function () {

            this.model.id = '';
            this.model.company_id = '';
            this.model.name = '';
            this.model.ip = '';
            this.model.hour_id = '';

            this.text = "Crear"
            this.color = "success";

            $('#userGroupModal').modal('show');
        }.bind(this));

        EventBus.$on('edit_modal', function (group) {

            this.model.id = group.id;
            this.model.company_id = group.company_id;
            this.model.name = group.name;
            this.model.ip = group.ip;
            this.model.horario_id = group.horario_id;

            if (group.permissions) {
                const parse = JSON.parse(group.permissions);

                this.model.permissions = parse;

                for (const key in parse) {
                    const element = parse[key];

                    for (const k in this.$refs.permissions.tree.model) {
                        const model = this.$refs.permissions.tree.model[k];

                        if (element.length) {
                            if (model.id == key) {
                                this.$set(model.states, 'checked', true);

                                for (let i = 0; i < element.length; i++) {
                                    const el = element[i];

                                    for (const ke in model.children) {
                                        if (model.children[ke].id == (key + "." + el)) {
                                            this.$set(model.children[ke].states, 'checked', true);

                                            if (!this.$refs.permissions.tree.checkedNodes.includes(model.children[ke])) {
                                                this.$refs.permissions.tree.checkedNodes.push(model.children[ke]);
                                            };
                                        };
                                    };
                                }
                            };
                        };

                        if (!this.$refs.permissions.tree.checkedNodes.includes(model)) {
                            this.$refs.permissions.tree.checkedNodes.push(model);
                        };
                        // const lengthNodeChecked = this.$refs.permissions.tree.checkedNodes.length;

                        // if (lengthNodeChecked) {
                        //     this.$refs.permissions.tree.checkedNodes[lengthNodeChecked + 1] = model;
                        // } else {
                        //     this.$refs.permissions.tree.checkedNodes[0] = model;
                        // };
                    };
                };
            };

            this.text = "Actualizar"
            this.color = "primary";

            $('#userGroupModal').modal('show');
        }.bind(this));
    },
    methods: {
        formController: function (url, event) {
            var vm = this;

            var target = $(event.target);
            var url = url;
            var fd = new FormData(event.target);

            const permissions = JSON.stringify(this.model.permissions);

            fd.append("permissions", permissions);

            EventBus.$emit('loading', true);

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
        onInputChange() {
            const checked = this.$refs.permissions.tree.checkedNodes;
            const { length } = checked;
            const permissions = {
                enterprise_configuration: [],
                presence_configuration: [],
                administration_configuration: [],
                collaborative: [],
                campaign_configuration: []
            };

            for (let i = 0; i < length; i++) {
                const element = checked[i];
                const { id } = element;

                const arrayId = id.split(".");

                if (arrayId.length == 2) {
                    permissions[arrayId[0]].push(arrayId[1]);
                };

                permissions[arrayId[0]] = permissions[arrayId[0]].filter(this.onlyUnique);
            };

            this.model.permissions = permissions;
        },
        onlyUnique(value, index, self) { 
            return self.indexOf(value) === index;
        },
    }
}
</script>
