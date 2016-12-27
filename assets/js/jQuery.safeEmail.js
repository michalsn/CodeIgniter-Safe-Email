/**
 * @author      Michal Sniatala <m.sniatala@gmail.com>
 * @link        https://github.com/michalsn/CodeIgniter-Safe-Email
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @version     1.0
 */
(function ($) {
    $.extend({
        safeEmail: function (options) {
            var defaults = {
                "className": "ci-safe-email",
            };
            var options = $.extend({}, defaults, options);

            $('span[data-class="'+options.className+'"]').each(function() {
                var elem    = $(this);
                var a       = '';
                var b       = '';
                var c       = '';
                var d       = '';
                var content = '';
                var attr    = '';
                var link    = '';
                
                a       = elem.attr('data-key');
                c       = elem.attr('data-cipher');
                content = elem.attr('data-content') || '';
                attr    = elem.attr('data-attr') || '';
                link    = elem.attr('data-link') || '';
                b       = a.replace('#', '@').split('').sort().join('');

                for (var i = 0; i < c.length; i++) {
                    d += b.charAt(a.indexOf(c.charAt(i)));
                }

                if (content === '') {
                    content = d;
                } else {
                    content = content.replace(a, d);
                }

                attr = attr.replace(a, d);

                if (link === 'true') {
                    elem.replaceWith('<a href="mailto:'+d+'" '+attr+'>'+content+'</a>');
                } else {
                    elem.replaceWith(content);
                }
            });
     
            return this;
        }
    });
})(jQuery);