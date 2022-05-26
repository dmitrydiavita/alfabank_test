export default {
    data() {
        return {
            category: {
                name: null
            },
            error: null
        }
    },
    template: `
        <h3 class="pb-3">Новая категория</h3>
        
        <form>
          <div class="mb-3">
            <label for="name" class="form-label">Название</label>
            <input v-model="category.name" class="form-control" id="name" placeholder="Введите название" autocomplete="off">
            
            <div id="nameHelp" class="form-text">Не меньше одного символа.</div>
            
            <div class="text-danger tex" v-if="category !== null">
                <small>{{ error }}</small>
            </div> 
          </div>
          
          <button @click="submit" class="btn btn-primary">Сохранить</button>
        </form>
        
        
    `,
    methods: {
        validateForm: function() {
            if (this.category.name === null || this.category.name.length === 0) {
                this.error = 'Название должно быть хотябы 1 символ';

                return false;
            } else {
                this.error = null;

                return true;
            }
        },

        submit: function() {
            if (! this.validateForm()) {
                return;
            }

            let fd = new FormData();
            fd.set('name', this.category.name);

            axios
                .post('/category-create', fd)
                .then(response => {
                    if (response.data.success) {
                        this.$router.push('/')
                    } else {
                        console.log(response.data);
                    }
                })
                .catch(e => {
                    console.log(e);
                })
        },
    }
}
