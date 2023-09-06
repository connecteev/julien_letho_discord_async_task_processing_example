<template>
    <div class="p-8">
        <button @click="startAsyncTask()" :class="buttonClass" :disabled="isButtonDisabled">
            {{ buttonText }}
        </button>
        <div class="py-8">
            Task id: {{ taskClone.id }}<br>
            Name: {{ taskClone.name }}<br>
            Started: {{ taskClone.job_started }}<br>
            Progress: {{ taskClone.progress }}%<br>
            Completed: {{ taskClone.job_completed }}<br>
            Output: <br>
            <span v-html="taskClone.output"></span><br>
        </div>
        <div class="w-1/12">
            <CircleProgressBar :value="taskClone.progress" :max="100" colorUnfilled="pink" colorFilled="red" percentage rounded>
                <div class="text-xs font-bold text-gray-400">{{ taskClone.progress }} / 100</div>
            </CircleProgressBar>
        </div>

    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { CircleProgressBar } from 'vue3-m-circle-progress-bar';

const props = defineProps({
    task: {
        type: Object
    }
});

const taskClone = ref({...props.task});

const startAsyncTask = () => {
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
let isJobRunning = ref(false);
let isJobStarted = ref(props.task.job_started);
let isJobCompleted = ref(props.task.job_completed);
let buttonText = ref('');
const buttonClass = ref('');
let isButtonDisabled = ref(false);

const updateStatusTaskReady = () => {
    isJobRunning.value = false;
    isJobCompleted.value = false;
    isButtonDisabled.value = false;
    buttonText.value = 'Start Async Task';
    buttonClass.value = 'bg-blue-500 opacity-100';
};

const updateStatusTaskRunning = () => {
    isJobRunning.value = true;
    isJobCompleted.value = false;
    isButtonDisabled.value = true;
    buttonText.value = 'Task running...';
    buttonClass.value = 'bg-gray-500 opacity-50 cursor-not-allowed';
};

const updateStatusTaskCompleted = () => {
    isJobRunning.value = false;
    isJobCompleted.value = true;
    isButtonDisabled.value = true;
    buttonText.value = 'Task Completed';
    buttonClass.value = 'bg-red-500 opacity-50 cursor-not-allowed';
};

if (isJobCompleted.value) {
    updateStatusTaskCompleted();
} else {
    if (isJobStarted.value) {
        updateStatusTaskRunning();
    } else {
        updateStatusTaskReady();
    }
}

const startPolling = () => {
    interval = setInterval(() => {
        /*router.reload({
            only: ['task']
        })*/

        updateStatusTaskRunning();
        axios
            .get(route('task.status', props.task.id))
            .then(res => {
                if (res.status !== 200) return;

                taskClone.value = res.data;

                if (res.data.job_completed) {
                    updateStatusTaskCompleted();
                    clearInterval(interval);
                    return;
                }
            })
    }, 1000);
};
</script>

<style>
button {
    @apply px-3 py-2 rounded-md text-center text-white;
}
.circle-progress__percentage {
    @apply font-bold text-2xl text-gray-500;
}
</style>
