<template>
    <div>
        <multiselect track-by="id" :loading="isLoading" @select="selectAction" @search-change="asyncFind"
                     :multiple="false" v-model="selectedRecord" :options="options" placeholder="Select a tag"
                     open-direction="top"
                     selectLabel="" label="name" :class="{'opacity-50' : isProcessing}">
        </multiselect>
    </div>
</template>

<script>
    import Multiselect from 'vue-multiselect'

    // register globally
    Vue.component('multiselect', Multiselect);

    export default {
        props: ['apiKey', 'apiUrl', 'record'],
        components: {Multiselect},
        data() {
            return {
                selectedRecord: {},
                options: [
                    {
                        'name': 'Austin',
                        'id': 'austin',
                    },
                    {
                        'name': 'Founder',
                        'id': 'founder',
                    },
                ],
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
                axios.get(this.apiUrl + '/list?api_key=' + this.apiKey).then((response) => {
                    this.options = response.data;
                    this.isLoading = false;
                });
            },
            selectAction(event) {
                console.log(event.id);
                console.log(event.id);

                let url = '/api/public-tags?api_key=' + this.apiKey + '&action=' + event.id + '&record=' + this.record.id;

                this.isProcessing = true;
                axios.get(url).then((response) => {
                    this.isProcessing = false;
                    if (response.data.message) {
                        window.Events.$emit('success', response.data.message);
                    }
                    if (response.data.record) {
                        window.Events.$emit('air-list-record-update', response.data.record);
                    }
                });
                },
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
