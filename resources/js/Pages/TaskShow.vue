<template>
    <div class="p-8">
        <button @click="startRendering()">Start Async Task</button>
        <div class="py-8">
            Name: {{ taskClone.name }}<br>
            Started: {{ taskClone.job_started }}<br>
            Progress: {{ taskClone.progress }}<br>
            Completed: {{ taskClone.job_completed }}<br>
        </div>
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    task: {
        type: Object
    }
})

const taskClone = ref({...props.task});

const startRendering = () => {
    router.post(route('task.start', props.task.id), null, {
        onSuccess: () => {
            startPolling();
        },
        onError: error => {
            console.log(error);
        }
    });
};

let interval;

const startPolling = () => {
    interval = setInterval(() => {
        /*router.reload({
            only: ['task']
        })*/

        axios
            .get(route('task.status', props.task.id))
            .then(res => {
                if (res.status !== 200) return

                if (res.data.job_completed) {
                    clearInterval(interval);
                    return;
                }

                taskClone.value = res.data;
            })
    }, 1000);
};
</script>

<style>
button {
    @apply px-3 py-2 rounded-md bg-blue-500 text-center text-white
}
</style>
