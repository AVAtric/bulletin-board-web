<template>
    <div class="container has-text-centered">
        <div v-if="is_loading" class="notification">
            Messages are loading!
        </div>
        <div v-if="has_warning" class="notification is-warning">
            {{warning}}
        </div>
        <div v-if="has_error" class="notification is-danger">
            {{error}}
        </div>
        <message
                v-for="message in messages"
                v-bind:key="message.id"
                v-bind:user="message.user"
                v-bind:message="message.message"
                v-bind:image="message.hasOwnProperty('user_image') ? message.user_image : ''"
        ></message>
    </div>
</template>

<script>
    import Message from './Message.vue';

    export default {
        components: {Message},

        data() {
            return {
                loading_messages: true,
                mod_time: null,
                messages: [],
                error: "",
                warning: ""
            }
        },

        computed: {
            has_error() {
                return this.error !== "";
            },
            has_warning() {
                return this.warning !== "";
            },
            is_loading() {
                return (this.error === "" && this.warning === "" && this.loading_messages === true)
            }
        },

        timers: {
            check_file: {time: 3000, autostart: true, repeat: true}
        },

        watch: {
            mod_time() {
                this.debounced_get_message();
            }
        },

        methods: {
            clear_problems(){
                this.error = "";
                this.warning = "";
            },
            has_problem() {
                return (this.error !== "" || this.warning !== "")
            },

            check_file() {
                let self = this;

                axios.get(window.location.href + 'posts.php?t')
                    .then(response => self.mod_time = self.validate_request(response, "check"))
                    .catch(error => self.error = error.reason
                    );
            },
            get_messages() {
                if(this.messages.length === 0)
                    this.loading_messages = true;

                let self = this;

                axios.get(window.location.href + 'posts.php')
                    .then(response => self.messages = self.validate_request(response, "message"))
                    .catch(error => self.error = error.reason);
            },
            validate_request(response, type) {
                if (response.data.hasOwnProperty('error'))
                    this.error = response.data.error;

                if (response.data.hasOwnProperty('warning'))
                    this.error = response.data.warning;

                switch (type) {
                    case "check":
                        if(this.has_problem())
                            return null;

                        return response.data.mod_time;
                    case "message":
                        this.loading_messages = false;
                        this.clear_problems();

                        if(this.has_problem())
                            return [];

                        return response.data.messages;
                }
            }
        },

        mounted() {
            this.check_file();
            this.debounced_get_message = _.debounce(this.get_messages, 500);
        }
    }
</script>
