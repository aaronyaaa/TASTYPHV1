document.addEventListener("DOMContentLoaded", () => {
  const chatBox = document.getElementById("chatBox");
  const chatToggle = document.getElementById("chatToggle");
  const closeChat = document.getElementById("closeChat");
  const senderId = document.getElementById("currentUserId")?.value;
  const receiverInputId = document.getElementById("receiverId")?.value;
  const receiverInputName = document.getElementById("receiverName")?.value;
  const chatThread = document.getElementById("chatThread");
  const chatInput = document.getElementById("chatMessageInput");
  const sendBtn = document.getElementById("chatSendBtn");
  const userList = document.getElementById("userList");

  window.currentReceiverId = "";

window.openChatWithUser = function (id, name) {
    if (!id) return;

    window.currentReceiverId = id;
    chatBox.style.display = "flex";
    chatBox.classList.remove("hide");
    chatBox.classList.add("show");
    chatInput.disabled = false;
    sendBtn.disabled = false;

    document.querySelectorAll("#userList li").forEach((li) => {
      li.classList.remove("active");
      if (li.dataset.userId === id) li.classList.add("active");
    });

    loadMessages();
    markMessagesAsRead(id);
  }

  function closeChatBox() {
    chatBox.classList.remove("show");
    chatBox.classList.add("hide");
    setTimeout(() => {
      chatBox.style.display = "none";
      chatThread.innerHTML = `<div class="text-muted small text-center mt-3">Select a user to start chatting</div>`;
      chatInput.disabled = true;
      sendBtn.disabled = true;
      window.currentReceiverId = "";
    }, 400);
  }

  function loadMessages() {
    if (!window.currentReceiverId) return;

    fetch(`../backend/chat/fetch_messages.php?receiver_id=${window.currentReceiverId}`)
      .then((res) => res.json())
      .then((data) => {
        chatThread.innerHTML = "";
        if (!data.length) {
          chatThread.innerHTML = '<div class="text-muted small text-center mt-3">No messages yet</div>';
        } else {
          data.forEach((msg) => {
            const div = document.createElement("div");
            div.className = msg.sender_id == senderId ? "message-outgoing" : "message-incoming";
            div.innerHTML = `
              <p class="mb-1 small">${msg.message_text}</p>
              <div class="text-end text-muted small">${msg.sent_at}</div>
            `;
            chatThread.appendChild(div);
          });
          chatThread.scrollTop = chatThread.scrollHeight;
        }
      });
  }

  function sendMessage() {
    const text = chatInput.value.trim();
    if (!text || !senderId || !window.currentReceiverId) return;

    const payload = {
      sender_id: senderId,
      receiver_id: window.currentReceiverId,
      message_text: text,
    };

    fetch("../backend/chat/send_message.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    })
      .then((res) => res.json())
      .then((response) => {
        if (response.success) {
          chatInput.value = "";
          loadMessages();
        } else {
          console.error("Message failed:", response.error, response.details ?? "");
        }
      })
      .catch((err) => console.error("Request failed:", err));
  }

  chatToggle?.addEventListener("click", () => {
    if (chatBox.classList.contains("show")) closeChatBox();
    else {
      chatBox.style.display = "flex";
      chatBox.classList.remove("hide");
      chatBox.classList.add("show");
      if (receiverInputId && receiverInputName)
        openChatWithUser(receiverInputId, receiverInputName);
    }
  });

  closeChat?.addEventListener("click", closeChatBox);
  sendBtn?.addEventListener("click", sendMessage);
  chatInput?.addEventListener("keypress", (e) => {
    if (e.key === "Enter") sendMessage();
  });

  document.body.addEventListener("click", (e) => {
    const btn = e.target.closest(".open-chat-btn");
    if (btn) {
      e.preventDefault();
      openChatWithUser(btn.dataset.userId, btn.dataset.userName);
    }
  });

  if (receiverInputId && receiverInputName) openChatWithUser(receiverInputId, receiverInputName);

  setInterval(() => {
    if (window.currentReceiverId) loadMessages();
  }, 3000);

  function loadChatUserList() {
    fetch("../backend/chat/fetch_chat_users.php")
      .then((res) => res.json())
      .then((users) => {
        userList.innerHTML = "";
        let totalUnread = 0;

        if (!Array.isArray(users) || users.length === 0) {
          userList.innerHTML = '<li class="text-muted small px-2">No chats yet</li>';
          return;
        }

        users.forEach((user) => {
          const li = document.createElement("li");
          li.classList.add("chat-user-entry");
          li.dataset.userId = user.id;

          const avatar = user.profile_pics ? `../${user.profile_pics}` : "../assets/images/default-profile.png";
          li.innerHTML = `
            <a href="#" class="text-decoration-none d-flex align-items-center gap-2 open-chat-btn" 
               data-user-id="${user.id}" 
               data-user-name="${user.first_name} ${user.last_name}">
              <img src="${avatar}" alt="profile" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
              <div>
                <div class="fw-semibold">${user.first_name} ${user.last_name}</div>
                <small class="text-muted">Chat</small>
              </div>
            </a>
          `;

          if (user.unread_count > 0) {
            const badge = document.createElement("span");
            badge.id = `userBadge-${user.id}`;
            badge.className = "badge bg-danger ms-1";
            badge.textContent = user.unread_count;
            li.querySelector("a").appendChild(badge);
            totalUnread += parseInt(user.unread_count);
          }

          userList.appendChild(li);
        });

        // Update global badge
        const globalBadge = document.querySelector("#chatToggle .badge");
        if (totalUnread > 0) {
          if (!globalBadge) {
            const span = document.createElement("span");
            span.className = "position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger";
            span.textContent = totalUnread;
            document.querySelector("#chatToggle button").appendChild(span);
          } else {
            globalBadge.textContent = totalUnread;
          }
        } else if (globalBadge) {
          globalBadge.remove();
        }
      })
      .catch((err) => console.error("Failed to load chat users:", err));
  }

  function markMessagesAsRead(senderId) {
    fetch("../backend/chat/mark_as_read.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `senderId=${encodeURIComponent(senderId)}`
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const badge = document.querySelector(`#userBadge-${senderId}`);
          if (badge) badge.remove();
          updateGlobalBadge();
        }
      });
  }

  function updateGlobalBadge() {
    const allBadges = document.querySelectorAll(".chat-user-entry .badge");
    const globalBadge = document.querySelector("#chatToggle .badge");

    if (allBadges.length === 0) {
      if (globalBadge) globalBadge.remove();
    } else {
      const total = Array.from(allBadges).reduce((sum, el) => sum + parseInt(el.textContent || "0"), 0);
      if (globalBadge) globalBadge.textContent = total;
    }
  }

  // Initial load
  const initialUnreadCount = parseInt(document.getElementById("globalUnreadCount")?.value || "0");
  if (initialUnreadCount > 0) {
    const span = document.createElement("span");
    span.className = "position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger";
    span.textContent = initialUnreadCount;
    document.querySelector("#chatToggle button").appendChild(span);
  }

  // Real-time refresh
  loadChatUserList();
  setInterval(loadChatUserList, 1000); // every second
});
