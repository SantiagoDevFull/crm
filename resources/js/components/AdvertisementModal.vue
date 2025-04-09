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

                        <div class="col-md-8 mb-2">
                            <label for="title" class="form-label">Título:</label>
                            <input v-model="model.title" type="text" class="form-control" id="title" name="title"
                                @focus="$parent.clearErrorMsg($event)">
                            <div id="title-error" class="error invalid-feedback"></div>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label for="file" class="form-label">Adjunto:</label>
                            <input type="file" class="form-control" id="file" name="file" @change="onFileChange($event)" @focus="$parent.clearErrorMsg($event)">
                            <div id="file-error" class="error invalid-feedback"></div>
                        </div>

                        <div class="col-md-12">
                            <label for="group_advertisement_ids" class="form-label">Lista de distribución:</label>
                            <multiselect v-model="model.group_advertisement_ids" :options="groups" name="group_advertisement_ids" label="name" track-by="id" :multiple="true"></multiselect>
                            <div id="group_advertisement_ids-error" class="error invalid-feedback"></div>
                        </div>

                        <div class="col-md-12">
                            <label for="text" class="form-label">Contenido:</label>
                            <quill-editor
                                v-model="model.text"
                                ref="text"
                                :options="editorOptions"
                            ></quill-editor>
                            <div id="text-error" class="error invalid-feedback"></div>
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
import 'vue-multiselect/dist/vue-multiselect.min.css';
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'
import Multiselect from 'vue-multiselect';
import { quillEditor } from 'vue-quill-editor';

export default {
    components: { Multiselect, quillEditor },
    props: {
        url: {
            type: String,
            default: ''
        },
        url_upload: {
            type: String,
            default: ''
        },
        groups: {
            type: Array,
            default: []
        },
    },
    data() {
        return {
            model: {
                id: '',
                title: '',
                text: '',
                file: '',
                group_advertisement_ids: [],
            },
            editorOptions: {
                placeholder: '',
                theme: 'snow',
            },
            text: '',
            color: ''
        }
    },
    created() {},
    mounted() {
        EventBus.$on('clear_modal', function () {
            this.model = {
                id: '',
                title: '',
                file: '',
                text: '',
                group_advertisement_ids: [],
            };

            this.text = "";

            $('#blockModal').modal('hide');
        }.bind(this));
        EventBus.$on('create_modal', function () {

            this.text = "Crear"
            this.color = "success";

            $('#blockModal').modal('show');
        }.bind(this));
        EventBus.$on('edit_modal', function (data) {

            const { advertisement, group_advertisement } = data;

            const { id, title, text } = advertisement;

            this.model.id = id;
            this.model.title = title;
            this.model.text = text;
            this.model.group_advertisement_ids = group_advertisement;

            this.text = "Actualizar"
            this.color = "primary";

            $('#blockModal').modal('show');
        }.bind(this));
    },
    methods: {
        formController: async function (url, event) {
            var target = $(event.target);
            var url = url;
            var fd = new FormData();

            EventBus.$emit('loading', true);

            if (this.model.file) {
                
                const fdFile = new FormData();

                fdFile.append('file', this.model.file);

                try {
                    const res = await axios.post(this.url_upload, fdFile, {
                        headers: {
                            'Content-type': 'application/x-www-form-urlencoded',
                        }
                    });

                    const { data } = res;
                    const { msg } = data;

                    fd.append("upload_id", msg);
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

            fd.append("id", this.model.id)
            fd.append("title", this.model.title)
            fd.append("text", this.model.text)

            const group_advertisement_ids = [];

            for (let i = 0; i < this.model.group_advertisement_ids.length; i++) {
                const element = this.model.group_advertisement_ids[i];
                group_advertisement_ids.push(element.id);
            };

            fd.append("group_advertisement_ids", JSON.stringify(group_advertisement_ids))

            EventBus.$emit('loading', true);
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
        onFileChange(event) {
            const selectedFiles = event.target.files;

            this.model.file = Array.from(selectedFiles)[0];
        }
    }
}

</script>