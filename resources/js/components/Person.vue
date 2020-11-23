<script>
    export default {
        props: ['person'],
        data() {
            return {
                isAddingPublicTag: null,
                public_tags: {},
            }
        },
        mounted() {
            this.public_tags = this.person.public_tags;
            window.Events.$on('person-updated', (person) => {
                this.public_tags = person.public_tags;
                this.isAddingPublicTag = false;
            });
        },
        methods: {
            togglePublicTag() {
                this.isAddingPublicTag = ! this.isAddingPublicTag;
                if (this.isAddingPublicTag) {
                    this.$nextTick(() => {
                        if (document.querySelector('.add-public-tag input.multiselect__input')) {
                            document.querySelector('.add-public-tag input.multiselect__input').focus();
                        }
                    });
                }
            },
        }
    }
</script>
