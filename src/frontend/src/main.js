import { createApp } from 'vue'
import { createPinia } from 'pinia'


import App from './App.vue'
import router from './router'

//se coloca aqui los paquetes de terceros para tener mas organizacion en el codigo
import VueGoogleMaps from '@fawmi/vue-google-maps'
/* import VueGoogleMaps from '@fawmi/vue-google-maps/src/main.js'; */


import './assets/main.css'

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.use(VueGoogleMaps, {
    load: {
        key: 'AIzaSyCAqdwRPpTtDGc6lWZKlSO0EPgkAKRo-8o',
        libraries: 'places'
    },
})

app.mount('#app')
