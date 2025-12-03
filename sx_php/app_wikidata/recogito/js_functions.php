<script>
    class SPARQLQueryDispatcher {
        constructor(endpoint) {
            this.endpoint = endpoint;
        }

        query(sparqlQuery) {
            const fullUrl = this.endpoint + '?query=' + encodeURIComponent(sparqlQuery);
            const headers = {
                'Accept': 'application/sparql-results+json'
            };

            return fetch(fullUrl, {
                headers
            }).then(body => body.json());
        }
    }

    const endpointUrl = 'https://query.wikidata.org/sparql';
    const sparqlQuery = `SELECT ?person ?personLabel ?genderLabel ?personDescription ?entityLabel ?death ?fatherLabel ?motherLabel ?birthplaceLabel ?article ?father ?mother ?birthplace ?gender ?periodLabel 
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

SERVICE wikibase:label { bd:serviceParam wikibase:language " el,[AUTO_LANGUAGE], en, de, es, fr, it, la, ca, sk" }
}
ORDER BY ?personLabel`;

    const queryDispatcher = new SPARQLQueryDispatcher(endpointUrl);
    queryDispatcher.query(sparqlQuery).then(console.log);
</script>