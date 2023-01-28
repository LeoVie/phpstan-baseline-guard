# phpstan-baseline-guard

The wonderful static analysis tool [PHPStan](https://phpstan.org/) has a
[baseline feature](https://phpstan.org/user-guide/baseline). With that, you can generate a file containing all existing
errors in your code. These errors than get ignored in further runs of PHPStan.

Using the baseline feature, you can make sure, that every new code you introduce
can get analyzed on a high PHPStan level without having to fix all errors
in your existing (legacy) code first.

You typically do not want to add more errors to you baseline, once generated.
`phpstan-baseline-guard` is a small tool to make sure, you do not do that.

## Installation
Install via Composer
```bash
composer require --dev leovie/phpstan-baseline-guard
```

## Usage
Given, you have generated a PHPStan baseline file `/foo/baseline.neon`

1. Run `phpstan-baseline-guard` with only the absolute path to your baseline file,
    e.g.
```bash
phpstan-baseline-guard /foo/baseline.neon
```

You will get an output like
```bash
 [INFO] Your baseline contains 20 ignored errors. 
```

2. Use the count of ignored errors from the first command run and run
    `phpstan-baseline-guard` with a fixed max count of ignored errors in the
    baseline, e.g.
```bash
phpstan-baseline-guard /foo/baseline.neon --max-ignored-errors=20
```

You will get an output like
```bash
 [INFO] Your baseline contains 20 ignored errors. - OK
```

Now, if you add more ignored errors to your baseline and run the same command
again, you will get an output like
```bash
 [ERROR] Your baseline contains 25 ignored errors. - That's more than allowed.
```

The command fails in that case and prevents you from adding more errors
to your baseline file than you explicitly allow.

### CI
You can add `phpstan-baseline-guard` to your CI pipeline. The command
will fail, when a developer tries to add more ignored errors to the baseline.