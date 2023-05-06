import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '@/views/LoginView.vue'
import LandingView from '@/views/LandingView.vue'
import LocationView from '@/views/LocationView.vue'
import MapView from '@/views/MapView.vue'
import axios from 'axios'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'login',
            component: LoginView
            },
        {
            path: '/landing',
            name: 'landing',
            component: LandingView
        },
        {
            path: '/location',
            name: 'location',
            component: LocationView
        },
        {
            path: '/map',
            name: 'map',
            component: MapView
        }
    ]
})

//esto se hace para que cada vez que entre a la aplicacion verifique el logeo del usuario 
router.beforeEach((to, from) => {
    if (to.name = 'login'){
        return true
    }

    if (!localStorage.getItem('token')) {
        return {
            name: 'login'
        }
    }

    //revisa la autenticidad del token y sino es correcta lo remueve
    checkTokenAuthenticity()
})

const checkTokenAuthenticity = () => {
    axios.get('http://127.0.0.1:8000/api/user', {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
        }

    })
    .then((response) => {})
    .catch((error) => {
        localStorage.removeItem('token')
        router.push({
            name: 'login'
        })
    })
}

export default router
