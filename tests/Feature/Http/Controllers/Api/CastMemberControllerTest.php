<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Resources\CastMemberResource;
use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Tests\Traits\TestResources;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CastMemberControllerTest extends TestCase
{

    use DatabaseMigrations, TestValidations, TestSaves, TestResources;

    private $castMember;

    private $serializedFields = [
        'id',
        'name',
        'type',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->castMember = factory(CastMember::class)->create([
            'type' => CastMember::TYPE_DIRECTOR
        ]);

    }

    protected function routeStore()
    {
        return route('api.cast_members.store');
    }

    protected function model()
    {
        return CastMember::class;
    }

    protected function routeUpdate()
    {
        return route('api.cast_members.update', ['cast_member' => $this->castMember->id]);
    }

    public function testIndex()
    {
        $response = $this->get(route('api.cast_members.index'));
        $response->assertStatus(200);
        $response->assertJson([
            'meta' => ['per_page' => 15]
        ]);
        $response->assertJsonStructure([
            'data' => [
                '*' => $this->serializedFields
            ],
            'meta' => [],
            'links' => [],
        ]);

        $resource = CastMemberResource::collection(collect([$this->castMember]));
        $this->assertResource($response, $resource);

    }

    public function testShow()
    {
        $response = $this->get(route('api.cast_members.show', ['cast_member' => $this->castMember->id]));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => $this->serializedFields
        ]);

        $idCastMember = $response->json('data.id');
        $resource = new CastMemberResource(CastMember::find($idCastMember));
        $this->assertResource($response, $resource);
    }

    public function testInvalidationData()
    {
        $data = ['name' => '', 'type' => ''];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = ['name' => str_repeat('a', 256)];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);

        $data = ['type' => 'a'];
        $this->assertInvalidationInStoreAction($data, 'in');
        $this->assertInvalidationInUpdateAction($data, 'in');
    }

    protected function assertValidationRequired(TestResponse $response)
    {
        $this->assertInvalidationFields($response, ['name'], 'required');
        $response->assertJsonMissingValidationErrors(['type']);
    }

    public function testStore()
    {

        $data = ['name' => 'teste', 'type' => CastMember::TYPE_DIRECTOR];
        $this->assertStore($data, $data + ['deleted_at' => null]);

        $data = ['name' => 'teste', 'type' => CastMember::TYPE_ACTOR];
        $this->assertStore($data, $data);

    }

    public function testUpdate()
    {
        $this->castMember = factory(CastMember::class)->create(['type' => CastMember::TYPE_DIRECTOR]);

        $data = ['name' => 'test', 'type' => CastMember::TYPE_ACTOR];
        $response = $this->assertUpdate($data, $data + ['deleted_at' => null]);
        $response->assertJsonStructure([
            'data' => $this->serializedFields
        ]);

        $castMemberId = $response->json('data.id');
        $resource = new CastMemberResource(CastMember::find($castMemberId));
        $this->assertResource($response, $resource);

        $data = ['name' => 'novo teste', 'type' => CastMember::TYPE_ACTOR];
        $this->assertUpdate($data, $data + ['deleted_at' => null]);

    }

    public function testDestroy()
    {
        $response = $this->json('DELETE', route('api.cast_members.destroy', ['cast_member' => $this->castMember->id]));
        $response->assertStatus(204);
        $this->assertNull(CastMember::find($this->castMember->id));
        $this->assertNotNull(CastMember::withTrashed()->find($this->castMember->id));

    }
}
