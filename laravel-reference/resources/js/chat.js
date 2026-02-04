import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import axios from 'axios';

const echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_SECRET,
    wsHost:
      process.env.MIX_PUSHER_HOST ??
      `ws-${process.env.MIX_PUSHER_APP_CLUSTER}.pusher.com`,
    wsPort: process.env.MIX_PUSHER_PORT ?? 80,
    wssPort: process.env.MIX_PUSHER_PORT ?? 443,
    forceTLS: (process.env.MIX_PUSHER_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    encrypted: true,
    authEndpoint: '/broadcasting/auth',
  });

  const date = new Date();


  let users_count = 0;


  if(document.readyState !== 'loading') {
    initChatRoom();
    initTextArea();
  }

function initTextArea(){
    const textarea = document.querySelector("#message");
    const throttleTime = 200; // 0.2 seconds
    const limit = 120;
    const messageContainer = document.querySelector('.chat');
    const room_id = messageContainer.dataset.chatId;
    let canPublish = true;

    textarea.addEventListener("input", function() {
        textarea.style.height = "";
        textarea.style.height = Math.min(textarea.scrollHeight, limit) + "px";
    });

    textarea.addEventListener("keyup", function(){
        if(canPublish){
            axios.post(
                expertTypingRoute,
                {
                    room_id,
                },
                {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'X-Socket-ID': echo.socketId(),
                    },
                }
            );

            canPublish = false;
            setTimeout(function(){
                canPublish = true;
            }, throttleTime);
        }
    });
}


  function initChatRoom(){
    const chatContainer = document.querySelector('.chat');

    echo
    .join(`chat-therapy.${chatContainer.dataset.chatId}`) // Join the presence channel
    .here((users) => {
        // Called when successfully subscribed to the channel and receive a list of users
        users_count = users.length;
    })
    .joining((user) => { // Called when a new user joins the channel
        users_count++;
    })
    .leaving((user) => { // Called when a user leaves the channel
        users_count--;
    })
    .listen('ChatTherapyMessageCreated', (event) => {
        const message = event.message;

        const messageElement = document.createElement('div');

        messageElement.classList.add(
            'clientMessage',
        );

        messageElement.innerHTML = `
            <span>
            ${new Date(date.getTime() - (date.getTimezoneOffset() * 60000)).toJSON().replace('T', ' ').slice(0, -8)}
            </span>
            <p class="text-left">
                ${message.replace(/\n/g, '<br>')}
            </p>
        `;

        chatContainer.insertBefore(messageElement, chatContainer.firstChild);
    })
    .listen('ClientStartedTyping', (event) => {
        showExpertTyping();
    }).listenForWhisper('ClientStartedTyping', () => {
        showExpertTyping();
    });

    const form = document.querySelector('#message-form');

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        sendMessage();
    });
  }

  function showExpertTyping(){
    const clearInterval = 2000;
    let clearTimerId;

    document.getElementById('client-is-typing').innerHTML = 'Client is typing...';

    //restart timeout timer
    clearTimeout(clearTimerId);

    clearTimerId = setTimeout(function () {
        //clear user is typing message
        document.getElementById('client-is-typing').innerHTML = '';
    }, clearInterval);
  }

  function sendMessage(){
    const message = document.querySelector('#message').value;
    const messageContainer = document.querySelector('.chat');
    const room_id = messageContainer.dataset.chatId;
    document.querySelector("#message").style.height = "";

    if (message.trim() === '') {
        return;
    }

    const messageElement = document.createElement('div');

    messageElement.classList.add(
        'expertMessage'
    );

    messageElement.classList.add('align-items-end');

    messageElement.innerHTML = `
        <span>
            ${new Date(date.getTime() - (date.getTimezoneOffset() * 60000)).toJSON().replace('T', ' ').slice(0, -8)}
        </span>
        <p class="text-right">
            ${message.replace(/\n/g, '<br>')}
        </p>
    `;

    messageContainer.insertBefore(messageElement, messageContainer.firstChild);

    document.querySelector('#message').value = '';

    axios.post(
        createMessageRoute,
        {
            message,
            room_id,
            users_count
        },
        {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-Socket-ID': echo.socketId(),
            },
        }
    );
}

// CHAT BUTTON EVENTS START
const endButton = document.getElementById('end_button');
const sendButton = document.getElementById('send_message');

document.addEventListener('DOMContentLoaded', function () {
    sendButton.addEventListener('click', sendMessage);
    endButton.addEventListener('click', endTherapy);
});

function endTherapy() {
  set_booking_end_date(room_id).then(function() {
    window.close();
  });
}

// Set the end of the consultation and delete chat messages
async function set_booking_end_date(room_id)
{
    await axios.post('/ajax/end-chat-therapy', {
        room_id: room_id,
   });
}
// CHAT BUTTON EVENTS END
