<template>
  <div id="app">
    <Nav></Nav>
    <router-view/>
  </div>
</template>

<script>
import Nav from './components/Nav'
export default {
  components:{
    Nav,
  },
  name: 'App'
}
</script>

<style>
body{
  font-family: 'Varela Round', sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
#app {

 
  color: #2c3e50;
}
</style>
