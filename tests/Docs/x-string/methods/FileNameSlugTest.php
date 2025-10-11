<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class FileNameSlugTest extends TestCase
{
    public function testFileNameSlugBasic(): void
    {
        $original = XString::new('Quarterly Report.xlsx');
        $result = $original->fileNameSlug();
        self::assertSame('quarterly-report.xlsx', (string) $result);
    }

    public function testFileNameSlugMultiExtension(): void
    {
        $original = XString::new('Archive.backup.TAR.GZ');
        $result = $original->fileNameSlug();
        self::assertSame('archive.backup.tar.gz', (string) $result);
    }

    public function testFileNameSlugReserved(): void
    {
        $original = XString::new('..\\Reports/2024:Q1*Summary?.pdf');
        $result = $original->fileNameSlug();
        self::assertSame('reports-2024-q1-summary.pdf', (string) $result);
    }

    public function testFileNameSlugCustomSeparator(): void
    {
        $original = XString::new('Vacation Photo 01.JPG');
        $result = $original->fileNameSlug('_');
        self::assertSame('vacation_photo_01.jpg', (string) $result);
    }

    public function testFileNameSlugEmpty(): void
    {
        $original = XString::new('');
        $result = $original->fileNameSlug();
        self::assertSame('', (string) $result);
    }

    public function testFileNameSlugEmptySeparator(): void
    {
        $original = XString::new('report.docx');
        $this->expectException(InvalidArgumentException::class);
        $original->fileNameSlug('');
    }

    public function testFileNameSlugImmutable(): void
    {
        $original = XString::new('Project Plan v1.2.doc');
        $result = $original->fileNameSlug();
        self::assertSame('Project Plan v1.2.doc', (string) $original);
        self::assertSame('project-plan-v1.2.doc', (string) $result);
    }

}
