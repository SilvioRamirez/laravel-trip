<template>
    <div class="pt-16">
        <h1 class="text-3xl font-semibold mb-4">Ingresa tu número de telefono</h1>
        <form v-if="!waitingOnVerification" action="#" @submit.prevent="handleLogin">
            <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                <div class="bg-white px-4 py-5 sm:p-6">
                    <div>
                        <input type="text" v-maska data-maska="+ (##) ###-#######" v-model="credentials.phone" name="phone" id="phone" placeholder="+ (##) ###-#######"
                            class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-black focus:outline-none">
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                    <button type="submit" @submit.prevent="handleLogin"
                        class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">
                        Continuar
                    </button>
                </div>
            </div>
        </form>
        <form v-else action="#" @submit.prevent="handleVerification">
            <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                <div class="bg-white px-4 py-5 sm:p-6">
                    <div>
                        <input type="text" v-maska data-maska="######" v-model="credentials.login_code" name="login_code" id="login_code" placeholder="123456"
                            class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-black focus:outline-none">
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                    <button type="submit" @submit.prevent="handleVerification"
                        class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">
                        Verificar
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>

//La palabra escript setup nos permite utilizar informacion de este componente a otro
<script setup>

import { vMaska } from 'maska' //funciona para hacer un mask en los input
import { reactive, ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router';

const router = useRouter()

const credentials = reactive({
    phone: null,
    login_code: null
})

const waitingOnVerification = ref(false)

//se utiliza para guardar o comprobar el estado anterior de la aplicacion para que de esta manera el usuario no deba hacer el proceso de autenticacion cada vez que abre la aplicacion
//se utiliza esta verificacion al momento de q carga la app(mounted)
onMounted(() => {
    if (localStorage.getItem('token')) {
        router.push({
            name: 'landing'
        })
    }
})

//Computed propieti permite crear propiedades reactivas acorde a propiedades reactivas de los compnentes y su contenido, se actualiz todo en tiempo real

const getFormattedCredentials = () => {
    return {
        phone: credentials.phone.replaceAll(' ', '').replace('(', '').replace(')', '').replace('-', ''),
        login_code: credentials.login_code
    }
}


const handleLogin = () => {
    //console.log(credentials.phone)
    //se envia a la direccion del host donde esta la api y se envie el objeto reactivo credentials, el then es el happy response, si el servidor tiene una respuesta 200
    //catch error nos sirve para capturar cualquier error que venga del servidor con su respectivo mensaje
    //en la direccion de acios http debemos asegurarnos de colocar la misma direccion que probamos en postman
    axios.post('http://127.0.0.1:8000/api/login', getFormattedCredentials())
    .then((response) => {
            console.log(response.data)
            waitingOnVerification.value = true
    })
    .catch((error) => {
        console.error(error)
        alert(error.response.data.message)
    })
}

const handleVerification = () => {
    axios.post('http://127.0.0.1:8000/api/login/verify', getFormattedCredentials())
        .then((response) => {
            //Este es el token de autenticacion, lo guardamos en localstorage para que este disponible en toda la pagina
            console.log(response.data) // auth token
            localStorage.setItem('token', response.data)
            router.push({
                name: 'landing'
            })
        })
        .catch((error) => {
            console.error(error)
            alert(error.response.data.message)
        })
}

</script>

<style>
</style>