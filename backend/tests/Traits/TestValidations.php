<?php
declare(strict_types=1);

namespace Tests\Traits;


use Illuminate\Foundation\Testing\TestResponse;

trait TestValidations
{

    abstract protected function model();
    abstract protected function routeStore();
    abstract protected function routeUpdate();


    protected function assertInvalidationInStoreAction(
        array $data,
        string $rule,
        $rulesParams = []
    ){
        $fields = array_keys($data);

        $response = $this->json('POST', $this->routeStore(), $data);

        $this->assertInvalidationFields($response, $fields, $rule, $rulesParams);
    }

    protected function assertInvalidationInUpdateAction(
        array $data,
        string $rule,
        $rulesParams = []
    ){
        $fields = array_keys($data);

        $response = $this->json('PUT', $this->routeUpdate(), $data);

        $this->assertInvalidationFields($response, $fields, $rule, $rulesParams);
    }

    protected function assertInvalidationFields(
        TestResponse $response,
        array $fields,
        string $rule,
        array $ruleParams = []
    ) {
        $response->assertStatus(422);
        $response->assertJsonValidationErrors($fields);

        foreach ($fields as $field) {
            $fieldName = str_replace('_', ' ', $field);
            $response->assertJsonFragment([
                \Lang::get('validation.'.$rule, ['attribute' => $fieldName] + $ruleParams)
            ]);
        }
    }
}
