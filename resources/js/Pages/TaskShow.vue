<template>
    <button @click="start()">Start</button>
    <div>
        <p v-for="update in updatesReceived" :key="update">
            {{ update }}
        </p>
    </div>
</template>

<script setup>

import { router, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    task: Object
})

const user = usePage().props.auth.user

// Public channel

window.Echo
      .channel('public')
      .listen('UpdateProgress', e => {
          updatesReceived.value.push(e.message)
      })

// Private Channel
window.Echo
      .private('private.' + props.task.id)
      .listen('UpdateProgress', e => {
          updatesReceived.value.push(e.message)
      })

const start = () => {
    router.post(route('task.start', props.task.id))
}

const updatesReceived = ref([])
</script>
