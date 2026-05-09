# 🤖 RAGBot: Multi-Tenant AI Platform

RAGBot is a sophisticated **multi-tenant Retrieval-Augmented Generation (RAG)** system built with Laravel. It empowers businesses and developers to create custom AI chatbots grounded in their own unique data, ensuring accurate, contextual, and domain-specific responses.

---

## 🚀 For Clients & Tenants: What RAGBot Does

RAGBot doesn't just "chat." It understands your specific business data. By uploading your documents, RAGBot indexes them into a high-performance database, allowing the AI to use your files as a "knowledge base" when answering questions.

### 📂 Secure Document Management
- **Universal Support:** Upload PDF, Microsoft Word (DOCX), and Text (TXT) files.
- **Automated Pipeline:** Documents are automatically processed, split into segments, and converted into searchable "AI vectors."
- **In-Browser Preview:** View and manage your uploaded files directly from your dashboard.

### 🤖 Custom AI Chatbots
- **Selective Knowledge:** Choose exactly which documents each chatbot should "know." Have one bot for HR and another for Customer Support.
- **Embeddable Widget:** Add your chatbot to any website with a single line of code.
- **Real-time Diagnostics:** Monitor processing in real-time. If a task fails, we provide detailed logs and a one-click retry button.

### 📊 Analytics & Transparency
- **Usage Tracking:** Monitor token consumption and active conversations across all your bots.
- **Data Privacy:** Your data and AI configurations are strictly isolated and encrypted.

---

## 🛠️ For Developers: Technical Architecture

RAGBot is built using a clean, layered architecture following the **Service-Repository pattern**, ensuring the core RAG logic remains decoupled and highly testable.

### ⚙️ The RAG Pipeline
```text
Ingestion:   Document → Chunking → Embedding (API) → Vector Store
Retrieval:   User Query → Embedding → Semantic Search → Context Retrieval
Generation:  Context + Prompt → LLM (OpenAI/Anthropic) → Response
```

### 🏢 Multi-Tenant Engine
RAGBot is designed for **SaaS environments**. Tenant resolution is handled via unique API keys, with strict container-based context isolation:
```php
// Re-establishes tenant context in background jobs or web requests
app()->instance('ragbot.project', $project);
```

### 🤖 AI SDK & Pluggable Drivers
- **First-Party Integration:** Uses the **Laravel AI SDK (`laravel/ai`)** for native embedding and text generation.
- **Flexible Providers:** Pluggable support for OpenAI, Anthropic, and local LLMs (via Ollama).
- **Resilient Batching:** Large documents are processed in parallel batches with `allowFailures()` support to handle temporary API glitches without stopping the entire pipeline.

### 📦 Core Tech Stack
- **Framework:** Laravel 13
- **Frontend:** Livewire 4 + Flux UI
- **Database:** PostgreSQL (with `pgvector`) or MySQL
- **Background Tasks:** Laravel Queues + Bus Batching
- **Authentication:** Custom Tenant Guards & API Key Security

---

## 🧩 Key Use Cases
- **Customer Support:** Automated answers based on product manuals.
- **Internal Knowledge:** An AI assistant for employee handbooks and policies.
- **Enterprise Search:** Semantic retrieval across massive document libraries.

---

## 🚀 Getting Started (Technical)
1. **Clone & Setup:** Run `composer setup` to install dependencies and run migrations.
2. **Configure AI:** Set your `OPENAI_API_KEY` in the Dashboard Settings.
3. **Start Workers:** `php artisan queue:work` to process document embeddings.
4. **Deploy Widget:** Access your chatbot's unique API key and embed the `widget.js` on your site.

---

*RAGBot: Empowering your data with the intelligence of AI.*
