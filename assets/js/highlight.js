// ================================================================
// SYNTAX HIGHLIGHTING
// ================================================================
// Uses highlight.js core (not the full bundle) to keep the JS
// payload small. Each language is imported individually.
//
// To add a new language:
//   1. Add the import: import go from "highlight.js/lib/languages/go"
//   2. Register it:    hljs.registerLanguage("go", go)
//
// Docs: https://highlightjs.org/
// ================================================================

import hljs from "highlight.js/lib/core";
import php from "highlight.js/lib/languages/php";
import bash from "highlight.js/lib/languages/bash";
import json from "highlight.js/lib/languages/json";
import yaml from "highlight.js/lib/languages/yaml";
import xml from "highlight.js/lib/languages/xml";
import css from "highlight.js/lib/languages/css";
import sql from "highlight.js/lib/languages/sql";
import javascript from "highlight.js/lib/languages/javascript";
import typescript from "highlight.js/lib/languages/typescript";

export function init() {
	hljs.registerLanguage("php", php);
	hljs.registerLanguage("bash", bash);
	hljs.registerLanguage("json", json);
	hljs.registerLanguage("yaml", yaml);
	hljs.registerLanguage("xml", xml);
	hljs.registerLanguage("css", css);
	hljs.registerLanguage("sql", sql);
	hljs.registerLanguage("javascript", javascript);
	hljs.registerLanguage("typescript", typescript);

	// Finds all <pre><code> blocks and applies highlighting
	hljs.highlightAll();
}
