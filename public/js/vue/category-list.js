export default {
    data() {
        return {
            items: null,
        }
    },
    template: `
        <h3 class="pb-3">Список категорий</h3>

        <div v-if="items === null" class="text-center">
            <div class="spinner-border text-primary" role="status">
              <span class="sr-only"></span>
            </div>
        </div>
        
        <table v-if="items !== null" class="table">
            <tr>
                <th>#</th>
                <th>Название</th>
                <th><span>Действия</span></th>
            </tr>
            <tr v-for="item in items">
                <td>{{ item.id }}</td>
                <td>{{ item.name }}</td>
                <td>
                    <router-link :to="{ name: 'edit', params: { id: item.id } }" class="">Редактировать</router-link>
                    <span @click="deleteCategory(item.id)" role="button">Удалить</span>
                </td>
            </tr>
        </table>
    `,
    methods: {
        deleteCategory: function(id) {
            axios
                .post('/category-delete/' + id)
                .then(response => {
                    if (response.data.success) {
                        this.items = null;
                        this.loadData();
                    } else {
                        console.log(response.data);
                    }
                })
                .catch(e => {
                    console.log(e);
                })
        },
        loadData: function () {
            axios.get(`/category-list`)
                .then(({ data }) => {
                    this.items = data;

                    console.log(this.items)
                })
        }
    },
    created() {
        this.loadData();
    }
}
