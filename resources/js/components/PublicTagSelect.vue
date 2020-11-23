<template>
    <div>
        <multiselect track-by="id" :loading="isLoading" @select="selectAction" @search-change="asyncFind"
                     :multiple="false" v-model="selectedRecord" :options="options" placeholder="Select a tag"
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
        props: ['apiKey', 'apiUrl', 'person'],
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
        },
        methods: {
            asyncFind(query) {
                this.isLoading = true;
                axios.get('/api/public-tags?api_key=' + this.apiKey + '&query=' + query).then((response) => {
                    this.options = response.data;
                    this.isLoading = false;
                });
            },
            selectAction(event) {
                console.log('Selected ' + event.id);

                let url = '/api/people/' + this.person.slug + '/public-tag?api_key=' + this.apiKey + '&tag=' + event.id;

                this.isProcessing = true;
                axios.get(url).then((response) => {
                    this.isProcessing = false;
                    if (response.data.message) {
                        window.Events.$emit('success', response.data.message);
                    }
                    if (response.data.person) {
                        window.Events.$emit('person-updated', response.data.person);
                    }
                });
            },
            tag(tagName) {
                let url = '/api/people/' + this.person.slug + '/public-tag?api_key=' + this.apiKey + '&new_tag=' + tagName;

                this.isProcessing = true;
                axios.get(url).then((response) => {
                    this.isProcessing = false;
                    if (response.data.message) {
                        window.Events.$emit('success', response.data.message);
                    }
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
