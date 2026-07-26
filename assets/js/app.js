// ================================================================
// ENTRY POINT — WebifyCMS frontend
// ================================================================
// Vite bundles everything starting from this file. Each module
// exports an init() function called once on page load.
//
// To add a new feature:
//   1. Create assets/js/yourFeature.js with an export function init()
//   2. Import and call it below
// ================================================================

import "../img/daisycon.svg";
import "../css/app.css";

import * as highlight from "./highlight.js";
import * as nav from "./nav.js";
import * as theme from "./theme.js";
import * as reveal from "./reveal.js";
import * as signup from "./signup.js";
import * as copy from "./copy.js";

highlight.init();
nav.init();
theme.init();
reveal.init();
signup.init();
copy.init();
