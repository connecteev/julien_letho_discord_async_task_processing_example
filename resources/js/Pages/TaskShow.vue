<template>
    <button @click="start()">Start</button>
    <div>

        <p v-for="o in output" :key="o">
            Created: [{{ o.created }}] | Message: {{ o.message }}
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
          console.log(e)
      })

// Private Channel
window.Echo
      .private('private.' + props.task.id)
      .listen('UpdateProgress', e => {
          console.log(e)
          output.value = e.task.output
      })

const output = ref()

const start = () => {
    router.post(route('task.start', props.task.id))
}
</script>
