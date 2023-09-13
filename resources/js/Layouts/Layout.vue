<template>
</template>

<script setup>
import { onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({})

const user = usePage().props.auth.user

onMounted(() => {
    window.Echo
          .private('update.available.' + user.id)
          .listen('UpdateProfileInformation', e => {
              console.log(e)
          })
})
</script>
