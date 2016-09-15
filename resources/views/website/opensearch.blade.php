<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/"
                       xmlns:moz="http://www.mozilla.org/2006/browser/search/">
    <ShortName>S.A. Proto</ShortName>
    <Description>
        This search engine lets you search in the data of Study Association Proto from the University of Twente.
    </Description>
    <InputEncoding>UTF-8</InputEncoding>
    <Image width="16" height="16" type="image/x-icon">{{ asset('images/favicons/favicon2.png') }}</Image>
    <Url type="text/html" method="get" template="{{ route('search') }}/?query={searchTerms}"/>
</OpenSearchDescription>