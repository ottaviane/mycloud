<template>
  <div id='tab-header'>
    <div id="hn">Name</div>
    <div id="hs">Size</div>
    <div id="hlm">Last modified</div>
  </div>
</template>

<script>
    export default {
        mounted() {
        }
    }
</script>
<style scoped>
#tab-header{
    max-height:56px;
    min-height:56px;
    display:flex;
    padding: 8px;
}
#hn{
    flex-grow: 1;
    align-items: center;
    display: flex;
}
#hs, #hlm{
    min-width: 8rem;
    max-width: 20rem;
    display: flex;
    align-items: center;
}
</style>


