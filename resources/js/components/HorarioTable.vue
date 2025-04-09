<template>
    <div class="container mt-2 p-5 border rounded bg-white">
        <div>
            <button class="btn btn-success" @click.prevent="create()" title="Crear">Crear Horario</button>
        </div>
        <hr>
        <table id="example" class="table table-striped text-center" style="width:100%">
            <thead>
                <tr>
                    <th class="col-2 text-center">NOMBRE</th>
                    <th class="col-1 text-center">HORARIO</th>
                    <th class="col-1 text-center">TOLERANCIA</th>
                    <th class="col-2 text-center">ESTADO</th>
                    <th class="col-2 text-center">OPCIONES</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="horario in horarios" :key="horario.id">
                    <td @dblclick.prevent="edit(horario)">{{ horario.name }}</td>
                    <td @dblclick.prevent="edit(horario)">{{ joinDays(horario.days) }}</td>
                    <td class="text-center" @dblclick.prevent="edit(horario)">{{ horario.tolerancia_min }}</td>
                    <td class="text-center" @dblclick.prevent="edit(horario)">{{ horario.state }}</td>
                    <td>
                        <button class="btn btn-primary" @click.prevent="edit(horario)" title="Editar">
                            <i class="fa-solid fa-pen-to-square fa-sm" style="color: #ffffff;"></i>
                        </button>
                        <button class="btn btn-danger" @click.prevent="deleteHorario(horario)" title="Eliminar">
                            <i class="fa-solid fa-trash-can fa-sm" style="color: #ffffff;"></i>
                        </button>

                    </td>
                </tr>
            </tbody>

        </table>
    </div>
</template>

<script>

export default {
    props: {
        horarios: {
            type: Array,
            default: ''
        },
        url_delete: {
            type: String,
            default: '',
        }
    },
    mounted() {},
    methods: {
        create() {
            EventBus.$emit('create_modal');
        },
        edit(horario) {
            EventBus.$emit('edit_modal', horario);
        },
        deleteHorario(horario) {
            Swal.fire({
                title: '¡Cuidado!',
                text: '¿Seguro que desea eliminar el horario ' + horario.name + '?',
                icon: "warning",
                heightAuto: false,
                showCancelButton: true,
                confirmButtonText: 'Sí',
                cancelButtonText: 'No'
            }).then(result => {
                EventBus.$emit('loading', true);

                if (result.value) {
                    axios.post(this.url_delete, {
                        id: horario.id,
                    }).then(response => {
                        EventBus.$emit('loading', false);
                        this.$parent.alertMsg(response.data);
                    }).catch(error => {
                        console.log(error);
                        console.log(error.response);
                    });
                } else if (result.dismiss == Swal.DismissReason.cancel) {
                    EventBus.$emit('loading', false);
                }
            });
        },
        joinDays(days) {
            let result = [];
            let currentGroup = { days: [], inicio: "", final: "" };

            days.forEach((dayObj, index) => {
                const dayName = dayObj.day;

                const { inicio, final } = dayObj;

                if (currentGroup.days.length === 0) {
                    currentGroup = { days: [dayName], inicio, final };
                } else if (currentGroup.inicio === inicio && currentGroup.final === final) {
                    currentGroup.days.push(dayName);
                } else {
                    result.push(currentGroup);
                    currentGroup = { days: [dayName], inicio, final };
                }

                if (index === days.length - 1) {
                    result.push(currentGroup);
                }
            });

            return result.map(group => {
                const daysFormatted = group.days.length > 1
                    ? `${group.days[0].slice(0, 3)}-${group.days[group.days.length - 1].slice(0, 3)}`
                    : group.days[0].slice(0, 3);
                return `${daysFormatted}: ${this.normalizeHour(group.inicio)}-${this.normalizeHour(group.final)}`;
            }).join(", ");
        },
        normalizeHour(hour) {
            const array = hour.split(":");

            let h = +array[0];
            let m = array[1];

            if (h > 12) h = h - 12;

            return h + ":" + m;
        },
    }
}
</script>
