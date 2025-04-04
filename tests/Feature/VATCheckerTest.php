<?php

namespace JairForo\VATChecker\Tests\Feature;

use DateTime;
use JairForo\VATChecker\Api\FakeGateway;
use JairForo\VATChecker\Exceptions\InvalidVATException;
use JairForo\VATChecker\Objects\VATResponse;
use JairForo\VATChecker\VATChecker;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class VATCheckerTest extends TestCase
{
    /** @var VATChecker */
    private $checker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->checker = new VATChecker();
        $this->checker->setApiGateway(new FakeGateway());
    }

    #[Test]
    public function should_return_a_valid_vat_response()
    {
        $countryCode = 'RO';
        $vatNumber = '11530967';

        $response = $this->checker->check($countryCode, $vatNumber);

        $this->assertInstanceOf(VATResponse::class, $response);
        $this->assertEquals('RO', $response->country_code);
        $this->assertEquals('11530967', $response->vat_number);
        $this->assertEquals(DateTime::createFromFormat('Y-m-dP', date('Y-m-dP')), $response->requested_at);
        $this->assertEquals('UNICORN B.V.', $response->company_name);
        $this->assertEquals('UNICORN STREET 007', $response->address);
        $this->assertEquals('1108DH', $response->zipcode);
        $this->assertEquals('AMSTERDAM', $response->city);
        $this->assertEquals('UNICORN STREET 007\n
            1108DH AMSTERDAM', $response->original_address);
    }
    
    #[Test]
    public function should_throw_an_invalid_vat_exception_for_being_an_invalid_vat_number()
    {
        $countryCode = 'BR';
        $vatNumber = '11530967';

        $this->expectException(InvalidVATException::class);

        $this->checker->check($countryCode, $vatNumber);
    }

    #[Test]
    public function should_be_a_valid_vat_number()
    {
        $countryCode = 'RO';
        $vatNumber = '11530967';

        $this->assertTrue($this->checker->isValid($countryCode, $vatNumber));
    }

    #[Test]
    public function should_be_an_invalid_vat_number()
    {
        $countryCode = 'BR';
        $vatNumber = '11530967';

        $this->assertFalse($this->checker->isValid($countryCode, $vatNumber));
    }
}
