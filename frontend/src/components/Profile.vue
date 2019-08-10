<template>
  <div style="margin-top:60px;">
    <v-snackbar :color="clr" v-model="snackbar" right="right" :timeout="timeout" top="top">
      {{ snackbarText }}
      <v-btn flat @click="snackbar = false">
        Close
      </v-btn>
    </v-snackbar>
    <div :class="blury">
      <v-img class="header_position" height="200" width="100%" :src="require('@/assets/imgs/blurcamera.jpg')">
        <v-layout fill-height align-center pa-3>
          <div style="margin: 0 auto;">
            <v-icon x-large dark>add_a_photo</v-icon><span style="color:#fff;" class="display-1 font-weight-bold">
              {{myphotos.length}}</span>
          </div>
        </v-layout>
      </v-img>
      <div class="imgPositioning">
        <v-badge overlap>
          <template v-slot:badge>
            <v-btn @click="openProfileModal()" outline flat icon color="white">
              <v-icon>edit</v-icon>
            </v-btn>
          </template>

          <v-avatar size="100px">
            <img :src="getProfPic()" alt="John">
          </v-avatar>
        </v-badge>
      </div>
      <v-layout justify-center>
        <v-flex xs12 sm12>
          <v-card>
            <v-container fluid grid-list-md>
              <v-layout row wrap>
                <v-flex v-for="(pic,i) in myphotos" :key="i" xs12 sm6 md4>
                  <v-hover>
                    <v-card slot-scope="{ hover }" :class="`elevation-${hover ? 12 : 2}`">
                      <v-img :src="$store.state.imagestore+'/images/user'+pic.user.id+'/'+pic.path" height="200px">
                      </v-img>

                      <v-card-actions>
                        <!-- 
                        <v-btn icon>
                          <v-icon>favorite</v-icon>
                        </v-btn> -->
                        <!-- <span class="subheading mr-2">256</span> -->
                        <v-btn icon>
                          <v-icon>comment</v-icon>
                        </v-btn>
                        <span class="subheading mr-2">{{pic.comments.length}}</span>
                        <v-spacer></v-spacer>
                        <v-speed-dial v-model="pic.fab" :top="top" :bottom="bottom" :right="right" :left="left"
                          :direction="direction" :open-on-hover="hover" :transition="transition">
                          <template v-slot:activator>
                            <v-btn v-model="pic.fab" color="blue darken-2" dark fab>
                              <v-icon>camera</v-icon>
                              <v-icon>close</v-icon>
                            </v-btn>
                          </template>
                          <v-btn @click="openEditModal(pic)" fab dark small color="green">
                            <v-icon>edit</v-icon>
                          </v-btn>
                          <v-btn @click="deleteImage(pic)" fab dark small color="red">
                            <v-icon>delete</v-icon>
                          </v-btn>
                        </v-speed-dial>
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
      </v-layout>
    </div>
    <v-dialog v-model="imageEditDialog" persistent max-width="500">
      <v-flex class="login-form text-xs-center">
        <v-card class="imageUploader" light="light">
          <div>
            <h3 style="padding-bottom:5px;">Change Image
            </h3>
          </div>
          <div>
            <v-img style="cursor:pointer;" @click="$refs.selectfile.click()" class="header_position" :src="getUpload()">
            </v-img>
            <input style="display:none" ref="selectfile" type="file" @change="onFileSelected">
          </div>
          <!-- <v-textarea v-model="caption" auto-grow label="Update Comment" rows="1">
          </v-textarea> -->
          <div>
            <v-btn color="grey darken-2 float-left" flat @click="closeEditModal()">Close</v-btn>
            <v-btn @click="updateImage()" :loading="imageEditLoading" color="blue-grey" class="white--text float-right">
              Upload
              <v-icon right dark>cloud_upload</v-icon>
            </v-btn>

          </div>
        </v-card>
      </v-flex>
    </v-dialog>
    <v-dialog v-model="profileEditDialog" persistent max-width="500">
      <v-flex class="login-form text-xs-center">
        <v-card class="imageUploader" light="light">
          <div>
            <h3 style="padding-bottom:5px;">Change Profile Image
            </h3>
          </div>
          <div>
            <v-avatar style="cursor:pointer;" @click="$refs.selectProfile.click()" size="100px">
              <img :src="profileSelected" alt="John">
            </v-avatar>
            <input style="display:none" ref="selectProfile" type="file" @change="onProfileSelected">
          </div>
          <div>
            <v-btn color="grey darken-2 float-left" flat @click="closeProfileModal()">Close</v-btn>
            <v-btn @click="uploadProfileImage()" color="blue-grey" :loading="loadingProfile"
              class="white--text float-right"> Upload
              <v-icon right dark>cloud_upload</v-icon>
            </v-btn>

          </div>
        </v-card>
      </v-flex>
    </v-dialog>
  </div>
