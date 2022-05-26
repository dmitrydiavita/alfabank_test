export default {
    data() {
        return {
            category: null,
            error: null
        }
    },
    template: `
        <h3 class="pb-3">Редактирование #{{ $route.params.id }}</h3>

        <div v-if="category === null" class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only"></span>
            </div>
        </div>
        
        <form v-if="category !== null">
          <div class="mb-3">
            <label for="name" class="form-label">Название</label>
            <input v-model="category.name" class="form-control" id="name" placeholder="Введите название">
            
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
                .post('/category-edit/' + this.category.id, fd)
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
    },
    created() {
        axios.get(`/category-read/` + this.$route.params.id)
            .then(({ data }) => {
                this.category = data;

                console.log(this.category)
            })
    }
}