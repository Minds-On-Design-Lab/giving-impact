## Running documentation

Inline documentation is written in standard docblock style, thus can be processed using standard tools. The initial documentation has been processed using Sami (https://github.com/fabpot/sami) and a Sami config file is included in the repository.

To re-run the documentation, first download Sami and verify it works, then from the directory Sami has been installed in:

    $] php ./sami.php update /path/to/application/sami.php

Updated docs will be built and placed in `/path/to/application/docs`.

Again, if Sami proves to be inadequate, other, more beefy documentation processors can be used (like phpDocumentor2).