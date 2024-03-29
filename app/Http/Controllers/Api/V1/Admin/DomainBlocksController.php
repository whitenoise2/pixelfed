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
    $domain_block = Instance::moderated()->find($id);

    if (!$domain_block) {
      return $this->json([ 'error' => 'Record not found'], [], 404);
    }

    return $this->json(new DomainBlockResource($domain_block));
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

    $existing_domain_block = Instance::moderated()->whereDomain($domain)->first();

    if ($existing_domain_block) {
      return $this->json([
        'error' => 'A domain block already exists for this domain',
        'existing_domain_block' => new DomainBlockResource($existing_domain_block)
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

    $domain_block = Instance::moderated()->find($id);

    if (!$domain_block) {
      return $this->json([ 'error' => 'Record not found'], [], 404);
    }

    $domain_block->banned = $severity === 'suspend';
    $domain_block->unlisted = $severity === 'silence';
    $domain_block->notes = [$private_comment];
    $domain_block->save();

    InstanceService::refresh();

    return $this->json(new DomainBlockResource($domain_block));
  }

  public function delete(Request $request, $id) {
    $domain_block = Instance::moderated()->find($id);

    if (!$domain_block) {
      return $this->json([ 'error' => 'Record not found'], [], 404);
    }

    $domain_block->banned = false;
    $domain_block->unlisted = false;
    $domain_block->save();

    InstanceService::refresh();

    return $this->json(null, [], 200);
  }
}
