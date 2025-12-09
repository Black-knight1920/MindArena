<?php
// views/front/chatbot.php
// Widget de chatbot MindArena (front) - IA HuggingFace + fallback "intelligent" local
?>
<style>
/* ====== MINDARENA CHATBOT WIDGET ====== */
.ma-chatbot-toggle {
    position: fixed;
    right: 24px;
    bottom: 24px;
    width: 58px;
    height: 58px;
    border-radius: 50%;
    background: radial-gradient(circle at top left, #ff4df0, #501755);
    box-shadow:
        0 0 0 2px rgba(255,255,255,0.3),
        0 0 18px rgba(255, 77, 240, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 9999;
    color: #fff;
    font-size: 26px;
    border: none;
    outline: none;
    transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
}
.ma-chatbot-toggle:hover {
    transform: scale(1.06) translateY(-1px);
    box-shadow:
        0 0 0 2px rgba(255,255,255,0.4),
        0 0 24px rgba(255, 77, 240, 1);
    background: radial-gradient(circle at top left, #ff7cf7, #6a25c8);
}
.ma-chatbot-window {
    position: fixed;
    right: 24px;
    bottom: 95px;
    width: 330px;
    max-height: 430px;
    background: rgba(10, 7, 30, 0.97);
    border-radius: 18px;
    box-shadow: 0 14px 35px rgba(0,0,0,0.85);
    border: 1px solid rgba(255,255,255,0.18);
    display: none;
    flex-direction: column;
    overflow: hidden;
    z-index: 9998;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    animation: maChatbotIn 0.2s ease-out;
}
.ma-chatbot-window.ma-open { display: flex; }
@keyframes maChatbotIn {
    from { transform: translateY(10px); opacity: 0; }
    to   { transform: translateY(0);   opacity: 1; }
}
.ma-chatbot-header {
    padding: 10px 14px;
    background: linear-gradient(120deg, #b01ba5, #ff2bdd);
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: #fff;
}
.ma-chatbot-header-left { display: flex; flex-direction: column; }
.ma-chatbot-title { font-weight: 700; font-size: 15px; }
.ma-chatbot-subtitle { font-size: 11px; opacity: 0.9; }
.ma-chatbot-close { cursor: pointer; font-size: 18px; opacity: 0.9; }
.ma-chatbot-close:hover { opacity: 1; }
.ma-chatbot-body {
    padding: 10px;
    flex: 1;
    overflow-y: auto;
    font-size: 13px;
}
.ma-chat-msg {
    margin-bottom: 8px;
    max-width: 85%;
    padding: 7px 10px;
    border-radius: 10px;
    line-height: 1.4;
}
.ma-chat-msg.ma-bot {
    background: rgba(255,255,255,0.08);
    color: #f5e9ff;
    border-bottom-left-radius: 2px;
}
.ma-chat-msg.ma-user {
    margin-left: auto;
    background: linear-gradient(120deg, #ff4df0, #b01ba5);
    color: #fff;
    border-bottom-right-radius: 2px;
}
.ma-chat-typing {
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.ma-chat-typing-dot {
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: rgba(255,255,255,0.9);
    animation: maTyping 1s infinite;
}
.ma-chat-typing-dot:nth-child(2) { animation-delay: 0.15s; }
.ma-chat-typing-dot:nth-child(3) { animation-delay: 0.3s; }
@keyframes maTyping {
    0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
    30%           { transform: translateY(-3px); opacity: 1; }
}
.ma-chatbot-footer {
    padding: 8px;
    border-top: 1px solid rgba(255,255,255,0.12);
    display: flex;
    gap: 6px;
}
.ma-chatbot-input {
    flex: 1;
    border-radius: 999px;
    border: none;
    padding: 7px 10px;
    font-size: 13px;
    background: rgba(255,255,255,0.12);
    color: #fff;
    outline: none;
}
.ma-chatbot-send {
    border-radius: 999px;
    border: none;
    padding: 7px 11px;
    font-size: 13px;
    background: linear-gradient(120deg, #ff4df0, #b01ba5);
    color: #fff;
    cursor: pointer;
}
.ma-chatbot-send:hover { filter: brightness(1.1); }
.ma-chatbot-body::-webkit-scrollbar { width: 6px; }
.ma-chatbot-body::-webkit-scrollbar-track { background: rgba(0,0,0,0.2); }
.ma-chatbot-body::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.35);
    border-radius: 3px;
}
@media (max-width: 768px) {
    .ma-chatbot-window {
        right: 10px;
        bottom: 90px;
        width: calc(100% - 20px);
    }
    .ma-chatbot-toggle {
        right: 12px;
        bottom: 18px;
    }
}
</style>

<div class="ma-chatbot-toggle" id="maChatbotToggle" title="Besoin d'aide ?">💬</div>

<div class="ma-chatbot-window" id="maChatbotWindow">
    <div class="ma-chatbot-header">
        <div class="ma-chatbot-header-left">
            <span class="ma-chatbot-title">MindArena Bot</span>
            <span class="ma-chatbot-subtitle" id="maChatbotSubtitle">
                Assistant IA (en ligne + local)
            </span>
        </div>
        <div class="ma-chatbot-close" id="maChatbotClose">×</div>
    </div>

    <div class="ma-chatbot-body" id="maChatbotBody"></div>

    <form class="ma-chatbot-footer" id="maChatbotForm">
        <input type="text"
               id="maChatbotInput"
               class="ma-chatbot-input"
               placeholder="Écris ta question..."
               autocomplete="off">
        <button type="submit" class="ma-chatbot-send">Envoyer</button>
    </form>
</div>

<script>
(function() {
    // contexte défini par la page : forums, forum_add, publications, etc.
    const ctx = (window.MA_CHAT_CONTEXT || 'default').toString();

    const toggle   = document.getElementById('maChatbotToggle');
    const windowEl = document.getElementById('maChatbotWindow');
    const closeEl  = document.getElementById('maChatbotClose');
    const bodyEl   = document.getElementById('maChatbotBody');
    const formEl   = document.getElementById('maChatbotForm');
    const inputEl  = document.getElementById('maChatbotInput');
    const subtitle = document.getElementById('maChatbotSubtitle');

    const subtitles = {
        default:          "Questions sur MindArena",
        forums:           "Aide sur les forums",
        forum_add:        "Aide pour créer un forum",
        publications:     "Aide sur les publications",
        publication_add:  "Aide pour publier un message",
        report:           "Aide sur les signalements"
    };
    if (subtitle) subtitle.textContent = subtitles[ctx] || subtitles.default;

    // URL de base (vient de PHP si défini)
    const BASE_URL = "<?= defined('BASE_URL') ? BASE_URL : '' ?>";

    // ===== Mémoire de conversation =====
    const conversation = []; // {role: 'user'|'assistant', content: '...'}
    let lastBotReply = "";
    let typingBubble = null;
    let isWaiting = false;

    /* ---------- UI --------- */
    function addMessage(html, from) {
        const div = document.createElement('div');
        div.className = 'ma-chat-msg ' + (from === 'user' ? 'ma-user' : 'ma-bot');
        div.innerHTML = html;
        bodyEl.appendChild(div);
        bodyEl.scrollTop = bodyEl.scrollHeight;
    }

    function showTyping() {
        if (typingBubble) return;
        const div = document.createElement('div');
        div.className = 'ma-chat-msg ma-bot';
        div.innerHTML =
            '<span class="ma-chat-typing">' +
            '<span class="ma-chat-typing-dot"></span>' +
            '<span class="ma-chat-typing-dot"></span>' +
            '<span class="ma-chat-typing-dot"></span>' +
            '</span>';
        typingBubble = div;
        bodyEl.appendChild(div);
        bodyEl.scrollTop = bodyEl.scrollHeight;
    }

    function hideTyping() {
        if (typingBubble && typingBubble.parentNode) {
            typingBubble.parentNode.removeChild(typingBubble);
        }
        typingBubble = null;
    }

    /* ---------- Intelligence locale (fallback) ---------- */

    function normalize(text) {
        return text
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s]/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function containsAny(text, keywords) {
        return keywords.some(kw => text.includes(normalize(kw)));
    }

    function localAnswer(userText, context, history) {
        const t = normalize(userText);

        // Intent : créer forum
        if (containsAny(t, ['creer forum','créer forum','nouveau forum','add forum'])) {
            return "<strong>Créer un forum sur MindArena</strong><br><br>" +
                   "1️⃣ Va sur la page <em>Forums</em> puis clique sur <strong>+ Créer un forum</strong>.<br>" +
                   "2️⃣ Saisis un <strong>titre</strong> clair (ex : Programmation, Gaming…).<br>" +
                   "3️⃣ Ajoute une <strong>description</strong> qui explique le thème.<br>" +
                   "4️⃣ Valide : si le JS ou le PHP détecte des champs vides, un message d’erreur apparaît.";
        }

        // Intent : publier un message
        if (containsAny(t, ['publication','publier','poster message','nouveau message'])) {
            return "<strong>Publier un message dans un forum</strong><br><br>" +
                   "1️⃣ Ouvre d’abord le forum qui t’intéresse.<br>" +
                   "2️⃣ Clique sur <strong>Créer une nouvelle publication</strong>.<br>" +
                   "3️⃣ Renseigne ton <strong>nom/pseudo</strong> dans le champ Auteur.<br>" +
                   "4️⃣ Écris ton <strong>message</strong> dans le contenu puis valide.";
        }

        // Intent : validation / erreur formulaire
        if (containsAny(t, ['erreur','bug','validation','ne marche pas','marche pas','champ','obligatoire'])) {
            return "<strong>Problème de validation de formulaire</strong><br><br>" +
                   "✅ Le projet valide les champs à la fois en <strong>JavaScript</strong> et en <strong>PHP</strong>.<br>" +
                   "Vérifie que :<br>" +
                   "- Le titre du forum n’est pas vide et assez long.<br>" +
                   "- La description du forum est renseignée.<br>" +
                   "- Pour une publication : Auteur et Contenu ne sont pas vides.<br><br>" +
                   "Tu peux aussi ouvrir la console (F12) pour voir d’éventuelles erreurs JS.";
        }

        // Intent : suppression
        if (containsAny(t, ['supprimer','delete','retirer'])) {
            return "<strong>Supprimer un forum ou une publication</strong><br><br>" +
                   "🗑️ <u>Forum</u> : sur la page des forums, clique sur <strong>Supprimer</strong> à côté du forum.<br>" +
                   "🗑️ <u>Publication</u> : sur la page des publications, utilise le lien <strong>Supprimer</strong> sous le message.<br>" +
                   "Un message de confirmation apparaît avant la suppression.";
        }

        // Intent : signalement
        if (containsAny(t, ['signaler','signalement','report','inapproprie','inapproprié'])) {
            return "<strong>Signaler un contenu</strong><br><br>" +
                   "Le signalement sert à prévenir l’admin qu’un contenu est problématique (insultes, spam, etc.).<br>" +
                   "1️⃣ Clique sur le bouton/lien <strong>Signaler</strong> à côté du forum ou de la publication.<br>" +
                   "2️⃣ Explique la raison du signalement dans le formulaire.<br>" +
                   "3️⃣ L’administrateur voit ensuite la liste des signalements dans le dashboard.";
        }

        // Intent : explication du projet
        if (containsAny(t, ['mindarena','projet','architecture','mvc','base de donnee','base de données'])) {
            return "<strong>Résumé rapide du projet MindArena</strong><br><br>" +
                   "• Architecture PHP de type <strong>MVC simple</strong> (index, controllers, models, views).<br>" +
                   "• Tables principales : <em>forums</em>, <em>publications</em>, <em>reports</em>.<br>" +
                   "• Gestion des forums : création, liste, suppression.<br>" +
                   "• Gestion des publications : ajout, affichage, suppression, signalement.<br>" +
                   "• Dashboard admin : statistiques + gestion des signalements.";
        }

        // Fallback générique
        const last = history.length ? history[history.length - 1].content : null;
        let extra = "";
        if (last) {
            extra = "<br><br>Tu peux aussi préciser ce qui te bloque par rapport à :<br><em>\"" +
                    last + "\"</em>";
        }

        return "Je ne suis pas sûr d’avoir tout compris, mais je peux t’aider sur :<br>" +
               "- la création de forums<br>" +
               "- la publication de messages<br>" +
               "- la suppression / le signalement de contenu<br>" +
               "- ou l’architecture générale du projet MindArena." + extra;
    }

    /* ---------- Appel serveur + fallback ---------- */

    async function sendToServer(userText) {
        let replyUsed = null;

        try {
            const res = await fetch(BASE_URL + '/chatbot_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    message: userText,
                    context: ctx,
                    history: conversation   // on envoie l’historique au backend
                })
            });

            let data = null;
            try {
                data = await res.json();
            } catch (e) {
                console.error('Réponse non JSON', e);
                const local = localAnswer(userText, ctx, conversation);
                replyUsed = "Réponse serveur invalide (non JSON).<br><br>" + local;
                addMessage(replyUsed, 'bot');
                return;
            }

            if (data.error) {
                console.error('Erreur chatbot_api.php:', data);
                const local = localAnswer(userText, ctx, conversation);
                replyUsed = "⚠️ " + data.error + "<br><br>" + local;
                addMessage(replyUsed, 'bot');
                return;
            }

            if (data.reply) {
                let r = (data.reply || "").trim();

                // Si c'est exactement la même réponse que la précédente, on force la réponse locale
                if (lastBotReply && r === lastBotReply.trim()) {
                    const local = localAnswer(userText, ctx, conversation);
                    r = local;
                }

                replyUsed = r;
                addMessage(replyUsed, 'bot');
                return;
            }

            // Pas de reply → fallback local
            const local = localAnswer(userText, ctx, conversation);
            replyUsed = local;
            addMessage(replyUsed, 'bot');

        } catch (e) {
            console.error(e);
            const local = localAnswer(userText, ctx, conversation);
            replyUsed = "Une erreur réseau s'est produite.<br><br>" + local;
            addMessage(replyUsed, 'bot');
        } finally {
            // Met à jour la mémoire si on a vraiment répondu
            if (replyUsed !== null) {
                conversation.push({ role: 'user',      content: userText });
                conversation.push({ role: 'assistant', content: replyUsed });
                lastBotReply = replyUsed;
            }
        }
    }

    function sendUserMessage(text) {
        const trimmed = text.trim();
        if (!trimmed || isWaiting) return;

        addMessage(trimmed, 'user');
        showTyping();
        isWaiting = true;

        sendToServer(trimmed).finally(() => {
            hideTyping();
            isWaiting = false;
        });
    }

    function renderWelcome() {
        let introContext = "";
        switch (ctx) {
            case 'forums':
                introContext = "Tu es sur la page des forums. Je peux t’expliquer comment les parcourir, en créer ou les supprimer.";
                break;
            case 'forum_add':
                introContext = "Tu es en train de créer un forum. Je peux t’aider sur le titre, la description et les erreurs de validation.";
                break;
            case 'publications':
                introContext = "Tu regardes les publications d’un forum. Je peux t’aider à comprendre comment publier, supprimer ou signaler un message.";
                break;
            case 'publication_add':
                introContext = "Tu vas publier un nouveau message. Pose-moi tes questions sur les champs auteur / contenu.";
                break;
            case 'report':
                introContext = "Tu es sur la partie signalement. Je peux t’expliquer quand et comment signaler un contenu.";
                break;
            default:
                introContext = "Je peux t’aider à utiliser les forums, les publications et les signalements sur MindArena.";
        }

        const welcome =
            "Salut 👋 Je suis <strong>MindArena Bot</strong>, ton assistant IA sur ce site.<br>" +
            introContext + "<br><br>" +
            "Tu peux me poser des questions du style :<br>" +
            "• <em>Comment créer un forum ?</em><br>" +
            "• <em>Pourquoi mon formulaire ne marche pas ?</em><br>" +
            "• <em>Comment signaler une publication ?</em>";

        addMessage(welcome, 'bot');
    }

    /* ---------- Ouverture / events ---------- */
    if (toggle && windowEl) {
        toggle.addEventListener('click', () => {
            const open = windowEl.classList.toggle('ma-open');
            if (open && !bodyEl.dataset.initialized) {
                renderWelcome();
                bodyEl.dataset.initialized = '1';
                setTimeout(() => inputEl && inputEl.focus(), 80);
            }
        });
    }
    if (closeEl) {
        closeEl.addEventListener('click', () => windowEl.classList.remove('ma-open'));
    }

    if (formEl && inputEl) {
        formEl.addEventListener('submit', function(e) {
            e.preventDefault();
            const text = inputEl.value;
            inputEl.value = '';
            sendUserMessage(text);
        });
        inputEl.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                formEl.dispatchEvent(new Event('submit'));
            }
        });
    }
})();
</script>

