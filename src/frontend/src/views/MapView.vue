<template>
    <div class="pt-16">
        <h1 class="text-3xl font-semibold mb-4">Este es tu viaje</h1>
            <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                <div class="bg-white px-4 py-5 sm:p-6">
                    <div>
                        <GMapMap 
                            v-if="location.destination.name !== ''" 
                            :zoom="11" 
                            :center="location.destination.geometry"
                            ref="gMap"
                            style="width: 100%; height: 256px;"
                            >
                            <!-- <GMapMarker :position="location.destination.geometry" /> -->
                        </GMapMap>
                    </div>

                    <div class="mt-2">
                        <p class="text-xl">En vía a <strong>{{ location.destination.name }}</strong></p>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                    <button type="button"
                        
                        class="rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">
                        ¡Comenzar!
                    </button>
                </div>
            </div>


    </div>
</template>
<script setup>

import { useLocationStore } from '@/stores/location'
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';

const location = useLocationStore()
const router = useRouter()

//se crea esta variable para saber q el mapa esta listo antes de dibujar una ruta
const gMap = ref(null)

//Mounted metod de vue, todo lo que se dispare aqui se hara apenas se monte el componente en vue, aqui vamos a realizar los primeros pasos para realizar el calculo de ruta del punto a al punto b
onMounted(async () => {

    //el usuario tiene una localizacion ya?
    if (location.destination.name === '') {
        router.push({
            name: 'location'
        })
    }

    //localizacion actual
    //al principio sale error en await, por eso se le coloca async en mounted
    await location.updateCurrentLocation()

    /* navigator.geolocation.getCurrentPosition((success) => {
        console.log(success)
    }, (error) => {
        console.log(error)
    }) */

    //dibujar una ruta en el mapa, mappromise nos consta que el objeto del mapa esta cargado completamente y lo podemos utiliar
    gMap.value.$mapPromise.then((mapObject) => {
        //se deben tener dos puntos, eeste es el actual
        let currentPoint = new google.maps.LatLng(location.current.geometry),
            //este es el destino
            destinationPoint = new google.maps.LatLng(location.destination.geometry),
            //dos variables mas, esta calcula
            directionsService = new google.maps.DirectionsService,
            //esta muestra en el mapa
            directionsDisplay = new google.maps.DirectionsRenderer({
                map: mapObject
            })
            
            directionsService.route({
                origin: currentPoint,
                destination: destinationPoint,
                avoidTolls: false,
                avoidHighways: false,
                travelMode: google.maps.TravelMode.DRIVING
            }, (res, status) => {
                if (status == google.maps.DirectionsStatus.OK) {
                    directionsDisplay.setDirections(res)
                }else{
                    console.error(status)
                }
            })
            
    })
})

</script>