</template>
<script>
  export default {
    data() {
      return {
        snackbarText: '',
        snackbar: false,
        clr:'',
        timeout: 2000,
        rating: 4.3,
        blury: '',
        makecomment: false,
        comment: '',
        fileselected: '',
        profileSelected: '',
        imageEditLoading: false,
        cards: [{
            title: 'Pre-fab homes',
            src: 'https://cdn.vuetifyjs.com/images/cards/house.jpg',
            flex: 4,
            fab: false
          },
          {
            title: 'Favorite road trips',
            src: 'https://cdn.vuetifyjs.com/images/cards/road.jpg',
            flex: 4,
            fab: false
          },
          {
            title: 'Best airlines',
            src: 'https://cdn.vuetifyjs.com/images/cards/plane.jpg',
            flex: 4,
            fab: false
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
        ],
        direction: 'top',
        imgId: '',
        fling: false,
        hover: false,
        tabs: null,
        top: false,
        right: true,
        bottom: true,
        left: false,
        imageEditDialog: false,
        caption: '',
        profileEditDialog: false,
        loadingProfile: false,
        transition: 'slide-y-reverse-transition'
      }
    },
    created() {
      this.$store.dispatch('getMyUploads')
        .then(r => {
          console.log(r.data.uploads)
        }).catch(error => {
          console.log(error)
        });
    },
    computed: {
      myphotos() {
        return this.$store.getters.getMyUploads
      },
      activeFab() {
        switch (this.tabs) {
          case 'one':
            return {
              'class': 'purple', icon: 'account_circle'
            }
            case 'two':
              return {
                'class': 'red', icon: 'edit'
              }
              case 'three':
                return {
                  'class': 'green', icon: 'keyboard_arrow_up'
                }
                default:
                  return {}
        }
      }
    },
    methods: {
      getProfPic() {
        if (!this.$store.state.user.profile) {
          return this.$store.state.imagestore + '/profiles/download.png'
        } else {
          return this.$store.state.imagestore + '/profiles/' + this.$store.state.user.profile
        }
      },
      getCommentProfile(user) {
        if (!user.profile) {
          return this.$store.state.imagestore + '/profiles/download.png'
        } else {
          return this.$store.state.imagestore + '/profiles/' + user.profile
        }
      },
      openEditModal(pic) {
        this.fileselected = this.$store.state.imagestore + '/images/user' + pic.user.id + '/' + pic.path
        let caption = pic.comments.find(item => item.caption == true)
        if (!caption) {
          this.caption = ''
        } else {
          this.caption = caption.comment
        }
        this.imgId = pic.id
        this.blury = 'hello'
        this.imageEditDialog = true
      },
      openProfileModal(card) {
        if(!this.$store.state.user.profile){
              this.profileSelected = this.$store.state.imagestore + '/profiles/download.png'
        }else{
              this.profileSelected = this.$store.state.imagestore + '/profiles/'+this.$store.state.user.profile
        }
        
        this.blury = 'hello'
        this.profileEditDialog = true
      },
      closeEditModal() {
        this.blury = ''
        this.imageEditDialog = false
      },
      closeProfileModal() {
        this.blury = ''
        this.profileEditDialog = false
      },
      onFileSelected(e) {
        this.serverError = ''
        let file = e.target.files[0];
        let reader = new FileReader();
        reader.onloadend = (file) => {
          this.fileselected = reader.result;
        }
        reader.readAsDataURL(file);
      },
      onProfileSelected(e) {
        this.serverError = ''
        let file = e.target.files[0];
        let reader = new FileReader();
        reader.onloadend = (file) => {
          this.profileSelected = reader.result;
        }
        reader.readAsDataURL(file);
      },
      getUpload() {
        let photo = (!this.fileselected) ? 'http://photographybackend.test/uploads/placeholder-image.jpg' : this
          .fileselected
        return photo;
      },
      uploadProfileImage() {
        this.loadingProfile = true
        this.$store.dispatch('profileUpload', {
            photo: this.profileSelected,
          }).then(r => {
            this.loadingProfile = false
           this.$store.dispatch('user')
          this.closeProfileModal()
          })
          .catch(error => {
            this.loadingProfile = false
            // console.log(error.response.data)
            console.log(error)
          })

      },
      updateImage() {
        this.imageEditLoading = true
        this.$store.dispatch('updateUpload', {
            photo: this.fileselected,
            caption: this.caption,
            id: this.imgId,
          }).then(r => {
            this.imageEditLoading = false
            this.$store.dispatch('getMyUploads')
          })
          .catch(error => {
            this.imageEditLoading = false
            // console.log(error.response.data)
            console.log(error)
          })

      },
      deleteImage(pic) {
        pic.imageDelLoading = true
        let confirmation = confirm("Are you sure you want to delete ?");
        if (!confirmation) {
          return
        }
        this.$store.dispatch('deleteImage', {
            id: pic.id,
          }).then(r => {
            pic.imageDelLoading = false
            this.$store.dispatch('getMyUploads')
          })
          .catch(error => {
            pic.imageEditLoading = false
            // console.log(error.response.data)
            console.log(error)
          })

      },
    }
  }

</script>
<style>
  .imgPositioning {
    text-align: center;
    margin-top: -40px;
  }

  .imageUploader {
    padding: 20px;
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
