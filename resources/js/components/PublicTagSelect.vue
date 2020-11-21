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
            emailHeroAboutContract() {
                window.open('mailto:' + this.record.hero_email +
                    '?subject=Hey ' + this.record.first_name
                );
            },
            emailClientAboutContract() {
                window.open('mailto:' + this.record.client_email +
                    '?subject=Contract #' + this.record.friendly_id + ' with ' + this.record.hero_name
                );
            },
            emailClientAboutJob() {
                window.open('mailto:' + this.record.client_email +
                    '?subject=Job #' + this.record.friendly_id + ' - ' + this.record.role
                );
            },
            emailClientAboutMatch() {
                window.open('mailto:' + this.record.client_match_emails.join(',') +
                    '?subject=#' + this.record.friendly_id + ' - ' + this.record.hero_name
                );
            },
            searchGmailByFriendlyId() {
                window.open(
                    'https://mail.google.com/mail/u/0/#search/' + this.record.friendly_id
                );
            },
            emailHeroAboutPayment() {
                window.open('mailto:' + this.record.hero_email +
                    '?subject=Payment #' + this.record.friendly_id
                );
            },
            email() {
                window.open('mailto:' + this.record.email +
                    '?subject=Hey ' + this.record.first_name
                );
            },
            emailClient() {
                window.open('mailto:' + this.record.client_email +
                    '?subject=Hey ' + this.record.client_first_name
                );
            },
            emailHero() {
                window.open('mailto:' + this.record.hero_email +
                    '?subject=Hey ' + this.record.hero_first_name
                );
            },
            removeRecord() {
                window.Events.$emit('air-list-record-remove', this.record);
            },
            editJob() {
                document.location = this.record.client_edit_url;
            },
            editJobAsAdmin() {
                document.location = '/admin/jobs/' + this.record.id;
            },
            viewJob() {
                document.location = this.record.url;
            },
            viewUserRecord() {
                document.location = '/admin/users/' + this.record.user_id;
            },
            selectAction(event) {
                if (event.id.substr(0, 3) === 'js_') {
                    let methodName = event.id.substr(3);
                    this[methodName]();
                } else if (event.id.substr(0, 7) === 'action_') {
                    let url = this.apiUrl + '/execute?api_key=' + this.apiKey + '&action=' + event.id + '&record=' + this.record.id;

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
                }
            },
        }
    }
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style>
    /*your styles*/
</style>
