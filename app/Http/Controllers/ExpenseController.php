<?php

namespace App\Http\Controllers;

use App\Http\Requests\Common\ActionRequest;
use App\Http\Requests\Expense\StoreExpenseRequest;
use App\Http\Requests\Expense\UpdateExpenseRequest;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Site;
use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class ExpenseController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('checkPermission:expenses.view', only: ['index']),
            new Middleware('checkPermission:expenses.create', only: ['create', 'store']),
            new Middleware('checkPermission:expenses.update', only: ['edit', 'update']),
            new Middleware('checkPermission:expenses.delete', only: ['destroy']),
            new Middleware('checkPermission:expenses.reports', only: ['siteReport', 'customerReport']),
        ];
    }

    public function index(Request $request): View
    {
        $query = Expense::query()->with(['customer', 'site', 'subscriber']);
        $this->applyCustomerVisibilityScope($request, $query);

        $expenses = $query
            ->when($request->integer('customer_id'), fn (Builder $q, $customerId) => $q->where('customer_id', $customerId))
            ->when($request->integer('site_id'), fn (Builder $q, $siteId) => $q->where('site_id', $siteId))
            ->when($request->string('category')->toString(), fn (Builder $q, $category) => $q->where('category', $category))
            ->when($request->date('date_from'), fn (Builder $q, $dateFrom) => $q->whereDate('expense_date', '>=', $dateFrom->toDateString()))
            ->when($request->date('date_to'), fn (Builder $q, $dateTo) => $q->whereDate('expense_date', '<=', $dateTo->toDateString()))
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $totals = (clone $query)
            ->selectRaw('SUM(amount) as total_amount')
            ->first();

        return view('expenses.index', [
            'expenses' => $expenses,
            'totals' => $totals,
            'customers' => $this->accessibleCustomers($request),
            'sites' => $this->accessibleSites($request),
            'categories' => $this->categories(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('expenses.create', [
            'subscribers' => Subscriber::query()->orderBy('name')->get(),
            'customers' => $this->accessibleCustomers($request),
            'sites' => $this->accessibleSites($request),
            'categories' => $this->categories(),
        ]);
    }

    public function store(StoreExpenseRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $subscriberId = $this->resolveSubscriberId($request);

        $customer = Customer::withoutGlobalScopes()->findOrFail((int) $data['customer_id']);
        $site = isset($data['site_id']) && $data['site_id']
            ? Site::withoutGlobalScopes()->findOrFail((int) $data['site_id'])
            : null;

        $this->assertOwnership($request, $customer, $site, $subscriberId);

        Expense::query()->create([
            'subscriber_id' => $subscriberId,
            'customer_id' => $customer->id,
            'site_id' => $site?->id,
            'expense_date' => $data['expense_date'],
            'category' => $data['category'],
            'amount' => (float) $data['amount'],
            'description' => $data['description'] ?? null,
        ]);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense created successfully.');
    }

    public function edit(Expense $expense, Request $request): View
    {
        $this->assertExistingRecordAccess($request, $expense);

        return view('expenses.edit', [
            'expense' => $expense,
            'subscribers' => Subscriber::query()->orderBy('name')->get(),
            'customers' => $this->accessibleCustomers($request),
            'sites' => $this->accessibleSites($request),
            'categories' => $this->categories(),
        ]);
    }

    public function update(UpdateExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $this->assertExistingRecordAccess($request, $expense);

        $data = $request->validated();
        $customer = Customer::withoutGlobalScopes()->findOrFail((int) $data['customer_id']);
        $site = isset($data['site_id']) && $data['site_id']
            ? Site::withoutGlobalScopes()->findOrFail((int) $data['site_id'])
            : null;

        $this->assertOwnership($request, $customer, $site, (int) $expense->subscriber_id);

        $expense->update([
            'customer_id' => $customer->id,
            'site_id' => $site?->id,
            'expense_date' => $data['expense_date'],
            'category' => $data['category'],
            'amount' => (float) $data['amount'],
            'description' => $data['description'] ?? null,
        ]);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense, ActionRequest $request): RedirectResponse
    {
        $this->assertExistingRecordAccess($request, $expense);

        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    public function siteReport(Request $request): View
    {
        $grouping = $this->normalizeGrouping($request->string('grouping')->toString());
        [$periodSelect, $periodGroup] = $this->periodExpressions($grouping);

        $query = Expense::query()->with(['customer', 'site', 'subscriber']);
        $this->applyCustomerVisibilityScope($request, $query);

        $query
            ->when($request->integer('customer_id'), fn (Builder $q, $customerId) => $q->where('customer_id', $customerId))
            ->when($request->integer('site_id'), fn (Builder $q, $siteId) => $q->where('site_id', $siteId))
            ->when($request->date('date_from'), fn (Builder $q, $dateFrom) => $q->whereDate('expense_date', '>=', $dateFrom->toDateString()))
            ->when($request->date('date_to'), fn (Builder $q, $dateTo) => $q->whereDate('expense_date', '<=', $dateTo->toDateString()));

        $groupedSubtotals = (clone $query)
            ->selectRaw("{$periodSelect} as period_label, site_id, SUM(amount) as subtotal_amount")
            ->groupByRaw("{$periodGroup}, site_id")
            ->with('site')
            ->orderBy('period_label')
            ->get();

        $siteTotals = (clone $query)
            ->selectRaw('site_id, SUM(amount) as site_total')
            ->groupBy('site_id')
            ->with('site.customer')
            ->orderByDesc('site_total')
            ->get();

        $overallTotal = (clone $query)
            ->selectRaw('SUM(amount) as total_amount')
            ->first();

        return view('expenses.report-site', [
            'grouping' => $grouping,
            'groupedSubtotals' => $groupedSubtotals,
            'siteTotals' => $siteTotals,
            'overallTotal' => $overallTotal,
            'customers' => $this->accessibleCustomers($request),
            'sites' => $this->accessibleSites($request),
        ]);
    }

    public function customerReport(Request $request): View
    {
        $grouping = $this->normalizeGrouping($request->string('grouping')->toString());
        [$periodSelect, $periodGroup] = $this->periodExpressions($grouping);

        $query = Expense::query()->with(['customer', 'site', 'subscriber']);
        $this->applyCustomerVisibilityScope($request, $query);

        $query
            ->when($request->integer('customer_id'), fn (Builder $q, $customerId) => $q->where('customer_id', $customerId))
            ->when($request->integer('site_id'), fn (Builder $q, $siteId) => $q->where('site_id', $siteId))
            ->when($request->date('date_from'), fn (Builder $q, $dateFrom) => $q->whereDate('expense_date', '>=', $dateFrom->toDateString()))
            ->when($request->date('date_to'), fn (Builder $q, $dateTo) => $q->whereDate('expense_date', '<=', $dateTo->toDateString()));

        $groupedSubtotals = (clone $query)
            ->selectRaw("{$periodSelect} as period_label, customer_id, SUM(amount) as subtotal_amount")
            ->groupByRaw("{$periodGroup}, customer_id")
            ->with('customer')
            ->orderBy('period_label')
            ->get();

        $customerTotals = (clone $query)
            ->selectRaw('customer_id, SUM(amount) as customer_total')
            ->groupBy('customer_id')
            ->with('customer')
            ->orderByDesc('customer_total')
            ->get();

        $overallTotal = (clone $query)
            ->selectRaw('SUM(amount) as total_amount')
            ->first();

        return view('expenses.report-customer', [
            'grouping' => $grouping,
            'groupedSubtotals' => $groupedSubtotals,
            'customerTotals' => $customerTotals,
            'overallTotal' => $overallTotal,
            'customers' => $this->accessibleCustomers($request),
            'sites' => $this->accessibleSites($request),
        ]);
    }

    protected function normalizeGrouping(string $grouping): string
    {
        return in_array($grouping, ['monthly', 'quarterly', 'yearly'], true)
            ? $grouping
            : 'monthly';
    }

    /**
     * @return array{0: string, 1: string}
     */
    protected function periodExpressions(string $grouping): array
    {
        return match ($grouping) {
            'quarterly' => ["CONCAT(YEAR(expense_date), '-Q', QUARTER(expense_date))", "CONCAT(YEAR(expense_date), '-Q', QUARTER(expense_date))"],
            'yearly' => ['CAST(YEAR(expense_date) AS CHAR)', 'YEAR(expense_date)'],
            default => ["DATE_FORMAT(expense_date, '%Y-%m')", "DATE_FORMAT(expense_date, '%Y-%m')"],
        };
    }

    protected function isCustomerUser(Request $request): bool
    {
        return (bool) ($request->user() && $request->user()->hasRole('customer'));
    }

    /**
     * @return array<int, int>
     */
    protected function ownedCustomerIds(Request $request): array
    {
        return Customer::query()
            ->where('user_id', $request->user()->id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * @return array<int, int>
     */
    protected function ownedSiteIds(Request $request): array
    {
        return Site::query()
            ->whereIn('customer_id', $this->ownedCustomerIds($request))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    protected function applyCustomerVisibilityScope(Request $request, Builder $query): void
    {
        if (! $this->isCustomerUser($request)) {
            return;
        }

        $ownedCustomerIds = $this->ownedCustomerIds($request);
        $query->whereIn('customer_id', $ownedCustomerIds);
    }

    protected function accessibleCustomers(Request $request): Collection
    {
        $query = Customer::query()->orderBy('name');

        if ($this->isCustomerUser($request)) {
            $query->whereIn('id', $this->ownedCustomerIds($request));
        }

        return $query->get();
    }

    protected function accessibleSites(Request $request): Collection
    {
        $query = Site::query()->orderBy('name');

        if ($this->isCustomerUser($request)) {
            $query->whereIn('id', $this->ownedSiteIds($request));
        }

        return $query->get();
    }

    /**
     * @return list<string>
     */
    protected function categories(): array
    {
        return [
            'travel',
            'fuel',
            'uniform',
            'equipment',
            'maintenance',
            'utilities',
            'misc',
        ];
    }

    protected function resolveSubscriberId(Request $request): int
    {
        if ($request->user() && $request->user()->hasRole('leadership')) {
            return (int) $request->integer('subscriber_id');
        }

        return tenant_id() ?? (int) $request->user()->subscriber_id;
    }

    protected function assertOwnership(Request $request, Customer $customer, ?Site $site, int $subscriberId): void
    {
        abort_unless($customer->subscriber_id === $subscriberId, 422, 'Customer does not belong to selected subscriber.');

        if ($site) {
            abort_unless($site->subscriber_id === $subscriberId, 422, 'Site does not belong to selected subscriber.');
            abort_unless($site->customer_id === $customer->id, 422, 'Site must belong to selected customer.');
        }

        if ($this->isCustomerUser($request)) {
            $ownedCustomerIds = $this->ownedCustomerIds($request);

            abort_unless(in_array((int) $customer->id, $ownedCustomerIds, true), 403, 'Customer can manage only own expenses.');

            if ($site) {
                $ownedSiteIds = $this->ownedSiteIds($request);
                abort_unless(in_array((int) $site->id, $ownedSiteIds, true), 403, 'Customer can manage only own site expenses.');
            }
        }
    }

    protected function assertExistingRecordAccess(Request $request, Expense $expense): void
    {
        if (! $this->isCustomerUser($request)) {
            return;
        }

        $ownedCustomerIds = $this->ownedCustomerIds($request);

        abort_unless(in_array((int) $expense->customer_id, $ownedCustomerIds, true), 403, 'Customer can access only own expenses.');

        if ($expense->site_id) {
            $ownedSiteIds = $this->ownedSiteIds($request);
            abort_unless(in_array((int) $expense->site_id, $ownedSiteIds, true), 403, 'Customer can access only own site expenses.');
        }
    }
}
