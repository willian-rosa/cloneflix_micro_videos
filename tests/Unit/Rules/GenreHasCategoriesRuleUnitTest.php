<?php


namespace Tests\Unit\Rules;

use App\Rules\GenreHasCategoriesRule;
use Illuminate\Contracts\Validation\Rule;
use Tests\Traits\ReflectionClassTrait;
use Tests\UnitTestCase;

class GenreHasCategoriesRuleUnitTest extends UnitTestCase
{

    use ReflectionClassTrait;

    public function testInstanceOf()
    {
        $this->assertInstanceOf(Rule::class, new GenreHasCategoriesRule([]));
    }

    public function testCategoriesIdFields()
    {
        $rule = new GenreHasCategoriesRule([2, 1, 2, 2, 1]);

        $categoriesId = $this->getValuePropertyProtected(GenreHasCategoriesRule::class, $rule, 'categoriesId');

        $this->assertEqualsCanonicalizing([1, 2], $categoriesId);

    }

    public function testGenreIdValue()
    {
        $rule = new GenreHasCategoriesRule([]);
        $rule->passes('', [1, 1, 2, 2]);

        $genresId = $this->getValuePropertyProtected(GenreHasCategoriesRule::class, $rule, 'genresId');

        $this->assertEqualsCanonicalizing([1, 2], $genresId);

    }

    public function testPassesReturnsFalseWhenCategoriesOrGenresIsArrayEmpty()
    {

        $rule = $this->createRuleMockery([1]);
        $this->assertFalse($rule->passes('', []));

        $rule = $this->createRuleMockery([]);
        $this->assertFalse($rule->passes('', [1]));
    }

    public function testPassesReturnsFalseWhenGetRowIsEmpty()
    {

        $rule = $this->createRuleMockeryGetRows([1], collect());
        $this->assertFalse($rule->passes('', [1]));
    }

    public function testPassesReturnsFalseWhenHasCategoriesWithoutGenres()
    {

        $rule = $this->createRuleMockeryGetRows(
            [1, 2],
            collect(['category_id' => 1])
        );
        $this->assertFalse($rule->passes('', [1]));
    }

    public function testPassesIsValid()
    {

        $rule = $this->createRuleMockeryGetRows(
            [1, 2],
            collect([
                ['category_id' => 1],
                ['category_id' => 2]
            ])
        );
        $this->assertTrue($rule->passes('', [1]));
    }

    private function createRuleMockeryGetRows(array $categoriesId, $getRows)
    {
        $rule = $this->createRuleMockery($categoriesId);
        $rule->shouldReceive('getRows')->withAnyArgs()->andReturn($getRows);

        return $rule;

    }

    private function createRuleMockery(array $categoriesId)
    {
        return \Mockery::mock(GenreHasCategoriesRule::class, [$categoriesId])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

    // https://portal.code.education/lms/#/169/155/98/conteudos?capitulo=663&conteudo=5821
    // 14:30

}
