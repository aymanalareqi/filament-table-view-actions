<?php

namespace Alareqi\TableViewActions\Livewire;

use App\Models\User;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Livewire\Component;
use phpDocumentor\Reflection\Types\Null_;

class TableViewComponent extends Component implements HasForms, HasTable
{
    use InteractsWithTable;

    use InteractsWithForms;

    use InteractsWithRecord {
        configureAction as configureActionRecord;
    }

    protected array $modalTableColumns = [];

    protected array $modalTableActions = [];

    protected array $modalTableBulkActions = [];

    protected array $modalTableFilters = [];

    protected Builder| null $modalTableQuery = null;

    protected Closure | null $modelModifyQueryUsing = null;

    protected Relation | Builder | null $modalTableRelationship = null;

    public function mount($actionData)
    {
        $this->modalTableColumns = $actionData['tableColumns'];
        $this->modalTableActions = $actionData['tableActions'] ?? [];
        $this->modalTableBulkActions = $actionData['tableBulkActions'] ?? [];
        $this->modalTableFilters = $actionData['tableFilters'] ?? [];
        $this->modalTableQuery = $actionData['tableQuery'] ?? null;
        $this->modalTableRelationship = $actionData['tableRelationship'] ?? null;
        $this->modelModifyQueryUsing = $actionData['modifyQueryUsing'] ?? null;
    }

    public function table(Table $table): Table
    {
        $_table = $table

            ->relationship(fn(): Relation | Builder | null => $this->modalTableRelationship)
            ->query($this->modalTableQuery)
            ->columns($this->modalTableColumns)
            ->filters($this->modalTableFilters)
            ->recordAction($this->modalTableActions)
            ->bulkActions($this->modalTableBulkActions);
        if ($this->modelModifyQueryUsing !== null) {
            $_table = $_table->modifyQueryUsing($this->modelModifyQueryUsing);
        }
        return $_table;
    }
    protected function configureAction(Action $action): void
    {
        $this->configureActionRecord($action);
    }
    public function render()
    {
        return view('table-view-actions::livewire.table-view-component');
    }
}
