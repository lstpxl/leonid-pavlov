import LazyLoad from "./vanilla-lazyload-19.1.3/dist/esm/lazyload.js";

document.addEventListener("DOMContentLoaded", function () {
  "use strict";
  const lazyLoadInstance = new LazyLoad({
    elements_selector: ".lazy",
  });
});