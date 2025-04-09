<template>
    <div class="container mt-2 p-5 bg-white border rounded">
        <form class="row g-3" @submit.prevent="formController(url, $event)">
            <input v-model="model.id" type="hidden" name="id" id="id">
            <div class="col-md-4">
                <label for="name" class="form-label">Empresa:</label>
                <input v-model="model.name" type="text" class="form-control" id="name" name="name"
                    @focus="$parent.clearErrorMsg($event)">
                <div id="name-error" class="error invalid-feedback"></div>
            </div>
            <div class="col-md-4">
                <label for="name" class="form-label">Nombre corto:</label>
                <input v-model="model.short_name" type="text" class="form-control" id="short_name" name="short_name"
                    @focus="$parent.clearErrorMsg($event)">
                <div id="short_name-error" class="error invalid-feedback"></div>
            </div>
            <div class="col-md-4">
                <label for="name" class="form-label">N° Documento:</label>
                <input v-model="model.document" type="text" class="form-control" id="document" name="document"
                    @focus="$parent.clearErrorMsg($event)">
                <div id="document-error" class="error invalid-feedback"></div>
            </div>
            <div class="col-md-4">
                <label for="pais" class="form-label">País:</label>
                <select class="form-select" v-model="model.pais" name="pais" id="pais"
                    @focus="$parent.clearErrorMsg($event)">
                    <option value="" selected disabled>Seleccionar</option>
                    <option value="Bolivia">Bolivia</option>
                    <option value="Chile">Chile</option>
                    <option value="Ecuador">Ecuador</option>
                    <option value="España">España</option>
                    <option value="Global">Global</option>
                    <option value="México">México</option>
                    <option value="Perú">Perú</option>
                    <option value="Venezuela">Venezuela</option>
                </select>
                <div id="pais-error" class="error invalid-feedback"></div>
            </div>
            <div class="col-md-4">
                <label for="contact" class="form-label">Contacto:</label>
                <input v-model="model.contact" type="text" class="form-control" id="contact" name="contact"
                    @focus="$parent.clearErrorMsg($event)">
                <div id="contact-error" class="error invalid-feedback"></div>
            </div>
            <div class="col-md-4">
                <label for="asist_type" class="form-label">Tipo de asistencia:</label>
                <select class="form-select" v-model="model.asist_type" name="asist_type" id="asist_type"
                    @focus="$parent.clearErrorMsg($event)">
                    <option value="" selected disabled>Seleccionar</option>
                    <option value="Login/Logout al Sistema">Login/Logout al Sistema</option>
                    <option value="Huella dactilar">Huella dactilar</option>
                </select>
                <div id="asist_type-error" class="error invalid-feedback"></div>
            </div>
            <div class="col-md-4">
                <label for="sufijo" class="form-label">Sufijo de usuario:</label>
                <input v-model="model.sufijo" type="text" class="form-control" id="sufijo" name="sufijo"
                    placeholder="@example.com" @focus="$parent.clearErrorMsg($event)">
                <div id="sufijo-error" class="error invalid-feedback"></div>
            </div>

            <div class="col-md-4">
                <label for="menu_color" class="form-label">Color del Menú:</label>
                <input v-model="model.menu_color" type="color" class="form-control" id="menu_color" name="menu_color"
                    @focus="$parent.clearErrorMsg($event)">
                <div id="menu_color-error" class="error invalid-feedback"></div>
            </div>

            <div class="col-md-4">
                <label for="text_color" class="form-label">Color de Textos:</label>
                <input v-model="model.text_color" type="color" class="form-control" id="text_color" name="text_color"
                    @focus="$parent.clearErrorMsg($event)">
                <div id="text_color-error" class="error invalid-feedback"></div>
            </div>

            <div class="col-md-4">
                <label for="sufijo" class="form-label">Logo:</label>
                <div class="w-full d-flex gap-2">
                    <button type="button" class="d-flex p-2 btn btn-primary" @click="openFileInput">
                        <i class="fa-solid fa-upload"></i>
                    </button>
                    <button type="button" class="d-flex p-2 btn btn-danger" @click="clearImage">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
                <div class="w-full h-auto mt-4">
                    <img v-if="imageURL" :src="imageURL" style="width: 150px; height: 150px;" />
                </div>
                <input type="file" ref="fileInput" accept="image/jpeg, image/png, image/jpg" class="form-control d-none"
                    id="logo" name="logo" @change="onFileChange($event)" @focus="$parent.clearErrorMsg($event)">
                <div id="logo-error" class="error invalid-feedback"></div>
            </div>

            <div class="col-md-12">
                <label for="sufijo" class="form-label"></label>
                <button type="submit" class="btn btn-success">Guardar</button>
            </div>
        </form>
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
                name: '',
                short_name: '',
                document: '',
                pais: '',
                contact: '',
                asist_type: '',
                sufijo: '',
                logo: null,
                menu_color: '',
                text_color: '',
            },
        }
    },
    created() {
        this.model.id = this.company.id;
        this.model.name = this.company.name;
        this.model.contact = this.company.contact;
        this.model.pais = this.company.pais;
        this.model.asist_type = this.company.asist_type;
        this.model.sufijo = this.company.sufijo;
        this.model.logo = this.company.logo;
        this.model.menu_color = this.company.menu_color;
        this.model.text_color = this.company.text_color;

        if (this.file) {
            const name = this.file.path.replace("uploads/", "");

            this.imageURL = "http://localhost:8000/empresa/logo/" + name;
        };
    },
    mounted() {
    },
    methods: {

        formController: async function (url, event) {

            var vm = this;

            var target = $(event.target);
            var url = url;
            const fd = new FormData();

            fd.append("id", this.model.id);
            fd.append("name", this.model.name);
            fd.append("contact", this.model.contact);
            fd.append("pais", this.model.pais);
            fd.append("asist_type", this.model.asist_type);
            fd.append("sufijo", this.model.sufijo);
            fd.append("menu_color", this.model.menu_color);
            fd.append("text_color", this.model.text_color);
            fd.append("logo", this.model.logo);

            Swal.fire({
                title: 'Advertencia!',
                text: '¿Seguro que desea guardar?',
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: 'Sí, guardar!',
                heightAuto: false
            }).then(async (result) => {
                if (result.value) {

                    EventBus.$emit('loading', true);

                    if (this.model.logo && typeof this.model.logo !== "string" && typeof this.model.logo !== "number") {
                        const fdFile = new FormData();

                        fdFile.append("file", this.model.logo);

                        try {
                            const res = await axios.post(this.url_upload, fdFile, {
                                headers: {
                                    'Content-type': 'application/x-www-form-urlencoded',
                                }
                            });

                            fd.append("logo", res.data.msg);
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

                            return
                        };
                    };

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
                }
            });

        },

        openFileInput() {
            this.$refs.fileInput.click();
        },

        onFileChange(event) {
            const selectedFiles = event.target.files;

            this.model.logo = Array.from(selectedFiles)[0];
            this.imageURL = URL.createObjectURL(this.model.logo);
        },

        clearImage() {
            this.model.logo = null;
            this.imageURL = null;
            this.$refs.fileInput.value = null;
        },

    }
}
</script>
