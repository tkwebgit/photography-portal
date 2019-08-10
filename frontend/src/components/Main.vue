<template>
  <div style="padding-top:60px;">
    <div :class="blury">
      <v-img class="header_position" height="500" width="100%"
        src="https://tkweb.se/photography/backend/public/uploads/blurcamera.jpg">
        <v-layout fill-height align-center pa-3>
          <v-flex xs12 md7 offset-md5>
            <h1 class="display-3 font-weight-medium white--text">
              We Capture Art
            </h1>
            <h3 class="white--text pl-2 mb-4">
              You relive it!!
            </h3>
            <div v-if="!$store.getters.loggedIn" class="text-xs-center">
              <v-btn @click="openRestrationModal()" class="light-blue darken-4" dark>Get Started</v-btn>
              <v-btn @click="openLoginModal()" depressed>Login Here</v-btn>
            </div>
          </v-flex>
        </v-layout>
      </v-img>
      <v-layout justify-center>
        <v-flex xs12 sm12>
          <v-card>
            <v-container fluid grid-list-md>
              <v-layout row wrap>
                <v-flex v-for="(pic,i) in photos" :key="i" xs12 sm6 md4>
                  <v-hover>
                    <v-card slot-scope="{ hover }" :class="`elevation-${hover ? 12 : 2}`">
                      <v-list-tile class=" grow">
                        <v-list-tile-avatar color="grey darken-3">
                          <v-img class="elevation-6" :src="getOwnerProfile(pic.user)">
                          </v-img>
                        </v-list-tile-avatar>

                        <v-list-tile-content>
                          <v-list-tile-title>{{pic.user.name}}</v-list-tile-title>
                        </v-list-tile-content>
                      </v-list-tile>
                      <v-img :src="$store.state.imagestore+'/images/user'+pic.user.id+'/'+pic.path" height="200px">
                      </v-img>

                      <v-card-actions>
<!-- 
                        <v-btn icon>
                          <v-icon>favorite</v-icon>
                        </v-btn>
                        <span class="subheading mr-2">256</span> -->
                        <v-btn icon>
                          <v-icon>comment</v-icon>
                        </v-btn>
                        <span class="subheading mr-2">{{pic.comments.length}}</span>
                        <v-spacer></v-spacer>
                        <v-btn @click="startComment(pic)" fab dark small color="cyan">
                          <v-icon dark>add_comment</v-icon>
                        </v-btn>
                      </v-card-actions>
                      <v-card-actions v-if="pic.makecomment">
                        <v-textarea v-model="pic.addcomment" auto-grow label="Post Comment" rows="1">
                        </v-textarea>
                        <v-btn @click="addComment(pic)" :loading="pic.loadingComment" :disabled="pic.addcomment == ''" fab dark small color="grey">
                          <v-icon dark>send</v-icon>
                        </v-btn>
                      </v-card-actions>
                      <v-expansion-panel>
                        <v-expansion-panel-content>
                          <template v-slot:header>
                            <v-subheader>View Comments</v-subheader>
                          </template>
                          <v-card v-for="(comment,index) in pic.comments" :key="index">
                            <v-card-title style="padding-bottom:0 !important;">
                              <v-list style="padding-bottom:0 !important;" subheader>
                                <v-list-tile avatar>
                                  <v-list-tile-avatar>
                                    <img :src="getCommentProfile(comment.user)">
                                  </v-list-tile-avatar>

                                  <v-list-tile-content>
                                    <v-list-tile-title>{{comment.user.name}}</v-list-tile-title>
                                    <v-subheader style="padding:0;">{{comment.updated_at|fTime}}</v-subheader>
                                  </v-list-tile-content>
                                </v-list-tile>
                              </v-list>
                            </v-card-title>
                            <v-card-text style="padding-top:0 !important;">
                              {{comment.comment}}
                            </v-card-text>
                            <v-divider v-if="index + 1 < items.length" :key="`divider-${index}`"></v-divider>
                          </v-card>

                        </v-expansion-panel-content>
                      </v-expansion-panel>
                    </v-card>
                  </v-hover>
                </v-flex>
              </v-layout>
            </v-container>
          </v-card>
        </v-flex>
        <v-btn @click="openUploadModal()" id="myBtn" icon fab dark color="green">
          <v-icon>add</v-icon>
        </v-btn>
      </v-layout>


    </div>
    <Login ref="openLoginModal" />
    <Regsiter ref="openRegistrationModal" />
    <ImageUpload ref="openUploadModal" />
  </div>
</template>

