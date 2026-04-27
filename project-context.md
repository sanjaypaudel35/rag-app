# project-context.md — sanjay/ragbot

## Overview

We are building a **production-ready, reusable Laravel package** named `sanjay/ragbot`.

It implements a **multi-tenant RAG (Retrieval-Augmented Generation) chatbot system** where each
tenant (project) has strict data isolation across documents, embeddings, conversations, and settings.

---

## Environment

| Concern        | Value                          |
|----------------|-------------------------------|
| PHP            | 8.3                           |
| Laravel        | 13                            |
| Livewire       | 4                             |
| Flux UI        | 2 (free tier)                 |
| Tailwind CSS   | 4                             |
| Queue driver   | database (default), redis     |
| Primary DB     | PostgreSQL (pgvector)         |
| Fallback DB    | MySQL (JSON vector fallback)  |
| Package name   | sanjay/ragbot                 |
| Root namespace | Sanjay\Ragbot                 |
| Package path   | packages/sanjay/ragbot/       |

---

## String Quoting Rule

Always use double quotes `"` instead of single quotes `'` for all PHP strings.

---

## Core Flow

```
Document → Chunk → Embed → Store
Query → Embed → Retrieve → Prompt → LLM → Response
```

---

## Package Folder Structure

```
packages/sanjay/ragbot/
├── composer.json
├── config/
│   └── ragbot.php
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── auth.blade.php
│       │   └── dashboard.blade.php
│       └── livewire/
├── routes/
│   ├── web.php
│   └── api.php
└── src/
    ├── RagbotServiceProvider.php
    ├── Contracts/
    │   ├── Repositories/
    │   │   ├── ProjectRepositoryInterface.php
    │   │   ├── UserRepositoryInterface.php
    │   │   ├── DocumentRepositoryInterface.php
    │   │   ├── ChunkRepositoryInterface.php
    │   │   ├── EmbeddingRepositoryInterface.php
    │   │   ├── ConversationRepositoryInterface.php
    │   │   ├── MessageRepositoryInterface.php
    │   │   └── ProjectSettingsRepositoryInterface.php
    │   └── Services/
    │       ├── EmbeddingInterface.php
    │       ├── VectorStoreInterface.php
    │       └── LLMInterface.php
    ├── Exceptions/
    │   ├── DocumentProcessingException.php
    │   ├── EmbeddingException.php
    │   ├── RetrievalException.php
    │   └── LlmResponseException.php
    ├── Http/
    │   ├── Controllers/
    │   │   ├── Api/
    │   │   │   └── ChatController.php
    │   │   └── WidgetController.php
    │   ├── Middleware/
    │   │   ├── ResolveProjectFromApiKey.php
    │   │   └── RedirectIfNotRagbotAuthenticated.php
    │   ├── Requests/
    │   │   ├── UploadDocumentRequest.php
    │   │   └── ChatRequest.php
    │   ├── Resources/
    │   │   ├── ChatResource.php
    │   │   └── ConversationResource.php
    │   └── Traits/
    │       └── HandlesApiExceptions.php
    ├── Jobs/
    │   ├── ProcessDocumentJob.php
    │   ├── ChunkDocumentJob.php
    │   ├── EmbedChunksJob.php
    │   └── StoreVectorsJob.php
    ├── Livewire/
    │   ├── Dashboard.php
    │   ├── DocumentManager.php
    │   ├── SettingsManager.php
    │   └── ApiKeyManager.php
    ├── LLM/
    │   ├── LLMManager.php
    │   ├── OpenAILLMService.php
    │   ├── AnthropicLLMService.php
    │   └── StubLLMService.php
    ├── Models/
    │   ├── Project.php
    │   ├── User.php
    │   ├── Document.php
    │   ├── Chunk.php
    │   ├── Embedding.php
    │   ├── Conversation.php
    │   ├── Message.php
    │   └── ProjectSettings.php
    ├── Repositories/
    │   ├── BaseRepository.php
    │   ├── ProjectRepository.php
    │   ├── UserRepository.php
    │   ├── DocumentRepository.php
    │   ├── ChunkRepository.php
    │   ├── EmbeddingRepository.php
    │   ├── ConversationRepository.php
    │   ├── MessageRepository.php
    │   └── ProjectSettingsRepository.php
    ├── Services/
    │   ├── DocumentService.php
    │   ├── ChunkingService.php
    │   ├── EmbeddingService.php
    │   ├── RetrievalService.php
    │   ├── PromptBuilderService.php
    │   ├── ChatService.php
    │   ├── ProjectSettingsService.php
    │   ├── ApiKeyService.php
    │   └── WidgetService.php
    ├── Support/
    │   └── TextExtractor.php
    └── VectorStore/
        ├── VectorStoreManager.php
        ├── MysqlVectorStoreService.php
        └── PgvectorStoreService.php
```

