<?php

namespace App\Exports;

use App\Models\Todo;
use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TodoExport implements FromCollection
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Todo::query();

        if ($this->request->has('title')) {
            $query->where('title', 'like', '%' . $this->request->title . '%');
        }

        if ($this->request->has('assignee')) {
            $assignees = explode(',', $this->request->assignee);
            $query->whereIn('assignee', $assignees);
        }

        if ($this->request->has('due_date')) {
            $dates = explode('&', $this->request->due_date);
            
          
            if (isset($dates[0]) && str_starts_with($dates[0], 'start=') && isset($dates[1]) && str_starts_with($dates[1], 'end=')) {
                $startDate = substr($dates[0], 6); 
                $endDate = substr($dates[1], 4);   
                $query->whereBetween('due_date', [$startDate, $endDate]);
            }
        }


        

        if ($this->request->has('time_tracked')) {
            $timeRange = explode('&', $this->request->time_tracked);
        

            if (isset($timeRange[0]) && str_starts_with($timeRange[0], 'min=') && isset($timeRange[1]) && str_starts_with($timeRange[1], 'max=')) {
                $minTime = (int) substr($timeRange[0], 4); // Remove "min="
                $maxTime = (int) substr($timeRange[1], 4); // Remove "max="
                $query->whereBetween('time_tracked', [$minTime, $maxTime]);
            }
        }
        


        if ($this->request->has('status')) {
            $statuses = explode(',', $this->request->status);
            $query->whereIn('status', $statuses);
        }

        if ($this->request->has('priority')) {
            $priorities = explode(',', $this->request->priority);
            $query->whereIn('priority', $priorities);
        }

        $todos = $query->get();

        // Include summary row
        $exportData = collect([
            ['ID', 'Title', 'Assignee', 'Due Date', 'Time Tracked', 'Status', 'Priority', 'Created At', 'Updated At']
        ]);

        foreach ($todos as $todo) {
            $exportData->push([
                $todo->id,
                $todo->title,
                $todo->assignee,
                $todo->due_date,
                $todo->time_tracked,
                $todo->status,
                $todo->priority,
                $todo->created_at,
                $todo->updated_at,
            ]);
        }

        // Add summary row
        $exportData->push([
            'Total Todos:', $todos->count(),
            'Total Time Tracked:', $todos->sum('time_tracked'),
            '', '', '', '', ''
        ]);

        return $exportData;
    }   
}
