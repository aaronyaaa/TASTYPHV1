<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Chat Widget Replica</title>
<style>
  body {
    font-family: Arial, sans-serif;
  }
  /* Floating chat container */
  .chat-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 280px;
    font-size: 14px;
    z-index: 9999;
    user-select: none;
  }

  /* Chat button with name and toggle */
  .chat-widget__button {
    background: #ff5722;
    color: white;
    border-radius: 24px;
    padding: 10px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(255, 87, 34, 0.3);
  }

  .chat-widget__button .name {
    font-weight: bold;
  }

  .chat-widget__button .toggle-more {
    background: rgba(255,255,255,0.3);
    border-radius: 50%;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 18px;
    user-select: none;
    transition: background 0.3s;
  }
  .chat-widget__button .toggle-more:hover {
    background: rgba(255,255,255,0.6);
  }

  /* Chat window panel hidden by default */
  .chat-widget__panel {
    margin-top: 8px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    display: none;
    flex-direction: column;
    height: 320px;
  }

  .chat-widget__panel.open {
    display: flex;
  }

  /* Chat header */
  .chat-widget__header {
    background: #ff5722;
    color: white;
    padding: 12px 16px;
    border-radius: 12px 12px 0 0;
    font-weight: bold;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .chat-widget__header .close-btn {
    cursor: pointer;
    font-size: 20px;
    user-select: none;
  }

  /* Messages area */
  .chat-widget__messages {
    flex: 1;
    padding: 12px 16px;
    overflow-y: auto;
    background: #fff4eb;
  }

  /* Single message bubble */
  .chat-widget__message {
    margin-bottom: 10px;
    max-width: 80%;
    padding: 8px 12px;
    border-radius: 16px;
    clear: both;
    word-wrap: break-word;
  }
  .chat-widget__message.user {
    background: #ffccbc;
    margin-left: auto;
    border-bottom-right-radius: 4px;
  }
  .chat-widget__message.agent {
    background: #ffe0b2;
    margin-right: auto;
    border-bottom-left-radius: 4px;
  }

  /* Input area */
  .chat-widget__input-area {
    padding: 8px 12px;
    border-top: 1px solid #eee;
    display: flex;
    gap: 8px;
  }
  .chat-widget__input-area input {
    flex: 1;
    padding: 8px 12px;
    border-radius: 24px;
    border: 1px solid #ddd;
    outline: none;
    font-size: 14px;
  }
  .chat-widget__input-area button {
    background: #ff5722;
    border: none;
    color: white;
    padding: 0 16px;
    border-radius: 24px;
    cursor: pointer;
    font-weight: bold;
    font-size: 14px;
    transition: background 0.3s;
  }
  .chat-widget__input-area button:hover {
    background: #e64a19;
  }

  /* Scrollbar styling for messages */
  .chat-widget__messages::-webkit-scrollbar {
    width: 6px;
  }
  .chat-widget__messages::-webkit-scrollbar-thumb {
    background: #ff5722aa;
    border-radius: 3px;
  }
</style>
</head>
<body>

<div class="chat-widget" aria-label="Chat widget" role="region" aria-live="polite">
  <div class="chat-widget__button" tabindex="0" aria-expanded="false" aria-controls="chatPanel" role="button">
    <div class="name">Seller Chat</div>
    <div class="toggle-more" aria-label="Toggle chat panel" title="More">⋮</div>
  </div>
  <div class="chat-widget__panel" id="chatPanel" role="region" aria-hidden="true">
    <div class="chat-widget__header">
      Chat with Seller
      <div class="close-btn" aria-label="Close chat panel" title="Close">&times;</div>
    </div>
    <div class="chat-widget__messages" role="log" aria-live="polite" aria-relevant="additions">
      <div class="chat-widget__message agent">Hi! How can I help you today?</div>
    </div>
    <form class="chat-widget__input-area" onsubmit="return false;">
      <input type="text" aria-label="Type your message" placeholder="Type a message..." />
      <button type="submit" aria-label="Send message">Send</button>
    </form>
  </div>
</div>

<script>
  const chatWidget = document.querySelector('.chat-widget');
  const toggleBtn = chatWidget.querySelector('.toggle-more');
  const chatPanel = chatWidget.querySelector('.chat-widget__panel');
  const closeBtn = chatWidget.querySelector('.close-btn');
  const messagesContainer = chatWidget.querySelector('.chat-widget__messages');
  const inputField = chatWidget.querySelector('.chat-widget__input-area input');
  const sendBtn = chatWidget.querySelector('.chat-widget__input-area button');
  const chatButton = chatWidget.querySelector('.chat-widget__button');

  // Toggle chat panel open/close
  function toggleChat(open) {
    if (open === undefined) open = !chatPanel.classList.contains('open');
    chatPanel.classList.toggle('open', open);
    chatButton.setAttribute('aria-expanded', open);
    chatPanel.setAttribute('aria-hidden', !open);
    if (open) inputField.focus();
  }

  toggleBtn.addEventListener('click', e => {
    e.stopPropagation();
    toggleChat();
  });

  closeBtn.addEventListener('click', () => toggleChat(false));

  // Send message function (just echo for demo)
  function sendMessage() {
    const text = inputField.value.trim();
    if (!text) return;
    // Append user message
    const userMsg = document.createElement('div');
    userMsg.className = 'chat-widget__message user';
    userMsg.textContent = text;
    messagesContainer.appendChild(userMsg);

    // Scroll to bottom
    messagesContainer.scrollTop = messagesContainer.scrollHeight;

    inputField.value = '';

    // Demo: reply after 1 second
    setTimeout(() => {
      const reply = document.createElement('div');
      reply.className = 'chat-widget__message agent';
      reply.textContent = 'Thanks for your message! We will get back to you soon.';
      messagesContainer.appendChild(reply);
      messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }, 1000);
  }

  sendBtn.addEventListener('click', sendMessage);
  inputField.addEventListener('keydown', e => {
    if (e.key === 'Enter') {
      e.preventDefault();
      sendMessage();
    }
  });

  // Close chat if clicking outside
  document.addEventListener('click', e => {
    if (!chatWidget.contains(e.target)) {
      toggleChat(false);
    }
  });
</script>

</body>
</html>
