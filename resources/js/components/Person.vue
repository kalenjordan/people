<script>
    export default {
        props: ['person'],
        data() {
            return {
                isAddingPublicTag: null,
                isAddingPrivateTag: null,
                tagsProcessing: false,
                public_tags: {},
                private_tags: {},
            }
        },
        mounted() {
            this.public_tags = this.person.public_tags;
            this.private_tags = this.person.private_tags;

            window.Events.$on('person-updated', (person) => {
                this.public_tags = person.public_tags;
                this.private_tags = person.private_tags;
                this.publicTagsProcessing = false;
                this.privateTagsProcessing = false;
            });
            window.Events.$on('hide-tag-select', () => {
                this.isAddingPublicTag = false;
                this.isAddingPrivateTag = false;
            });
            window.Events.$on('public-tags-processing', () => {
                this.publicTagsProcessing = true;
            });
            window.Events.$on('private-tags-processing', () => {
                this.privateTagsProcessing = true;
            });
            document.addEventListener('keydown', (e) => {
                let activeElement = document.activeElement;
                this.hotkeys(e, activeElement);
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
            togglePrivateTag() {
                this.isAddingPrivateTag = ! this.isAddingPrivateTag;
                if (this.isAddingPrivateTag) {
                    this.$nextTick(() => {
                        if (document.querySelector('.add-private-tag input.multiselect__input')) {
                            document.querySelector('.add-private-tag input.multiselect__input').focus();
                        }
                    });
                }
            },
            hotkeys(e, activeElement) {
                if (e.code === 'Escape') {
                    this.isAddingPublicTag = false;
                    this.isAddingPrivateTag = false;
                }
            },
        }
    }
</script>
