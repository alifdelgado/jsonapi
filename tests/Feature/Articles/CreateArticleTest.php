<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_create_articles()
    {
        $this->withoutExceptionHandling();
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type'  =>  'articles',
                'attributes'    =>  [
                    'title'     =>  'New Article',
                    'slug'      =>  'new-article',
                    'content'   =>  'Content article'
                ]
            ]
        ]);
        $response->assertCreated();
        $article = Article::first();
        $response->assertHeader(
            'Location',
            route('api.v1.articles.show', $article)
        );
        $response->assertExactJson([
            'data'  =>  [
                'type'  =>  'articles',
                'id'    =>  (string)$article->getRouteKey(),
                'attributes'    =>  [
                    'title'     =>  'New Article',
                    'slug'      =>  'new-article',
                    'content'   =>  'Content article'
                ],
                'links' =>  [
                    'self'  =>  route('api.v1.articles.show', $article)
                ]
            ]
        ]);
    }

    /** @test */
    public function title_is_required()
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type'  =>  'articles',
                'attributes'    =>  [
                    'slug'      =>  'new-article',
                    'content'   =>  'Content article'
                ]
            ]
        ]);
        $response->assertJsonValidationErrors('data.attributes.title');
    }

    /** @test */
    public function title_must_be_at_least_four_characters()
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type'  =>  'articles',
                'attributes'    =>  [
                    'title'     =>  'New',
                    'slug'      =>  'new-article',
                    'content'   =>  'Content article'
                ]
            ]
        ]);
        $response->assertJsonValidationErrors('data.attributes.title');
    }

    /** @test */
    public function slug_is_required()
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type'  =>  'articles',
                'attributes'    =>  [
                    'title'     =>  'New Article',
                    'content'   =>  'Content article'
                ]
            ]
        ]);
        $response->assertJsonValidationErrors('data.attributes.slug');
    }

    /** @test */
    public function content_is_required()
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type'  =>  'articles',
                'attributes'    =>  [
                    'title'     =>  'New Article',
                    'slug'      =>  'new-article',
                ]
            ]
        ]);
        $response->assertJsonValidationErrors('data.attributes.content');
    }
}
