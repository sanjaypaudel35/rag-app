<?php

namespace Sanjay\Ragbot\Enums;

/**
 * Enum representing supported LLM providers.
 */
enum LlmProvider: string
{
    case OpenAI = "openai";
    case Anthropic = "anthropic";
    case Gemini = "gemini";
}
