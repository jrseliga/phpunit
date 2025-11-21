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

trait TestMethodsTrait
{
    #[Group('trait-group')]
    public function testFromTrait(): void
    {
        $this->assertTrue(true);
    }

    #[Group('trait-group')]
    public function testAnotherFromTrait(): void
    {
        $this->assertTrue(true);
    }
}
