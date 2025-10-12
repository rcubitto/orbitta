<?php

test('function to_cents works as expected', function () {
    expect(to_cents("$1"))->toBe(100);
    expect(to_cents("$12.33"))->toBe(1233);
    expect(to_cents("14.92"))->toBe(1492);
    expect(to_cents("$13"))->toBe(1300);
    expect(to_cents("17"))->toBe(1700);
    expect(to_cents("14.00001"))->toBe(1400);
    expect(to_cents("$64.99"))->toBe(6499);
    expect(to_cents("422.2"))->toBe(42220);
    expect(to_cents("10000"))->toBe(1000000);
    expect(to_cents(255.5))->toBe(25550);
});
