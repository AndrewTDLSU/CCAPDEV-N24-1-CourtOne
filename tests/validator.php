<?php
// Run with: php tests/validator.php
require_once __DIR__ . '/../core/Validator.php';

set_error_handler(static function (int $severity, string $message) {
    throw new RuntimeException($message);
});

$validator = new Validator();
$check = static function (mixed $input, array $rule, mixed $expected, bool $valid = true) use ($validator): void {
    $actual = $validator->validate(['field' => $input], ['field' => $rule]);
    if ($actual !== $valid || $validator->clean() !== ($valid ? ['field' => $expected] : [])) {
        throw new RuntimeException('Unexpected result for ' . var_export($input, true));
    }
};

$rate = ['required' => true, 'type' => 'float', 'min' => 0.01, 'max' => 99999999.99, 'decimals' => 2];
$check(' 12.50 ', $rate, 12.5);
$check('.01', $rate, 0.01);
$check('99999999.99', $rate, 99999999.99);
foreach (['0', '-0.01', '100000000', '1.001', '1e2', '1,000.00', INF, NAN, true, ['1.50']] as $invalid) {
    $check($invalid, $rate, null, false);
}
$check('0', ['type' => 'float'], 0.0);

$check('42', Validator::ID_RULE, 42);
foreach (['42x', '4 2', '1.0', '2147483648', 0, true, ['42']] as $invalid) {
    $check($invalid, Validator::ID_RULE, null, false);
}

$date = ['type' => 'date', 'required' => true];
$check('2028-02-29', $date, '2028-02-29');
foreach (['2027-02-29', '2028-02-30', '2028-2-9', '0000-01-01', ['2028-02-29']] as $invalid) {
    $check($invalid, $date, null, false);
}

$password = ['required' => true, 'trim' => false, 'min' => 8, 'maxBytes' => 72];
$check(' Aa1!bc ', $password, ' Aa1!bc ');
$check('Abc1!' . str_repeat('é', 33) . ' ', $password, 'Abc1!' . str_repeat('é', 33) . ' ');
$check('Abc1!' . str_repeat('é', 34), $password, null, false);
$check("abc1!\0def", $password, null, false);
$check('other', ['allowed' => ['user', 'court_owner']], null, false);

$message = ['required' => true, 'max' => 500, 'multiline' => true];
$check(str_repeat('😀', 498) . "\r\nx", $message, str_repeat('😀', 498) . "\nx");
$check(str_repeat('😀', 501), $message, null, false);
$check("\u{3000}", $message, null, false);
$check("\xff", $message, null, false);
// Reusing the validator clears both previous values and errors.
$check('  Ready  ', $message, 'Ready');
if (!$validator->validate([], ['optional' => ['type' => 'int']]) ||
    $validator->clean() !== [] || $validator->errors() !== []) {
    throw new RuntimeException('Validation state persisted between requests.');
}

echo "PASS: decimal ranges and precision, IDs, calendar dates, password bytes, and Unicode input.\n";
