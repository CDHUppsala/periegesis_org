<script>
    function makeSPARQLQuery(endpointUrl, sparqlQuery, doneCallback) {
        var settings = {
            headers: {
                Accept: 'application/sparql-results+json'
            },
            data: {
                query: sparqlQuery
            }
        };
        return $.ajax(endpointUrl, settings).then(doneCallback);
    }

    var endpointUrl = 'https://query.wikidata.org/sparql',
        sparqlQuery = "SELECT ?person ?personLabel ?genderLabel ?personDescription ?entityLabel ?death ?fatherLabel ?motherLabel ?birthplaceLabel ?article ?father ?mother ?birthplace ?gender ?periodLabel \n" +
        "WHERE\n" +
        "{\n" +
        " ?person wdt:P1343 wd:Q3825645 .\n" +
        "  ?person wdt:P31 ?entity .\n" +
        "  \n" +
        "  OPTIONAL{?person wdt:P25 ?mother .}       \n" +
        "  OPTIONAL{?person wdt:P22 ?father .}\n" +
        "  OPTIONAL{?person wdt:P21 ?gender .}\n" +
        "    OPTIONAL{?person wdt:P570 ?death .}\n" +
        "	    OPTIONAL{?person wdt:P2348 ?period .}\n" +
        "   OPTIONAL{?person wdt:P19 ?birthplace .}\n" +
        "   OPTIONAL { ?article schema:about ?person ;\n" +
        " schema:isPartOf <https://en.wikipedia.org/>.}\n" +
        "\n" +
        "SERVICE wikibase:label { bd:serviceParam wikibase:language \" el,[AUTO_LANGUAGE], en, de, es, fr, it, la, ca, sk\" }\n" +
        "}\n" +
        "ORDER BY ?personLabel";

    makeSPARQLQuery(endpointUrl, sparqlQuery, function(data) {
        $('body').append($('<pre>').text(JSON.stringify(data)));
        console.log(data);
    });
</script>