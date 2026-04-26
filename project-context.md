# Project context

We are building a **production-ready Laravel 13 reusable package** named:

    sanjay/ragbot

This package implements a **multi-tenant RAG (Retrieval-Augmented Generation) chatbot system**.

Each tenant (project) must have **strict data isolation**, meaning:
- documents, embeddings, conversations, and settings must belong to a specific tenant
- no data leakage between tenants

## System Overview

- Multi-tenant RAG chatbot system
- Packaged as Laravel reusable package
- Supports dashboard, API, and widget

Core flow:
Document → Chunk → Embed → Store
Query → Retrieve → Prompt → LLM → Response

## Tenant Features

Each tenant (project) can:

### Authentication
- register, login, logout
- password reset

### Dashboard
- upload documents (pdf, doc, text)
- manage documents

### AI Configuration
- select LLM provider
- configure API keys

### Vector Storage
- primary: pgvector (PostgreSQL)
- fallback: mysql (JSON)

### Chat Access
- API mode (JSON)
- widget mode (popup chat)

### API Key
- generate API key for integration

## Architecture Rules

- Use Repository pattern strictly
- Use Strategy pattern for:
  - LLM providers
  - Vector storage
- Use Service layer for business logic
- No direct model usage in services
- Use dependency injection only

## Multi-Tenancy

- Every table must include project_id
- Every query must filter project_id
- No cross-tenant data leakage

- Middleware must:
  - resolve project via API key
  - bind project in container

- Repositories must enforce project scope

## Service Responsibilities

### DocumentService
- store document
- dispatch processing job

### ChunkingService
- split text into chunks

### EmbeddingService
- generate embeddings

### VectorStoreService
- store + search vectors

### RetrievalService
- fetch relevant chunks

### PromptBuilderService
- build prompt from chunks

### LLMService
- generate response

### ChatService
- orchestrate full pipeline

## UI Rules

- Use Livewire + Flux UI + Tailwind
- Keep logic in services (not components)
- Components only trigger actions

## Prompt Execution Rules

- Build stage-by-stage
- Never generate multiple layers at once
- Always review before next stage
- Follow repository + multi-tenant rules strictly

## Backend Engineering Rules

### Controllers

- Controllers must NOT contain business logic
- Controllers must only:
  - accept request
  - call service
  - return response

- All controllers must:
  - use Form Request classes for validation
  - be wrapped in try-catch block

Example structure:

try {
    // call service
} catch (\Throwable ) {
    return ->handleException();
}

### Request Validation

- All validation must be handled via Form Request classes
- No validation logic inside controllers or services

### Exception Handling
- Create a centralized exception handler trait:

HandlesApiExceptions

Responsibilities:
- map exceptions to HTTP status codes
- return JSON response

- All controllers must use this trait

### Custom Exceptions
- Create custom exception classes for business logic:

Examples:
- DocumentProcessingException
- EmbeddingException
- RetrievalException
- LlmResponseException

Rules:
- Throw exceptions inside service layer
- Do NOT handle exceptions inside services
- Let controllers handle them via centralized handler

### Repository Pattern (STRICT)
- Each model must have its own repository

Examples:
- DocumentRepository
- ChunkRepository
- ConversationRepository

- All repositories must extend BaseRepository

- Rules:
  - No business logic in repository
  - Must enforce project_id filtering
  - Services must depend on repository interfaces only

### API Response Standard
- Use Laravel API Resources (JsonResource) and ResourceCollection all JSON responses

Examples:
- ChatResource
- ConversationResource

- Use ResourceCollection where needed

- Responses must include:
  - proper HTTP status codes
  - structured JSON format

### Code Quality
- Follow PSR-12 coding standard
- Add PHPDoc for:
  - all classes
  - all public methods
  - service responsibilities

- Service classes must include a top-level description explaining responsibility

### Service Layer Rules
- Services must:
  - contain all business logic
  - throw custom exceptions
  - never return raw models directly
  - use repository interfaces

- Services must be small and focused

## Database Rules

- All tables must use UUID as primary key
- Add indexes whenever required
- Use foreign key constraints where applicable

- Embedding storage:
  - PostgreSQL: vector column (pgvector)
  - MySQL: JSON fallback

- Migrations must include comments
- while creating each column on migration add comment as well

## Queue Rules

- All heavy operations must be queued:
  - document processing
  - embedding generation

- Use ShouldQueue interface
- Ensure jobs are idempotent

## Tenant Context Handling

- Resolved tenant must be injected into services
- Services must always operate within tenant scope
- Never pass raw project_id manually when avoidable
- Prefer container-bound tenant instance

## Widget Requirements

- Provide embeddable JS snippet
- Widget must:
  - accept API key
  - open chat popup
  - call API endpoint
- Tenant can enable/disable widget


## Service Contracts

- Each core service must have an interface:
  - EmbeddingInterface
  - VectorStoreInterface
  - LLMInterface

- Use dependency injection
- No direct instantiation

### Other rules
use "" instead of ''

### API SECURITY
