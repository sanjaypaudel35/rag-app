<?php

namespace Sanjay\Ragbot\Livewire\Tenant;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Services\Tenant\CostCalculator;

class Billing extends Component
{
    /**
     * The project instance.
     */
    public Project $project;

    /**
     * The start date for filtering.
     */
    #[Url]
    public string $startDate = '';

    /**
     * The end date for filtering.
     */
    #[Url]
    public string $endDate = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->project = app('ragbot.project');

        // Default to current month if not set
        if (empty($this->startDate)) {
            $this->startDate = now()->startOfMonth()->format('Y-m-d');
        }

        if (empty($this->endDate)) {
            $this->endDate = now()->endOfMonth()->format('Y-m-d');
        }
    }

    /**
     * Get the chatbot performance data.
     */
    #[Computed]
    public function performanceData(): Collection
    {
        $costCalculator = app(CostCalculator::class);

        $usage = DB::table('rag_messages')
            ->join('rag_conversations', 'rag_messages.conversation_id', '=', 'rag_conversations.id')
            ->join('rag_chatbots', 'rag_conversations.chatbot_id', '=', 'rag_chatbots.id')
            ->where('rag_messages.project_id', $this->project->id)
            ->where('rag_messages.role', 'assistant') // Only assistant messages have tokens/model
            ->whereBetween('rag_messages.created_at', [
                $this->startDate.' 00:00:00',
                $this->endDate.' 23:59:59',
            ])
            ->select([
                'rag_chatbots.id as chatbot_id',
                'rag_chatbots.name as chatbot_name',
                'rag_chatbots.deleted_at as chatbot_deleted_at',
                'rag_messages.model',
                DB::raw('SUM(rag_messages.input_tokens) as total_input_tokens'),
                DB::raw('SUM(rag_messages.output_tokens) as total_output_tokens'),
            ])
            ->groupBy('rag_chatbots.id', 'rag_chatbots.name', 'rag_chatbots.deleted_at', 'rag_messages.model')
            ->get();

        return $usage->groupBy('chatbot_id')->map(function ($models, $chatbotId) use ($costCalculator) {
            $first = $models->first();
            $chatbotName = $first->chatbot_name;
            $chatbotDeletedAt = $first->chatbot_deleted_at;

            $modelBreakdown = $models->map(function ($item) use ($costCalculator) {
                $cost = $costCalculator->calculate(
                    $item->model ?? 'gpt-4o-mini',
                    (int) $item->total_input_tokens,
                    (int) $item->total_output_tokens
                );

                return [
                    'model' => $item->model ?? 'Unknown',
                    'input_tokens' => (int) $item->total_input_tokens,
                    'output_tokens' => (int) $item->total_output_tokens,
                    'cost' => $cost,
                ];
            });

            return [
                'id' => $chatbotId,
                'name' => $chatbotName,
                'deleted_at' => $chatbotDeletedAt,
                'total_input_tokens' => $modelBreakdown->sum('input_tokens'),
                'total_output_tokens' => $modelBreakdown->sum('output_tokens'),
                'total_cost' => $modelBreakdown->sum('cost'),
                'models' => $modelBreakdown,
            ];
        });
    }

    /**
     * Get the total aggregated metrics for the period.
     */
    #[Computed]
    public function totals(): array
    {
        $data = $this->performanceData();

        return [
            'input_tokens' => $data->sum('total_input_tokens'),
            'output_tokens' => $data->sum('total_output_tokens'),
            'cost' => $data->sum('total_cost'),
        ];
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('ragbot::livewire.tenant.billing')
            ->layout('ragbot::layouts.dashboard');
    }
}
