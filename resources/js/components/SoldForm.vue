<template>
    <form @submit.prevent="formController(url, $event)">
      <div class="container mt-2 p-3 border rounded bg-white">
          <div class="col-12">
            <h3>Cabecera de la Venta</h3>
          </div>

          <div class="col-md-6">
              <label for="state_id" class="form-label">Estado :</label>
              <select class="form-select" v-model="state_id" name="state_id" id="state_id" @focus="$parent.clearErrorMsg($event)">
                  <option value="0" selected disabled>Seleccionar</option>
                  <option v-for="state in states" :value="state.id" :key="state.id">{{ state.name }}</option>
              </select>
              <div id="state_id-error" class="error invalid-feedback"></div>
          </div>
      </div>

      <div class="row container m-auto my-2 p-3 border rounded bg-white" v-for="block in blocks" :key="block.id">
          <div class="col-12">
            <h3>{{ block.name }}</h3>
          </div>

          <div v-for="field in fields" v-if="field.block_id == block.id" :class="[{'form-check form-switch': field.type_field_id == 8}, 'col-' + field.width_col]">
              <label :for="field.name" class="form-label">{{ field.name }}:</label>
              <input v-if="field.type_field_id == 1" v-model="model[block.id][field.id]" type="text" class="form-control" :id="field.name" :name="field.name" @focus="$parent.clearErrorMsg($event)">
              <input v-else-if="field.type_field_id == 2" v-model="model[block.id][field.id]" type="text" class="form-control" :id="field.name" :name="field.name" @focus="$parent.clearErrorMsg($event)">
              <select v-else-if="field.type_field_id == 3" v-model="model[block.id][field.id]" class="form-control" :id="field.name" :name="field.name" @focus="$parent.clearErrorMsg($event)">
                  <option value="" selected disabled>Seleccionar</option>
                  <option v-for="option in field.options.split('\r\n')" :value="option" :key="option">{{ option }}</option>
              </select>
              <multiselect v-else-if="field.type_field_id == 4" v-model="model[block.id][field.id]" :options="field.options.split('\r\n')" multiple></multiselect>
              <input v-else-if="field.type_field_id == 5" v-model="model[block.id][field.id]" type="date" class="form-control" :id="field.name" :name="field.name" @focus="$parent.clearErrorMsg($event)">
              <input v-else-if="field.type_field_id == 6" v-model="model[block.id][field.id]" type="datetime-local" class="form-control" :id="field.name" :name="field.name" @focus="$parent.clearErrorMsg($event)">
              <input v-else-if="field.type_field_id == 7" v-model="model[block.id][field.id]" type="number" class="form-control" :id="field.name" :name="field.name" @focus="$parent.clearErrorMsg($event)">
              <!-- <switches v-else-if="field.type_field_id == 8" v-model="model[block.id][field.id]" :name="field.name" :id="field.name" @focus="$parent.clearErrorMsg($event)"></switches> -->
              <input v-else-if="field.type_field_id == 8" v-model="model[block.id][field.id]" name="field.name" :id="field.name" @focus="$parent.clearErrorMsg($event)" class="form-check-input" type="checkbox" role="switch">
              <input v-else-if="field.type_field_id == 9" type="file" class="form-control" :id="field.name" :name="field.name" @change="onFileChange(block.id, field.id, $event)" @focus="$parent.clearErrorMsg($event)">
              <input v-else-if="field.type_field_id == 10" type="file" multiple class="form-control" :id="field.name" :name="field.name" @change="onFileChange(block.id, field.id, $event)" @focus="$parent.clearErrorMsg($event)">
              <input v-else v-model="model[block.id][field.id]" type="text" class="form-control" :id="field.name" :name="field.name" @focus="$parent.clearErrorMsg($event)">
              <div :id="field.name + '-error'" class="error invalid-feedback"></div>
          </div>
      </div>
      <div class="row container m-auto my-2 border rounded bg-white">
          <button type="submit" class="btn btn-success">Guardar</button>
      </div>
    </form>
</template>

<script>
import 'vue-multiselect/dist/vue-multiselect.min.css';
import Multiselect from 'vue-multiselect';
import Switches from 'vue-switches';

export default {
  components: { Multiselect, Switches },
  props: {
      model: {},
      campain: {},
      tab_state: {},
      states: [],
      blocks: [],
      fields: [],
      id: "",
      state_id: "",
      url: "",
      url_list: "",
      url_upload: "",
  },
  data() {
      return {
        // id: "",
        // state_id: ""
      }
  },
  created() {},
  mounted() {},
  methods: {
    formController: async function(url, event) {
      try {
        EventBus.$emit('loading', true);

        const inputFiles = [];

        for (let i = 0; i < this.fields.length; i++) {
          const field = this.fields[i];

          if (field.type_field_id == 9 || field.type_field_id == 10) inputFiles.push(field.id);
        };

        for (let i = 0; i < inputFiles.length; i++) {
          const inputFile = inputFiles[i];

          for (const key in this.model) {
            const block = this.model[key];
            const field = block[inputFile];

            if (field) {
              if (field.length) {
                  const ids = [];

                  for (let i = 0; i < field.length; i++) {
                      const f = field[i];

                      const fd = new FormData();

                      fd.append('file', f);

                      const res = await axios.post(this.url_upload, fd, {
                          headers: {
                              'Content-type': 'application/x-www-form-urlencoded',
                          }
                      });

                      const { data } = res;
                      const { msg } = data;

                      if (typeof msg == "number") ids.push(msg);
                  };

                  this.model[key][inputFile] = ids;
              } else {
                  const fd = new FormData();

                  fd.append('file', field)

                  const res = await axios.post(this.url_upload, fd, {
                      headers: {
                          'Content-type': 'application/x-www-form-urlencoded',
                      }
                  });

                  const { data } = res;
                  const { msg } = data;

                  if (typeof msg == "number") this.model[key][inputFile] = msg;
              };
            };
          };
        };

        const dataJSON = JSON.stringify(this.model);

        const fd = new FormData();

        fd.append('id', this.id)
        fd.append('campain_id', this.campain.id)
        fd.append('tab_state_id', this.tab_state.id)
        fd.append('state_id', this.state_id)
        fd.append('data', dataJSON)

        const res = await axios.post(url, fd, {
            headers: {
                'Content-type': 'application/x-www-form-urlencoded',
            }
        });
        const { data } = res;

        EventBus.$emit('loading', false);
        EventBus.$emit('refresh_table');
        EventBus.$emit('clear_modal');

        this.$parent.alertMsg(data);
        window.location.href = this.url_list + "?id=" + this.campain.id;
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
    },
    onFileChange(block_id, id, event) {
      const selectedFiles = event.target.files;

      if (selectedFiles.length > 1) {
        this.model[block_id][id] = Array.from(selectedFiles);
      } else {
        this.model[block_id][id] = Array.from(selectedFiles)[0];
      };
    }
  }
}

</script>