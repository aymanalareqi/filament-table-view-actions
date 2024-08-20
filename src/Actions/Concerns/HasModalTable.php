<?php

namespace Alareqi\TableViewActions\Actions\Concerns;

use Closure;
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

    public function getModalContent(): View | Htmlable | null
    {
        return view('table-view-actions::table-view', ["actionData" => $this->getActionData()]);
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
        ];
    }
}
