<template>
    <div>
        <div class="d-flex flex-column justify-items-center py-4 align-items-center" :class="{ 'd-none': tab_select !== 0 }">
            <p class="mx-auto text-center border-bottom" style="font-size: 32px; font-weight: 700; width: 90%;">{{ user.name }}</p>
            <div class="mx-auto px-2 text-start border-bottom d-flex justify-items-center align-items-center gap-4" style="width: 90%;">
                <p style="font-size: 16px;">Grupo:</p>
                <p style="font-size: 16px;">{{ group.name }}</p>
            </div>
            <div class="mx-auto px-2 text-start border-bottom d-flex justify-items-center align-items-center gap-4" style="width: 90%;">
                <p style="font-size: 16px;">Usuario:</p>
                <p style="font-size: 16px;">{{ user.user }}</p>
            </div>
            <div class="mx-auto px-2 text-start border-bottom d-flex justify-items-center align-items-center gap-4" style="width: 90%;">
                <p style="font-size: 16px;">Teléfono:</p>
                <p style="font-size: 16px;">{{ user.telefono }}</p>
            </div>
            <div class="mx-auto px-2 text-start border-bottom d-flex justify-items-center align-items-center gap-4" style="width: 90%;">
                <p style="font-size: 16px;">Genero:</p>
                <p style="font-size: 16px;">{{ user.genero }}</p>
            </div>
            <div class="mx-auto px-2 text-start border-bottom d-flex justify-items-center align-items-center gap-4" style="width: 90%;">
                <p style="font-size: 16px;">Fecha de Nacimiento:</p>
                <p style="font-size: 16px;">{{ user.fecha_naci }}</p>
            </div>
        </div>
        <div class="d-flex flex-column justify-items-center py-4 align-items-center" :class="{ 'd-none': tab_select !== 1 }">
            <div class="calendar-container">
                <div class="month-navigation">
                    <button @click="prevMonth">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <span class="date-name">{{ currentMonthName }} {{ currentYear }}</span>
                    <button @click="nextMonth">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
                <div class="week-days">
                    <div v-for="(day, index) in daysOfWeek" :key="'week' + day" class="week-day">{{ day }}</div>
                </div>
                <div class="days-grid">
                    <div v-for="(day, index) in blankDays" :key="'empty' + index" class="empty-day"></div>
                    <div v-for="(day, index) in daysInMonth" :key="'day' + day.day" class="day">
                        <p class="w-100 text-end mb-0">{{ day.day }}</p>
                        <div v-for="login in day.logins" class="login">
                            <i v-if="login.login" class="fa-solid fa-right-to-bracket"></i>
                            <i v-if="!login.login" class="fa-solid fa-right-from-bracket"></i>
                            <p class="mb-0">{{ new Date(login.created_at).toLocaleTimeString() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex flex-column justify-items-center py-4 align-items-center" :class="{ 'd-none': tab_select !== 2 }">
            <v-expansion-panels v-model="active">
                <v-expansion-panel v-for="advertisement in advertisements" :key="advertisement.id">
                    <v-expansion-panel-header class="title-advertisement">{{ advertisement.title }}</v-expansion-panel-header>
                    <v-expansion-panel-content class="content-advertisement">
                        <div class="creator-content">
                            <img src="https://sicacentercrm.com/system/Assets/app-files/user-photos/1%20926973f2-c6d9-428b-a3ec-7de1ff068e77.jpg" class="created-img" />
                            <p>
                                Creado por: {{ advertisement.created_at_user }}
                                <br />
                                Editado por: {{ advertisement.updated_at_user }}
                                <br />
                                Fecha de Creación: {{ new Date(advertisement.created_at).toLocaleDateString() }}
                                <br />
                                Fecha de Edición: {{ new Date(advertisement.updated_at).toLocaleDateString() }}
                            </p>
                        </div>
                        <div v-html="advertisement.text"></div>
                        <div v-if="advertisement.upload_id" class="w-100 d-flex justify-content-center align-items-center">
                            <button class="btn btn-primary" @click="downloadFile(advertisement.upload_id)">
                                <i class="fa-solid fa-download"></i> Descargar adjunto
                            </button>
                        </div>
                    </v-expansion-panel-content>
                </v-expansion-panel>
            </v-expansion-panels>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        user: {
            type: Object,
            default: {},
        },
        group: {
            type: Object,
            default: {},
        },
        advertisements: {
            type: Array,
            default: [],
        },
        logins: {
            type: Array,
            default: [],
        },
        url_download: {
            type: String,
            default: "",
        },
    },
    data() {
        return {
            tab_select: 0,
            active: 0,
            currentYear: new Date().getFullYear(),
            currentMonth: new Date().getMonth(),
            daysOfWeek: ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sabado", "Domingo"],
        }
    },
    computed: {
        currentMonthName() {
            return new Date(this.currentYear, this.currentMonth).toLocaleString('default', { month: 'long' });
        },
        daysInMonth() {
            const days = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();

            const arrayDays = Array.from({ length: days }, (v, i) => i + 1);
            const arrayData = arrayDays.map(i => {

                const date = i + "/" + (this.currentMonth + 1) + "/" + this.currentYear;

                const logins = this.logins.filter(e => new Date(e.created_at).toLocaleDateString() == date);

                return ({
                    day: i,
                    date,
                    logins,
                })
            });

            return arrayData;
        },
        blankDays() {
            const firstDay = new Date(this.currentYear, this.currentMonth, 1).getDay();
            return Array.from({ length: firstDay === 0 ? 6 : firstDay - 1 });
        }
    },
    created() {
        this.advertisements = this.advertisements.reverse();
    },
    mounted() {
        EventBus.$on('show_data', function(tab_select) {
            this.tab_select = tab_select;
        }.bind(this))
    },
    methods: {
        nextMonth() {
            if (this.currentMonth === 11) {
                this.currentMonth = 0;
                this.currentYear++;
            } else {
                this.currentMonth++;
            }
        },
        prevMonth() {
            if (this.currentMonth === 0) {
                this.currentMonth = 11;
                this.currentYear--;
            } else {
                this.currentMonth--;
            }
        },
        async downloadFile(id) {
            EventBus.$emit('loading', true);

            try {
                const response = await axios({
                    url: this.url_download.replace('__ID__', id),
                    method: 'GET',
                    responseType: 'blob'
                });

                let fileName = 'archivo.pdf';

                const contentDisposition = response.headers['content-disposition'];

                if (contentDisposition) {
                    const fileArray = contentDisposition.split(";")

                    if (fileArray.length > 1) {
                        const name = fileArray[1].replace("filename=", "");

                        if (name) fileName = name;
                    };
                };

                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', fileName);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                EventBus.$emit('loading', false);
            } catch (error) {
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
            }
        }
    }
}

</script>

<style scoped>
.title-advertisement {
    background-color: #212529;
    color: #fff;
}
.content-advertisement {
    padding-top: 16px;
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.creator-content {
    display: flex;
    gap: 16px;
    align-items: center;
    justify-content: start;
}
.created-img {
    width: 100px;
    height: auto;
    object-fit: cover;
}

.login {
    width: 100%;
    display: flex;
    align-items: center;
    justify-items: start;
    gap: 8px;
    padding: 2px 4px;
    background-color: #212529;
    color: #fff;
    font-size: 10px;
    margin-bottom: 8px;
}

.calendar-container {
    width: 100%;
    margin: 0 auto;
}
.month-navigation {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    text-align: center;
    margin-bottom: 10px;
}
.week-days, .days-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    text-align: center;
}
.day, .empty-day, .week-day {
    padding: 10px;
    border: 1px solid #ddd;
}
.day {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: end;
}
.date-name {
    text-transform: uppercase;
    font-weight: 700;
    font-size: 16px;
}
</style>