<?php

/*
 * This file is part of the SimHashPhp package.
 *
 * (c) Titouan Galopin <http://titouangalopin.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tga\SimHash;

use Tga\SimHash\Comparator\GaussianComparator;
use Tga\SimHash\Extractor\SimpleTextExtractor;


/**
 * SimHash functional tests
 *
 * @author Titouan Galopin <galopintitouan@gmail.com>
 */
class SimHashTest extends \PHPUnit\Framework\TestCase
{
    public function testDifferentTexts()
    {
        $text1 = file_get_contents(__DIR__ . '/../../../resources/text/file1.txt');
        $text2 = file_get_contents(__DIR__ . '/../../../resources/text/file3.txt');

        $simhash = new SimHash();
        $extractor = new SimpleTextExtractor();
        $comparator = new GaussianComparator();

        $fp1 = $simhash->hash($extractor->extract($text1));
        $fp2 = $simhash->hash($extractor->extract($text2));

        self::assertLessThan(0.1, $comparator->compare($fp1, $fp2));
    }

    public function testSimilarTexts()
    {
        $text1 = file_get_contents(__DIR__ . '/../../../resources/text/file1.txt');
        $text2 = file_get_contents(__DIR__ . '/../../../resources/text/file2.txt');

        $simhash = new SimHash();
        $extractor = new SimpleTextExtractor();
        $comparator = new GaussianComparator();

        $fp1 = $simhash->hash($extractor->extract($text1));
        $fp2 = $simhash->hash($extractor->extract($text2));

        self::assertLessThan(0.9, $comparator->compare($fp1, $fp2));
        self::assertGreaterThan(0.1, $comparator->compare($fp1, $fp2));
    }

    public function testEqualTexts()
    {
        $text1 = file_get_contents(__DIR__ . '/../../../resources/text/file1.txt');
        $text2 = file_get_contents(__DIR__ . '/../../../resources/text/file1.txt');

        $simhash = new SimHash();
        $extractor = new SimpleTextExtractor();
        $comparator = new GaussianComparator();

        $fp1 = $simhash->hash($extractor->extract($text1));
        $fp2 = $simhash->hash($extractor->extract($text2));

        self::assertEquals(1, $comparator->compare($fp1, $fp2));
    }

    public function testGetBinary()
    {
        $text = "
   Mycket ansträngt på Stockholms sjukhus \n
                                       Vårdläget i Region Stockholm är mycket ansträngt inför helgerna, enligt chefläkaren Johan Bratt. Både covid-19 och säsongsinfluensan pressar sjukhusen – samtidigt
   ";
        $simhash   = new \Tga\SimHash\SimHash();
        $extractor = new \Tga\SimHash\Extractor\SimpleTextExtractor();

        $binary = $simhash->hash($extractor->extract($text), \Tga\SimHash\SimHash::SIMHASH_64)->getBinary();

        self::assertIsString($binary);
    }

    public function testThatBinaryUnaffectedBySwitchingFromFloatToInteger(){
        $text = "To anmeldelser om vold i weekenden Politiet har fået to anmeldelser om vold begået lørdag. Politiet har fået to anmeldelser om vold begået lørdag. I det ene tilfælde har en 23 årig kvinde fra Stenlille anmeldt, at hun udenfor Café Fox på Hovedgaden i Stenlille blev overfaldet og slået af en 30-årig kvinde, ligeledes fra Stenlille. Politiet formoder, at der er tale om et internt opgør. Samme lørdag kl. 18 blev en ansat på bostedet på den tidligere Kongskilde Friluftsgård overfaldet af en af beboerne. Også her blev der uddelt slag. I ingen af tilfældene kom nogen noget alvorligt til.";
        $simhash   = new \Tga\SimHash\SimHash();
        $extractor = new \Tga\SimHash\Extractor\SimpleTextExtractor();

        $binary = $simhash->hash($extractor->extract($text), \Tga\SimHash\SimHash::SIMHASH_64)->getBinary();

        self::assertEquals('0010010100101110101001011011100011000011000110100101100000000000',$binary);
    }
}
