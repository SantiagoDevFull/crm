<template>
    <div class="container mt-2 p-5 border rounded bg-white">
      <div class="w-100 d-grid grid-cols-3 gap-4">
        <div class="w-100 h-auto" v-for="route in rutas">
          <a :href="route.url" class="w-100 h-auto">
            <img class="w-100 h-auto" :src="route.img" alt="">
          </a>
        </div>
      </div>
    </div>
</template>

<script>

export default {
    props: {
        rutas: {
            type: Array,
            default: [],
        }
    },
    data() {
        return {}
    }
}

</script>