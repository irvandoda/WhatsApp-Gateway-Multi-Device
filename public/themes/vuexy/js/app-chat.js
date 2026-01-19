'use strict';

document.addEventListener('DOMContentLoaded', () => {
  const elements = {
    chatContactsBody: document.querySelector('.app-chat-contacts .sidebar-body'),
    chatHistoryBody: document.querySelector('.chat-history-body'),
    chatSidebarLeftBody: document.querySelector('.app-chat-sidebar-left .sidebar-body'),
    chatSidebarRightBody: document.querySelector('.app-chat-sidebar-right .sidebar-body'),
    chatUserStatus: Array.from(document.querySelectorAll(".form-check-input[name='chat-user-status']")),
    chatSidebarLeftUserAbout: document.getElementById('chat-sidebar-left-user-about'),
    formSendMessage: document.querySelector('.form-send-message'),
    messageInput: document.querySelector('.message-input'),
    searchInput: document.querySelector('.chat-search-input'),
    chatContactListItems: Array.from(document.querySelectorAll('.chat-contact-list-item:not(.chat-contact-list-item-title)')),
    textareaInfo: document.getElementById('textarea-maxlength-info'),
    conversationButton: document.getElementById('app-chat-conversation-btn'),
    chatHistoryHeader: document.querySelector(".chat-history-header [data-target='#app-chat-contacts']"),
    speechToText: document.querySelectorAll('.speech-to-text'),
    appChatConversation: document.getElementById('app-chat-conversation'),
    appChatHistory: document.getElementById('app-chat-history')
  };

  const headerNameEl = document.querySelector('.chat-history-header .fw-normal');
  const headerPhoneEl = document.querySelector('.chat-history-header .user-status');
  const headerAvatarEl = document.querySelector('.chat-history-header .avatar');
  
  const chatFooter = document.querySelector('.chat-history-footer');

  const rightSidebar = document.getElementById('app-chat-sidebar-right');
  const rightAvatarEl = rightSidebar.querySelector('.chat-sidebar-avatar');
  const rightNameEl = rightSidebar.querySelector('h5');
  const rightStatusEl = rightSidebar.querySelector('span');
  const rightAboutEl = rightSidebar.querySelector('.sidebar-body p.mb-0');
  const defaultRightAbout = rightAboutEl ? rightAboutEl.textContent : '';
  const viewContactBtn = document.getElementById('view-contact');
  
  const emojiBtn = document.getElementById('emoji-btn');
  const emojiPortal = document.getElementById('emoji-portal');
  let emojiPicker;

  const toggleAiBtn = document.getElementById('toggle-ai');
  const deleteConvBtn = document.getElementById('delete-conversation');
  const aiIconBtn = document.getElementById('ai-toggle-icon');

  const replyPreview = document.getElementById('reply-preview');
  const replyPreviewName = document.getElementById('reply-preview-name');
  const replyPreviewText = document.getElementById('reply-preview-text');
  const replyPreviewClose = document.getElementById('reply-preview-close');

  const messageStore = new Map();

  function moveContactToTop(contact) {
    if (!contact) return;
    const list = document.getElementById('chat-list');
    if (!list) return;
    if (contact.parentNode !== list) list.appendChild(contact);
    const headerItem = list.querySelector('.chat-contact-list-item-title');
    const ref = headerItem ? headerItem.nextElementSibling : list.firstElementChild;
    list.insertBefore(contact, ref || null);
  }

  function last2(s) { return (s || '').toString().slice(-2).toUpperCase() }
  function avatarHasUrl(el, url) { const img = el && el.querySelector('img'); return !!(img && img.getAttribute('src') === url) }
  function avatarHasDigits(el, digits) { const span = el && el.querySelector('span.avatar-initial'); return !!(span && span.textContent.trim() === digits) }
  function setAvatar(el, url, digits) {
    if (!el) return;
    if (url && url.trim() !== '') {
      if (avatarHasUrl(el, url)) return;
      el.innerHTML = `<img src="${url}" class="rounded-circle">`;
      return;
    }
    if (avatarHasDigits(el, digits)) return;
    el.innerHTML = `<span class="avatar-initial rounded-circle bg-label-primary">${digits}</span>`;
  }
  
  function updateToggleAiButton() {
    if (toggleAiBtn) {
      const on = Number(window.currentStopAI || 0) === 1;
      toggleAiBtn.textContent = on ? 'Start AI from conversation' : 'Stop AI from conversation';
    }
    if (aiIconBtn) {
      const on = Number(window.currentStopAI || 0) === 1;
      aiIconBtn.className = on
        ? 'btn bg-danger-subtle btn-text-danger cursor-pointer d-sm-inline-flex d-none me-1 btn-icon rounded-pill'
        : 'btn btn-text-secondary cursor-pointer d-sm-inline-flex d-none me-1 btn-icon rounded-pill';
    }
  }

  if (toggleAiBtn) {
    toggleAiBtn.addEventListener('click', () => {
      if (!window.currentSessionId) return;
      const next = Number(window.currentStopAI || 0) === 1 ? 0 : 1;
      socket.emit('conversation:toggle-ai', {
        sessionId: window.currentSessionId,
        user_id: window.currentUserId,
        value: next
      });
      notyf.success(Lang['StartStopAI']);
    });
  }
  
  if (aiIconBtn) {
    aiIconBtn.addEventListener('click', () => {
      if (!window.currentSessionId) return;
      const next = Number(window.currentStopAI || 0) === 1 ? 0 : 1;
      socket.emit('conversation:toggle-ai', {
        sessionId: window.currentSessionId,
        user_id: window.currentUserId,
        value: next
      });
      notyf.success(Lang['StartStopAI']);
    });
  }

  if (deleteConvBtn) {
    deleteConvBtn.addEventListener('click', () => {
      if (!window.currentSessionId) return;
      if (!confirm(Lang['DeleteThis'])) return;
      socket.emit('conversation:delete', {
        sessionId: window.currentSessionId,
        user_id: window.currentUserId
      });
    });
  }

  const getRelativeTime = dateString => {
    const now = new Date();
    const time = new Date(dateString);
    const diff = now - time;
    const seconds = Math.floor(diff / 1000);
    const minutes = Math.floor(diff / (1000 * 60));
    const hours = Math.floor(diff / (1000 * 60 * 60));
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    if (seconds < 60) return NowLang;
    if (minutes < 60) return `${minutes} ${Lang['Minutes_ago']}`;
    if (hours < 24) return `${hours} ${Lang['Hours_ago']}`;
    if (days < 7) return `${days} ${Lang['Days_ago']}`;
    return time.toLocaleDateString();
  };

  const contactsList = document.getElementById('chat-list');
  const titleItem = contactsList.querySelector('.chat-contact-list-item-title');
  const selectedDeviceBody = window.selectedDeviceBody || '';

  const attachMenu = document.getElementById('attach-menu');
  const inputDoc = document.getElementById('file-input-document');
  const inputMedia = document.getElementById('file-input-media');
  const inputCam = document.getElementById('file-input-camera');
  const inputAudio = document.getElementById('file-input-audio');

  const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
  if (!isMobile) {
    const camItem = attachMenu.querySelector('[data-action="camera"]');
    if (camItem) camItem.parentElement.classList.add('d-none');
  }

  attachMenu.addEventListener('click', function (e) {
    const a = e.target.closest('a[data-action]');
    if (!a) return;
    e.preventDefault();
    if (!window.currentSessionId) return;
    const action = a.dataset.action;
    if (action === 'document') inputDoc.click();
    else if (action === 'media') inputMedia.click();
    else if (action === 'camera') inputCam.click();
    else if (action === 'audio') inputAudio.click();
  });

  function uploadViaChunks(kind, file) {
    const uploadId = `${Date.now()}-${Math.random().toString(36).slice(2)}`;
    const chunkSize = 256 * 1024;
    const totalChunks = Math.ceil(file.size / chunkSize);
    let offset = 0;
    let chunkNo = 0;
    function readNext() {
      const slice = file.slice(offset, offset + chunkSize);
      const reader = new FileReader();
      reader.onload = function (ev) {
        socket.emit('upload-chunk', {
          uploadId,
          kind,
          sessionId: window.currentSessionId,
          sessionBody: window.currentSessionBody,
          sessionPhone: window.currentSessionPhone,
          user_id: window.currentUserId,
          pushName: window.currentSessionName,
          name: file.name,
          type: file.type,
          size: file.size,
          chunkNo,
          totalChunks,
          replyToId: window.currentReplyToId || null
        }, ev.target.result);
        chunkNo++;
        offset += chunkSize;
        if (offset < file.size) readNext();
        else socket.emit('upload-complete', { uploadId });
      };
      reader.readAsArrayBuffer(slice);
    }
    readNext();
  }

  inputDoc.addEventListener('change', function (e) { const f = e.target.files[0]; if (f) uploadViaChunks('document', f); e.target.value = ''; });
  inputMedia.addEventListener('change', function (e) { const f = e.target.files[0]; if (f) uploadViaChunks('media', f); e.target.value = ''; });
  inputCam.addEventListener('change', function (e) { const f = e.target.files[0]; if (f) uploadViaChunks('media', f); e.target.value = ''; });
  inputAudio.addEventListener('change', function (e) { const f = e.target.files[0]; if (f) uploadViaChunks('audio', f); e.target.value = ''; });

  let psChatHistory;
  const initPerfectScrollbar = els => {
    els.forEach(el => {
      if (!el) return;
      const ps = new PerfectScrollbar(el, { wheelPropagation: false, suppressScrollX: true });
      if (el === elements.chatHistoryBody) psChatHistory = ps;
    });
  };
  
  function ensurePicker() {
    if (emojiPicker) return;
    emojiPicker = document.createElement('emoji-picker');
    emojiPicker.id = 'emoji-picker';
    emojiPortal.appendChild(emojiPicker);
  }

  function placePicker() {
    if (!emojiBtn || !emojiPortal) return;
    const rect = emojiBtn.getBoundingClientRect();
    const pickerEl = emojiPicker;
    const w = pickerEl.offsetWidth || 320;
    const h = pickerEl.offsetHeight || 350;
    let left = rect.right - w;
    let top = rect.top - h - 8;
    if (top < 8) top = rect.bottom + 8;
    if (left < 8) left = 8;
    emojiPortal.style.left = left + 'px';
    emojiPortal.style.top = top + 'px';
  }

  function openPicker() {
    ensurePicker();
    emojiPortal.classList.remove('d-none');
    emojiPortal.style.visibility = 'hidden';
    requestAnimationFrame(function(){
      placePicker();
      emojiPortal.style.visibility = 'visible';
    });
  }

  function closePicker() {
    emojiPortal.classList.add('d-none');
  }

  elements.messageInput.addEventListener('keydown', function(e){
    if (e.key === 'Enter') {
      if (isMobile) return;
      if (!e.shiftKey) {
        e.preventDefault();
        elements.formSendMessage.requestSubmit();
      }
    }
  });
  
  if (emojiBtn && emojiPortal) {
    emojiBtn.addEventListener('click', function(e){
      e.preventDefault();
      if (emojiPortal.classList.contains('d-none')) openPicker(); else closePicker();
    });
    document.addEventListener('click', function(e){
      if (!emojiPortal.classList.contains('d-none')) {
        if (!emojiPortal.contains(e.target) && !emojiBtn.contains(e.target)) closePicker();
      }
    });
    window.addEventListener('scroll', function(){ if (!emojiPortal.classList.contains('d-none')) placePicker(); }, true);
    window.addEventListener('resize', function(){ if (!emojiPortal.classList.contains('d-none')) placePicker(); });
  }

  function maxTwoLines(el) {
    var lh = parseFloat(getComputedStyle(el).lineHeight) || 20;
    return Math.round(lh * 2);
  }
  function autosizeClamp(el){
    if(!el) return;
    el.style.height = 'auto';
    var maxH = maxTwoLines(el);
    var h = Math.min(el.scrollHeight, maxH);
    el.style.height = h + 'px';
    el.style.overflowY = el.scrollHeight > maxH ? 'auto' : 'hidden';
  }
  elements.messageInput.addEventListener('input', function(){ autosizeClamp(elements.messageInput); });
  autosizeClamp(elements.messageInput);

  document.addEventListener('emoji-click', function(event){
    if (!emojiPicker || emojiPortal.classList.contains('d-none')) return;
    var unicode = (event.detail && (event.detail.unicode || (event.detail.emoji && event.detail.emoji.unicode))) || '';
    if (!unicode) return;
    var ta = elements.messageInput;
    ta.focus();
    var start = ta.selectionStart || ta.value.length;
    var end = ta.selectionEnd || ta.value.length;
    ta.value = ta.value.slice(0,start) + unicode + ta.value.slice(end);
    var pos = start + unicode.length;
    ta.setSelectionRange(pos, pos);
    autosizeClamp(ta);
  });

  const scrollToBottom = () => { setTimeout(() => { elements.chatHistoryBody.scrollTo(0, elements.chatHistoryBody.scrollHeight); }, 500); };

  function getLiName(li) { return li.dataset.pushName || li.dataset.sessionPhone; }
  function debounce(func, wait) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => func(...a), wait); }; }

  const switchToChatConversation = () => {
    elements.appChatConversation.classList.replace('d-flex', 'd-none');
    elements.appChatHistory.classList.replace('d-none', 'd-block');
  };

  const filterChatContacts = (selector, value, placeholder) => {
    const items = document.querySelectorAll(`${selector}:not(.chat-contact-list-item-title)`);
    let visible = 0;
    items.forEach(item => {
      const match = item.textContent.toLowerCase().includes(value);
      item.classList.toggle('d-flex', match);
      item.classList.toggle('d-none', !match);
      if (match) visible++;
    });
    const placeholderEl = document.querySelector(placeholder);
    if (placeholderEl) placeholderEl.classList.toggle('d-none', visible > 0);
  };

  initPerfectScrollbar([
    elements.chatContactsBody,
    elements.chatHistoryBody,
    elements.chatSidebarLeftBody,
    elements.chatSidebarRightBody
  ]);
  scrollToBottom();

  elements.conversationButton.addEventListener('click', switchToChatConversation);
  elements.searchInput.addEventListener('keyup', debounce(e => { const val = e.target.value.toLowerCase(); filterChatContacts('#chat-list li', val, '.chat-list-item-0'); }, 300));

  function clearReplyPreview(){
	  window.currentReplyToId = null;
	  replyPreview.classList.remove('show');
	  chatFooter && chatFooter.classList.remove('reply-open');
	}
	replyPreviewClose.addEventListener('click', clearReplyPreview);

  elements.formSendMessage.addEventListener('submit', e => {
    e.preventDefault();
    const text = elements.messageInput.value.trim();
    if (!text || !window.currentSessionId) return;
    const li = document.querySelector(`[data-session-id="${window.currentSessionId}"]`);
    const csName = li ? (li.dataset.csName || '') : (window.currentCsName || '');
    socket.emit('send-message', {
      sessionId: window.currentSessionId,
      sessionBody: window.currentSessionBody,
      sessionPhone: window.currentSessionPhone,
      user_id: window.currentUserId,
      pushName: window.currentSessionName,
      csName: csName,
      message: text,
      replyToId: window.currentReplyToId || null
    });
    elements.messageInput.value = '';
    clearReplyPreview();
  });

  elements.chatHistoryHeader.addEventListener('click', () => {
    document.querySelector('.app-chat-sidebar-left .close-sidebar').removeAttribute('data-overlay');
  });

  function buildAvatarHtml(isIncoming, m) {
    const url = isIncoming ? (m.profile_sender || '') : (m.profile_receive || '')
    const digits = isIncoming ? last2(m.phone_number || '') : last2(window.currentSessionPhone || '')
    if (url) return `<div class="avatar avatar-sm"><img src="${url}" class="rounded-circle"></div>`
    return `<div class="avatar avatar-sm"><span class="avatar-initial rounded-circle bg-label-primary">${digits}</span></div>`
  }

  function typeLabel(t, orig) {
    if (t === 'text') return orig || ''
    if (t === 'image') return Lang['Image'] || 'Image'
    if (t === 'video') return Lang['Video'] || 'Video'
    if (t === 'audio') return Lang['Audio'] || 'Audio'
    if (t === 'sticker') return Lang['Sticker'] || 'Sticker'
    if (t === 'document') return (orig && orig !== '' ? orig : (Lang['Document'] || 'Document'))
    if (t === 'vcard') return Lang['VCard'] || 'VCard'
    if (t === 'location') return 'Location'
    return orig || ''
  }

  function quoteNameFor(orig) {
    if (!orig) return ''
    if (orig.direction === 'outgoing') return 'You'
    return window.currentSessionName || window.currentSessionPhone || ''
  }

  function buildQuoteHtml(orig) {
    const name = quoteNameFor(orig)
    const text = typeLabel(orig.type, orig.type === 'text' ? orig.message : (orig.original_file || ''))
    return `<div class="p-2 rounded reply-chip mb-1"><div class="fw-semibold text-truncate">${name}</div><div class="text-truncate">${escapeHtml(text)}</div></div>`
  }

  function escapeHtml(s) {
    return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
  }

  function renderMessages(messages) {
    const ul = document.getElementById('messages-list');
    ul.innerHTML = '';
    messageStore.clear()
    let lastDirection = null;
    let lastLi = null;
    messages.forEach(m => {
      messageStore.set(Number(m.id), m)
      m.profile_sender = window.currentProfileSender || ''
      m.profile_receive = window.currentProfileReceive || ''
      m.phone_number = window.currentSessionPhone || m.phone_number
      if (m.direction === lastDirection && lastLi) {
        appendToLi(lastLi, m);
      } else {
        lastLi = createLi(m);
        ul.appendChild(lastLi);
        lastDirection = m.direction;
      }
    });
    psChatHistory?.update();
    scrollToBottom();
  }

  function createLi(m){
	  const li = document.createElement('li');
	  li.className = m.direction === 'outgoing' ? 'chat-message chat-message-right' : 'chat-message';
	  li.dataset.direction = m.direction;
	  li.innerHTML = buildMessageGroupHtml(m, true);
	  return li;
  }

  function addOrAppendMessage(m) {
    const ul = document.getElementById('messages-list');
    messageStore.set(Number(m.id), m)
    const lastLi = ul.lastElementChild;
    if (lastLi && lastLi.dataset.direction === m.direction) {
      appendToLi(lastLi, m);
    } else {
      const newLi = createLi(m);
      ul.appendChild(newLi);
    }
    psChatHistory?.update();
    scrollToBottom();
  }
  
  function resolveQuoted(m){
	  const rid = Number(m.reply_message_id || 0);
	  if (!rid) return null;
	  return messageStore.get(rid) || m.reply_preview || null;
  }
  
  function buildQuoteHtmlFromAny(q){
	  const name = q.direction === 'outgoing' ? 'You' : (window.currentSessionName || window.currentSessionPhone || '');
	  const text = q.type === 'text' ? q.message : (q.original_file || typeLabel(q.type,''));
	  return `<div class="p-2 rounded reply-chip mb-1"><div class="fw-semibold text-truncate">${escapeHtml(name)}</div><div class="text-truncate">${escapeHtml(text)}</div></div>`;
  }
  
  function insertQuoteBefore(wrapper, beforeEl, m){
	  const q = resolveQuoted(m);
	  if (!q) return;
	  const el = document.createElement('div');
	  el.innerHTML = buildQuoteHtmlFromAny(q);

	  const bubble = (beforeEl && beforeEl.classList && beforeEl.classList.contains('chat-message-text'))
		? beforeEl
		: (wrapper.querySelector('.chat-message-text') || null);

	  if (bubble) bubble.insertBefore(el.firstChild, bubble.firstChild);
	}

  function appendReplyIcon(bubble, msgId){
	  const act = document.createElement('span');
	  act.className = 'reply-action';
	  act.innerHTML = `<i class="icon-base ti tabler-arrow-back-up"></i>`;
	  act.dataset.msgId = String(msgId);
	  bubble.style.position = 'relative';
	  const m = messageStore.get(Number(msgId));
	  if (m && m.direction === 'outgoing') {
		act.style.left = '-24px';
		act.style.right = 'auto';
	  } else {
		act.style.right = '-24px';
		act.style.left = 'auto';
	  }
	  bubble.appendChild(act);
  }

  function appendQuoteIfAny(wrapper, m) {
    const rid = Number(m.reply_message_id || 0)
    if (!rid) return
    const orig = messageStore.get(rid)
    if (!orig) return
    const q = document.createElement('div')
    q.innerHTML = buildQuoteHtml(orig)
    const firstMeta = wrapper.querySelector('.mt-1')
    wrapper.insertBefore(q.firstChild, firstMeta)
  }

  function appendToLi(li, m) {
	  const wrapper = li.querySelector('.chat-message-wrapper');
	  if (m.type === 'image' && m.attachment) {
		const imgDiv = document.createElement('div');
		imgDiv.className = 'chat-message-text mt-2';
		imgDiv.innerHTML = `<img src="${m.attachment}" class="chat-img-preview" style="max-width:250px;border-radius:8px;cursor:pointer;">`;
		insertQuoteBefore(wrapper, imgDiv, m);
		wrapper.insertBefore(imgDiv, wrapper.querySelector('.mt-1'));
		appendReplyIcon(imgDiv, m.id);
		const imgEl = imgDiv.querySelector('img');
		imgEl.addEventListener('click', () => {
		  const modalEl = document.getElementById('imageModal');
		  const modalImg = document.getElementById('imageModalImg');
		  modalImg.src = m.attachment;
		  bootstrap.Modal.getOrCreateInstance(modalEl).show();
		});
	  } else if (m.type === 'vcard') {
		const vcardDiv = document.createElement('div');
		vcardDiv.className = 'chat-message-text mt-2 p-2 border rounded';
		vcardDiv.style.cursor = 'pointer';
		vcardDiv.style.backgroundColor = '#f5ecb9';
		const vraw = decodeURIComponent(m.message);
		const lines = vraw.split('\n');
		const fnLine  = (lines.find(l => l.startsWith('FN:'))  || '').replace('FN:','');
		const telLine = (lines.find(l => l.startsWith('TEL')) || '').split(':')[1] || '';
		vcardDiv.innerHTML = `<i class="icon-base ti tabler-user icon-22px me-2"></i><strong>${fnLine}</strong><br><small>${telLine}</small>`;
		insertQuoteBefore(wrapper, vcardDiv, m);
		wrapper.insertBefore(vcardDiv, wrapper.querySelector('.mt-1'));
		appendReplyIcon(vcardDiv, m.id);
		vcardDiv.addEventListener('click', () => {
		  const blob = new Blob([vraw], { type: 'text/vcard;charset=utf-8' });
		  const url  = URL.createObjectURL(blob);
		  const a    = document.createElement('a');
		  a.href     = url;
		  a.download = `${fnLine || 'contact'}.vcf`;
		  document.body.appendChild(a);
		  a.click();
		  a.remove();
		  URL.revokeObjectURL(url);
		});
	  } else if (m.type === 'sticker' && m.attachment) {
		const stickDiv = document.createElement('div');
		stickDiv.className = 'chat-message-text mt-2';
		stickDiv.innerHTML = `<img src="${m.attachment}" style="max-width:140px;">`;
		insertQuoteBefore(wrapper, stickDiv, m);
		wrapper.insertBefore(stickDiv, wrapper.querySelector('.mt-1'));
		appendReplyIcon(stickDiv, m.id);
	  } else if (m.type === 'location') {
		const { degreesLatitude: lat, degreesLongitude: lng } = JSON.parse(m.attachment);
		const link = document.createElement('a');
		link.href = `https://www.google.com/maps?q=${lat},${lng}`;
		link.target = '_blank';
		const iframe = document.createElement('iframe');
		iframe.src = `https://maps.google.com/maps?q=${lat},${lng}&z=15&output=embed`;
		iframe.width = '200';
		iframe.height = '150';
		iframe.style.border = '0';
		iframe.style.borderRadius = '8px';
		link.appendChild(iframe);
		const mapDiv = document.createElement('div');
		mapDiv.className = 'chat-message-text mt-2';
		mapDiv.appendChild(link);
		insertQuoteBefore(wrapper, mapDiv, m);
		wrapper.insertBefore(mapDiv, wrapper.querySelector('.mt-1'));
		appendReplyIcon(mapDiv, m.id);
	  } else if (m.type === 'audio' && m.attachment) {
		const audDiv = document.createElement('div');
		audDiv.className = 'chat-message-text mt-2';
		audDiv.innerHTML = `<audio style="max-width:250px;border-radius:8px;" controls><source src="${m.attachment}" type="audio/ogg"></audio>`;
		insertQuoteBefore(wrapper, audDiv, m);
		wrapper.insertBefore(audDiv, wrapper.querySelector('.mt-1'));
		appendReplyIcon(audDiv, m.id);
	  } else if (m.type === 'video' && m.attachment) {
		const vidDiv = document.createElement('div');
		vidDiv.className = 'chat-message-text mt-2';
		vidDiv.innerHTML = `<video style="width:100%;max-width:250px;border-radius:8px;" controls><source src="${m.attachment}" type="video/mp4"></video>`;
		insertQuoteBefore(wrapper, vidDiv, m);
		wrapper.insertBefore(vidDiv, wrapper.querySelector('.mt-1'));
		appendReplyIcon(vidDiv, m.id);
	  } else if (m.type === 'document' && m.attachment) {
		const docDiv = document.createElement('div');
		docDiv.className = 'chat-message-text mt-2';
		docDiv.innerHTML = `<a href="${m.attachment}" download><i class="icon-base ti tabler-file-type-doc icon-22px" style="max-width:250px;border-radius:8px;"></i> ${m.original_file || ''}</a>`;
		insertQuoteBefore(wrapper, docDiv, m);
		wrapper.insertBefore(docDiv, wrapper.querySelector('.mt-1'));
		appendReplyIcon(docDiv, m.id);
	  } else {
		const newTextDiv = document.createElement('div');
		newTextDiv.className = 'chat-message-text mt-2';
		newTextDiv.innerHTML = `<p class="mb-0" style="white-space: break-spaces;">${formatWppMarkdown(m.message)}</p>`;
		insertQuoteBefore(wrapper, newTextDiv, m);
		wrapper.insertBefore(newTextDiv, wrapper.querySelector('.mt-1'));
		appendReplyIcon(newTextDiv, m.id);
	  }
	  const timeEl = wrapper.querySelector('.mt-1 small');
	  timeEl.textContent = new Date(m.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }

  function buildMessageGroupHtml(m, includeAvatar) {
    const isIncoming = m.direction === 'incoming';
    const avatarBlock = includeAvatar ? `<div class="user-avatar flex-shrink-0 ${isIncoming ? 'me-4' : 'ms-4'}">${buildAvatarHtml(isIncoming, m)}</div>` : '';
    let contentHtml;
    const baseTime = `${new Date(m.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`
    if (m.type === 'image' && m.attachment) {
      contentHtml = `<div class="chat-message-wrapper flex-grow-1"><div class="chat-message-text"><img src="${m.attachment}" style="max-width:250px;border-radius:8px;cursor:pointer;" data-bs-toggle="modal" data-bs-target="#imageModal" data-bs-src="${m.attachment}" /></div><div class="${isIncoming ? 'text-body-secondary' : 'text-end'} mt-1"><small>${baseTime}</small></div></div>`;
    } else if (m.type === 'sticker' && m.attachment) {
	  contentHtml = `<div class="chat-message-wrapper flex-grow-1"><div class="chat-message-text"><img src="${m.attachment}" style="max-width:140px;"></div><div class="${isIncoming ? 'text-body-secondary' : 'text-end'} mt-1"><small>${baseTime}</small></div></div>`;
	} else if (m.type === 'location') {
      const { degreesLatitude: lat, degreesLongitude: lng } = JSON.parse(m.attachment);
      const mapUrl = `https://www.google.com/maps?q=${lat},${lng}`;
      const embedSrc = `https://maps.google.com/maps?q=${lat},${lng}&z=15&output=embed`;
      contentHtml = `<div class="chat-message-wrapper flex-grow-1"><div class="chat-message-text"><a href="${mapUrl}" target="_blank"><iframe src="${embedSrc}" width="200" height="150" style="border:0;border-radius:8px;"></iframe></a></div><div class="${isIncoming ? 'text-body-secondary' : 'text-end'} mt-1"><small>${baseTime}</small></div></div>`;
    } else if (m.type === 'audio' && m.attachment) {
      contentHtml = `<div class="chat-message-wrapper flex-grow-1"><div class="chat-message-text"><audio style="max-width:250px;border-radius:8px;" controls><source src="${m.attachment}" type="audio/ogg"></audio></div><div class="${isIncoming ? 'text-body-secondary' : 'text-end'} mt-1"><small>${baseTime}</small></div></div>`;
    } else if (m.type === 'video' && m.attachment) {
      contentHtml = `<div class="chat-message-wrapper flex-grow-1"><div class="chat-message-text"><video style="max-width:250px;border-radius:8px;" controls><source src="${m.attachment}" type="video/mp4"></video></div><div class="${isIncoming ? 'text-body-secondary' : 'text-end'} mt-1"><small>${baseTime}</small></div></div>`;
    } else if (m.type === 'vcard') {
      const vraw = decodeURIComponent(m.message)
      const lines = vraw.split('\n')
      const fnLine = (lines.find(l => l.startsWith('FN:')) || '').replace('FN:', '')
      const telLine = (lines.find(l => l.startsWith('TEL')) || '').split(':')[1] || ''
      contentHtml = `<div class="chat-message-wrapper flex-grow-1"><div class="chat-message-text p-2 border rounded" style="cursor: pointer;background-color: #f5ecb9;" data-vcard="${encodeURIComponent(vraw)}" data-download="${fnLine || 'contact'}"><i class="icon-base ti tabler-user icon-22px me-2"></i><strong>${fnLine}</strong><br><small>${telLine}</small></div><div class="${isIncoming ? 'text-body-secondary' : 'text-end'} mt-1"><small>${baseTime}</small></div></div>`;
    } else if (m.type === 'document' && m.attachment) {
      contentHtml = `<div class="chat-message-wrapper flex-grow-1"><div class="chat-message-text"><a href="${m.attachment}" download><i class="icon-base ti tabler-file-type-doc icon-22px" style="max-width:250px;border-radius:8px;"></i> ${m.original_file || ''}</a></div><div class="${isIncoming ? 'text-body-secondary' : 'text-end'} mt-1"><small>${baseTime}</small></div></div>`;
    } else {
      contentHtml = `<div class="chat-message-wrapper flex-grow-1"><div class="chat-message-text"><p class="mb-0" style="white-space: break-spaces;">${formatWppMarkdown(m.message)}</p></div><div class="${isIncoming ? 'text-body-secondary' : 'text-end'} mt-1">${!isIncoming ? '<i class="icon-base ti tabler-checks icon-16px text-success me-1"></i>' : ''}<small>${baseTime}</small></div></div>`;
    }
    const q = resolveQuoted(m);
	const quotedHtml = q ? buildQuoteHtmlFromAny(q) : '';
    const container = `<div class="d-flex overflow-hidden">${isIncoming ? avatarBlock : ''}${contentHtml}${!isIncoming ? avatarBlock : ''}</div>`
    const tmp = document.createElement('div')
    tmp.innerHTML = container
    const wrap = tmp.querySelector('.chat-message-wrapper')
	if (wrap && quotedHtml) {
	  const bubbleEl = wrap.querySelector('.chat-message-text')
	  if (bubbleEl) {
		const q = document.createElement('div')
		q.innerHTML = quotedHtml
		bubbleEl.insertBefore(q.firstChild, bubbleEl.firstChild)
	  }
	}
	const bubble = tmp.querySelector('.chat-message-text') || wrap
    if (bubble) appendReplyIcon(bubble, m.id)
    return tmp.innerHTML
  }

  function updateContactAvatar(li, profileSenderUrl, phone) {
    const av = li.querySelector('.avatar')
    if (!av) return
    if (profileSenderUrl && profileSenderUrl.trim() !== '') {
      if (avatarHasUrl(av, profileSenderUrl)) return
      av.innerHTML = `<img src="${profileSenderUrl}" class="rounded-circle"/>`
    } else {
      const digits = last2(phone)
      if (avatarHasDigits(av, digits)) return
      av.innerHTML = `<span class="avatar-initial rounded-circle bg-label-primary">${digits}</span>`
    }
  }

  function updateHistoryAvatars(profile_sender, profile_receive) {
    const list = document.getElementById('messages-list')
    if (!list) return
    const inDigits = last2(window.currentSessionPhone || '')
    list.querySelectorAll('.chat-message:not(.chat-message-right) .user-avatar').forEach(el => {
      const holder = el
      if (profile_sender) {
        if (!avatarHasUrl(holder, profile_sender)) holder.innerHTML = `<div class="avatar avatar-sm"><img src="${profile_sender}" class="rounded-circle"></div>`
      } else {
        if (!avatarHasDigits(holder, inDigits)) holder.innerHTML = `<div class="avatar avatar-sm"><span class="avatar-initial rounded-circle bg-label-primary">${inDigits}</span></div>`
      }
    })
    list.querySelectorAll('.chat-message.chat-message-right .user-avatar').forEach(el => {
      const holder = el
      if (profile_receive) {
        if (!avatarHasUrl(holder, profile_receive)) holder.innerHTML = `<div class="avatar avatar-sm"><img src="${profile_receive}" class="rounded-circle"></div>`
      } else {
        if (!avatarHasDigits(holder, inDigits)) holder.innerHTML = `<div class="avatar avatar-sm"><span class="avatar-initial rounded-circle bg-label-primary">${inDigits}</span></div>`
      }
    })
  }

  function populateRightSidebar(info) {
    const phone = window.currentSessionPhone || ''
    const name = info?.name || window.currentSessionName || phone
    const profile = info?.profile || window.currentProfileSender || ''
    const about = info?.about || ''
    setAvatar(rightAvatarEl, profile, last2(phone))
    if (rightNameEl) rightNameEl.textContent = name
    if (rightStatusEl) rightStatusEl.textContent = phone
    if (rightAboutEl) rightAboutEl.textContent = about || defaultRightAbout
  }

  function requestContactInfo(e) {
    if (e && e.preventDefault) e.preventDefault()
    if (!window.currentSessionPhone || !window.currentSessionBody) return
    socket.emit('get-info', { body: window.currentSessionBody, phone: window.currentSessionPhone, user_id: window.currentUserId }, res => {
      if (!res || !res.ok) return
      populateRightSidebar(res.data)
    })
  }
  
  function formatWppMarkdown(text) {
    if (typeof text !== 'string' || !text) return '';
    let s = text;
    const blocks = [];
    s = s.replace(/```([\s\S]*?)```/g, function (_, code) {
      blocks.push(code);
      return '\uE000BLOCK' + (blocks.length - 1) + '\uE000';
    });
    const inlines = [];
    s = s.replace(/`([^`\n]+?)`/g, function (_, code) {
      inlines.push(code);
      return '\uE000INLINE' + (inlines.length - 1) + '\uE000';
    });
    s = s.replace(/^> ?(.*)$/gm, '<span class="border-start border-3 p-2 d-block">$1</span>');
    s = s.replace(/\*(?=\S)(.+?)(?<=\S)\*/g, '<b>$1</b>');
    s = s.replace(/_(?=\S)(.+?)(?<=\S)_/g, '<i>$1</i>');
    s = s.replace(/~(?=\S)(.+?)(?<=\S)~/g, '<s>$1</s>');
    s = s.replace(/(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig, function (url) {
      return '<a href="' + url + '" target="_blank" rel="noopener noreferrer">' + url + '</a>';
    });
    s = s.replace(/\n/g, '<br>');
    s = s.replace(/\uE000INLINE(\d+)\uE000/g, function (_, i) {
      return '<code>' + inlines[Number(i)] + '</code>';
    });
    s = s.replace(/\uE000BLOCK(\d+)\uE000/g, function (_, i) {
      return '<pre style="white-space:pre-wrap;margin:0"><code>' + blocks[Number(i)] + '</code></pre>';
    });
    return s;
  }

  headerAvatarEl.addEventListener('click', e => { requestContactInfo(e); });
  viewContactBtn.addEventListener('click', e => { requestContactInfo(e); });

  function openReplyForMessageId(id){
	  const m = messageStore.get(Number(id));
	  if (!m) return;
	  window.currentReplyToId = Number(id);
	  replyPreviewName.textContent = quoteNameFor(m);
	  replyPreviewText.textContent = typeLabel(m.type, m.type === 'text' ? m.message : (m.original_file || ''));
	  replyPreview.classList.add('show');
	  chatFooter && chatFooter.classList.add('reply-open');
	}

  document.getElementById('messages-list').addEventListener('click', function(e){
    const btn = e.target.closest('.reply-action')
    if (!btn) return
    const id = btn.dataset.msgId
    if (!id) return
    openReplyForMessageId(id)
  })

  const contactsListClick = e => {
    const li = e.target.closest('li.chat-contact-list-item');
    if (!li || li === titleItem) return;
    contactsList.querySelectorAll('li.chat-contact-list-item').forEach(i => i.classList.remove('active'));
    li.classList.add('active');
    headerNameEl.textContent = li.dataset.pushName || li.dataset.sessionPhone;

    const sessionId    = li.dataset.sessionId;
    const sessionBody  = li.dataset.sessionBody;
    const sessionPhone = li.dataset.sessionPhone;
    const sessionName  = li.dataset.pushName;
    const sessionCsName  = li.dataset.csName || '';
    const profileSender = li.dataset.profileSender || '';
    const profileReceive = li.dataset.profileReceive || '';
    const stopAi = Number(li.dataset.stopAi || 0);
    window.currentStopAI = stopAi;
    updateToggleAiButton();

    if (window.currentSessionId) socket.emit('leave-session', { sessionId: window.currentSessionId });
    window.currentSessionId    = sessionId;
    window.currentSessionBody  = sessionBody;
    window.currentSessionPhone = sessionPhone;
    window.currentSessionName  = sessionName;
    window.currentCsName  = sessionCsName || '';
    window.currentProfileSender = profileSender;
    window.currentProfileReceive = profileReceive;

    if (headerPhoneEl) headerPhoneEl.textContent = sessionPhone;
    setAvatar(headerAvatarEl, profileSender, last2(sessionPhone));
    updateHistoryAvatars(window.currentProfileSender, window.currentProfileReceive);
    clearReplyPreview();

    document.querySelector('.app-overlay').classList.remove('show');
    document.getElementById('app-chat-contacts').classList.remove('show');

    socket.emit('join-session', { sessionId });
    switchToChatConversation();
    fetch(FetchUrl.replace(':id', sessionId))
      .then(r => r.json())
      .then(renderMessages);
  };

  contactsList.addEventListener('click', contactsListClick);

  socket.on('message:new', data => {
    if (data.sessionId == window.currentSessionId) {
      const prevS = window.currentProfileSender || ''
      const prevR = window.currentProfileReceive || ''
      if (typeof data.profile_sender !== 'undefined') window.currentProfileSender = data.profile_sender || ''
      if (typeof data.profile_receive !== 'undefined') window.currentProfileReceive = data.profile_receive || ''
      data.profile_sender = window.currentProfileSender || data.profile_sender || ''
      data.profile_receive = window.currentProfileReceive || data.profile_receive || ''
      addOrAppendMessage(data);
      if (headerPhoneEl && !headerPhoneEl.textContent) headerPhoneEl.textContent = window.currentSessionPhone;
      setAvatar(headerAvatarEl, window.currentProfileSender, last2(window.currentSessionPhone));
      if (prevS !== window.currentProfileSender || prevR !== window.currentProfileReceive) {
        updateHistoryAvatars(window.currentProfileSender, window.currentProfileReceive);
      }
      scrollToBottom();
    }
    const contact = document.querySelector(`[data-session-id="${data.sessionId}"]`);
    if (contact) {
      const nameOrPhone = data.push_name || data.phone_number;
      contact.dataset.pushName = data.push_name || '';
      contact.querySelector('.chat-contact-name').textContent = nameOrPhone;
      contact.querySelector('.chat-contact-status').textContent = data.message;
      contact.querySelector('.chat-contact-list-item-time').textContent = NowLang;
      if (typeof data.profile_sender !== 'undefined') {
        const old = contact.dataset.profileSender || ''
        const nu = data.profile_sender || ''
        if (old !== nu) {
          contact.dataset.profileSender = nu;
          updateContactAvatar(contact, nu, data.phone_number);
        }
      }
      moveContactToTop(contact);
    }
  });
  
  socket.on('conversation:ai-toggled', data => {
    const li = document.querySelector(`[data-session-id="${data.sessionId}"]`);
    if (li) li.dataset.stopAi = String(data.stop_ai);
    if (String(data.sessionId) === String(window.currentSessionId)) {
      window.currentStopAI = Number(data.stop_ai);
      updateToggleAiButton();
    }
  });

  socket.on('conversation:deleted', data => {
    const li = document.querySelector(`[data-session-id="${data.sessionId}"]`);
    if (li) li.remove();
    if (String(data.sessionId) === String(window.currentSessionId)) {
      window.currentSessionId = null;
      window.currentSessionBody = null;
      window.currentSessionPhone = null;
      window.currentSessionName = null;
      const ul = document.getElementById('messages-list');
      if (ul) ul.innerHTML = '';
      const hist = document.getElementById('app-chat-history');
      const convo = document.getElementById('app-chat-conversation');
      if (hist && convo) {
        hist.classList.add('d-none');
        convo.classList.remove('d-none');
        convo.classList.add('d-flex');
      }
      if (headerNameEl) headerNameEl.textContent = '{{ __("Contact") }}';
      if (headerPhoneEl) headerPhoneEl.textContent = '';
    }
  });

  socket.on('session:updated', data => {
    if (selectedDeviceBody && data.body !== selectedDeviceBody) return;
    const contact = document.querySelector(`[data-session-id="${data.sessionId}"]`);
    const incomingName = data.push_name && data.push_name.trim() !== '' ? data.push_name.trim() : null;
    if (contact) {
      const currentName =
        (contact.dataset.pushName && contact.dataset.pushName.trim() !== '' ? contact.dataset.pushName.trim() : '') ||
        (contact.querySelector('.chat-contact-name')?.textContent.trim() || '');
      const finalName = incomingName || currentName || data.phone_number;
      contact.dataset.pushName = finalName;
      contact.querySelector('.chat-contact-name').textContent = finalName;
      contact.querySelector('.chat-contact-status').textContent =
        typeof Lang !== 'undefined' && Lang[data.last_message] ? Lang[data.last_message] : data.last_message;
      contact.querySelector('.chat-contact-list-item-time').textContent = getRelativeTime(data.updated_at);
      if (typeof data.profile_sender !== 'undefined') {
        const old = contact.dataset.profileSender || ''
        const nu = data.profile_sender || ''
        if (old !== nu) {
          contact.dataset.profileSender = nu;
          updateContactAvatar(contact, nu, data.phone_number);
        }
      }
      moveContactToTop(contact);
    } else {
      const finalName = incomingName || data.phone_number;
      const li = document.createElement('li');
      li.className = 'chat-contact-list-item mb-1';
      li.dataset.sessionId    = data.sessionId;
      li.dataset.sessionPhone = data.phone_number;
      li.dataset.sessionBody  = data.body;
      li.dataset.pushName     = finalName;
      li.dataset.profileSender = data.profile_sender || '';
      li.dataset.profileReceive = data.profile_receive || '';
      li.innerHTML = `
        <a class="d-flex align-items-center">
          <div class="flex-shrink-0 avatar">
            ${data.profile_sender ? `<img src="${data.profile_sender}" class="rounded-circle"/>` : `<span class="avatar-initial rounded-circle bg-label-primary">${last2(data.phone_number)}</span>`}
          </div>
          <div class="chat-contact-info flex-grow-1 ms-4 text-truncate">
            <div class="d-flex justify-content-between align-items-center">
              <h6 class="chat-contact-name text-truncate m-0 fw-normal">${finalName}</h6>
              <small class="chat-contact-list-item-time">${getRelativeTime(data.updated_at)}</small>
            </div>
            <small class="chat-contact-status">
              ${typeof Lang !== 'undefined' && Lang[data.last_message] ? Lang[data.last_message] : data.last_message}
            </small>
          </div>
        </a>`;
      contactsList.insertBefore(li, titleItem.nextSibling);
    }
    if (window.currentSessionId && data.sessionId == window.currentSessionId) {
      const prevS = window.currentProfileSender || ''
      const prevR = window.currentProfileReceive || ''
      if (typeof data.profile_receive !== 'undefined') window.currentProfileReceive = data.profile_receive || ''
      if (typeof data.profile_sender !== 'undefined') window.currentProfileSender = data.profile_sender || ''
      setAvatar(headerAvatarEl, window.currentProfileSender, last2(window.currentSessionPhone));
      if (prevS !== window.currentProfileSender || prevR !== window.currentProfileReceive) {
        updateHistoryAvatars(window.currentProfileSender, window.currentProfileReceive);
      }
    }
  });
});
