<template>
        <div class="container has-text-centered">
            <div v-if="this.loading_messages" class="notification">
                Messages are loading!
            </div>
            <div v-if="messages.length === 0 && error === '' && !this.loading_messages" class="notification is-warning">
                No messages found!
            </div>
            <div v-if="error !== ''" class="notification is-danger">
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
                messages: [],
                mod_time: null,
                error: ""
            }
        },

        timers: {
            check_file: { time: 3000, autostart: true, repeat: true }
        },

        watch: {
            mod_time (){
                this.debounced_get_message();
            }
        },

        methods: {
            check_file () {
                let self = this;

                axios.get(window.location.href + 'posts.php?t')
                    .then(response => this.mod_time = self.validate_request(response, "check"));
            },
            get_messages () {
                if(this.error !== "File not found!")
                    this.loading_messages = true;

                this.error = "";

                let self = this;

                axios.get(window.location.href + 'posts.php')
                    .then(response => this.messages = self.validate_request(response, "message"));
            },
            validate_request (response, type) {
                let self = this;

                switch (type) {
                    case "check":
                        if(!this.has_data(response))
                            return null;

                        if(this.has_error(response.data))
                            return null;

                        this.loading_messages = false;

                        if(response.data.hasOwnProperty('mod_time'))
                            return response.data.mod_time;

                        this.error = "File mod date not available!";

                        return null;
                    case "message":
                        if(!this.has_data(response))
                            return [];

                        if(this.has_error(response.data))
                            return [];

                        response.data.forEach(function (element) {
                            if(element.user === "" || element.message === "")
                                self.error = "Data corrupted!";
                        });

                        this.loading_messages = false;

                        if(this.error !== '')
                            return [];

                        return response.data;
                }
            },
            has_data(response){
                if(!response.hasOwnProperty('data')){
                    this.error = "Error in response!";
                    return false;
                }

                return true;
            },
            has_error(data){
                if(data.hasOwnProperty('error')){
                    this.messages = [];
                    this.error = data.error;
                    return true;
                }

                return false;
            }
        },

        mounted() {
            this.check_file();
            this.debounced_get_message = _.debounce(this.get_messages, 500);
        }
    }
</script>
