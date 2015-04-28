# search-bundle

## Installation
1. in AppKernel
<pre>
    new Ivory\LuceneSearchBundle\IvoryLuceneSearchBundle(),
    new Lpi\Bundle\SearchBundle\LpiSearchBundle(),
</pre>

## Configuration
2. in app/config/config.yml
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
