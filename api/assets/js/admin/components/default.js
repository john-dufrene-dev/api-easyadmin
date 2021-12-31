import React from "react";
import SerpPreview from "react-serp-preview";

function Default() {
    return (
        // Test with preview search engine module : https://www.npmjs.com/package/react-serp-preview
        <SerpPreview
            title="domain name"
            metaDescription={`Meta description`}
            url="https://site.com/"
        />
    );
}

export default Default;
