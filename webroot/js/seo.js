(function($) {

    /**
     * Manage Seo Meta Tags block in admin view.
     */
    $.fn.seoMetas = function(options) {
        var defaults = {
            addButton: '#addMetaTag',
            metaTags: '#metaTags',
            template: '<tr>\
                <td><div class="form-group"><input type="text" name="seo_meta_tags[{{lgth}}][name]" placeholder="Ex : og:title" maxlength="255" id="seo-meta-tags-{{lgth}}-name" class="form-control" value=""></div></td>\
                <td><div class="form-group"><input type="text" name="seo_meta_tags[{{lgth}}][content]" maxlength="255" id="seo-meta-tags-{{lgth}}-content" class="form-control" value=""></div></td>\
                <td><div class="checkbox-custom mb5"><input type="hidden" name="seo_meta_tags[{{lgth}}][is_http_equiv]" value="0"><input type="checkbox" name="seo_meta_tags[{{lgth}}][is_http_equiv]" value="1" id="seo-meta-tags-{{lgth}}-is-http-equiv"><label for="seo-meta-tags-{{lgth}}-is-http-equiv">Is Http Equiv</label></div></td>\
                <td><div class="checkbox-custom mb5"><input type="hidden" name="seo_meta_tags[{{lgth}}][is_property]" value="0"><input type="checkbox" name="seo_meta_tags[{{lgth}}][is_property]" value="1" id="seo-meta-tags-{{lgth}}-is-property"><label for="seo-meta-tags-{{lgth}}-is-property">Is Property</label></div></td></tr>'
        }
        var settings = $.extend({}, defaults, options);

        $(settings.addButton).on('click', function(e) {
            e.preventDefault();
            $metaTags = $(settings.metaTags);

            var lgth = $metaTags.find('tr').length;
            
            tpl = settings.template.replace(/{{lgth}}/g, lgth);

            $metaTags.append(tpl);
        });
    }

})(jQuery);