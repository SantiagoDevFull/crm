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
                        <input v-model="model.campain_id" class="d-none" type="text" name="campain_id" id="campain_id">
                        <div class="col-md-6">
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
    },
    data() {
        return {
            model: {
                id: '',
                campain_id: '',
                name: '',
                order: '',
            },
            text: ''
        }
    },
    created() {

    },
    mounted() {
        EventBus.$on('clear_modal', function () {
            this.model.id = '';
            this.model.campain_id = '';
            this.model.name = '';
            this.model.order = '';

            this.text = "";

            $('#blockModal').modal('hide');
        }.bind(this));
        EventBus.$on('create_modal', function (campain_id) {

            this.model.campain_id = campain_id;

            this.text = "Crear"
            this.color = "success";

            $('#blockModal').modal('show');
        }.bind(this));
        EventBus.$on('edit_modal', function (block) {

            this.model.id = block.id;
            this.model.campain_id = block.campain_id;
            this.model.name = block.name;
            this.model.order = block.order;

            this.text = "Actualizar"
            this.color = "primary";

            $('#blockModal').modal('show');
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