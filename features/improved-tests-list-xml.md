# Improved Tests List XML Output

## Overview

Enhance the `--list-tests-xml` command output to include source file location and line number information for each test method. This provides better traceability, especially for tests that use traits.

## Problem

The current XML output from `ListTestsAsXmlCommand` only includes the test method `id` and `name` attributes. When test methods come from traits, there's no way to determine:
- Where the method is actually defined (class file vs trait file)
- What line number the method is on in the source file

## Solution

Add `file` and `line` attributes to every `testMethod` element:
- **`file`**: The absolute path to the file where the method is defined (class file or trait file)
- **`line`**: The line number where the method starts in that file

These values are already available from PHPUnit's existing `TestMethod` value object via:
- `$test->valueObjectForEvents()->file()`
- `$test->valueObjectForEvents()->line()`

## Example Output

### Before
```xml
<testMethod id="Tests\MyTest::testExample" name="testExample"/>
```

### After
```xml
<testMethod id="Tests\MyTest::testExample" name="testExample" file="/path/to/MyTest.php" line="42"/>
```

### With Traits
```xml
<testClass name="Tests\MyTest" file="/path/to/MyTest.php">
  <!-- Method defined in class -->
  <testMethod id="Tests\MyTest::testDirect"
              name="testDirect"
              file="/path/to/MyTest.php"
              line="27"/>

  <!-- Method from trait -->
  <testMethod id="Tests\MyTest::testFromTrait"
              name="testFromTrait"
              file="/path/to/MyTestTrait.php"
              line="16"/>
</testClass>
```

## Implementation Plan

### Implementation Checklist

- [x] Modify `ListTestsAsXmlCommand::execute()` method
  - [x] Add `file` attribute to `testMethod` elements
  - [x] Add `line` attribute to `testMethod` elements
  - [x] Ensure attributes are added for all TestCase instances

### Testing Checklist

#### Test Fixtures
- [ ] Create test fixture with trait usage
  - [ ] Create a trait with test methods (e.g., `TestMethodsTrait.php`)
  - [ ] Create a test class using the trait with some direct methods
  - [ ] Place in `tests/end-to-end/_files/list-tests-xml-with-traits/`

#### Regression Tests (Update Existing)
- [ ] Update `tests/end-to-end/cli/listing-tests-and-groups/list-tests-xml.phpt`
  - [ ] Add `file="%s"` pattern to existing testMethod expectations
  - [ ] Add `line="%d"` pattern to existing testMethod expectations
  - [ ] Verify output still includes all existing attributes (id, name)
- [ ] Update `tests/end-to-end/cli/listing-tests-and-groups/list-tests-xml-include-group.phpt`
  - [ ] Add file and line attribute expectations
- [ ] Update `tests/end-to-end/cli/listing-tests-and-groups/list-tests-xml-exclude-group.phpt`
  - [ ] Add file and line attribute expectations
- [ ] Update `tests/end-to-end/cli/listing-tests-and-groups/list-tests-xml-include-filter.phpt`
  - [ ] Add file and line attribute expectations
- [ ] Update `tests/end-to-end/cli/listing-tests-and-groups/list-tests-xml-exclude-filter.phpt`
  - [ ] Add file and line attribute expectations
- [ ] Update `tests/end-to-end/regression/5908-list-tests-xml.phpt`
  - [ ] Add file and line attribute expectations

#### New Feature Tests
- [ ] Create `tests/end-to-end/cli/listing-tests-and-groups/list-tests-xml-with-traits.phpt`
  - [ ] Verify methods defined directly in class have file matching testClass/@file
  - [ ] Verify methods from traits have file pointing to trait file
  - [ ] Verify line numbers are correct for both class and trait methods
  - [ ] Verify file paths are different for trait methods vs class methods

#### Validation Tests
- [ ] Verify testClass structure unchanged
- [ ] Verify phpt test handling unchanged
- [ ] Verify groups section unchanged
- [ ] Verify XML is well-formed and validates against schema
- [ ] Verify line numbers are positive integers
- [ ] Verify file paths are absolute paths

### File Changes

**File:** `src/TextUI/Command/Commands/ListTestsAsXmlCommand.php`

**Location:** Lines 86-89 (testMethod XML generation)

**Current code:**
```php
$writer->startElement('testMethod');
$writer->writeAttribute('id', $test->valueObjectForEvents()->id());
$writer->writeAttribute('name', $test->valueObjectForEvents()->methodName());
$writer->endElement();
```

**New code:**
```php
$writer->startElement('testMethod');
$writer->writeAttribute('id', $test->valueObjectForEvents()->id());
$writer->writeAttribute('name', $test->valueObjectForEvents()->methodName());
$writer->writeAttribute('file', $test->valueObjectForEvents()->file());
$writer->writeAttribute('line', (string) $test->valueObjectForEvents()->line());
$writer->endElement();
```

## Benefits

1. **Trait Visibility**: Immediately see which methods come from traits by comparing `testMethod/@file` with `testClass/@file`
2. **Direct Navigation**: Jump directly to the source code line for any test method
3. **Better Tooling**: IDEs and test runners can provide better navigation and reporting
4. **Consistency**: Aligns with JUnit XML logger which already provides similar information
5. **No Breaking Changes**: Only adds new attributes, existing parsers will continue to work

## Technical Details

### Why No Custom Reflection Needed

PHP's `ReflectionMethod` automatically returns:
- The trait file path when calling `getFileName()` on a method from a trait
- The trait line number when calling `getStartLine()` on a method from a trait

PHPUnit's `TestMethodBuilder` already uses this via `Reflection::sourceLocationFor()` and stores it in the `TestMethod` value object.

### Performance Impact

Minimal - the value objects are already created and cached during test discovery. No additional reflection calls are needed.