---

## Config File Structure

The complete shape of `config/ragbot.php` must be defined from Stage 1 and never changed
structurally in later stages — only values are filled in as features are built.

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Route Prefix & Middleware
    |--------------------------------------------------------------------------
    */
    "prefix"         => env("RAGBOT_PREFIX", "ragbot"),
    "middleware"     => ["web"],
    "api_middleware" => ["api"],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guard
    |--------------------------------------------------------------------------
    */
    "guard" => "ragbot",

    /*
    |--------------------------------------------------------------------------
    | LLM Configuration
    |--------------------------------------------------------------------------
    */
    "llm" => [
        "default" => env("RAGBOT_LLM_PROVIDER", "openai"),
        "providers" => [
            "openai" => [
                "model"    => env("RAGBOT_OPENAI_MODEL", "gpt-4o-mini"),
                "base_url" => "https://api.openai.com/v1",
            ],
            "anthropic" => [
                "model"    => env("RAGBOT_ANTHROPIC_MODEL", "claude-3-haiku-20240307"),
                "base_url" => "https://api.anthropic.com",
            ],
            "stub" => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Embedding Configuration
    |--------------------------------------------------------------------------
    */
    "embedding" => [
        "default"    => env("RAGBOT_EMBEDDING_PROVIDER", "openai"),
        "dimensions" => 1536,
        "providers"  => [
            "openai" => [
                "model"    => "text-embedding-3-small",
                "base_url" => "https://api.openai.com/v1",
            ],
            "stub" => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Vector Store Configuration
    |--------------------------------------------------------------------------
    */
    "vector_store" => [
        "default" => env("RAGBOT_VECTOR_STORE", "mysql"),
        "drivers" => [
            "pgvector" => [],
            "mysql"    => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Chunking Configuration
    |--------------------------------------------------------------------------
    */
    "chunking" => [
        "size"    => 500,
        "overlap" => 50,
    ],

    /*
    |--------------------------------------------------------------------------
    | Retrieval Configuration
    |--------------------------------------------------------------------------
    */
    "retrieval" => [
        "top_k" => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Widget Configuration
    |--------------------------------------------------------------------------
    */
    "widget" => [
        "enabled"         => true,
        "allowed_origins" => "*",
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage
    |--------------------------------------------------------------------------
    */
    "storage" => [
        "disk" => env("RAGBOT_STORAGE_DISK", "local"),
        "path" => "ragbot/{project_id}/documents",
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    "rate_limit" => [
        "chat_api" => env("RAGBOT_RATE_LIMIT", 60), // requests per minute per project
    ],

];
```

---

## Database Schema

### Rules
- All primary keys: UUID (`char(36)`)
- Every table includes `project_id` (UUID, indexed, FK → projects)
- All migrations must include column-level comments
- Soft deletes only on `documents` table
- Timestamps on all tables

### Tables

#### projects
| Column     | Type          | Notes                        |
|------------|---------------|------------------------------|
| id         | uuid PK       |                              |
| name       | string        |                              |
| slug       | string unique |                              |
| api_key    | string unique | indexed, used for resolution |
| is_active  | boolean       | default true                 |
| timestamps |               |                              |

#### ragbot_users
| Column         | Type    | Notes                          |
|----------------|---------|--------------------------------|
| id             | uuid PK |                                |
| project_id     | uuid FK | → projects, cascade delete     |
| name           | string  |                                |
| email          | string  | unique per project_id          |
| password       | string  |                                |
| remember_token | string  |                                |
| timestamps     |         |                                |

#### documents
| Column        | Type                                        | Notes              |
|---------------|---------------------------------------------|--------------------|
| id            | uuid PK                                     |                    |
| project_id    | uuid FK                                     | → projects         |
| name          | string                                      |                    |
| file_path     | string                                      |                    |
| mime_type     | string                                      |                    |
| status        | enum(pending, processing, completed, failed)|                    |
| error_message | text nullable                               |                    |
| deleted_at    | timestamp nullable                          | soft delete        |
| timestamps    |                                             |                    |

#### chunks
| Column      | Type    | Notes              |
|-------------|---------|--------------------|
| id          | uuid PK |                    |
| project_id  | uuid FK | → projects         |
| document_id | uuid FK | → documents        |
| content     | text    |                    |
| chunk_index | integer |                    |
| token_count | integer | nullable           |
| timestamps  |         |                    |

#### embeddings
| Column     | Type    | Notes                              |
|------------|---------|------------------------------------|
| id         | uuid PK |                                    |
| project_id | uuid FK | → projects                         |
| chunk_id   | uuid FK | → chunks                           |
| vector     | json    | MySQL fallback (pgvector uses tsvector column instead) |
| timestamps |         |                                    |

#### conversations
| Column     | Type    | Notes         |
|------------|---------|---------------|
| id         | uuid PK |               |
| project_id | uuid FK | → projects    |
| session_id | string  | indexed       |
| metadata   | json    | nullable      |
| timestamps |         |               |

#### messages
| Column          | Type              | Notes           |
|-----------------|-------------------|-----------------|
| id              | uuid PK           |                 |
| project_id      | uuid FK           | → projects      |
| conversation_id | uuid FK           | → conversations |
| role            | enum(user,assistant)|               |
| content         | text              |                 |
| timestamps      |                   |                 |

#### project_settings
| Column         | Type    | Notes                          |
|----------------|---------|--------------------------------|
| id             | uuid PK |                                |
| project_id     | uuid FK | unique, → projects             |
| llm_provider   | string  | default: openai                |
| llm_api_key    | string  | nullable, **encrypted at rest**|
| llm_model      | string  | nullable                       |
| embedding_provider | string | default: openai              |
| vector_store   | string  | default: mysql                 |
| widget_enabled | boolean | default: true                  |
| timestamps     |         |                                |

---

## Architecture Rules

### Layer Responsibilities

```
Request → Middleware → Controller → Service → Repository → Model
                                 ↘ Exception → HandlesApiExceptions
```

| Layer       | Responsibility                                         | Can it touch Model directly? |
|-------------|--------------------------------------------------------|------------------------------|
| Controller  | Accept request, call service, return response          | No                           |
| Service     | Business logic, throw exceptions                       | No — via repository only     |
| Repository  | Data access, enforce project_id scope                  | Yes                          |
| Middleware  | Resolve tenant, bind to container                      | Yes (lookup only)            |
| Livewire    | Trigger service actions, hold UI state                 | No                           |

### Controller Rules
- Must use Form Request for all validation
- Must be wrapped in try/catch using `HandlesApiExceptions` trait
- Must return API Resources for all JSON responses
- No business logic whatsoever

```php
public function store(ChatRequest $request): JsonResponse
{
    try {
        $result = $this->chatService->chat(...);
        return new ChatResource($result);
    } catch (\Throwable $e) {
        return $this->handleException($e);
    }
}
```

### Service Rules
- Contain all business logic
- Throw custom exceptions — never catch them internally
- Never return raw Eloquent models — return DTOs or arrays
- Always receive Project via constructor injection (from container binding)
- Must have PHPDoc class description explaining responsibility

### Repository Rules
- Extend `BaseRepository`
- ALL queries must scope by `project_id`
- No business logic
- Implement a corresponding interface

## Multi-Tenancy Rules

- Middleware `ResolveProjectFromApiKey` reads `X-Api-Key` header
- Looks up `Project` where `api_key = ? AND is_active = true`
- Binds to container: `app()->instance("ragbot.project", $project)`
- All services receive Project via DI — never pass `project_id` as raw string if avoidable
- Every repository method that queries data must accept and filter by `project_id`

---

## Service Contracts (Interfaces)

### EmbeddingInterface
```php
public function embed(string $text): array; // returns float[]
```

### VectorStoreInterface
```php
public function store(Project $project, string $chunkId, array $vector): void;
public function search(Project $project, array $queryVector, int $topK): Collection;
```

### LLMInterface
```php
public function complete(string $prompt, array $options = []): string;
```

---

## Exception Mapping

| Exception                    | HTTP Status |
|------------------------------|-------------|
| `DocumentProcessingException`| 422         |
| `EmbeddingException`         | 502         |
| `RetrievalException`         | 502         |
| `LlmResponseException`       | 502         |
| `\Illuminate\Auth\AuthenticationException` | 401 |
| `\Illuminate\Validation\ValidationException` | 422 |
| Generic `\Throwable`         | 500         |

---

## API Standards

- All API routes: `{prefix}/api/v1/`
- All API routes protected by `ragbot.auth` middleware
- All responses use Laravel API Resources
- HTTP status codes must be explicit (never rely on defaults)
- Versioning via URL prefix (`v1`)

### Chat Endpoint
```
POST {prefix}/api/v1/chat
Header: X-Api-Key: {key}
Body: { "message": string, "session_id": string }
Response: ChatResource
```

### ChatResource shape
```json
{
  "data": {
    "message": "string",
    "session_id": "string",
    "conversation_id": "uuid",
    "timestamp": "ISO8601"
  }
}
```

---

## Queue Jobs

| Job                | Triggers              | Idempotent check           |
|--------------------|-----------------------|----------------------------|
| ProcessDocumentJob | After document upload | Skip if status = completed |
| ChunkDocumentJob   | After ProcessDocument | Skip if chunks exist       |
| EmbedChunksJob     | After ChunkDocument   | Skip if embeddings exist   |
| StoreVectorsJob    | After EmbedChunks     | Skip if vectors stored     |

All jobs implement `ShouldQueue`. Failures update document status to `failed` with error message.

---

## Security Rules

- LLM API keys stored in `project_settings.llm_api_key` must use Laravel `encrypted` cast
- API keys for widget/integration must never be exposed in full after creation
- Chat API must be rate limited per project (default: 60 req/min)
- Widget JS endpoint must check `widget_enabled` before serving script
- CORS must be open (`*`) only for `/api/v1/chat` and `/widget.js`
- All other routes use standard Laravel CSRF protection

---

## Widget Rules

- Served at: `GET {prefix}/widget.js?api_key={key}`
- Content-Type: `application/javascript`
- If `widget_enabled = false` → return empty JS file (200, no error)
- If `api_key` invalid → return empty JS file (200, no error — never expose 401 to public)
- Widget is pure vanilla JS + inline CSS — zero external dependencies
- Widget must work when embedded on non-Laravel sites
- Embed snippet format:
```html
<script src="https://yourdomain.com/ragbot/widget.js?api_key=YOUR_KEY"></script>
```

---

## Authentication

- Package uses a custom `ragbot` guard
- Guard uses `ragbot_users` table (not Laravel's default `users`)
- Auth is per-project: `ragbot_users` are scoped to `project_id`
- A user from Project A cannot authenticate under Project B
- Password reset emails resolve the correct project via session-stored `project_id`
- After login → redirect to `{prefix}/dashboard`
- After logout → redirect to `{prefix}/login`

---

## UI Rules

- Use Livewire 4 components (class-based)
- Use Flux UI v2 free components only
- Use Tailwind CSS v4 utility classes
- Livewire components must NOT contain business logic
- Components call services via injected dependencies
- Components may hold UI state (loading, errors, flash messages)

---

## Code Quality Standards

- PSR-12 coding standard throughout
- PHPDoc required on:
  - All classes (with responsibility description on service classes)
  - All public methods (with `@param` and `@return`)
- PHP 8.3 features encouraged:
  - Constructor property promotion
  - Readonly properties where applicable
  - Enums for status fields
  - Named arguments where clarity improves
- No inline comments except for exceptionally complex logic
- Always use curly braces on control structures

---

## Testing Rules

- Framework: PHPUnit (no Pest)
- Test type: Feature tests preferred, unit tests for pure logic
- Every repository must have a test verifying `project_id` scoping
- Every service must have tests for happy path, failure path, and edge cases
- Use model factories — never create models manually in tests
- Mock HTTP calls to LLM providers — never make real API calls in tests

---

## What This Package Does NOT Include (Future Scope)

- SaaS billing / subscription management
- Super admin panel
- Public tenant onboarding/signup flow
- Multi-project per account
- Usage analytics
- Document sync (Notion, Google Drive, etc.)
- Streaming LLM responses
- Team members per project

These are concerns of the SaaS application built on top of this package.