import { ref, computed, reactive } from 'vue'
import { defineStore } from 'pinia'

//Funcion de localizacion del usuario asyncrona esto permite que la aplicacion no haga mas nada sino tiene la localizacion del usuarioe establecida
//response, reject

const getUserLocation = async () => {
    return new Promise ((res, rej) => {
        navigator.geolocation.getCurrentPosition(res, rej)
    })
}


//El manejador de estados pinia nos funciona para utilizar valores de una vista a otra, q por ejemplo el valor de la localizacion de la ubicacion este disponible en otra vista y que al podamos utilizar sin problemas
export const useLocationStore = defineStore('location', () => {
    
    const destination = reactive({
        name: '',
        address: '',
        geometry: {
            lat: null,
            lng: null,
        }
    })

    const current = reactive({
        geometry: {
            lat: null,
            lng: null
        }
    })

    //con esto se utiliza  la funcion de arriba para llenar el objeto current de pinia, de esta manera debe estar disponible en toda la aplicacion
    const updateCurrentLocation = async() => {
        const userLocation = await getUserLocation()
        current.geometry = {
            lat: userLocation.coords.latitude,
            lng: userLocation.coords.longitude
        }
    }


    return { destination, current, updateCurrentLocation }
})
