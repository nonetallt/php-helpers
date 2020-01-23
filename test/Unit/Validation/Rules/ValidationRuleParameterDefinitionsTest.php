<?php

namespace Test\Unit\Validation\Rules;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Validation\Parameters\ValidationRuleParameterDefinitions;
use Nonetallt\Helpers\Validation\Parameters\ValidationRuleParameterDefinition;

class ValidationRuleParameterDefinitionsTest extends TestCase
{
    public function testTrueStringValueCanBeConvertedToBooleanAndPassValueValidation()
    {
        $params = new ValidationRuleParameterDefinitions([
            new ValidationRuleParameterDefinition(1, 'test_param', true, 'boolean')
        ]);

        $values = [
            'test_param' => 'true'
        ];

        /* Apply mapping to use mutators */
        $values = $params->mapValues($values);
        $this->assertTrue($params->validateValues($values, 'test'));
    }
    
    public function testFooStringValueDoesNotPassValidation()
    {
        $params = new ValidationRuleParameterDefinitions([
            new ValidationRuleParameterDefinition(1, 'test_param', true, 'boolean')
        ]);

        $values = [
            'test_param' => 'foo'
        ];

        $msg = "Value 'foo' could not be converted to boolean";
        $this->expectExceptionMessage($msg);

        /* Apply mapping to use mutators */
        $values = $params->mapValues($values);
        $params->validateValues($values, 'test');
    }
}
