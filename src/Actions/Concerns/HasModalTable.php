<?php

namespace Alareqi\TableViewActions\Actions\Concerns;

use Closure;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

trait HasModalTable
{

    protected array | Closure | null $tableColumns = null;

    protected array | Closure | null $tableActions = null;

    protected array | Closure | null $tableBulkActions = null;

    protected Closure | Builder | null $tableQuery = null;

    protected Closure | null $modifyQuerUsing = null;

    protected Closure | null $modalTable = null;

    protected string | Closure | null $relationshipName = null;

    protected array | Closure | null $tableFilters = null;

    public static function getDefaultName(): ?string
    {
        return 'show-table';
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->icon('heroicon-s-table-cells');
    }

    public function modalTable(Closure $table): static
    {
        $this->modalTable = $table;
        return $this;
    }

    public function getModalTable(): ?Closure
    {
        return $this->modalTable;
    }

    public function tableQuery(Builder| Closure $query): static
    {
        $this->tableQuery = $query;
        return $this;
    }

    public function modifyQuerUsing(Closure $modifyQuerUsing): static
    {
        $this->modifyQuerUsing = $modifyQuerUsing;
        return $this;
    }

    public function relationship(string | Closure $name, ?Closure $modifyQuerUsing = null): static
    {
        $this->relationshipName = $name;
        if ($modifyQuerUsing != null) {
            $this->modifyQuerUsing($modifyQuerUsing);
        }
        return $this;
    }

    public function getRelationship(): Relation | Builder | null
    {
        $relationshipName = $this->evaluate($this->relationshipName);
        if ($relationshipName == null) {
            return null;
        }
        return $this->getRecord()->{$this->relationshipName}();
    }

    public function tableFilters(array | Closure $filters): static
    {
        $this->tableFilters = $filters;
        return $this;
    }

    public function tableActions(array | Closure $actions): static
    {
        $this->tableActions = $actions;
        return $this;
    }

    public function tableBulkActions(array | Closure $bulkActions): static
    {
        $this->tableBulkActions = $bulkActions;

        return $this;
    }
    public function getForm(Form $form): ?Form
    {
        return $form
            ->schema([
                ViewField::make('table-view-actions::table-view-component')
                    ->view('table-view-actions::table-view')
                    ->viewData(["actionData" => $this->getActionData()]),
            ]);
    }


    public function getTableActions(): array
    {
        if (! $this->tableActions) {
            return [];
        }
        return $this->evaluate($this->tableActions);
    }


    public function getTableBulkActions(): array
    {
        if (! $this->tableBulkActions) {
            return [];
        }
        return $this->evaluate($this->tableBulkActions);
    }


    public function getTableFilters(): array
    {
        if (! $this->tableFilters) {
            return [];
        }
        return $this->evaluate($this->tableFilters);
    }

    public function tableColumns(array | Closure $columns): static
    {
        $this->tableColumns = $columns;

        return $this;
    }

    public function getModalFooterActions(): array
    {
        return [];
    }
    public function hasModalContent(): bool
    {
        return true;
    }

    public function getTableColumns(): array
    {
        if (! $this->tableColumns) {
            return [];
        }
        return $this->evaluate($this->tableColumns);
    }


    public function getTableQuery(): Builder | null
    {
        return $this->evaluate($this->tableQuery);
    }

    public function getActionData(): array
    {

        return [
            'tableColumns' => $this->getTableColumns(),
            'tableQuery' => $this->getTableQuery(),
            'modifyQueryUsing' => $this->modifyQuerUsing,
            'tableRelationship' => $this->getRelationship(),
            'tableActions' => $this->getTableActions(),
            'tableBulkActions' => $this->getTableBulkActions(),
            'tableFilters' => $this->getTableFilters(),
            'modalTable' => $this->getModalTable(),
            'ownerAction' => $this,
        ];
    }
}