<script>
  import Login from './Login'
  import Regsiter from './Register'
  import ImageUpload from './ImageUpload'
  export default {
    components: {
      Login,
      Regsiter,
      ImageUpload,
    },
    data() {
      return {
        blury: '',
        comment: '',
        msg: 'Hello world component',
        makecomment: false,
        cards: [{
            title: 'Pre-fab homes',
            src: 'https://cdn.vuetifyjs.com/images/cards/road.jpg',
            flex: 4,
            makecomment: false,
            comments: [{
                src: '',
                name: '',
                comment: '',
                time: null
              },
              {
                src: '',
                name: '',
                comment: '',
                time: null
              },
              {
                src: '',
                name: '',
                comment: '',
                time: null
              },
            ]
          },
          {
            title: 'Favorite road trips',
            src: 'https://cdn.vuetifyjs.com/images/cards/road.jpg',
            flex: 4,
            makecomment: false
          },
          {
            title: 'Best airlines',
            src: 'https://cdn.vuetifyjs.com/images/cards/plane.jpg',
            flex: 4,
            makecomment: false
          }
        ],
        items: [{
            header: 'Today'
          },
          {
            avatar: 'https://cdn.vuetifyjs.com/images/lists/5.jpg',
            title: 'Mike Miller',
            subtitle: "We should eat this: Grate, Squash, Corn, and tomatillo Tacos.We should eat this: Grate, Squash, Corn, and tomatillo Tacos.We should eat this: Grate, Squash, Corn, and tomatillo Tacos.We should eat this: Grate, Squash, Corn, and tomatillo Tacos.We should eat this: Grate, Squash, Corn, and tomatillo Tacos.We should eat this: Grate, Squash, Corn, and tomatillo Tacos.",
            action: '15 min ago'
          },
          {
            divider: true,
            inset: true
          },
          {
            avatar: 'https://cdn.vuetifyjs.com/images/lists/2.jpg',
            title: 'John DOe',
            subtitle: "interesting capture, if only photography could speak",
            action: '15 min ago'
          },
          {
            divider: true,
            inset: true
          },
          {
            avatar: 'https://cdn.vuetifyjs.com/images/lists/3.jpg',
            title: 'Oui oui',
            subtitle: "An image worth a million words and more, great work",
            action: '15 min ago'
          },
          {
            divider: true,
            inset: true
          },
          {
            avatar: 'https://cdn.vuetifyjs.com/images/lists/4.jpg',
            title: 'Jane Doe',
            subtitle: "Have any ideas about what we should get Heidi for her birthday?",
            action: '15 min ago'
          }
        ]
      }
    },
    watch: {
      dialogStatus(newVal, oldVal) {
        if (newVal == false) {
          this.blury = ''
        }
      },
      dialogImageStatus(newVal, oldVal) {
        if (newVal == false) {
          this.blury = ''
        }
      }
    },
    computed: {
      dialogStatus() {
        return this.$store.getters.loginDialog
      },
      dialogImageStatus() {
        return this.$store.getters.imageDialog
      },
      photos() {
        return this.$store.getters.getUploads
      }
    },
    methods: {
      startComment(card) {
        if(!this.$store.getters.loggedIn){
          this.blur = 'hello'
          this.$store.commit('changeBlur')
          this.$store.commit('changeText')
           return
        }
        card.makecomment = !card.makecomment
      },
      getOwnerProfile(user) {
        if (!user.profile) {
          return this.$store.state.imagestore + '/profiles/download.png'
        } else {
          return this.$store.state.imagestore + '/profiles/' + user.profile
        }
      },
      getCommentProfile(user) {
        if (!user.profile) {
          return this.$store.state.imagestore + '/profiles/download.png'
        } else {
          return this.$store.state.imagestore + '/profiles/' + user.profile
        }
      },
      addComment(pic) {

        pic.loadingComment = true
        this.$store.dispatch('addComment', {
            pic_id: pic.id,
            comment: pic.addcomment,
          }).then(r => {
            pic.loadingComment =  false
             this.$store.dispatch('getUploads')
          })
          .catch(error => {
            pic.loadingComment =  false
            console.log(error)
          })

      },
      openLoginModal() {
        this.blury = 'hello'
        this.$store.commit('changeTextBack')
        this.$refs.openLoginModal.loginModal()

      },
      openRestrationModal() {
        this.blury = 'hello'
        this.$refs.openLoginModal.regModal()

      },
      openUploadModal() {
        this.blury = 'hello'
        this.$refs.openUploadModal.uploadModal()

      },
    },
    created() {
      this.$store.dispatch('getUploads')
        .then(r => {
          console.log(r.data.uploads)
        }).catch(error => {
          console.log(error)
        });
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  #myBtn {
    position: fixed;
    bottom: 20px;
    right: 30px;
    z-index: 99;
  }

  .v-btn .v-btn--round .theme--dark .primary {
    background-color: #607d8b;
  }

  .header_position {
    font-family: 'Avenir', Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    color: #2c3e50;
  }

  .hello {
    color: green;
    filter: blur(5px);
    -webkit-filter: blur(5px);
    -moz-filter: blur(5px);
    -o-filter: blur(5px);
    -ms-filter: blur(5px);
    box-shadow: 0px 0px 1px 5000px rgba(0, 0, 0, 0.8);
    -webkit-box-shadow: 0px 0px 1px 5000px rgba(0, 0, 0, 0.8);

  }

</style>
