<?php

namespace Leeto\MoonShine\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Routing\Redirector;
use Leeto\MoonShine\Resources\BaseResource;
use Leeto\MoonShine\Traits\Resources\ExportTrait;

class BaseMoonShineController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use ExportTrait;

    protected BaseResource $resource;

    /**
     * @throws AuthorizationException
     */
    public function index(): Factory|View|Response|Application|ResponseFactory
    {
        if($this->resource->isWithPolicy()) {
            $this->authorize('viewAny', $this->resource->getModel());
        }

        if(request()->has('exportCsv')) {
            return $this->exportCsv();
        }

        return view($this->resource->baseIndexView(), [
            'resource' => $this->resource,
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function create(): View|Factory|Redirector|RedirectResponse|Application
    {
        if($this->resource->isWithPolicy()) {
            $this->authorize('create', $this->resource->getModel());
        }

        if(!in_array('create', $this->resource->getActions())) {
            return redirect($this->resource->route('index'));
        }

        return $this->editView();
    }

    /**
     * @throws AuthorizationException
     */
    public function edit($id): View|Factory|Redirector|RedirectResponse|Application
    {
        if(!in_array('edit', $this->resource->getActions())) {
            return redirect($this->resource->route('index'));
        }

        $item = $this->resource->getModel()
            ->where(['id' => $id])
            ->firstOrFail();

        if($this->resource->isWithPolicy()) {
            $this->authorize('update', $item);
        }

        $this->resource->setItem($item);

        return $this->editView($item);
    }

    /**
     * @throws AuthorizationException
     */
    public function show($id): Redirector|Application|RedirectResponse
    {
        $item = $this->resource->getModel()
            ->where(['id' => $id])
            ->firstOrFail();

        if($this->resource->isWithPolicy()) {
            $this->authorize('view', $item);
        }

        return redirect($this->resource->route('index'));
    }

    /**
     * @throws AuthorizationException
     */
    public function update($id, Request $request): Factory|View|Redirector|Application|RedirectResponse
    {
        if(!in_array('edit', $this->resource->getActions())) {
            return redirect($this->resource->route('index'));
        }

        $item = $this->resource->getModel()
            ->where(['id' => $id])
            ->firstOrFail();

        if($this->resource->isWithPolicy()) {
            $this->authorize('update', $item);
        }

        return $this->save($request, $item);
    }

    /**
     * @throws AuthorizationException
     */
    public function store(Request $request): Factory|View|Redirector|Application|RedirectResponse
    {
        if(!in_array('edit', $this->resource->getActions()) && !in_array("create", $this->resource->getActions())) {
            return redirect($this->resource->route('index'));
        }

        $item = $this->resource->getModel();

        if($this->resource->isWithPolicy()) {
            $this->authorize('create', $item);
        }

        return $this->save($request, $item);
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy($id): Redirector|Application|RedirectResponse
    {
        if(!in_array('delete', $this->resource->getActions())) {
            return redirect($this->resource->route('index'));
        }

        if(request()->has('ids')) {
            $this->resource->getModel()
                ->whereIn('id', explode(';', request('ids')))
                ->delete();

            if($this->resource->isWithPolicy()) {
                $this->authorize('delete', $this->resource->getModel());
            }
        } else {
            $this->resource->getModel()->destroy($id);

            if($this->resource->isWithPolicy()) {
                $this->authorize('delete', $this->resource->getModel());
            }
        }

        return redirect($this->resource->route('index'))
            ->with('alert', trans('moonshine::ui.deleted'));
    }

    protected function editView(Model $item = null)
    {
        return view($this->resource->baseEditView(), [
            'resource' =>  $this->resource,
            'item' => $item ? $item : $this->resource->getModel(),
        ]);
    }

    protected function save(Request $request, Model $item): Factory|View|Redirector|Application|RedirectResponse
    {
        if($request->isMethod('post') || $request->isMethod('put')) {
            $this->resource->validate($item);

            $item = $this->resource->save($item);

            return redirect($this->resource->route('index'))
                ->with('alert', trans('moonshine::ui.saved'));
        }

        return $this->editView($item);
    }
}
