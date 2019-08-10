import Vue from 'vue'
import Vuex from 'vuex'
import axios from "axios";
Vue.use(Vuex)
axios.defaults.baseURL = 'https://tkweb.se/photography/backend/public/api'
// axios.defaults.baseURL = 'http://photographybackend.test/api'
const store = new Vuex.Store({
  state: {
    loginDialog: false,
    loginText: 'Login Here',
    imageDialog: false,
    registerDialog: false,
    images: [],
    myimages: [],
    token: null,
    user: {},
    imagestore: 'https://tkweb.se/photography/backend/public/uploads'
    // imagestore: 'http://photographybackend.test/uploads'
  },
  getters: {
    loginDialog(state) {
      return state.loginDialog
    },
    loginText(state) {
      return state.loginText
    },
    loggedIn(state) {
      return state.token !== null
    },
    imageDialog(state) {
      return state.imageDialog
    },
    getUploads(state) {
      return state.images
    },
    getMyUploads(state) {
      return state.myimages
    },
  },
  mutations: {
    retrieveToken(state, r) {
      console.log(r.access_token)
      state.token = r.access_token
      state.user = r.user
    },
    updateUser(state, r) {
      state.user = r
    },
    changeBlur(state) {
      if (state.loginDialog == true) {
        state.loginDialog = false
      }
      state.loginDialog = true
    },
    changeText(state) {
      state.loginText = 'Login First'
    },
    changeTextBack(state) {
      state.loginText = 'Login Here'
    },
    removeBlur(state) {
      state.loginDialog = false
    },
    changeImageBlur(state) {
      if (state.imageDialog == true) {
        state.imageDialog = false
      }
      state.imageDialog = true
    },
    removeImageBlur(state) {
      state.imageDialog = false
    },
    removeToken(state) {
      state.token = null
      state.myimages = null
      state.user = {}
    },
    getUploads(state, payload) {
      payload.forEach(function (e) {
        e.makecomment = false, e.flex = 4,
          e.addcomment = '',
          e.loadingComment = false
      })

      state.images = payload
    },
    getMyUploads(state, payload) {
      payload.forEach(function (e) {
        e.makecomment = false, e.flex = 4,
          e.fab = false,
          e.imageDelLoading = false
      })
      state.myimages = payload
    },
  },
  actions: {
    register(context, credentials) {
      console.log(credentials)
      return new Promise((resolve, reject) => {
        axios
          .post("/register", credentials)
          .then(response => {

            resolve(response);
          })
          .catch(error => {
            if ((error.response.data.message == 'Unauthenticated.') ||
              (error.response.data.message == 'This action is unauthorized.')) {
              console.log('reached')
              store.dispatch('preventUnauth')
            }
            reject(error);
          });
      });
    },
    login(context, credentials) {
      return new Promise((resolve, reject) => {
        axios
          .post("/login", credentials)
          .then(response => {
            const token = response.data.access_token;
            localStorage.setItem("access_token", token);
            context.commit("retrieveToken", response.data);
            resolve(response);
          })
          .catch(error => {
            reject(error);
          });
      });
    },
    user(context, credentials) {
      return new Promise((resolve, reject) => {
        axios
          .get("/user", credentials)
          .then(response => {
            console.log(response.data)
            context.commit("updateUser", response.data);
            resolve(response);
          })
          .catch(error => {
            reject(error);
          });
      });
    },
    imageUpload(context, imgdata) {
      axios.defaults.headers.common["Authorization"] =
        "Bearer " + context.state.token;
      return new Promise((resolve, reject) => {
        axios
          .post("/upload", imgdata)
          .then(response => {
            resolve(response);
          })
          .catch(error => {
            reject(error);
          });
      });
    },
    updateUpload(context, imgdata) {
      axios.defaults.headers.common["Authorization"] =
        "Bearer " + context.state.token;
      console.log('reached here')
      return new Promise((resolve, reject) => {
        axios
          .patch("/upload/" + imgdata.id, imgdata)
          .then(response => {
            resolve(response);
          })
          .catch(error => {
            reject(error);
          });
      });
    },
    deleteImage(context, imgdata) {
      axios.defaults.headers.common["Authorization"] =
        "Bearer " + context.state.token;
      console.log('reached here')
      return new Promise((resolve, reject) => {
        axios
          .delete("/upload/" + imgdata.id)
          .then(response => {
            resolve(response);
          })
          .catch(error => {
            reject(error);
          });
      });
    },
    profileUpload(context, imgdata) {
      axios.defaults.headers.common["Authorization"] =
        "Bearer " + context.state.token;
      return new Promise((resolve, reject) => {
        axios
          .post("/profile", imgdata)
          .then(response => {
            resolve(response);
          })
          .catch(error => {
            reject(error);
          });
      });
    },
    removeToken(context) {
      axios.defaults.headers.common["Authorization"] =
        "Bearer " + context.state.token;
      return new Promise((resolve, reject) => {
        axios
          .post("/logout")
          .then(response => {
            localStorage.removeItem("access_token");
            context.commit("removeToken");
            resolve(response);
          })
          .catch(error => {
            localStorage.removeItem("access_token");
            context.commit("removeToken");
            reject(error);
          });
      });
    },
    getUploads(context) {
      return new Promise((resolve, reject) => {
        axios
          .get("/getuploads")
          .then(response => {
            context.commit('getUploads', response.data.uploads)
            resolve(response);
          })
          .catch(error => {
            reject(error);
          });
      });
    },
    getMyUploads(context) {
      axios.defaults.headers.common["Authorization"] =
        "Bearer " + context.state.token;
      return new Promise((resolve, reject) => {
        axios
          .get("/myUploads")
          .then(response => {
            context.commit('getMyUploads', response.data.myuploads)
            resolve(response);
          })
          .catch(error => {
            reject(error);
          });
      });
    },
    addComment(context, comment) {
      axios.defaults.headers.common["Authorization"] =
        "Bearer " + context.state.token;
      return new Promise((resolve, reject) => {
        axios
          .post("/comment", comment)
          .then(response => {
            resolve(response);
          })
          .catch(error => {
            reject(error);
          });
      });
    },

  }
})

export default store
