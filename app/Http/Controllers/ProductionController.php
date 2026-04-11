<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductionRequest;
use App\Http\Requests\UpdateProductionRequest;
use App\Models\Production;
use App\Services\ActivityLogService;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class ProductionController extends Controller
{
    public function __construct(private readonly InventoryService $inventoryService)
    {
    }

    public function index(): View
    {
        $productions = Production::with('user')->latest()->paginate(10);
        return view('productions.index', compact('productions'));
    }

    public function create(): View
    {
        return view('productions.create');
    }

    public function store(StoreProductionRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $production = Production::create([
                ...$request->validated(),
                'user_id' => Auth::id(),
            ]);

            $this->inventoryService->addStock((float) $production->quantity_produced, 'production', $production->id, Auth::id(), 'Production stock added');
            ActivityLogService::log(Auth::id(), 'create', 'productions', 'Created production #'.$production->id, $request);
        });

        return redirect()->route('productions.index')->with('success', 'Production recorded successfully.');
    }

    public function edit(Production $production): View
    {
        return view('productions.edit', compact('production'));
    }

    public function update(UpdateProductionRequest $request, Production $production): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request, $production) {
                $oldQuantity = (float) $production->quantity_produced;
                $data = $request->validated();
                $newQuantity = (float) $data['quantity_produced'];

                $production->update($data);

                if ($newQuantity > $oldQuantity) {
                    $this->inventoryService->addStock($newQuantity - $oldQuantity, 'production_adjustment', $production->id, Auth::id(), 'Production quantity increased');
                } elseif ($newQuantity < $oldQuantity) {
                    $this->inventoryService->deductStock($oldQuantity - $newQuantity, 'production_adjustment', $production->id, Auth::id(), 'Production quantity reduced');
                }

                ActivityLogService::log(Auth::id(), 'update', 'productions', 'Updated production #'.$production->id, $request);
            });
        } catch (RuntimeException $exception) {
            return back()->withInput()->withErrors(['quantity_produced' => $exception->getMessage()]);
        }

        return redirect()->route('productions.index')->with('success', 'Production updated successfully.');
    }

    public function destroy(Request $request, Production $production): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request, $production) {
                $this->inventoryService->deductStock((float) $production->quantity_produced, 'production_delete', $production->id, Auth::id(), 'Deleted production stock reversal');
                $id = $production->id;
                $production->delete();
                ActivityLogService::log(Auth::id(), 'delete', 'productions', 'Deleted production #'.$id, $request);
            });
        } catch (RuntimeException $exception) {
            return back()->withErrors(['production' => 'Cannot delete this production because current stock is too low to reverse it.']);
        }

        return redirect()->route('productions.index')->with('success', 'Production deleted successfully.');
    }
}
