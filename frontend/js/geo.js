import * as Msg from './msg'
import * as Hook from './hook'
import * as Lang from './lang'
import $ from "jquery";
import * as Ajax from "./ajax"

/**
 * Гео-объекты
 */


/**
 * Инициализация селектов выбора гео-объекта
 */
export function initSelect() {
    $.each($('.js-geo-select'), function (k, v) {
        $(v).find('.js-geo-country').bind('change', function (e) {
            this.loadRegions($(e.target));
        }.bind(this));
        $(v).find('.js-geo-region').bind('change', function (e) {
            this.loadCities($(e.target));
        }.bind(this));
    }.bind(this));
};

export function loadRegions($country) {
    $region = $country.parents('.js-geo-select').find('.js-geo-region');
    $city = $country.parents('.js-geo-select').find('.js-geo-city');
    $region.empty();
    $region.append('<option value="">' + Lang.get('geo_select_region') + '</option>');
    $city.empty();
    $city.hide();

    if (!$country.val()) {
        $region.hide();
        return;
    }

    var url = aRouter['ajax'] + 'geo/get/regions/';
    var params = {country: $country.val()};
    Hook.marker('loadRegionsBefore');
    Ajax.ajax(url, params, function (result) {
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            $.each(result.aRegions, function (k, v) {
                $region.append('<option value="' + v.id + '">' + v.name + '</option>');
            });
            $region.show();
            Hook.run('ls_geo_load_regions_after', [$country, result]);
        }
    });
};

export function loadCities($region) {
    $city = $region.parents('.js-geo-select').find('.js-geo-city');
    $city.empty();
    $city.append('<option value="">' + Lang.get('geo_select_city') + '</option>');

    if (!$region.val()) {
        $city.hide();
        return;
    }

    var url = aRouter['ajax'] + 'geo/get/cities/';
    var params = {region: $region.val()};
    Hook.marker('loadCitiesBefore');
    Ajax.ajax(url, params, function (result) {
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            $.each(result.aCities, function (k, v) {
                $city.append('<option value="' + v.id + '">' + v.name + '</option>');
            });
            $city.show();
            Hook.run('ls_geo_load_cities_after', [$region, result]);
        }
    });
};
