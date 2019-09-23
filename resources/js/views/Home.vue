<template>
    <div class="container">
        <div class="columns">
            <div class="column">
                <div class="message" v-for="status in statuses">
                    <header class="message-header">
                        A template for creating views in Vue
                    </header>
                    <div class="message-body">
                        Hello World!
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'home'
    }
</script>
