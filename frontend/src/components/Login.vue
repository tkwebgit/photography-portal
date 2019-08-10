<template>
  <v-layout row justify-center>
    <v-btn style="display:none" ref="displayModal" color="primary" dark @click.stop="dialog = true">
      Open Dialog
    </v-btn>
    <v-snackbar :color="clr" v-model="snackbar" right="right" :timeout="timeout" top="top">
      {{ snackbarText }}
      <v-btn flat @click="snackbar = false">
        <v-icon>close</v-icon>
      </v-btn>
    </v-snackbar>
    <v-dialog v-model="dialog" persistent max-width="390">
      <v-flex class="login-form text-xs-center">
        <v-alert :timeout="timeout" :value="alert" type="success" transition="scale-transition">
          Account successfully created
        </v-alert>
        <v-card light="light">
          <v-card-text>
            <div><template v-if="options.isLoggingIn">{{$store.getters.loginText}}
              </template><template v-else>Create an account</template></div>
            <v-form>
              <v-text-field v-if="!options.isLoggingIn" v-model="user.name" light="light" prepend-icon="person"
                label="Name"></v-text-field>
              <v-text-field v-model="user.email" light="light" prepend-icon="email" label="Email" type="email">
              </v-text-field>
              <v-text-field v-model="user.password" light="light" prepend-icon="lock" label="Password" type="password">
              </v-text-field>
              <v-btn @click.prevent="login()" :loading="loadinglogin" color="blue-grey darken-2" class="white--text" v-if="options.isLoggingIn"
                block="block" type="submit">Sign in</v-btn>
              <v-btn color="blue-grey darken-2" :loading="loadingregister" class="white--text" v-else block="block"
                type="submit" @click.prevent="register()">Sign up
              </v-btn>
            </v-form>
          </v-card-text>
          <div v-if="options.isLoggingIn">Don't have an account?
            <v-btn light="light" @click="options.isLoggingIn = false">Sign up
            </v-btn>
            <v-btn color="grey darken-2" flat @click="$store.commit('removeBlur')">Close</v-btn>
          </div>
          <div v-if="!options.isLoggingIn">Back to login
            <v-btn light="light" :loading="loadinglogin" @click="login(),options.isLoggingIn = true">Sign In</v-btn>
            <v-btn color="grey darken-2" flat @click="$store.commit('removeBlur')">Close</v-btn>
          </div>
        </v-card>

      </v-flex>
    </v-dialog>
  </v-layout>
</template>
<script>
  export default {
    data() {
      return {
        snackbar:false,
        snackbarText:'',
        loadingregister: false,
        loadinglogin: false,
        timeout: 2000,
        clr:'',
        alert: false,
        user: {
          email: '',
          password: '',
          name: '',
        },
        options: {
          isLoggingIn: true,
        },
      }
    },
    computed: {
      dialog: {
        // getter
        get: function () {
          return this.$store.getters.loginDialog
        },
        // setter
        set: function (newValue) {
          return false
        }

      },
    },
    methods: {
      testSnackBar(){
        this.clr = 'red'
        this.snackbar = true,
        this.snackbarText = 'Successfully done!!'
      },
      loginModal() {
        this.options.isLoggingIn = true
        this.$store.commit('changeBlur')
        // console.log('unyama')
      },
      regModal() {
        this.options.isLoggingIn = false
        this.$store.commit('changeBlur')
      },
      login() {
        // alert('clicked')
        
        this.loadinglogin = true
        this.$store.dispatch('login', {
            password: this.user.password,
            email: this.user.email,
          }).then(this.onSuccessLogin)
          .catch(error => {
            // console.log(error.response.data)
            this.loadinglogin = false
            console.log(error)
          })

      },
      register() {
        // alert('clicked')
        
        this.loadingregister = true
        this.$store.dispatch('register', {
            name: this.user.name,
            email: this.user.email,
            password: this.user.password
          }).then(this.onSuccess)
          .catch(error => {
            console.log(error)
            this.loadingregister = false

          })

      },
      onSuccess(r) {
        this.loadingregister = false
        options.isLoggingIn = true
        this.snackbar = true
        this.clr = 'green'
        this.snackbarText = r.data.message
        this.options.isLoggingIn = true
      },
      onSuccessLogin(r) {
        this.$store.commit('removeBlur')
        this.loadinglogin = false
        this.snackbar = true
        this.clr = 'green'
        this.snackbarText = r.data.message
      },

    }
  }

</script>
<style scoped>
  .login-form {
    max-width: 500px;
  }

  .v-alert.success {
    background-color: #32a036f1;
  }

</style>
