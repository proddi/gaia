<pre>
<?php

error_reporting(E_ALL);

require_once('../../libs/gaia.php');

function printBaseUri() {
    return function($req) {
        var_dump($req->baseUri, $req->uri);
    };
};

gaiaServer::run(
    function($req, $res) {
        echo "---> '/gaia/scratchbox/baseUri/'\n".
             "---? '$req->baseUri'\n\n";
    },

    gaiaServer::path('/docs/page-:pageId*', printBaseUri()),
    gaiaServer::router(array(
        '/docs/page-123' => printBaseUri(),
        '/docs/page-:pageId*' => function($req, $res) {
                                     echo "---> '/gaia/scratchbox/baseUri/docs/page-[pageId]/'\n".
                                          "---? '$req->baseUri'\n\n";
                                 },

        '/foo*' => printBaseUri(),
        '/foo' => printBaseUri(),
        '*' =>  function($req, $res) {
                    echo "---> '/gaia/scratchbox/baseUri/'\n".
                         "---? '$req->baseUri'\n\n";
                },

    )),

    function($req, $res) {
        echo "---> '/gaia/scratchbox/baseUri/'\n".
             "---? '$req->baseUri'\n\n";
    }
);


?>
</pre>