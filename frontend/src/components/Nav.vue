<template>
  <div>
    <v-toolbar fixed>
      <v-toolbar-side-icon @click.stop="drawer = !drawer" class="hidden-md-and-up"></v-toolbar-side-icon>
      <v-toolbar-title>
        <v-btn to="/">
          Logo
        </v-btn>
      </v-toolbar-title>
      <v-spacer></v-spacer>
      <div class="hidden-sm-and-down">
        <v-btn  v-if="$store.getters.loggedIn"  @click="logout()" flat>Logout</v-btn>
        <v-btn   v-if="$store.getters.loggedIn" to="/profile" color="blue-grey" class="white--text">
          Account
          <v-icon right dark>account_circle</v-icon>
        </v-btn>
        <v-avatar  v-if="$store.getters.loggedIn">
          <img :src="getProfPic()" alt="profile">
        </v-avatar>
        <span  v-if="$store.getters.loggedIn">{{$store.state.user.name}}</span>
      </div>
    </v-toolbar>

    <v-navigation-drawer v-model="drawer" fixed temporary>
      <v-list class="pa-1" v-if="$store.getters.loggedIn" >
        <v-list-tile avatar>
          <v-list-tile-avatar>
            <img :src="getProfPic()">
          </v-list-tile-avatar>

          <v-list-tile-content>
            <v-list-tile-title>{{$store.state.user.name}}</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>
      </v-list>

      <v-list class="pt-0" dense v-if="$store.getters.loggedIn" >
        <v-divider></v-divider>

        <v-list-tile v-for="item in items" :key="item.title" @click="gotopath(item.path)">
          <v-list-tile-action>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>{{ item.title }}</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>
      </v-list>
    </v-navigation-drawer>
  </div>
</template>
<script>
  export default {
    name: 'Nav',
    data() {
      return {
        msg: 'Welcome to Your Vue.js App',
        drawer: null,
        items: [{
            title: 'Account',
            icon: 'camera_enhance',
            path: '/profile',
          },
          {
            title: 'Logout',
            icon: 'question_answer',
            path: '/logout'
          }
        ]

      }
    },
    methods: {
      gotopath(route) {
        if (route == '/logout') {
          this.logout()
          return
        }
        this.$router.push({
          path: route,
        })
      },
      getProfPic() {
        if (!this.$store.state.user.profile) {
          return this.$store.state.imagestore + '/profiles/download.png'
        } else {
          return this.$store.state.imagestore + '/profiles/' + this.$store.state.user.profile
        }
      },
      logout() {
        this.$store.dispatch('removeToken')
          .then(r => {
            this.$router.push({
              path: '/'
            })
          })
          .catch(error => {
            console.log(error)
            this.$router.push({
              path: '/'
            })
          })
      }
    }
  }

</script>
<style scoped>
  #drawer {
    background: rgba(240, 240, 240, 1);

  }

</style>
