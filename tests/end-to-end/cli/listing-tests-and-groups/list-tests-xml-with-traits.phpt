--TEST--
phpunit --no-output --list-tests-xml php://stdout ../../_files/list-tests-xml-with-traits
--FILE--
<?php declare(strict_types=1);
$_SERVER['argv'][] = '--do-not-cache-result';
$_SERVER['argv'][] = '--no-configuration';
$_SERVER['argv'][] = '--no-output';
$_SERVER['argv'][] = '--list-tests-xml';
$_SERVER['argv'][] = 'php://stdout';
$_SERVER['argv'][] = __DIR__ . '/../../_files/list-tests-xml-with-traits';

require_once __DIR__ . '/../../../bootstrap.php';

(new PHPUnit\TextUI\Application)->run($_SERVER['argv']);
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

<?xml version="1.0"?>
<testSuite xmlns="https://xml.phpunit.de/testSuite">
 <tests>
  <testClass name="PHPUnit\TestFixture\ListTestsXmlWithTraits\TestWithTraitTest" file="%sTestWithTraitTest.php">
   <testMethod id="PHPUnit\TestFixture\ListTestsXmlWithTraits\TestWithTraitTest::testDirectInClass" name="testDirectInClass" file="%sTestWithTraitTest.php" line="%d"/>
   <testMethod id="PHPUnit\TestFixture\ListTestsXmlWithTraits\TestWithTraitTest::testAnotherDirectInClass" name="testAnotherDirectInClass" file="%sTestWithTraitTest.php" line="%d"/>
   <testMethod id="PHPUnit\TestFixture\ListTestsXmlWithTraits\TestWithTraitTest::testFromTrait" name="testFromTrait" file="%sTestMethodsTrait.php" line="%d"/>
   <testMethod id="PHPUnit\TestFixture\ListTestsXmlWithTraits\TestWithTraitTest::testAnotherFromTrait" name="testAnotherFromTrait" file="%sTestMethodsTrait.php" line="%d"/>
  </testClass>
 </tests>
 <groups>
  <group name="class-group">
   <test id="PHPUnit\TestFixture\ListTestsXmlWithTraits\TestWithTraitTest::testDirectInClass"/>
   <test id="PHPUnit\TestFixture\ListTestsXmlWithTraits\TestWithTraitTest::testAnotherDirectInClass"/>
  </group>
  <group name="trait-group">
   <test id="PHPUnit\TestFixture\ListTestsXmlWithTraits\TestWithTraitTest::testFromTrait"/>
   <test id="PHPUnit\TestFixture\ListTestsXmlWithTraits\TestWithTraitTest::testAnotherFromTrait"/>
  </group>
 </groups>
</testSuite>%A
