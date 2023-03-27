# Behat Context Gatherer Extension

# Table of contents

* [Installation](#installation)
* [Usage](#usage)
* [Versioning and release cycle](#versioning-and-release-cycle)
* [License](#license)

## Installation

Compatible with PHP 7.4 and later.

Compatible with Behat 3.0.0 and later

1. Require this extension using *Composer*

```bash
composer require --dev zayon/behat-gather-context-extension
```

2. Enable it within your Behat configuration:

```yaml
# behat.yaml.dist / behat.yaml

default:
  extensions:
    Zayon\BehatGatherContextExtension\ContextGathererExtension: ~
```

## Usage

```php
<?php
# tests/Behat/DemoContext.php

namespace Acme\Tests\Behat;

use Behat\Behat\Context\Context;

final class DemoContext implements Context
{
    // a beforeScenario hook will automatically be created to gather AnotherContext
    private AnotherContext $anotherContext;
}
```

## Versioning and release cycle

This package follows [semantic versioning](https://semver.org/).

## License

This extension is completely free and released under permissive [MIT license](LICENSE).
