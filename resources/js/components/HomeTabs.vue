<template>
    <ul class="list-unstyled pl-0 mb-0 d-sm-flex bg-dark rounded-top">
        <li class="text-light rounded-top p-2 cursor-pointer cursor-pointer" :class="{ 'bg-white text-dark': tab_select == 0 }" @click="changeTab(0)">Mi Perfil</li>
        <li class="text-light rounded-top p-2 cursor-pointer cursor-pointer" :class="{ 'bg-white text-dark': tab_select == 1 }" @click="changeTab(1)">Mi Asistencia</li>
        <li class="text-light rounded-top p-2 cursor-pointer cursor-pointer" :class="{ 'bg-white text-dark': tab_select == 2 }" @click="changeTab(2)">Anuncios</li>
    </ul>
</template>

<script>

export default {
    props: {},
    data() {
        return {
            tab_select: 0
        }
    },
    methods: {
        changeTab(id) {
            this.tab_select = id;
            EventBus.$emit('show_data', this.tab_select);
        },
    }
}

</script>