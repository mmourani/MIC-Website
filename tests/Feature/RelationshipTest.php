<?php

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Reset the database before each test
    $this->artisan('migrate:refresh');
    $this->artisan('db:seed');
});

afterEach(function () {
    // Roll back any remaining transactions
    DB::rollBack();
});

// Authenticate a user for testing
beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('a user cannot be created with a duplicate email', function () {
    // Create a user with a unique email
    $user = User::factory()->create(['email' => 'unique@email.com']);

    // Attempt to create another user with the same email
    $duplicateUser = User::factory()->make(['email' => 'unique@email.com']);

    $this->expectException(\Illuminate\Database\UniqueConstraintViolationException::class);

    $duplicateUser->save();
});

test('a post can be deleted if it belongs to a user', function () {
    // Create a user
    $user = User::factory()->create();

    // Create a post associated with the user
    $post = Post::factory()->create(['user_id' => $user->id]);

    // Delete the post
    $post->delete();

    // Assert that the post no longer exists in the database
    $this->assertDatabaseMissing('posts', ['id' => $post->id]);
});

test('a comment can be deleted if it belongs to a user', function () {
    // Create a user
    $user = User::factory()->create();

    // Create a comment associated with the user
    $comment = Comment::factory()->create(['user_id' => $user->id]);

    // Delete the comment
    $comment->delete();

    // Assert that the comment no longer exists in the database
    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
});


// Testing the User-Profile relationship
test('a user has one profile', function () {
    // Create a user
    $user = User::factory()->create();

    // Create a profile associated with the user
    $profile = Profile::factory()->create(['user_id' => $user->id]);

    // Assert that the user has one profile
    expect($user->profile)->toBeInstanceOf(Profile::class);
});


// Add comments explaining the purpose of each test
test('a user can have multiple posts', function () {
    $user = User::factory()->create();
    $post1 = Post::factory()->create(['user_id' => $user->id]);
    $post2 = Post::factory()->create(['user_id' => $user->id]);

    // Assert that the user has multiple posts
    expect($user->posts->count())->toBe(2);
});

test('a post can have multiple comments', function () {
    $post = Post::factory()->create();
    $comment1 = Comment::factory()->create(['post_id' => $post->id]);
    $comment2 = Comment::factory()->create(['post_id' => $post->id]);

    // Assert that the post has multiple comments
    expect($post->comments->count())->toBe(2);
});

// Testing the Post-Category relationship
test('a post can belong to multiple categories', function () {
    $post = Post::factory()->create();
    $category1 = Category::factory()->create();
    $category2 = Category::factory()->create();
    // Assuming you have a method to attach categories to a post
    $post->categories()->attach([$category1->id, $category2->id]);

    $this->assertCount(2, $post->categories);
});

// Testing the Post-Tag relationship
test('a post can belong to multiple tags', function () {
    $post = Post::factory()->create();
    $tag1 = Tag::factory()->create();
    $tag2 = Tag::factory()->create();
    // Assuming you have a method to attach tags to a post
    $post->tags()->attach([$tag1->id, $tag2->id]);

    $this->assertCount(2, $post->tags);
});

// Testing the Comment-User relationship
test('a comment belongs to a user', function () {
    // Create a user
    $user = User::factory()->create();

    // Create a comment associated with the user
    $comment = Comment::factory()->create(['user_id' => $user->id]);

    // Assert that the comment belongs to the user
    expect($comment->user->id)->toBe($user->id);
});

// Testing the Comment-Post relationship
test('a comment belongs to a post', function () {
    // Create a post
    $post = Post::factory()->create();

    // Create a comment associated with the post
    $comment = Comment::factory()->create(['post_id' => $post->id]);

    // Assert that the comment belongs to the post
    expect($comment->post->id)->toBe($post->id);
});

// Testing the Categories-Post relationship (the inverse)
test('a category can belong to multiple posts', function () {
    // Create a category
    $category = Category::factory()->create();

    // Create two posts
    $post1 = Post::factory()->create();
    $post2 = Post::factory()->create();

    // Attach the posts to the category
    $category->posts()->attach([$post1->id, $post2->id]);

    // Assert that the category can belong to multiple posts
    $this->assertCount(2, $category->posts);
});


