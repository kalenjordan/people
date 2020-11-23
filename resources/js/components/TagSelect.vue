<template>
    <div>
        <multiselect track-by="id" :loading="isLoading" @select="select" @search-change="asyncFind"
                     :multiple="false" v-model="selectedRecord" :options="options" :placeholder="placeholder"
                     open-direction="top" @tag="tag" :taggable="true" tag-position="bottom"
                     selectLabel="" deselectLabel="" label="name" :class="{'opacity-50' : isProcessing}">
        </multiselect>
    </div>
</template>

<script>
    import Multiselect from 'vue-multiselect'

    // register globally
    Vue.component('multiselect', Multiselect);

    export default {
        props: ['apiKey', 'apiUrl', 'person', 'placeholder', 'type'],
        components: {Multiselect},
        data() {
            return {
                selectedRecord: {},
                options: [],
                isLoading: false,
                isProcessing: false,
            }
        },
        mounted() {
            // console.log(this.record);
            // Nothing for now
            if (this.type !== 'public' && this.type !== 'private') {
                console.error("Type should be public or private");
            }
        },
        methods: {
            asyncFind(query) {
                this.isLoading = true;
                axios.get('/api/' + this.type + '-tags?api_key=' + this.apiKey + '&query=' + query).then((response) => {
                    this.options = response.data;
                    this.isLoading = false;
                });
            },
            select(event) {
                let url = '/api/people/' + this.person.slug + '/' + this.type + '-tag?api_key=' + this.apiKey + '&tag=' + event.id;
                this.isProcessing = true;

                axios.get(url).then((response) => {
                    this.isProcessing = false;
                    if (response.data.person) {
                        window.Events.$emit('person-updated', response.data.person);
                    }
                });
            },
            tag(tagName) {
                let url = '/api/people/' + this.person.slug + '/' + this.type + '-tag?api_key=' + this.apiKey + '&new_tag=' + tagName;

                this.isProcessing = true;
                axios.get(url).then((response) => {
                    this.isProcessing = false;
                    if (response.data.person) {
                        window.Events.$emit('person-updated', response.data.person);
                    }
                });
            }
        }
    }
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style>
    .multiselect__option.multiselect__option--highlight {
        background-color: #fce96a;
        color: #252f3f;
    }

    /*your styles*/
</style>
