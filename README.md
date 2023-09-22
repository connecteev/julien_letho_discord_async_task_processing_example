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
- One-time setup:
  - Publish the config/websockets.php config file using:
    - php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"
  - Make sure you have dummy pusher config set in your .env file. Also, make sure the host, port, scheme are set correctly.
      PUSHER_APP_ID=dummy_pusher_app_id
      PUSHER_APP_KEY=dummy_pusher_app_key
      PUSHER_APP_SECRET=dummy_pusher_app_secret
      PUSHER_HOST=localhost
      PUSHER_PORT=6001
      PUSHER_SCHEME=http
      PUSHER_APP_CLUSTER=mt1
  - Note: We needed to run `composer require react/promise:^2.3` to fix some console exceptions that we were seeing in the `php artisan websockets:serve` console.
- php artisan serve (or use Laravel Herd)
- npm run dev
- php artisan queue:clear
- php artisan queue:work
- php artisan queue:work --queue=broadcasting
- php artisan queue:clear --queue=tasks
- php artisan queue:work --queue=tasks
- php artisan websockets:serve
- Login as jj@gmail.com / password
- Chrome Tab 1: Go to http://julien_letho_discord_async_task_processing_example.test/show/3
In the console, you see:
```
Connection id 235810010.930042050 sending message {"event":"pusher:connection_established","data":"{\"socket_id\":\"235810010.930042050\",\"activity_timeout\":30}"}
dummy_pusher_app_id: connection id 235810010.930042050 received message: {"event":"pusher:subscribe","data":{"auth":"","channel":"public"}}.
Connection id 235810010.930042050 sending message {"event":"pusher_internal:subscription_succeeded","channel":"public"}
dummy_pusher_app_id: connection id 235810010.930042050 received message: {"event":"pusher:subscribe","data":{"auth":"dummy_pusher_app_key:14029be0fb2de98b2634dc1d4eadd4104c3b855fb2a5a7ce232505edcf665c06","channel":"private-private.3"}}.
Connection id 235810010.930042050 sending message {"event":"pusher_internal:subscription_succeeded","channel":"private-private.3"}
dummy_pusher_app_id: connection id 235810010.930042050 received message: {"event":"pusher:subscribe","data":{"auth":"dummy_pusher_app_key:f975f8673679e9880230ef592e79079c390ae412f33ad2d1bcd13ee2850d9ef9","channel":"private-update.available.1"}}.
Connection id 235810010.930042050 sending message {"event":"pusher_internal:subscription_succeeded","channel":"private-update.available.1"}
```

- Chrome Tab 2: Go to http://julien_letho_discord_async_task_processing_example.test/test2
In the console, you see:
```
Connection id 235810010.930042050 sending message {"channel":"private-update.available.1","event":"App\\Events\\UpdateProfileInformation","data":"{\"queue\":\"broadcasting\"}"}
```
Also, you see `{queue: 'broadcasting'}` in the console logs for the /show/3 chrome tab

Note:
/show/3 just connects to the websockets server and listens for the event
/test2 dispatches the event

