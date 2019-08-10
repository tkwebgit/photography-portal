<template>
  <v-layout row justify-center>
    <v-btn style="display:none" ref="displayModal" color="primary" dark @click.stop="dialog = true">
      Open Dialog
    </v-btn>
    <v-snackbar :color="clr" v-model="snackbar" right="right" :timeout="timeout" top="top">
      {{ snackbarText }}
      <v-btn flat @click="snackbar = false">
        Close
      </v-btn>
    </v-snackbar>
    <v-dialog v-model="imageDialog" persistent max-width="500">
      <v-flex class="login-form text-xs-center">
        <v-alert :timeout="timeout" :value="alert" type="success" transition="scale-transition">
          Account successfully created
        </v-alert>
        <v-card class="imageUploader" light="light">
          <div>
            <h3 style="padding-bottom:5px;">Upload Image
            </h3>
          </div>
          <div>
            <v-img style="cursor:pointer;" @click="$refs.selectfile.click()" class="header_position" :src="getUpload()">
            </v-img>
            <input style="display:none" ref="selectfile" type="file" @change="onFileSelected">
          </div>
          <v-textarea v-model="comment" auto-grow label="Post Comment" rows="1">
          </v-textarea>
          <div>
            <v-btn color="grey darken-2 float-left" flat @click="$store.commit('removeImageBlur')">Close</v-btn>
            <v-btn @click="uploadImage()" color="blue-grey" class="white--text float-right"> Upload
              <v-icon right dark>cloud_upload</v-icon>
            </v-btn>
            
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
        snackbar: false,
        comment: '',
        fileselected: '',
        snackbarText: '',
        loadingregister: false,
        loadinglogin: false,
        timeout: 2000,
        clr: '',
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
      imageDialog: {
        // getter
        get: function () {
          return this.$store.getters.imageDialog
        },
        // setter
        set: function (newValue) {
          return false
        }

      },
    },
    methods: {
      onFileSelected(e) {
        this.serverError = ''
        let file = e.target.files[0];
        let reader = new FileReader();
        reader.onloadend = (file) => {
          this.fileselected = reader.result;
        }
        reader.readAsDataURL(file);
      },
      getUpload() {
        let photo = (!this.fileselected) ? 'https://alittlebitdeeper.co.ke/photographydemo/backend/public/uploads/placeholder-image.jpg' : this
          .fileselected
        return photo;
      },
      uploadImage() {
        this.$store.dispatch('imageUpload', {
            photo: this.fileselected,
            comment: this.comment,
          }).then(r => {
            this.$store.commit('removeImageBlur')
            this.snackbar = true
            this.clr = 'green'
            this.snackbarText = r.data.message
            this.$store.dispatch('getUploads')
          })
          .catch(error => {
            this.$store.commit('removeImageBlur')
            this.snackbar = true
            this.clr = 'red'
            this.snackbarText = 'Error occured, try again later'
          })

      },
      uploadModal() {
        if(!this.$store.getters.loggedIn){
           this.$store.commit('changeBlur')
           this.$store.commit('changeText')
           return
        }
        this.$store.commit('changeImageBlur')
        // console.log('unyama')
      },
      testSnackBar() {
        this.clr = 'red'
        this.snackbar = true,
          this.snackbarText = 'Successfully done!!'
      },
      login() {
        // alert('clicked')
        this.btnState = 'loading'
        this.btnText = 'logging in...'
        this.$store.dispatch('login', {
            name: this.user.name,
            password: this.user.password,
            email: this.user.email,
          }).then(this.onSuccess)
          .catch(error => {
            // console.log(error.response.data)
            this.btnState = ''
            this.btnText = 'Login'
            this.serverError = error.response.data.message
            if (error.response.data.errors) {
              this.errors.record(error.response.data.errors)
            }
            if (error.response.data.loggedin) {
              this.ifLoggedin = 'already'
            }
            if (error.response.data.twofactorauth) {
              this.start2fa()
            }

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
            // this.serverError = error.response.data.message

          })

      },
      onSuccess() {
        this.loadingregister = false
        options.isLoggingIn = true
        this.alert = true
      },

    }
  }

</script>
<style scoped>
  .imageUploader{
        padding: 20px;
  }
  .login-form {
    max-width: 500px;
  }

  .v-alert.success {
    background-color: #32a036f1;
  }

</style>
