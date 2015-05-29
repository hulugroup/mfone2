function amshopby_slider_ui_update_values( prefix, values ) {
    if ($amQuery('#' + prefix + '-from')) {
        $amQuery('#' + prefix + '-from').val(values[0]);
        $amQuery('#' + prefix + '-to').val(values[1]);
    }

    if ($amQuery('#' + prefix + '-from-slider')) {
        $amQuery('#' + prefix + '-from-slider').html(values[0]);
        $amQuery('#' + prefix + '-to-slider').html(values[1]);
    }
}

function amshopby_slider_ui_apply_filter( evt, values) {
    if (evt && evt.type == 'keypress' && 13 != evt.keyCode)
        return;

    var prefix = 'amshopby-price';

    if (typeof(evt) == 'string'){
        prefix = evt;
    }

    var a = prefix + '-from';
    var b = prefix + '-to';

    var numFrom = parseFloat($amQuery('#' + a).val());
    var numTo = parseFloat($amQuery('#' + b).val());

    var url =  $amQuery('#' + prefix + '-url').val().replace(a, values[0]).replace(b, values[1]);

    if (typeof amshopby_working != 'undefined' && !amshopby_ajax_fallback_mode()) {
        amshopby_ajax_push_state(url);
        amshopby_ajax_request(url);
    } else {
        setLocation(url);
    }
}

function amshopby_slider_ui_init(from, to, max, prefix, min, step) {

    var slider = $amQuery('#' + prefix + '-ui');

    from = from ? from : min;
    to = to ? to : max;

    if (slider) {
        slider.slider({
            range: true,
            min: parseFloat(min),
            max: parseFloat(max),
            step: parseFloat(step),
            values: [parseFloat(from), parseFloat(to)],
            slide: function (event, ui) {
                amshopby_slider_ui_update_values(prefix, ui.values);
            },
            change: function (event, ui) {
                if (ui.values[0] != from || ui.values[1] != to) {
                    amshopby_slider_ui_apply_filter(prefix, ui.values);
                }
            }
        });
    }
}

function amshopby_jquery_init () {
    $amQuery('.amshopby-slider-ui-param').each(function() {
        var params = this.value.split(',');
        amshopby_slider_ui_init( params[0], params[1], parseInt(params[2]), params[3], parseInt(params[4]), params[5] );
    });
}

(function ($) {
    $('document').ready(function () {
        amshopby_jquery_init();
    });
})($amQuery);