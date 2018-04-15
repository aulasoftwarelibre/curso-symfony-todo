global.$ = global.jQuery = require('jquery');

require('semantic-ui-css');

$(document)
    .ready(function() {
        $('.ui.checkbox').checkbox();
        $('.ui.dropdown').dropdown();
    })
;