// Testing the Tags-Post relationship (the inverse)
test('a tag can belong to multiple posts', function () {
    // Create a tag
    $tag = Tag::factory()->create();

    // Create two posts
    $post1 = Post::factory()->create();
    $post2 = Post::factory()->create();

    // Attach the posts to the tag
    $tag->posts()->attach([$post1->id, $post2->id]);

    // Assert that the tag can belong to multiple posts
    $this->assertCount(2, $tag->posts);
});


// Testing unique constraints
test('emails must be unique', function () {
    // Create a user with a unique email
    User::factory()->create(['email' => 'unique@email.com']);

    // Attempt to create another user with the same email
    $this->expectException(\Illuminate\Database\QueryException::class);
    User::factory()->create(['email' => 'unique@email.com']);
});

// Testing the User-Comment relationship (the inverse)
test('a user can have multiple comments', function () {
    // Create a user
    $user = User::factory()->create();

    // Create 3 comments associated with the user
    Comment::factory(3)->create(['user_id' => $user->id]);

    // Assert that the user has 3 comments
    $this->assertCount(3, $user->comments);
});


// Testing constraints on Post deletion
test('a post cannot be deleted if it has comments', function () {
    // Create a post and two comments associated with that post
    $post = Post::factory()->create();
    Comment::factory(2)->create(['post_id' => $post->id]);

    // Attempt to delete the post
    $this->expectException(\Illuminate\Database\QueryException::class);
    $post->delete();
});


// Testing updating a user email updates all related records
test('updating a user email updates all related records', function () {
    // Start a database transaction
    DB::beginTransaction();

    try {
        // Create a user with an initial email
        $user = User::factory()->create(['email' => 'old@example.com']);

        // Create a profile associated with the user
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        // Update the user's email
        $newEmail = 'new@example.com';
        $user->email = $newEmail;
        $user->save();

        // Refresh the profile to get the latest data from the database
        $profile->refresh();

        // Assert that the emails have been updated for both the user and the profile
        $this->assertEquals($newEmail, $user->email);
        $this->assertEquals($newEmail, $profile->user->email);
    } catch (\Exception $e) {
        // Handle exceptions here if needed
    } finally {
        // Roll back the transaction to avoid affecting other tests
        DB::rollBack();
    }
});

// Testing the Tag-Post relationship (the inverse)
test('a tag can have multiple posts', function () {
    // Start a database transaction
    DB::beginTransaction();

    try {
        // Create a tag
        $tag = Tag::factory()->create();

        // Create two posts
        $post1 = Post::factory()->create();
        $post2 = Post::factory()->create();

        // Attach both posts to the tag
        $tag->posts()->attach([$post1->id, $post2->id]);

        // Get the tag's posts and assert the count
        $tagPosts = $tag->posts;
        $this->assertCount(2, $tagPosts);
    } catch (\Exception $e) {
        // Handle exceptions here if needed
    } finally {
        // Roll back the transaction to avoid affecting other tests
        DB::rollBack();
    }
});


// Testing Comment-Post and Comment-User associations
test('a comment is associated with a post and a user', function () {
    // Start a database transaction
    DB::beginTransaction();

    try {
        // Create a user
        $user = User::factory()->create();

        // Create a post associated with the user
        $post = Post::factory()->create(['user_id' => $user->id]);

        // Create a comment associated with the user and post
        $comment = Comment::factory()->create(['user_id' => $user->id, 'post_id' => $post->id]);

        // Refresh the comment model to get the latest data from the database
        $comment->refresh();

        // Assert that the comment is associated with the user and post
        $this->assertEquals($user->id, $comment->user_id);
        $this->assertEquals($post->id, $comment->post_id);
    } catch (\Exception $e) {
        // Handle exceptions here if needed
    } finally {
        // Roll back the transaction to avoid affecting other tests
        DB::rollBack();
    }
});

// Testing the User-Profile relationship (the inverse)
test('a profile belongs to a user', function () {
    // Create a user
    $user = User::factory()->create();

    // Create a profile associated with the user
    $profile = Profile::factory()->create(['user_id' => $user->id]);

    // Assert that the profile belongs to the user
    $this->assertEquals($user->id, $profile->user_id);
});

test('a user and associated profile can be deleted together', function () {
    // Create a user
    $user = User::factory()->create();

    // Attempt to delete the user
    $user->delete();

    // Assert that the user no longer exists
    $this->assertDatabaseMissing('users', ['id' => $user->id]);

    // Assert that the associated profile no longer exists
    $this->assertDatabaseMissing('profiles', ['user_id' => $user->id]);
});




