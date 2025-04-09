<template>
    <ul class="list-unstyled pl-0 mb-0 d-sm-flex bg-dark rounded-top">
        <li v-for="tab_state in tab_states" :key="tab_state.id" class="text-light rounded-top p-2 cursor-pointer" :class=" { 'bg-white text-dark': tab_state.id == tab_state_id } " @click="changeTab(tab_state.id)">{{ tab_state.name }}</li>
    </ul>
</template>

<script>

export default {
    props: {
        campain: {},
        tab_states: [],
        url: "",
        url_list: "",
        url_delete: "",
        url_deshabilitar: "",
        url_habilitar: "",
        url_download: "",
    },
    data() {
        return {
            tab_state_id: 0,
        }
    },
    created() {
        if (this.tab_states.length) {
            EventBus.$emit('loading', true);
            const id = this.tab_states[0].id;

            this.tab_state_id = id;

            const fd = new FormData();

            fd.append('campain_id', this.campain.id);
            fd.append('tab_state_id', id);
            
            axios.post(this.url_list, fd, {
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded',
                }
            }).then(response => {
                const { forms, fields } = response.data;

                EventBus.$emit('loading', false);
                EventBus.$emit('show_table', forms, fields, id, this.campain.id);

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
        }
    },
    mounted() {},
    methods: {
        changeTab(id) {
            EventBus.$emit('loading', true);

            this.tab_state_id = id;

            const fd = new FormData();

            fd.append('campain_id', this.campain.id);
            fd.append('tab_state_id', id);

            axios.post(this.url_list, fd, {
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded',
                }
            }).then(response => {
                const { forms, fields } = response.data;

                this.$parent.alertMsg(response.data);
                EventBus.$emit('loading', false);
                // EventBus.$emit('show_table', forms, fields, id, this.campain.id);
                EventBus.$emit('refresh_table', id, this.campain.id);

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
        }
    }
}

</script>