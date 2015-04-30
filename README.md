# search-bundle
The search bundle is a bundle that offer to you a light search engine.
It works on lucene search library and include zend components.
## Installation
1. in AppKernel
<pre>
    new Ivory\LuceneSearchBundle\IvoryLuceneSearchBundle(),
    new Lpi\Bundle\SearchBundle\LpiSearchBundle(),
</pre>

## Configuration
1. in app/config/config.yml
<pre>
    ivory_lucene_search:

        # Index identifier
        search_index:
            # Path to store the index (Required)
            path: %kernel.cache_dir%/search_index

            # Index analyser (Optional)
            # See http://framework.zend.com/manual/en/zend.search.lucene.charset.html
            analyzer: ZendSearch\Lucene\Analysis\Analyzer\Common\Text\CaseInsensitive

            # Max Buffered documents (Optional)
            # See http://framework.zend.com/manual/en/zend.search.lucene.index-creation.html#zend.search.lucene.index-creation.optimization.maxbuffereddocs
            max_buffered_docs: 10

            # Max merge documents (Optional)
            # See http://framework.zend.com/manual/en/zend.search.lucene.index-creation.html#zend.search.lucene.index-creation.optimization.maxmergedocs
            max_merge_docs: 10000 # (default: PHP_INT_MAX)

            # Merge factor (Optional)
            # See http://framework.zend.com/manual/en/zend.search.lucene.index-creation.html#zend.search.lucene.index-creation.optimization.mergefactor
            merge_factor: 10

            # Index directory permission (Optional)
            # See http://framework.zend.com/manual/en/zend.search.lucene.index-creation.html#zend.search.lucene.index-creation.permissions
            permissions: 0777

            # Auto optmized flag (Optional)
            # If this flag is true, each time you request an index, it will be optmized
            # See http://framework.zend.com/manual/en/zend.search.lucene.index-creation.html#zend.search.lucene.index-creation.optimization
            auto_optimized: false

            # Query parser encoding (Optional)
            # See http://framework.zend.com/manual/en/zend.search.lucene.searching.html#zend.search.lucene.searching.query_building.parsing
            query_parser_encoding: "UTF-8" # (default: "")
</pre>
2. For each entities you want to be indexed, you will have to add the "Lpi\Bundle\SearchBundle\Model\IndexableInterface" 
    an entity need to have these 4 methods:
    * getId()
    * getTitle()
    * getSlug()
    * getDescription()
3. in app/config/config.yml

<pre>
    lpi_search:
        mappings: 
            - { value: ApplicationLpiEventBundle:Event , path: programmation_detail} #name of the entity you want to be indexed
</pre>

## Usage
Now you have all indexes registered and you want to uses them, really easy!

in a controller for example :

<pre>
/**
 * @param Request $request
 * @return array
 * @Route("/search", name="path_search")
 * @Template()
 * @Method({"GET", "POST"})
 */
public function searchAction(Request $request) {
    $results = null;
    if ($request->request->has('term') and '' !== $request->request->get('term')) {
        $results = $this->get('lpi_lucene.search')->search($request->request->get('term'));
    }

    return array(
        'results' => $results
    );
}
</pre>

and the render in your view for example:

    {% if results is defined and results|length > 0 %}
        {% for result in results %}
            <div class="col-xs-12">
                <a href="{{ result.url }}" title="{{ result.title }}">
                    {{ result.title }}
                </a>
            </div>
        {% endfor %}
    {% else %}
        <div class="alert alert-warning">{{ 'search.result.nothing'|trans({}, 'messages') }}</div>
    {% endif %}

