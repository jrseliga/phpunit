<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TestFixture\ListTestsXmlWithTraits;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

final class TestWithTraitTest extends TestCase
{
    use TestMethodsTrait;

    #[Group('class-group')]
    public function testDirectInClass(): void
    {
        $this->assertTrue(true);
    }

    #[Group('class-group')]
    public function testAnotherDirectInClass(): void
    {
        $this->assertTrue(true);
    }
}
