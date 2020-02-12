<template>
    <div class="corpo-container">
        <mycloud-path-component></mycloud-path-component>
        <mycloud-tab-header-component></mycloud-tab-header-component>
        <mycloud-tabella-files-component></mycloud-tabella-files-component>
    </div>
</template>

<script>
    export default {
        mounted() {

        }
    }
</script>
<style scoped>
div{
    flex-grow: 4;
}
.corpo-container{
    display:flex;
    flex-direction: column;
}


</style>



