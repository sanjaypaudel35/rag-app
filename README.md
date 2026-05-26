# 🤖 RAGBot: Intelligent AI assistant

RAGBot is a sophisticated **Retrieval-Augmented Generation (RAG)** system built with Laravel. It empowers businesses and developers to create custom AI chatbots grounded in their own unique data, ensuring accurate, contextual, and domain-specific responses.

---

## 🌟 About RAGBot

RAGBot doesn't just "chat." It understands your specific business data. By uploading your documents, RAGBot indexes them into a high-performance database, allowing the AI to use your files as a "knowledge base" when answering questions.

### How it Works
1.  **Ingestion:** Upload your documents (PDF, DOCX, TXT). RAGBot segmentizes the text, converts it into mathematical vectors (embeddings), and stores them in a vector database.
2.  **Retrieval:** When a user asks a question, RAGBot performs a semantic search to find the most relevant pieces of information from your documents.
3.  **Generation:** The relevant context is sent to a Large Language Model (like GPT-4o), which generates a human-like response based *only* on the provided data.

### Where it's Applicable
-   **Customer Support:** Automated answers based on product manuals and FAQs.
-   **Internal Knowledge:** An AI assistant for employee handbooks, policies, and internal documentation.
-   **E-commerce:** Help customers find product information and compatibility details.
-   **Enterprise Search:** Semantic retrieval across massive private document libraries.

---

## 🏢 Technical Architecture

RAGBot is built using a clean, layered architecture following the **Service-Repository pattern**, ensuring the core RAG logic remains decoupled and highly testable.

### The RAG Pipeline
```text
Ingestion:   Document → Chunking → Embedding (API) → Vector Store
Retrieval:   User Query → Embedding → Semantic Search → Context Retrieval
Generation:  Context + Prompt → LLM (OpenAI/Anthropic) → Response
```

### Core Tech Stack
-   **Framework:** Laravel 13
-   **Frontend:** Livewire 4 + Flux UI
-   **Vector Storage:** Native support for PostgreSQL (`pgvector`) and MySQL.
-   **Processing:** Asynchronous bus batching for high-volume document ingestion.


---

## 🛠️ Developer Extension Guide

Ragbot is designed to be highly extensible. You can override core logic by implementing interfaces and extending managers.

### Custom Embedding Logic
If you need to use a local embedding model (via Ollama or Llama.cpp), a specialized private API, or custom pre-processing logic, you can easily do so. Simply implement the `EmbeddingInterface` and register it in your `AppServiceProvider@boot`.

**The Interface:**
```php
interface EmbeddingInterface
{
    /**
     * Generates vector embeddings for text.
     *
     * @return array<float>
     */
    public function embed(string $text, ?Project $project = null): array;
}
```

**Registration:**
```php
$this->app->make(EmbeddingManager::class)->extend('my-driver', function ($app, $project) {
    return new MyLocalEmbedding();
});
```

### Custom LLM Service
If you need to use a local LLM (like Ollama, LocalAI, or vLLM), a private inference server, or a specialized model not supported out-of-the-box, you can register a custom driver. Implement the `LlmInterface` and register it in your `AppServiceProvider@boot`.

**The Interface:**
```php
interface LlmInterface
{
    /**
     * Generate a completion for the given prompt.
     *
     * @param  array<string, mixed>  $options
     */
    public function complete(string $prompt, array $options = []): LlmResponse;
}
```

**Registration:**
```php
$this->app->make(LlmManager::class)->extend('local-llm', function ($app, $project) {
    return new MyLocalLlmService($project);
});
```
*Once registered, you can set your LLM Provider to `local-llm` in your project settings.*

### Custom Vector Store
If you want to use other specialized vector databases such as **Pinecone**, **Weaviate**, **Milvus**, or **ChromaDB**, you can easily integrate them. Just implement the `VectorStoreInterface` and extend the `VectorStoreManager` to register your custom driver.

**The Interface:**
```php
interface VectorStoreInterface
{
    /**
     * Stores vector embedding for a chunk.
     */
    public function store(Project $project, string $chunkId, array $vector): void;

    /**
     * Searches for chunks similar to the query vector.
     *
     * @return \Illuminate\Support\Collection
     */
    public function search(Project $project, array $queryVector, int $topK = 5, array $documentIds = []): Collection;
}
```

**Registration:**
```php
$this->app->make(VectorStoreManager::class)->extend('pinecone', function ($app) {
    return new PineconeStore();
});
```
*Once registered, you can select "Custom" in the LLM Settings tab and provide your driver name (e.g., `pinecone`).*

---

## 🚀 Getting Started

Follow this guide to get your RAGBot instance up and running for development or testing.

### 1. Prerequisites
-   **PHP 8.3+**
-   **PostgreSQL** (Recommended for `pgvector`) or **MySQL**
-   **Composer**
-   **Node.js & NPM**

### 2. Installation & Setup

```bash
# Clone the repository
git clone https://github.com/your-username/ragbot.git
cd ragbot

# Install dependencies
composer install
npm install && npm run build

# Setup environment
cp .env.example .env
php artisan key:generate

# Run migrations and seeders
php artisan migrate --seed
php artisan storage:link
```

### 3. Initial Access
1.  Start the server: `php artisan serve`
2.  Navigate to `http://127.0.0.1:8000`.
3.  **Seeded Project:** A "Test Project" is automatically created during the seeder.
    *   **Registration:** Register your account within the test project here: `http://127.0.0.1:8000/ragbot/tenant/test-project/register`
    *   **Dashboard:** After registering, access your dashboard here: `http://127.0.0.1:8000/ragbot/tenant/test-project/dashboard`
    *   **Customization:** Once logged in, you can update the project title and logo from the **Project Setting** tab in the dashboard.

### 4. Configuration Workflow

1.  **AI Engine Setup:** 
    *   Navigate to **Settings > LLM Setting**.
    *   Enter your **OpenAI** or **Anthropic** API Key.
    *   Select your preferred Chat and Embedding models.
2.  **Training the AI (Documents):** 
    *   Upload your business documents (PDF, DOCX, TXT) in the **Documents** section.
    *   Monitor the **Processing Queue** tab. Once the status changes to `Success`, your documents are fully indexed and searchable.
3.  **Creating Chatbots & Knowledge Bases:**
    *   Navigate to **Chatbots** to create your AI assistants.
    *   **Selective Knowledge:** For each chatbot, you can select a specific "Knowledge Base" by picking which processed documents it should have access to.
    *   **Multiple Bots:** You can create multiple chatbots (e.g., "HR Bot", "Technical Support"), each linked to a different set of documents.
4.  **Security & Widget Configuration:**
    *   Navigate to **Settings > Chatbot Setting**.
    *   **Rate Limits:** Set a global rate limit for the chatbot and a specific limit per user session to prevent API abuse.
    *   **Widget Customization:** Configure the brand color, title, and screen position (Left/Right) for the embeddable chat widget.
5.  **Integration & Deployment:**
    *   Each chatbot is assigned a unique **API Key**.
    *   Visit the **Integration Guide** on your dashboard to see how to implement your bot via the **REST API** or by using the **1-line Widget Script**.


---

*RAGBot: Empowering your data with the intelligence of AI.*
