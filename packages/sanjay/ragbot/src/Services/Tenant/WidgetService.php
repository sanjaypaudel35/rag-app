<?php

namespace Sanjay\Ragbot\Services\Tenant;

use Sanjay\Ragbot\Contracts\Repositories\ProjectSettingRepositoryInterface;
use Sanjay\Ragbot\Models\Project;

/**
 * Serves and configures the embeddable chat widget script.
 */
class WidgetService
{
    /**
     * Create a new service instance.
     */
    public function __construct(
        protected ProjectSettingRepositoryInterface $projectSettingRepository
    ) {}

    /**
     * Get the widget JavaScript script.
     */
    public function getScript(Project $project, string $baseUrl): string
    {
        $settings = $this->projectSettingRepository->findOneBy(['project_id' => $project->id]);

        if (! $settings || ! $settings->widget_enabled) {
            return '/* Chat widget is disabled for this project. */';
        }

        $config = json_encode([
            'baseUrl' => $baseUrl,
            'apiKey' => $project->api_key_plain ?? '', // We'll need to pass this if available or use the hashed one if the script is loaded with it
            'title' => $settings->widget_title,
            'color' => $settings->widget_color,
            'logo' => $settings->widget_logo,
            'position' => $settings->widget_position,
            'fullPage' => $settings->widget_full_page,
            'prefix' => config('ragbot.prefix', 'ragbot'),
        ]);

        return $this->generateJs($config);
    }

    /**
     * Generate the self-contained vanilla JS for the widget.
     */
    protected function generateJs(string $configJson): string
    {
        // This is a template for the widget JS.
        // In a real scenario, this might be loaded from a file and minified.
        return <<<JS
(function() {
    const config = {$configJson};
    
    // Create widget container
    const container = document.createElement('div');
    container.id = 'ragbot-widget-container';
    document.body.appendChild(container);

    // Add styles
    const style = document.createElement('style');
    style.textContent = `
        #ragbot-widget-button {
            position: fixed;
            bottom: 20px;
            \${config.position === 'left' ? 'left' : 'right'}: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: \${config.color};
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: transform 0.3s ease;
        }
        #ragbot-widget-button:hover {
            transform: scale(1.05);
        }
        #ragbot-widget-panel {
            position: fixed;
            bottom: 90px;
            \${config.position === 'left' ? 'left' : 'right'}: 20px;
            width: \${config.fullPage ? 'calc(100% - 40px)' : '380px'};
            height: \${config.fullPage ? 'calc(100% - 110px)' : '500px'};
            max-height: 80vh;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            display: none;
            flex-direction: column;
            overflow: hidden;
            z-index: 9998;
        }
        #ragbot-widget-header {
            background-color: \${config.color};
            color: white;
            padding: 16px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        #ragbot-widget-messages {
            flex: 1;
            padding: 16px;
            overflow-y: auto;
            background: #f9fafb;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .ragbot-message {
            padding: 8px 12px;
            border-radius: 8px;
            max-width: 80%;
            font-size: 14px;
            line-height: 1.4;
        }
        .ragbot-message-user {
            align-self: flex-end;
            background-color: \${config.color};
            color: white;
        }
        .ragbot-message-bot {
            align-self: flex-start;
            background-color: #e5e7eb;
            color: #1f2937;
        }
        #ragbot-widget-input-container {
            padding: 12px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 8px;
        }
        #ragbot-widget-input {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            outline: none;
        }
        #ragbot-widget-send {
            padding: 8px 16px;
            background-color: \${config.color};
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        #ragbot-widget-send:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    `;
    document.head.appendChild(style);

    // Create Button
    const button = document.createElement('div');
    button.id = 'ragbot-widget-button';
    button.innerHTML = config.logo ? `<img src="\${config.logo}" style="width:30px;height:30px;">` : `
        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
    `;
    container.appendChild(button);

    // Create Panel
    const panel = document.createElement('div');
    panel.id = 'ragbot-widget-panel';
    panel.innerHTML = `
        <div id="ragbot-widget-header">
            <span>\${config.title}</span>
            <span id="ragbot-widget-close" style="cursor:pointer">&times;</span>
        </div>
        <div id="ragbot-widget-messages"></div>
        <div id="ragbot-widget-input-container">
            <input type="text" id="ragbot-widget-input" placeholder="Type your message...">
            <button id="ragbot-widget-send">Send</button>
        </div>
    `;
    container.appendChild(panel);

    // Toggle Panel
    button.onclick = () => {
        panel.style.display = panel.style.display === 'flex' ? 'none' : 'flex';
    };
    document.getElementById('ragbot-widget-close').onclick = () => {
        panel.style.display = 'none';
    };

    // Chat Logic
    const input = document.getElementById('ragbot-widget-input');
    const sendBtn = document.getElementById('ragbot-widget-send');
    const messagesContainer = document.getElementById('ragbot-widget-messages');
    let currentSessionId = null;

    function addMessage(text, role) {
        const msg = document.createElement('div');
        msg.className = "ragbot-message ragbot-message-" + role;
        msg.textContent = text;
        messagesContainer.appendChild(msg);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    sendBtn.onclick = async () => {
        const text = input.value.trim();
        if (!text) return;

        input.value = '';
        addMessage(text, 'user');
        
        sendBtn.disabled = true;
        const loadingMsg = document.createElement('div');
        loadingMsg.className = 'ragbot-message ragbot-message-bot';
        loadingMsg.textContent = '...';
        messagesContainer.appendChild(loadingMsg);

        try {
            // Get config from script tag attributes
            const scriptTag = document.currentScript || document.querySelector('script[data-api-key]');
            const attrApiKey = scriptTag ? scriptTag.getAttribute('data-api-key') : null;
            const attrBaseUrl = scriptTag ? scriptTag.getAttribute('data-base-url') : null;

            const apiKey = config.apiKey || attrApiKey;
            const baseUrl = attrBaseUrl || config.baseUrl;

            const payload = { message: text };
            if (currentSessionId) payload.session_id = currentSessionId;

            const response = await fetch(baseUrl + "/" + config.prefix + "/api/v1/chat", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Api-Key': apiKey,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();
            loadingMsg.remove();

            if (data.data && data.data.message) {
                addMessage(data.data.message, 'bot');
                if (data.data.session_id) {
                    currentSessionId = data.data.session_id;
                }
            } else {
                addMessage(data.error || 'Something went wrong.', 'bot');
            }
        } catch (error) {
            loadingMsg.remove();
            addMessage('Failed to connect to the server.', 'bot');
        } finally {
            sendBtn.disabled = false;
        }
    };

    input.onkeypress = (e) => {
        if (e.key === 'Enter') sendBtn.click();
    };
})();
JS;
    }
}
