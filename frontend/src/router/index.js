import Vue from 'vue'
import Router from 'vue-router'
import Main from '@/components/Main'
import Profile from '@/components/Profile'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'Main',
      component: Main
    },
    {
      path: '/profile',
      name: 'Profile',
      component: Profile
    }
  ]
})
