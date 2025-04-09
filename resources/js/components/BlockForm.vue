<template>
    <div class="container mt-2 p-5 bg-white border rounded">
        <form class="row g-3" @submit.prevent="formController(url, $event)">
            <div class="col-md-6">
                <label for="campain_id" class="form-label">Campaña:</label>
                <select class="form-select" v-model="model.campain_id" name="campain_id" id="campain_id" @focus="$parent.clearErrorMsg($event)">
                    <option value="" selected disabled>Seleccionar</option>
                        <option v-for="campain in campains" :value="campain.id" :key="campain.id">{{ campain.name }}</option>
                </select>
                <div id="campain_id-error" class="error invalid-feedback"></div>
            </div>
            <div class="col-md-12">
                <label for="sufijo" class="form-label"></label>
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </form>

    </div>
</template>

<script>

export default {
    props: {
        campain_id: {
            type: Number,
            default: 0
        },
        url: {
            type: String,
            default: ''
        },
        campains: {
            type: Array,
            default: []
        }
    },
    data() {
        return {
            model: {
                campain_id: '',
            },
        }
    },
    created() {
        if (this.campain_id) {
            this.model.campain_id = `${this.campain_id}`;

            var fd = new FormData();

            fd.append('campain_id', `${this.campain_id}`);

            EventBus.$emit('loading', true);
            axios.post(this.url, fd, {
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded',
                }
            }).then(response => {
                EventBus.$emit('loading', false);
                EventBus.$emit('show_table', response.data, `${this.campain_id}`);
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
        };
    },
    mounted() {},
    methods: {
        formController: function (url, event) {

            var vm = this;

            var target = $(event.target);
            var url = url;
            var fd = new FormData(event.target);

            target.find('input').prop('disabled', true);
            target.find('select').prop('disabled', true);
            target.find('button').prop('disabled', true);

            EventBus.$emit('loading', true);
            axios.post(url, fd, {
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded',
                }
            }).then(response => {
                EventBus.$emit('loading', false);
                EventBus.$emit('show_table', response.data, vm.model.campain_id);

                target.find('input').prop('disabled', false);
                target.find('select').prop('disabled', false);
                target.find('button').prop('disabled', false);
            }).catch(error => {
                EventBus.$emit('loading', false);
                target.find('input').prop('disabled', false);
                target.find('select').prop('disabled', false);
                target.find('button').prop('disabled', false);

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