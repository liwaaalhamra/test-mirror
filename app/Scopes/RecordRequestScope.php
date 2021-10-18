<?php

namespace App\Scopes;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class RecordRequestScope implements Scope{

	public function apply( Builder $builder, Model $model ) {

		return $builder->join('record_type_mapping', 'record_type_mapping.id', '=', 'record_request_mapping.record_type_id')
			->select('record_type_mapping.record_type_id as record_type_s_id', 'record_type_mapping.record_type_name as record_type_name', 'record_request_mapping.*');
	}


}