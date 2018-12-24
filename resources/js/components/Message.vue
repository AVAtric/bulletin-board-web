<template>
    <article class="media">
        <figure v-if="has_image" class="media-left">
            <p class="image is-64x64">
                <img v-bind:src="image">
            </p>
        </figure>
        <div class="media-content">
            <div class="content">
                <p>
                    <strong>{{ user }}</strong> <small>sagt:</small>
                    <br>
                    {{ message }}
                </p>
            </div>
        </div>
    </article>
</template>

<script>
    export default {
        props: ['user', 'message', 'image'],

        computed: {
            has_image() {
                return (this.$props.image !== "");
            }
        }
    }
</script>
