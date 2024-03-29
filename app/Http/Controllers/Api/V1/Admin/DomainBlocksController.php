<?php

namespace App\Http\Controllers\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Api\ApiController;
use App\Instance;
use App\Services\InstanceService;
use App\Http\Resources\MastoApi\Admin\DomainBlockResource;

class DomainBlocksController extends ApiController {
  public function index(Request $request) {
    $this->validate($request, [
      'limit' => 'sometimes|integer|max:100|min:1',
    ]);

    $limit = $request->input('limit', 100);

    $res = Instance::moderated()
      ->orderBy('id')
      ->cursorPaginate($limit)
      ->withQueryString();

    return $this->json(DomainBlockResource::collection($res), [
      'Link' => $this->linksForCollection($res)
    ]);
  }

  public function show(Request $request, $id) {
    $res = Instance::moderated()
      ->findOrFail($id);

    return $this->json(new DomainBlockResource($res));
  }

  public function create(Request $request) {
    $this->validate($request, [
      'domain' => 'required|string|min:1|max:120',
      'severity' => [
        'sometimes',
        Rule::in(['noop', 'silence', 'suspend'])
      ],
      'reject_media' => 'sometimes|required|boolean',
      'reject_reports' => 'sometimes|required|boolean',
      'private_comment' => 'sometimes|string|min:1|max:1000',
      'public_comment' => 'sometimes|string|min:1|max:1000',
      'obfuscate' => 'sometimes|required|boolean'
    ]);
    
    $domain = $request->input('domain');
    $severity = $request->input('severity');
    $private_comment = $request->input('private_comment');

		abort_if(!strpos($domain, '.'), 400, 'Invalid domain');
		abort_if(!filter_var($domain, FILTER_VALIDATE_DOMAIN), 400, 'Invalid domain');

    $existing = Instance::moderated()->whereDomain($domain)->first();

    if ($existing) {
      return $this->json([
        'error' => 'A domain block already exists for this domain',
        'existing_domain_block' => new DomainBlockResource($existing)
      ], [], 422);
    }

    $domain_block = Instance::updateOrCreate(
      [ 'domain' => $domain ],
      [ 'banned' => $severity === 'suspend', 'unlisted' => $severity === 'silence', 'notes' => [$private_comment]]
    );

    InstanceService::refresh();

    return $this->json(new DomainBlockResource($domain_block));
  }

  public function update(Request $request, $id) {
    $this->validate($request, [
      'severity' => [
        'sometimes',
        Rule::in(['noop', 'silence', 'suspend'])
      ],
      'reject_media' => 'sometimes|required|boolean',
      'reject_reports' => 'sometimes|required|boolean',
      'private_comment' => 'sometimes|string|min:1|max:1000',
      'public_comment' => 'sometimes|string|min:1|max:1000',
      'obfuscate' => 'sometimes|required|boolean'
    ]);
    
    $severity = $request->input('severity');
    $private_comment = $request->input('private_comment');

    $instance = Instance::moderated()->findOrFail($id);

    $instance->banned = $severity === 'suspend';
    $instance->unlisted = $severity === 'silence';
    $instance->notes = [$private_comment];
    $instance->save();

    InstanceService::refresh();

    return $this->json(new DomainBlockResource($instance));
  }

  public function delete(Request $request, $id) {
    $instance = Instance::moderated()->findOrFail($id);
    $instance->banned = false;
    $instance->unlisted = false;
    $instance->save();

    InstanceService::refresh();

    return $this->json([], [], 200);
  }
}
