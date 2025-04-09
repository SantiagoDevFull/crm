<template>

    <div class="modal fade" id="horarioModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
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
                            <label for="name" class="form-label">Nombre:</label>
                            <input v-model="model.name" type="text" class="form-control" id="name" name="name"
                                @focus="$parent.clearErrorMsg($event)">
                            <div id="model-name-error" class="error invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="tolerancia_min" class="form-label">Tolerancia (mins):</label>
                            <div class="input-group">
                                <input v-model="model.tolerancia_min" type="text" class="form-control"
                                    id="tolerancia_min" name="tolerancia_min" @focus="$parent.clearErrorMsg($event)">
                                <div id="model-tolerancia_min-error" class="error invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-6 form-check form-switch">
                            <label class="form-check-label" for="flexSwitchCheckChecked">Pedir motivo por llegar
                                tarde</label>
                            <input v-model="model.motivo_tardanza" class="form-check-input" type="checkbox"
                                role="switch" id="flexSwitchCheckChecked">
                        </div>
                        <div class="col-md-6 form-check form-switch">
                            <label class="form-check-label" for="flexSwitchCheckChecked">Pedir motivo por salir
                                temprano</label>
                            <input v-model="model.motivo_temprano" class="form-check-input" type="checkbox"
                                role="switch" id="flexSwitchCheckChecked">
                        </div>
                        <div class="col-md-6 form-check form-switch">
                            <label class="form-check-label" for="flexSwitchCheckChecked">Restringir acceso fuera de este
                                horario</label>
                            <input v-model="model.restringir_last" class="form-check-input" type="checkbox"
                                role="switch" id="flexSwitchCheckChecked">
                        </div>
                        <div class="col-md-6 form-check form-switch">
                            <label class="form-check-label" for="flexSwitchCheckChecked">Restringir gestión fuera de
                                este horario</label>
                            <input v-model="model.restringir_gest" class="form-check-input" type="checkbox"
                                role="switch" id="flexSwitchCheckChecked">
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label badge bg-info">Día</label>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label badge bg-info">Entrada</label>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label badge bg-info">Salida</label>
                        </div>
                        <!--LUNES-->
                        <div v-for="(detail, index) in details" :key="index" class="row mb-2">
                            <div class="col-md-4">
                                <input v-model="detail.day" type="text" class="form-control" id="day" name="day" readonly @focus="$parent.clearErrorMsg($event)">
                                <div id="day-error" class="error invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <input v-model="detail.start" type="time" class="form-control" id="start" name="start" @change="synchronizeHours(detail, $event)" @focus="$parent.clearErrorMsg($event)">
                                <div :id="'details-' + index + '-start-error'" class="error invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <input v-model="detail.end" type="time" class="form-control" id="end" name="end" @change="synchronizeHours(detail, $event)" @focus="$parent.clearErrorMsg($event)">
                                <div :id="'details-' + index + '-end-error'" class="error invalid-feedback"></div>
                            </div>
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
        url_get_day: {
            type: String,
            default: ''
        }

    },
    data() {
        return {
            model: {
                id: '',
                name: '',
                tolerancia_min: false,
                motivo_tardanza: false,
                motivo_temprano: false,
                restringir_last: false,
                restringir_gest: false,
            },
            details: [
                { day: 'Lunes', start: '', end: '' },
                { day: 'Martes', start: '', end: '' },
                { day: 'Miércoles', start: '', end: '' },
                { day: 'Jueves', start: '', end: '' },
                { day: 'Viernes', start: '', end: '' },
                { day: 'Sábado', start: '', end: '' },
                { day: 'Domingo', start: '', end: '' }
            ],
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
            this.model.tolerancia_min = '';
            this.model.motivo_tardanza = false;
            this.model.motivo_temprano = false;
            this.model.restringir_last = false;
            this.model.restringir_gest = false;

            this.text = "Crear"
            this.color = "success";
            this.resetDetails();

            $('#horarioModal').modal('show');
        }.bind(this));
        EventBus.$on('edit_modal', function (horario) {

            EventBus.$emit('loading', true);

            this.model.id = horario.id;
            this.model.name = horario.name;
            this.model.tolerancia_min = horario.tolerancia_min;
            this.model.motivo_tardanza = horario.motivo_tardanza == 1 ? true : false;
            this.model.motivo_temprano = horario.motivo_temprano == 1 ? true : false;
            this.model.restringir_last = horario.restringir_last == 1 ? true : false;
            this.model.restringir_gest = horario.restringir_gest == 1 ? true : false;

            this.text = "Actualizar"
            this.color = "primary";


            axios.post(this.url_get_day, {
                id: horario.id,
            }).then(response => {
                EventBus.$emit('loading', false);
                this.resetDetails();
                this.details = response.data;
                $('#horarioModal').modal('show');
            }).catch(error => {
                console.log(error);
                console.log(error.response);
            });

        }.bind(this));
    },
    methods: {
        resetDetails() {
            this.details = [
                { day: 'Lunes', start: '', end: '' },
                { day: 'Martes', start: '', end: '' },
                { day: 'Miércoles', start: '', end: '' },
                { day: 'Jueves', start: '', end: '' },
                { day: 'Viernes', start: '', end: '' },
                { day: 'Sábado', start: '', end: '' },
                { day: 'Domingo', start: '', end: '' }
            ];
        },
        formController: function (url, event) {

            var target = $(event.target);

            EventBus.$emit('loading', true);

            axios.post(this.url, {
                model: this.model,
                details: this.details,
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

                    let c_target;

                    let parts = i.split('.');
                    if (i.startsWith('details.')) {
                        let index = parts[1];
                        let field = parts[2];
                        c_target = $("#details-" + index + '-' + field + '-error');
                    } else {
                        let field = parts[1];
                        c_target = $("#model-" + field + '-error');
                    }

                    c_target.prev().addClass('is-invalid');
                    c_target.css('display', 'block');
                    c_target.html(item);

                });
            });
        },
        synchronizeHours(detail, event) {
            const { day, start, end } = detail;

            if (day.toLowerCase() == "lunes") {
                if (start.trim()) {
                    this.details[1].start = start;
                    this.details[2].start = start;
                    this.details[3].start = start;
                    this.details[4].start = start;
                };

                if (end.trim()) {
                    this.details[1].end = end;
                    this.details[2].end = end;
                    this.details[3].end = end;
                    this.details[4].end = end;
                };
            };
        }

    }
}
</script>
