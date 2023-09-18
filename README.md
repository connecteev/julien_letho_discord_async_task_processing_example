## Original code by Julien (Letho on Discord): 
- Trigger start of an async command / task / job from the front-end
- Front-end logic to continuously poll and display the status of the command / task / job on the front-end
- Initial attempt (using a different approach that did not work) here: https://gist.github.com/connecteev/d5469df973fd67a7819a189a34723877

### Installation Steps
- composer install
- npm install
- cp .env.example .env
- php artisan key:generate
- php artisan migrate:fresh --seed
- npm run dev
- php artisan queue:work (important otherwise tasks will not get picked up and processed)
- Go to http://julien_letho_discord_async_task_processing_example.test/show/1 and trigger the async command / job by pressing the "Start Async Task" button.
- NOTE: Once a task is done, there is a check to make sure that a completed task cannot be started again. Clean the database To re-trigger it.

### TODO:
- Task live logs / streaming logs

### To get up and running with the websockets branch:
- php artisan serve
- npm run dev
- php artisan queue:clear
- php artisan queue:work
- php artisan queue:work --queue=broadcasting
- php artisan queue:clear --queue=tasks
- php artisan queue:work --queue=tasks
- php artisan websockets:serve
