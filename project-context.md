# project-context.md — sanjay/ragbot

## Overview

We are building a **production-ready, reusable Laravel package** named `sanjay/ragbot`.

It implements a **multi-tenant RAG (Retrieval-Augmented Generation) chatbot system** where each
tenant (project) has strict data isolation across documents, embeddings, conversations, and settings.

## Package Identity

| Concern        | Value                        |
|----------------|------------------------------|
| Package name   | sanjay/ragbot                |
| Root namespace | Sanjay\Ragbot                |
| Package path   | packages/sanjay/ragbot/      |
| PHP            | 8.3                          |
| Laravel        | 13.6.0                       |
| Livewire       | 4.2.4                        |
| Flux UI        | 2.13.2 (free tier only)      |
| Tailwind CSS   | 4.2.2                        |
| Primary DB     | PostgreSQL (pgvector)        |
| Fallback DB    | MySQL (JSON vector fallback) |
| Queue driver   | database (default), redis    |

---

## What This Package Does

A **production-ready, multi-tenant RAG chatbot** Laravel package.
Each tenant (project) has strict data isolation across documents, embeddings,
conversations, and settings.

Core flow:
```
Document → Chunk → Embed → Store
Query → Embed → Retrieve → Prompt → LLM → Response
```

---

## Non-Negotiable Code Rules

- **String quoting:** Always `"` double quotes, never `'` single quotes for PHP strings.
- **Primary keys:** UUID `char(36)` on every table.
- **project_id:** Every table has `project_id` (UUID, indexed, FK → rag_projects). Every repository query must scope by it — no exceptions.
- **PHP 8.3 features:** Constructor property promotion, readonly properties, enums for status fields, named arguments where they aid clarity.
- **PSR-12** throughout.
- **PHPDoc** required on all classes and public methods (`@param`, `@return`). Service classes need a responsibility description.
- No inline comments except for exceptionally complex logic.
- Always use curly braces on control structures.
- Use existing project folder structure if relatable.
- Use inline comment on design pattern or resolved class through service container.

---

## Architecture — Layer Rules

```
Request → Middleware → Controller → Service → Repository → Model
                                 ↘ Exception → HandlesApiExceptions
```

| Layer      | Rule                                                                 |
|------------|----------------------------------------------------------------------|
| Controller | Form Request for validation. Try/catch via HandlesApiExceptions. Return API Resources. Zero business logic. |
| Service    | All business logic. Throws custom exceptions — never catches internally. Returns DTOs/arrays, not Eloquent models. Receives `Project` via constructor DI. |
| Repository | Data access only. ALL queries scoped by `project_id`. Implements interface. No business logic. |
| Middleware | Resolves tenant, binds to container. Lookup only. |
| Livewire   | UI state only. Calls services via DI. Zero business logic. |

---

## Multi-Tenancy

- Middleware `ResolveProjectFromApiKey` reads `X-Api-Key` header.
- Resolves `Project` where `api_key = ? AND is_active = true`.
- Binds as: `app()->instance("ragbot.project", $project)`.
- Services always receive `Project` via DI — never pass raw `project_id` strings if avoidable.

---

## Custom Authentication

- **Guards:** `ragbot` (for tenants), `web` (for platform default).
- **Tables:** `ragbot_users` (for tenants), `users` (for platform).
- Users are scoped to `project_id` — a user from Project A cannot auth under Project B.
- **Tenant Auth Flow:**
  - Routes: `tenant/{project_slug}/login` and `tenant/{project_slug}/register`.
  - Redirects: Login → `tenant/{project_slug}/dashboard`, Logout → `tenant/{project_slug}/login`.
  - Registration: **Manual login required** after successful registration (redirects to login).
- **Platform Auth Flow:**
  - Routes: `login` and `register`.
  - Redirects: Login → `dashboard`, Logout → `login`.
  - Registration: **Manual login required** after successful registration.
- **Middleware:** `guest` applied to all auth views; authenticated users are redirected to their respective dashboards if they hit login/register.

---

## Database Tables

All tables have `timestamps`. All PKs are UUID.

| Table                  | Key columns (beyond id + timestamps)                                                      |
|------------------------|-------------------------------------------------------------------------------------------|
| `rag_projects`         | name, slug (unique), api_key (unique, indexed), is_active (bool, default true), settings (json nullable) |
| `ragbot_users`         | project_id FK, name, email (unique per project_id), password, remember_token             |
| `rag_documents`        | project_id FK, name, file_path, mime_type, status (enum), error_message (text nullable)  |
| `rag_document_chunks`  | project_id FK, document_id FK, content (text), chunk_index (int), token_count (int nullable) |
| `rag_embeddings`       | project_id FK, chunk_id FK, vector (pgvector 1536-dim)                                   |
| `rag_conversations`    | project_id FK, session_id (indexed), metadata (json nullable)                             |
| `rag_messages`         | project_id FK, conversation_id FK, role (enum: user/assistant), content (text)           |
| `rag_project_settings` | project_id FK (unique), llm_provider, llm_api_key (nullable, encrypted cast), llm_model (nullable), vector_store, widget_enabled (bool) |

> Migration rule: all columns must have a column-level comment.

---

## Service Contracts

```php
// EmbeddingInterface
public function embed(string $text): array; // float[]

// VectorStoreInterface
public function store(Project $project, string $chunkId, array $vector): void;
public function search(Project $project, array $queryVector, int $topK): Collection;

// LLMInterface
public function complete(string $prompt, array $options = []): string;
```

---

## Queue Jobs

| Job                  | Idempotent skip condition      |
|----------------------|-------------------------------|
| `ProcessDocumentJob` | Skip if status = completed     |
| `ChunkDocumentJob`   | Skip if chunks exist           |
| `EmbedChunksJob`     | Skip if embeddings exist       |
| `StoreVectorsJob`    | Skip if vectors stored         |

All jobs implement `ShouldQueue`. Failures set document status → `failed` with error message.

---

## API

- Base path: `{prefix}/api/v1/`
- Auth: `ragbot.auth` middleware on all API routes.
- Responses: Laravel API Resources only. Always explicit HTTP status codes.

### Chat endpoint
```
POST {prefix}/api/v1/chat
Header: X-Api-Key: {key}
Body: { "message": string, "session_id": string }
```
ChatResource response shape:
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

## Exception → HTTP Mapping

| Exception                              | Status |
|----------------------------------------|--------|
| `DocumentProcessingException`          | 422    |
| `EmbeddingException`                   | 502    |
| `RetrievalException`                   | 502    |
| `LlmResponseException`                 | 502    |
| `AuthenticationException`              | 401    |
| `ValidationException`                  | 422    |
| Generic `\Throwable`                   | 500    |

---

## Security Rules

- `llm_api_key` in project_settings → Laravel `encrypted` cast.
- API keys never exposed in full after creation.
- Chat API rate-limited per project (default 60 req/min).
- CORS open (`*`) only for `/api/v1/chat` and `/widget.js`.
- All other routes use standard Laravel CSRF protection.

---

## Widget

- Endpoint: `GET {prefix}/widget.js?api_key={key}` → `Content-Type: application/javascript`
- If `widget_enabled = false` OR invalid `api_key` → return empty JS (200, never 401).
- Pure vanilla JS + inline CSS, zero external dependencies.
- Must work when embedded on non-Laravel sites.

---

## Testing

- PHPUnit only (no Pest).
- Feature tests preferred; unit tests for pure logic.
- Every repository must have a test verifying `project_id` scoping.
- Every service: happy path + failure path + edge cases.
- Use model factories — never create models manually in tests.
- Mock all HTTP calls to LLM providers — no real API calls in tests.

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