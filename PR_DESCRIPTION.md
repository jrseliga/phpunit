# Add `file` and `line` attributes to `--list-tests-xml` output

Simple PR adding new attributes to `<testMethod>` elements:
- `file`
- `line`

**Before:**
```xml
<testClass name="Tests\Feature\PostTest" file="/path/to/tests/Feature/PostTest.php">
  <testMethod id="PostTest::testBelongsToUser" name="testBelongsToUser"/>
  <testMethod id="PostTest::testPostContent" name="testPostContent"/>
</testClass>
```

**After:**
```xml
<testClass name="Tests\Feature\PostTest" file="/path/to/tests/Feature/PostTest.php">
  <testMethod id="PostTest::testBelongsToUser" name="testBelongsToUser" file="/path/to/tests/Concerns/BelongsToUserCases.php" line="8"/>
  <testMethod id="PostTest::testPostContent" name="testPostContent" file="/path/to/tests/Feature/PostTest.php" line="13"/>
</testClass>
```

## Motivation
Testing tools in IDEs need to know where tests actually live so they navigate users to the correct place when clicking on a test result or when a test fails. Some tools use static analysis to figure this out themselves and others ask PHPUnit via `--list-tests-xml`.

Currently IDEs (and other tools) relying on PHPUnit to provide a dictionary of tests (`--list-tests-xml`) are unable to accurately represent test method locations. Specifically, when test methods come from traits The current XML output only tells you about the test class file and not where the method actually lives.

For example, assume you have a trait that confirms a specific relationship is defined on a given `Model`:

```php
trait BelongsToUserCases
{
    public function testBelongsToUser(): void
    {
        $model = $this->model::factory()->for(User::factory())->create();

        $this->assertInstanceOf(User::class, $model->user);
    }
}

final class PostTest extends TestCase
{
    use BelongsToUserCases;

    protected string $model = Post::class;

    public function testPostContent(): void
    {
        // Test implementation
    }
}

final class CommentTest extends TestCase
{
    use BelongsToUserCases;

    protected string $model = Comment::class;

    public function testCommentContent(): void
    {
        // Test implementation
    }
}
```

When `testBelongsToUser` fails for `PostTest`, you want your IDE to jump to where that test is actually written (`BelongsToUserCases.php`). Right now it can only take you to `PostTest.php`, and then you're stuck hunting around for the trait.

With this change IDE testing tools can easily and accurately identify the location of the test method provided in `BelongsToUserCases.php` and the test method provided in `PostTest.php`.

## Testing notes

While the feature change was extremely straightforward, I struggled a bit on where and how to test it. Happy to make any changes if my first attempt is not what you'd expect.
