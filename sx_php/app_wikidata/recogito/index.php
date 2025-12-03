<?php

class SPARQLQueryDispatcher
{
    private $endpointUrl;

    public function __construct(string $endpointUrl)
    {
        $this->endpointUrl = $endpointUrl;
    }

    public function query(string $sparqlQuery): array
    {

        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'Accept: application/sparql-results+json',
                    'User-Agent: WDQS-example PHP/' . PHP_VERSION, // TODO adjust this; see https://w.wiki/CX6
                ],
            ],
        ];
        $context = stream_context_create($opts);

        $url = $this->endpointUrl . '?query=' . urlencode($sparqlQuery);
        $response = file_get_contents($url, false, $context);
        return json_decode($response, true);
    }
}

function get_recogito_from_wikidata($lang) {
$endpointUrl = 'https://query.wikidata.org/sparql';
//$sparqlQueryString = <<< 'SPARQL'
$sparqlQueryString = '
SELECT ?person ?personLabel ?genderLabel ?personDescription ?entityLabel ?death ?fatherLabel ?motherLabel ?birthplaceLabel ?article ?father ?mother ?birthplace ?gender ?periodLabel 
WHERE
{
 ?person wdt:P1343 wd:Q3825645 .
  ?person wdt:P31 ?entity .
  
  OPTIONAL{?person wdt:P25 ?mother .}       
  OPTIONAL{?person wdt:P22 ?father .}
  OPTIONAL{?person wdt:P21 ?gender .}
    OPTIONAL{?person wdt:P570 ?death .}
	    OPTIONAL{?person wdt:P2348 ?period .}
   OPTIONAL{?person wdt:P19 ?birthplace .}
   OPTIONAL { ?article schema:about ?person ;
 schema:isPartOf <https://en.wikipedia.org/>.}

SERVICE wikibase:label { bd:serviceParam wikibase:language "'.$lang.'" }
}
ORDER BY ?personLabel
';
//SPARQL;

$queryDispatcher = new SPARQLQueryDispatcher($endpointUrl);
$queryResult = $queryDispatcher->query($sparqlQueryString);

var_export($queryResult);
}