<template>
    <div id="centeredDiv">
        <div class="mycontainer">
            <slot></slot>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {

        }
    }
</script>
<style scoped>

        #centeredDiv {
            flex-grow: 1;
            display: block;
            padding: 40px;
            padding-top: 10px;
        }
        .mycontainer {
            position: relative;
            height: 100%;
            overflow: scroll;
            box-shadow:rgba(30, 31, 36, 0.84) 0px 1.5rem 2rem !important;
            border-radius: 0.25rem !important;
            overflow: auto;
            padding: 6px;
        }
</style>


