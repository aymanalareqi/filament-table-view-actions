<?php

namespace Alareqi\TableViewActions\Livewire;

use App\Models\User;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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

    protected TableAction $ownerAction;

    protected array $modalTableColumns = [];

    protected array $modalTableActions = [];

    protected array $modalTableBulkActions = [];

    protected array $modalTableFilters = [];

    protected Builder| null $modalTableQuery = null;

    protected Closure | null $modelModifyQueryUsing = null;

    protected Closure | null $modalTable = null;

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
        $this->ownerAction = $actionData['ownerAction'];
    }

    public function table(Table $table): Table
    {
        $_table = $table
            ->relationship(fn(): Relation | Builder | null => $this->modalTableRelationship)
            ->query(fn(): Relation | Builder | null => $this->modalTableQuery)
            ->columns($this->modalTableColumns)
            ->filters($this->modalTableFilters)
            ->actions($this->modalTableActions)
            ->recordAction(function (Model $record, Table $table): ?string {
                foreach (['view', 'edit'] as $action) {
                    $action = $table->getAction($action);

                    if (! $action) {
                        continue;
                    }

                    $action->record($record);

                    if ($action->isHidden()) {
                        continue;
                    }

                    if ($action->getUrl()) {
                        continue;
                    }

                    return $action->getName();
                }

                return null;
            })
            ->recordUrl(function (Model $record, Table $table): ?string {
                foreach (['view', 'edit'] as $action) {
                    $action = $table->getAction($action);

                    if (! $action) {
                        continue;
                    }

                    $action->record($record);

                    if ($action->isHidden()) {
                        continue;
                    }

                    $url = $action->getUrl();

                    if (! $url) {
                        continue;
                    }

                    return $url;
                }

                return null;
            })
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
    protected function configureTableAction(TableAction $action): void
    {
        match (true) {

            $action instanceof ViewAction => $this->configureViewAction($action),
            default => null,
        };
    }
    protected function configureViewAction(ViewAction $action): void
    {
        $action
            ->infolist(fn(Infolist $infolist): Infolist => $this->ownerAction->getInfolist())
            ->form(fn(Form $form): Form => $this->ownerAction->getForm($form));
    }
    public function render()
    {
        return view('table-view-actions::livewire.table-view-component');
    }
}
