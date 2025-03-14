<x-layouts.admin>
    <div class="chat-container">
        <div class="chat-header">
            <h1 class="h4 mb-0">Chat with {{ $receiver->name }}</h1>
        </div>
        <div id="chat-box" class="chat-box">
            @foreach ($messages as $message)
                <div class="message {{ $message->sender_id == auth()->id() ? 'sent' : 'received' }}">
                    <div class="message-content">
                        {{ $message->message }}
                    </div>
                </div>
            @endforeach
        </div>
        <form id="message-form" class="message-form">
            <div id="typing-indicator" class="typing-indicator">
                {{ $receiver->name }} is typing <span class="dots"><span></span><span></span><span></span></span>
            </div>
            @csrf
            <div class="input-group">
                <input type="text" id="message-input" class="form-control" placeholder="Type a message...">
                <button type="submit" class="btn btn-primary btn-send">Send</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let receiverId = {{ $receiver->id }};
            let senderId = {{ auth()->id() }};
            let chatBox = document.getElementById('chat-box');
            let messageForm = document.getElementById('message-form');
            let messageInput = document.getElementById('message-input');
            let typingIndicator = document.getElementById('typing-indicator');

            // Function to scroll to bottom
            function scrollToBottom() {
                chatBox.scrollTop = chatBox.scrollHeight;
            }

            // Scroll to bottom on page load
            scrollToBottom();

            // Hide typing indicator on scroll
            chatBox.addEventListener('scroll', function() {
                if (typingIndicator.style.display === 'flex') {
                    typingIndicator.style.display = 'none';
                }
            });

            // Set user online
            axios.post('/chat/online', {}, {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).catch(error => console.error('Error setting user online:', error));

            // Subscribe to chat channel
            window.Echo.private('chat.' + senderId)
                .listen('MessageSent', (e) => {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'message received';
                    messageDiv.innerHTML = `<div class="message-content">${e.message.message}</div>`;
                    chatBox.appendChild(messageDiv);
                    scrollToBottom();
                });

            // Subscribe to typing channel
            window.Echo.private('typing.' + receiverId)
                .listen('UserTyping', (e) => {
                    if (e.typerId === receiverId) {
                        typingIndicator.style.display = 'flex';
                        typingIndicator.style.alignItems = 'center';
                        setTimeout(() => {
                            if (typingIndicator.style.display === 'flex') {
                                typingIndicator.style.display = 'none';
                            }
                        }, 5000);
                    }
                });

            // Send message
            messageForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const message = messageInput.value;
                if (message) {
                    axios.post(`/chat/${receiverId}/send`, {
                        message
                    }, {
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(() => {
                        const messageDiv = document.createElement('div');
                        messageDiv.className = 'message sent';
                        messageDiv.innerHTML = `<div class="message-content">${message}</div>`;
                        chatBox.appendChild(messageDiv);
                        scrollToBottom();
                        messageInput.value = '';
                    }).catch(error => console.error('Error sending message:', error));
                }
            });

            // Typing indicator
            let typingTimeOut;
            messageInput.addEventListener('input', function() {
                clearTimeout(typingTimeOut);
                axios.post(`/chat/typing`, {}, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).catch(error => console.error('Error sending typing indicator:', error));
                typingTimeOut = setTimeout(() => {
                    if (typingIndicator.style.display === 'flex') {
                        typingIndicator.style.display = 'none';
                    }
                }, 5000);
            });

            // Set user offline on window close
            window.addEventListener('beforeunload', function() {
                axios.post('/chat/offline', {}, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).catch(error => console.error('Error setting user offline:', error));
            });
        });
    </script>
</x-layouts.admin>
